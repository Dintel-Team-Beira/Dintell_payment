<?php

namespace App\Helpers;

use App\Models\DocumentTemplate;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Response;

class DocumentTemplateHelper
{
    /**
     * Configurações otimizadas para performance
     */
    const DEFAULT_PAPER_SIZE = 'A4';
    const DEFAULT_ORIENTATION = 'portrait';
    const DEFAULT_FONT = 'DejaVu Sans';
    const CACHE_TTL = 300; // 5 minutos
    
    /**
     * Gerar e baixar PDF do template (versão otimizada)
     *
     * @param DocumentTemplate $template
     * @param array $data
     * @param array $options
     * @return Response
     */
    public static function downloadPdfDocument(
        DocumentTemplate $template, 
        array $data, 
        array $options = []
    ): Response {
        $startTime = microtime(true);
        
        try {
            // Renderizar HTML (com cache se habilitado)
            $html = self::renderHtmlOptimized($template, $data, $options);
            
            // Gerar PDF usando instância reutilizável
            $pdfOutput = self::generatePdfOptimized($html, $options);
            
            // Nome do arquivo simples
            $fileName = self::generateSimpleFileName($template);
            
            // Log apenas se habilitado
            if ($options['enable_logging'] ?? false) {
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                self::logPdfGeneration($template, $fileName, $duration);
            }
            
            // Resposta otimizada
            return self::createOptimizedDownloadResponse($pdfOutput, $fileName);
            
        } catch (\Exception $e) {
            // Log de erro simplificado
            Log::error('PDF generation failed', [
                'template_id' => $template->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Renderizar HTML com otimizações
     */
    protected static function renderHtmlOptimized(
        DocumentTemplate $template, 
        array $data, 
        array $options = []
    ): string {
        // Cache do HTML renderizado (opcional)
        $enableCache = $options['enable_cache'] ?? false;
        $cacheKey = $enableCache ? 'template_html_' . $template->id . '_' . md5(serialize($data)) : null;
        
        if ($enableCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // Renderizar template
        $html = Blade::render($template->html_template, $data);
        
        // CSS otimizado
        if ($template->css_styles) {
            $css = self::processCssOptimized($template->css_styles);
            $html = "<style>{$css}</style>{$html}";
        }
        
        // Cache se habilitado
        if ($enableCache && $cacheKey) {
            Cache::put($cacheKey, $html, self::CACHE_TTL);
        }
        
        return $html;
    }
    
    /**
     * Gerar PDF com otimizações máximas
     */
    protected static function generatePdfOptimized(string $html, array $options = []): string
    {
        $dompdf = self::getDompdfInstance($options);
        
        // Reset da instância (método correto)
        $dompdf = new Dompdf(self::getOptimizedOptions($options));
        
        // Carregar HTML
        $dompdf->loadHtml($html);
        
        // Configurar papel (apenas se diferente do padrão)
        $paperSize = $options['paper_size'] ?? self::DEFAULT_PAPER_SIZE;
        $orientation = $options['orientation'] ?? self::DEFAULT_ORIENTATION;
        
        if ($paperSize !== self::DEFAULT_PAPER_SIZE || $orientation !== self::DEFAULT_ORIENTATION) {
            $dompdf->setPaper($paperSize, $orientation);
        }
        
        // Renderizar
        $dompdf->render();
        
        return $dompdf->output();
    }
    
    /**
     * Obter instância do dompdf (sem singleton para evitar conflitos)
     */
    protected static function getDompdfInstance(array $options = []): Dompdf
    {
        // Sempre criar nova instância para evitar problemas
        return new Dompdf(self::getOptimizedOptions($options));
    }
    
    /**
     * Opções otimizadas para performance máxima
     */
    protected static function getOptimizedOptions(array $customOptions = []): Options
    {
        $options = new Options();
        
        // Configurações mínimas para máxima performance
        $performanceConfig = [
            'defaultFont' => self::DEFAULT_FONT,
            'isRemoteEnabled' => false, // Desabilitar para performance
            'isHtml5ParserEnabled' => true,
            'chroot' => public_path(),
            'tempDir' => sys_get_temp_dir(),
            'enableFontSubsetting' => false, // Melhora performance
            'pdfBackend' => 'CPDF',
            'defaultMediaType' => 'print',
            'dpi' => 96, // Menor DPI = melhor performance
            'enablePhp' => false,
            'enableJavascript' => false,
            'debugKeepTemp' => false,
            'debugCss' => false,
            'debugLayout' => false,
            'logOutputFile' => null,
            'fontDir' => null, // Usar fontes do sistema
            'fontCache' => null,
        ];
        
        // Merge com opções customizadas
        $finalConfig = array_merge($performanceConfig, $customOptions);
        
        foreach ($finalConfig as $key => $value) {
            if ($value !== null) {
                $options->set($key, $value);
            }
        }
        
        return $options;
    }
    
    /**
     * Processar CSS de forma otimizada
     */
    protected static function processCssOptimized($cssStyles): string
    {
        if (is_string($cssStyles)) {
            return $cssStyles;
        }
        
        if (is_array($cssStyles)) {
            return implode('', $cssStyles); // Sem espaços desnecessários
        }
        
        return '';
    }
    
    /**
     * Nome de arquivo simples (sem sanitização complexa)
     */
    protected static function generateSimpleFileName(DocumentTemplate $template): string
    {
        $safeName = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $template->name);
        return $safeName . '_' . date('Ymd') . '.pdf';
    }
    
    /**
     * Resposta otimizada para download
     */
    protected static function createOptimizedDownloadResponse(string $pdfContent, string $fileName): Response
    {
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Content-Length' => strlen($pdfContent),
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache'
        ]);
    }
    
    /**
     * Log simplificado
     */
    protected static function logPdfGeneration(DocumentTemplate $template, string $fileName, float $duration): void
    {
        Log::info('PDF generated', [
            'template_id' => $template->id,
            'file_name' => $fileName,
            'duration_ms' => $duration,
            'user_id' => auth()->id()
        ]);
    }
    
    /**
     * Versão ultra-rápida para downloads frequentes
     */
    public static function quickDownload(DocumentTemplate $template, array $data): Response
    {
        // HTML mínimo
        $html = Blade::render($template->html_template, $data);
        
        if ($template->css_styles) {
            $html = "<style>{$template->css_styles}</style>{$html}";
        }
        
        // PDF com configurações mínimas
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);
        $options->set('dpi', 72);
        $options->set('enablePhp', false);
        $options->set('debugCss', false);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Nome simples
        $fileName = $template->id . '_' . time() . '.pdf';
        
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
    
    /**
     * Limpar cache se necessário
     */
    public static function clearCache(int $templateId = null): void
    {
        if ($templateId) {
            Cache::forget("template_html_{$templateId}_*");
        } else {
            Cache::flush();
        }
    }
    
    /**
     * Limpeza simplificada (o GC do PHP cuida da memória)
     */
    public static function cleanup(): void
    {
        // Forçar coleta de lixo se necessário
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }
}

/**
 * Trait para controllers que usam o helper (performance)
 */
trait OptimizedPdfDownload
{
    protected function quickPdfDownload(int $templateId, array $data): Response
    {
        $template = DocumentTemplate::select(['id', 'name', 'html_template', 'css_styles'])
            ->findOrFail($templateId);
            
        return DocumentTemplateHelper::quickDownload($template, $data);
    }
    
    protected function cachedPdfDownload(int $templateId, array $data): Response
    {
        $template = DocumentTemplate::findOrFail($templateId);
        
        return DocumentTemplateHelper::downloadPdfDocument($template, $data, [
            'enable_cache' => true,
            'enable_logging' => false
        ]);
    }
}