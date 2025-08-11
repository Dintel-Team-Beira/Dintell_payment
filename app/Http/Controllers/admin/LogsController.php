<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LogsController extends Controller
{
    protected $logPath;

    public function __construct()
    {
        $this->logPath = storage_path('logs');
    }

    /**
     * Display a listing of the logs.
     */
    public function index(Request $request)
    {
        $logs = $this->getLogFiles();
        $stats = $this->getLogStats($logs);

        // Filtros
        if ($request->filled('type')) {
            $logs = collect($logs)->filter(function ($log) use ($request) {
                return str_contains($log['name'], $request->type);
            })->values()->all();
        }

        if ($request->filled('date')) {
            $logs = collect($logs)->filter(function ($log) use ($request) {
                return $log['date']->format('Y-m-d') === $request->date;
            })->values()->all();
        }

        return view('admin.logs.index', compact('logs', 'stats'));
    }

    /**
     * Display the specified log.
     */
    public function show($logFile)
    {
        $logPath = $this->logPath . '/' . $logFile;

        if (!File::exists($logPath) || !$this->isValidLogFile($logFile)) {
            abort(404, 'Log file not found');
        }

        $content = File::get($logPath);
        $lines = $this->parseLogContent($content);

        $stats = [
            'total_lines' => count($lines),
            'error_count' => count(array_filter($lines, fn($line) => str_contains($line['level'] ?? '', 'ERROR'))),
            'warning_count' => count(array_filter($lines, fn($line) => str_contains($line['level'] ?? '', 'WARNING'))),
            'info_count' => count(array_filter($lines, fn($line) => str_contains($line['level'] ?? '', 'INFO'))),
            'file_size' => $this->formatBytes(File::size($logPath))
        ];

        return view('admin.logs.show', compact('logFile', 'lines', 'stats'));
    }

    /**
     * Download the specified log.
     */
    public function download($logFile)
    {
        $logPath = $this->logPath . '/' . $logFile;

        if (!File::exists($logPath) || !$this->isValidLogFile($logFile)) {
            abort(404, 'Log file not found');
        }

        return response()->download($logPath);
    }

    /**
     * Remove the specified log.
     */
    public function destroy($logFile)
    {
        $logPath = $this->logPath . '/' . $logFile;

        if (!File::exists($logPath) || !$this->isValidLogFile($logFile)) {
            return response()->json(['success' => false, 'message' => 'Log file not found'], 404);
        }

        if (File::delete($logPath)) {
            return response()->json(['success' => true, 'message' => 'Log excluído com sucesso!']);
        }

        return response()->json(['success' => false, 'message' => 'Erro ao excluir log'], 500);
    }

    /**
     * Clear all logs.
     */
    public function clear()
    {
        try {
            $files = File::files($this->logPath);
            $deletedCount = 0;

            foreach ($files as $file) {
                if ($this->isValidLogFile($file->getFilename())) {
                    File::delete($file->getPathname());
                    $deletedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Todos os logs foram limpos! ({$deletedCount} arquivos excluídos)"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao limpar logs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all log files.
     */
    private function getLogFiles()
    {
        if (!File::exists($this->logPath)) {
            return [];
        }

        $files = File::files($this->logPath);
        $logs = [];

        foreach ($files as $file) {
            if ($this->isValidLogFile($file->getFilename())) {
                $logs[] = [
                    'name' => $file->getFilename(),
                    'path' => $file->getPathname(),
                    'size' => $this->formatBytes($file->getSize()),
                    'size_bytes' => $file->getSize(),
                    'date' => Carbon::createFromTimestamp($file->getMTime()),
                    'type' => $this->getLogType($file->getFilename()),
                    'level' => $this->getLogLevel($file->getPathname())
                ];
            }
        }

        // Ordenar por data mais recente
        usort($logs, function ($a, $b) {
            return $b['date']->timestamp - $a['date']->timestamp;
        });

        return $logs;
    }

    /**
     * Get log statistics.
     */
    private function getLogStats($logs)
    {
        $totalSize = array_sum(array_column($logs, 'size_bytes'));
        $types = array_count_values(array_column($logs, 'type'));

        return [
            'total_files' => count($logs),
            'total_size' => $this->formatBytes($totalSize),
            'types' => $types,
            'latest_log' => count($logs) > 0 ? $logs[0]['date']->diffForHumans() : 'Nenhum log',
            'oldest_log' => count($logs) > 0 ? end($logs)['date']->diffForHumans() : 'Nenhum log'
        ];
    }

    /**
     * Parse log content into structured data.
     */
    private function parseLogContent($content)
    {
        $lines = explode("\n", $content);
        $parsedLines = [];

        foreach ($lines as $index => $line) {
            if (empty(trim($line))) continue;

            $parsed = $this->parseLogLine($line);
            $parsed['line_number'] = $index + 1;
            $parsedLines[] = $parsed;
        }

        return array_reverse(array_slice($parsedLines, -1000)); // Últimas 1000 linhas
    }

    /**
     * Parse a single log line.
     */
    private function parseLogLine($line)
    {
        // Padrão Laravel: [2024-01-01 12:00:00] local.ERROR: Message
        $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.*)$/';

        if (preg_match($pattern, $line, $matches)) {
            return [
                'timestamp' => $matches[1],
                'environment' => $matches[2],
                'level' => $matches[3],
                'message' => $matches[4],
                'raw' => $line,
                'formatted_time' => Carbon::parse($matches[1])->format('H:i:s'),
                'level_class' => $this->getLevelClass($matches[3])
            ];
        }

        return [
            'timestamp' => '',
            'environment' => '',
            'level' => 'UNKNOWN',
            'message' => $line,
            'raw' => $line,
            'formatted_time' => '',
            'level_class' => 'text-gray-600'
        ];
    }

    /**
     * Check if file is a valid log file.
     */
    private function isValidLogFile($filename)
    {
        return str_ends_with($filename, '.log');
    }

    /**
     * Get log type from filename.
     */
    private function getLogType($filename)
    {
        if (str_contains($filename, 'laravel')) return 'Laravel';
        if (str_contains($filename, 'error')) return 'Error';
        if (str_contains($filename, 'access')) return 'Access';
        if (str_contains($filename, 'query')) return 'Query';
        if (str_contains($filename, 'mail')) return 'Mail';
        if (str_contains($filename, 'queue')) return 'Queue';

        return 'General';
    }

    /**
     * Get log level from file content.
     */
    private function getLogLevel($filepath)
    {
        $content = File::get($filepath);

        if (str_contains($content, '.ERROR:')) return 'ERROR';
        if (str_contains($content, '.WARNING:')) return 'WARNING';
        if (str_contains($content, '.INFO:')) return 'INFO';
        if (str_contains($content, '.DEBUG:')) return 'DEBUG';

        return 'MIXED';
    }

    /**
     * Get CSS class for log level.
     */
    private function getLevelClass($level)
    {
        return match(strtoupper($level)) {
            'ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY' => 'text-red-600 bg-red-50',
            'WARNING' => 'text-yellow-600 bg-yellow-50',
            'INFO', 'NOTICE' => 'text-blue-600 bg-blue-50',
            'DEBUG' => 'text-gray-600 bg-gray-50',
            default => 'text-gray-600'
        };
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
