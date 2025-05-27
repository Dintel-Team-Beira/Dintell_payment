<?php

namespace App\Console\Commands;

use App\Models\ApiLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanupApiLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:cleanup
                            {--days=30 : Number of days to keep logs}
                            {--force : Force deletion without confirmation}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old API logs to free up database space';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $daysToKeep = (int) $this->option('days');
        $force = $this->option('force');
        $dryRun = $this->option('dry-run');

        if ($daysToKeep < 1) {
            $this->error('Days must be a positive number.');
            return 1;
        }

        $cutoffDate = Carbon::now()->subDays($daysToKeep);

        $this->info("Cleaning up API logs older than {$daysToKeep} days (before {$cutoffDate->format('Y-m-d H:i:s')})...");

        // Count logs to be deleted
        $logsToDelete = ApiLog::where('created_at', '<', $cutoffDate)->count();

        if ($logsToDelete === 0) {
            $this->info('No logs found to delete.');
            return 0;
        }

        $this->line("Found {$logsToDelete} logs to delete.");

        if ($dryRun) {
            $this->warn('DRY RUN: No logs will actually be deleted.');
            $this->showStatistics($cutoffDate);
            return 0;
        }

        // Confirm deletion unless forced
        if (!$force) {
            if (!$this->confirm("Are you sure you want to delete {$logsToDelete} logs?")) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        // Show statistics before deletion
        $this->showStatistics($cutoffDate);

        // Perform deletion in batches to avoid memory issues
        $deleted = 0;
        $batchSize = 1000;

        $this->output->progressStart($logsToDelete);

        do {
            $batch = ApiLog::where('created_at', '<', $cutoffDate)
                           ->limit($batchSize)
                           ->delete();

            $deleted += $batch;
            $this->output->progressAdvance($batch);

            // Small delay to prevent overwhelming the database
            usleep(10000); // 10ms

        } while ($batch > 0);

        $this->output->progressFinish();

        $this->info("\nSuccessfully deleted {$deleted} API logs.");

        // Show remaining statistics
        $remaining = ApiLog::count();
        $this->line("Remaining logs in database: {$remaining}");

        return 0;
    }

    /**
     * Show statistics about logs to be deleted
     */
    private function showStatistics(Carbon $cutoffDate): void
    {
        $this->line('');
        $this->line('<comment>Statistics for logs to be deleted:</comment>');

        // Count by status
        $statusCounts = ApiLog::where('created_at', '<', $cutoffDate)
            ->selectRaw('
                CASE
                    WHEN response_code >= 200 AND response_code < 300 THEN "Success"
                    WHEN response_code >= 300 AND response_code < 400 THEN "Redirect"
                    WHEN response_code >= 400 AND response_code < 500 THEN "Client Error"
                    WHEN response_code >= 500 THEN "Server Error"
                    ELSE "Unknown"
                END as status_type,
                COUNT(*) as count
            ')
            ->groupBy('status_type')
            ->get();

        $this->table(
            ['Status Type', 'Count'],
            $statusCounts->map(fn($item) => [$item->status_type, number_format($item->count)])
        );

        // Top domains
        $topDomains = ApiLog::where('created_at', '<', $cutoffDate)
            ->selectRaw('domain, COUNT(*) as count')
            ->groupBy('domain')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        if ($topDomains->isNotEmpty()) {
            $this->line('');
            $this->line('<comment>Top 5 domains:</comment>');
            $this->table(
                ['Domain', 'Count'],
                $topDomains->map(fn($item) => [$item->domain, number_format($item->count)])
            );
        }

        // Date range
        $oldestLog = ApiLog::where('created_at', '<', $cutoffDate)
            ->orderBy('created_at')
            ->first();

        if ($oldestLog) {
            $this->line('');
            $this->line("Oldest log to be deleted: {$oldestLog->created_at->format('Y-m-d H:i:s')} ({$oldestLog->created_at->diffForHumans()})");
        }
    }
}