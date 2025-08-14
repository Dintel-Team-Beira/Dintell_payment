<?php

namespace App\Helpers;

use App\Models\DocumentTemplate;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

class DocumentTemplateHelper
{
    /**
     * Configurações padrão para PDF
     */
    const DEFAULT_PAPER_SIZE = 'A4';
    const DEFAULT_ORIENTATION = 'portrait';
    const DEFAULT_FONT = 'DejaVu Sans';
    
    /**
     * Gerar e baixar PDF do template
     *
     * @param DocumentTemplate $template
     * @param array $data
     * @param array $options
     * @return Response
     * @throws \Exception
     */
    public static function downloadPdfDocument(
        DocumentTemplate $template, 
        array $data, 
        array $options = []
    ): Response {
        try {
            // Renderizar HTML
            $html = self::renderHtml($template, $data);
            
            // Gerar PDF
            $dompdf = self::generatePdf($html, $options);
            
            // Nome do arquivo
            $fileName = self::generateFileName($template, $options);
            
            // Log da operação
            self::logPdfGeneration($template, $fileName);
            
            return self::createDownloadResponse($dompdf, $fileName);
            
        } catch (\Exception $e) {
            Log::error('Erro ao gerar PDF do template', [
                'template_id' => $template->id,
                'template_name' => $template->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new \Exception('Falha ao gerar PDF: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Renderizar apenas o HTML (para preview)
     *
     * @param DocumentTemplate $template
     * @param array $data
     * @return string
     */
    public static function renderHtml(DocumentTemplate $template, array $data): string
    {
        try {
            // Renderizar template Blade
            $html = Blade::render($template->html_template, $data);
            
            // Aplicar CSS
            if ($template->css_styles) {
                $cssStyles = self::processCssStyles($template->css_styles);
                $html = "<style>{$cssStyles}</style>" . $html;
            }
            
            return $html;
            
        } catch (\Exception $e) {
            Log::error('Erro ao renderizar HTML do template', [
                'template_id' => $template->id,
                'error' => $e->getMessage()
            ]);
            
            throw new \Exception('Falha ao renderizar template: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Gerar PDF a partir do HTML
     *
     * @param string $html
     * @param array $options
     * @return Dompdf
     */
    public static function generatePdf(string $html, array $options = []): Dompdf
    {
        $dompdfOptions = self::loadOptions($options);
        $dompdf = new Dompdf($dompdfOptions);
        
        // Carregar HTML
        $dompdf->loadHtml($html);
        
        // Configurar papel
        $paperSize = $options['paper_size'] ?? self::DEFAULT_PAPER_SIZE;
        $orientation = $options['orientation'] ?? self::DEFAULT_ORIENTATION;
        $dompdf->setPaper($paperSize, $orientation);
        
        // Renderizar
        $dompdf->render();
        
        return $dompdf;
    }
    
    /**
     * Salvar PDF em arquivo
     *
     * @param DocumentTemplate $template
     * @param array $data
     * @param string $filePath
     * @param array $options
     * @return bool
     */
    public static function savePdfToFile(
        DocumentTemplate $template,
        array $data,
        string $filePath,
        array $options = []
    ): bool {
        try {
            $html = self::renderHtml($template, $data);
            $dompdf = self::generatePdf($html, $options);
            
            // Salvar arquivo
            $pdfContent = $dompdf->output();
            return file_put_contents($filePath, $pdfContent) !== false;
            
        } catch (\Exception $e) {
            Log::error('Erro ao salvar PDF', [
                'template_id' => $template->id,
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Configurar opções do dompdf
     *
     * @param array $customOptions
     * @return Options
     */
    protected static function loadOptions(array $customOptions = []): Options
    {
        $options = new Options();
        
        // Configurações padrão
        $defaultOptions = [
            'defaultFont' => self::DEFAULT_FONT,
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'chroot' => public_path(),
            'tempDir' => sys_get_temp_dir(),
            'fontDir' => storage_path('fonts/'),
            'fontCache' => storage_path('fonts/'),
            'logOutputFile' => null,
            'enableFontSubsetting' => false,
            'pdfBackend' => 'CPDF',
            'defaultMediaType' => 'print',
            'dpi' => 96,
            'enablePhp' => false,
            'enableJavascript' => false,
            'debugKeepTemp' => false,
            'debugCss' => false,
            'debugLayout' => false,
            'debugLayoutLines' => false,
            'debugLayoutBlocks' => false,
            'debugLayoutInline' => false,
            'debugLayoutPaddingBox' => false,
            'adminUsername' => null,
            'adminPassword' => null,
        ];
        
        // Mesclar com opções customizadas
        $finalOptions = array_merge($defaultOptions, $customOptions);
        
        // Aplicar opções
        foreach ($finalOptions as $key => $value) {
            if ($value !== null) {
                $options->set($key, $value);
            }
        }
        
        return $options;
    }
    
    /**
     * Processar estilos CSS
     *
     * @param mixed $cssStyles
     * @return string
     */
    protected static function processCssStyles($cssStyles): string
    {
        if (is_array($cssStyles)) {
            return implode(' ', array_values($cssStyles));
        }
        
        if (is_string($cssStyles)) {
            return $cssStyles;
        }
        
        // Se for JSON
        if (is_string($cssStyles) && self::isJson($cssStyles)) {
            $decoded = json_decode($cssStyles, true);
            return is_array($decoded) ? implode(' ', array_values($decoded)) : '';
        }
        
        return '';
    }
    
    /**
     * Verificar se string é JSON válido
     *
     * @param string $string
     * @return bool
     */
    protected static function isJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    /**
     * Gerar nome do arquivo
     *
     * @param DocumentTemplate $template
     * @param array $options
     * @return string
     */
    protected static function generateFileName(DocumentTemplate $template, array $options = []): string
    {
        $prefix = $options['file_prefix'] ?? '';
        $suffix = $options['file_suffix'] ?? '';
        $includeTimestamp = $options['include_timestamp'] ?? true;
        $includeType = $options['include_type'] ?? true;
        
        $parts = [];
        
        if ($prefix) {
            $parts[] = self::sanitizeFileName($prefix);
        }
        
        if ($includeType) {
            $parts[] = ucfirst($template->type);
        }
        
        $parts[] = self::sanitizeFileName($template->name);
        
        if ($includeTimestamp) {
            $timestamp = $options['timestamp_format'] ?? 'Y-m-d_H-i-s';
            $parts[] = now()->format($timestamp);
        }
        
        if ($suffix) {
            $parts[] = self::sanitizeFileName($suffix);
        }
        
        return implode('_', $parts) . '.pdf';
    }
    
    /**
     * Sanitizar nome do arquivo
     *
     * @param string $filename
     * @return string
     */
    protected static function sanitizeFileName(string $filename): string
    {
        // Converter para ASCII
        $filename = transliterator_transliterate('Any-Latin; Latin-ASCII', $filename);
        
        // Remover caracteres especiais
        $filename = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $filename);
        
        // Remover underscores duplicados
        $filename = preg_replace('/_+/', '_', $filename);
        
        return trim($filename, '_');
    }
    
    /**
     * Criar resposta de download
     *
     * @param Dompdf $dompdf
     * @param string $fileName
     * @return Response
     */
    protected static function createDownloadResponse(Dompdf $dompdf, string $fileName): Response
    {
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length' => strlen($dompdf->output()),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
    
    /**
     * Log da geração de PDF
     *
     * @param DocumentTemplate $template
     * @param string $fileName
     */
    protected static function logPdfGeneration(DocumentTemplate $template, string $fileName): void
    {
        Log::info('PDF gerado com sucesso', [
            'template_id' => $template->id,
            'template_name' => $template->name,
            'template_type' => $template->type,
            'file_name' => $fileName,
            'user_id' => auth()->id(),
            'company_id' => $template->company_id,
            'timestamp' => now()->toISOString()
        ]);
    }
    
    /**
     * Validar template antes de processar
     *
     * @param DocumentTemplate $template
     * @throws \Exception
     */
    public static function validateTemplate(DocumentTemplate $template): void
    {
        if (empty($template->html_template)) {
            throw new \Exception('Template HTML está vazio');
        }
        
        if (!in_array($template->type, ['invoice', 'quote'])) {
            throw new \Exception('Tipo de template não suportado: ' . $template->type);
        }
        
        // Validar se o template tem conteúdo mínimo
        if (strlen(trim($template->html_template)) < 50) {
            throw new \Exception('Template HTML muito pequeno para ser válido');
        }
    }
    
    /**
     * Obter informações do PDF gerado
     *
     * @param Dompdf $dompdf
     * @return array
     */
    public static function getPdfInfo(Dompdf $dompdf): array
    {
        return [
            'page_count' => $dompdf->getCanvas()->get_page_count(),
            'page_size' => $dompdf->getPaperSize(),
            'orientation' => $dompdf->getPaperOrientation(),
            'file_size' => strlen($dompdf->output()),
            'file_size_formatted' => self::formatBytes(strlen($dompdf->output()))
        ];
    }
    
    /**
     * Formatar bytes para leitura humana
     *
     * @param int $bytes
     * @return string
     */
    protected static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}