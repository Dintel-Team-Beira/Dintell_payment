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
            // Renderizar HTML para PDF (com processamento de caminhos)
            $html = self::renderTemplate($template, $data, true, $options);

            // Gerar PDF usando instância reutilizável
            $pdfOutput = self::generatePdfOptimized($html, $options);

            // Nome do arquivo dinâmico baseado nos dados ou opções
            $fileName = self::generateDynamicFileName($template, $data, $options);

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
     * 🆕 Renderizar HTML para visualização no navegador
     */
    public static function renderHtmlForBrowser(
        DocumentTemplate $template, 
        array $data
    ): string {
        return self::renderTemplate($template, $data, false);
    }

    /**
     * 🆕 Método de preview no navegador
     */
    public static function previewInBrowser(DocumentTemplate $template, array $data): Response
    {
        $html = self::renderTemplate($template, $data, false);
        
        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8'
        ]);
    }

    /**
     * 🆕 Método unificado para renderização com flag de controle
     */
    protected static function renderTemplate(
        DocumentTemplate $template, 
        array $data, 
        bool $forPdf = false,
        array $options = []
    ): string {
        // Cache do HTML renderizado (opcional)
        $enableCache = $options['enable_cache'] ?? false;
        $cacheKey = $enableCache ? 'template_html_' . $template->id . '_' . md5(serialize($data)) . '_' . ($forPdf ? 'pdf' : 'html') : null;

        if ($enableCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Renderizar template
        $html = Blade::render($template->html_template, $data);

        // 🔥 PROCESSAR APENAS SE FOR PARA PDF
        if ($forPdf) {
            $html = self::processPathsForPdf($html);
        }

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
     * 🆕 Converter caminhos para PDF (corrigido para imagens)
     */
    protected static function processPathsForPdf(string $html): string
    {
        
        // 1. Processar {{ asset('...') }} primeiro
        $html = preg_replace_callback(
            '/\{\{\s*asset\([\'"]([^\'"]*)[\'"]\)\s*\}\}/',
            function($matches) {
                $assetPath = $matches[1];
                $fullPath = public_path($assetPath);
                
                // Debug: verificar se arquivo existe
                if (!file_exists($fullPath)) {
                    \Log::warning("Asset não encontrado: {$fullPath}");
                }
                
                return $fullPath;
            },
            $html
        );

        // 2. Processar asset() dentro de atributos
        $html = preg_replace_callback(
            '/asset\([\'"]([^\'"]*)[\'"]\)/',
            function($matches) {
                $assetPath = $matches[1];
                $fullPath = public_path($assetPath);
                
                // Debug: verificar se arquivo existe
                if (!file_exists($fullPath)) {
                    \Log::warning("Asset não encontrado: {$fullPath}");
                }
                
                return $fullPath;
            },
            $html
        );

        // 3. Processar URLs relativos em src de imagens
        $html = preg_replace_callback(
            '/src=[\'"]([^\'"]*)[\'"]/i',
            function($matches) {
                $src = $matches[1];
                
                // Se já é um caminho absoluto ou data URL, não alterar
                if (str_starts_with($src, '/') && str_starts_with($src, public_path())) {
                    return $matches[0];
                }
                if (str_starts_with($src, 'data:')) {
                    return $matches[0];
                }
                if (str_starts_with($src, 'http')) {
                    return $matches[0];
                }
                
                // Se contém asset(), processar
                if (str_contains($src, 'asset(')) {
                    return $matches[0]; // Já foi processado acima
                }
                
                // Se é um caminho relativo, converter para absoluto
                if (str_starts_with($src, 'storage/') || str_starts_with($src, '/storage/')) {
                    $cleanPath = ltrim($src, '/');
                    $fullPath = public_path($cleanPath);
                    
                    if (!file_exists($fullPath)) {
                        \Log::warning("Imagem não encontrada: {$fullPath}");
                    }
                    
                    return 'src="' . $fullPath . '"';
                }
                
                return $matches[0];
            },
            $html
        );

        // 4. OPÇÃO ALTERNATIVA: Converter imagens para base64 (mais confiável)
        $html = preg_replace_callback(
            '/src=[\'"]([^\'"]*)[\'"]/i',
            function($matches) {
                $src = trim($matches[1], '"\'');
                
                // Pular se já é data URL
                if (str_starts_with($src, 'data:')) {
                    return $matches[0];
                }
                
                // Determinar caminho do arquivo
                $filePath = null;
                
                if (str_starts_with($src, '/') && file_exists($src)) {
                    // Já é caminho absoluto
                    $filePath = $src;
                } elseif (str_starts_with($src, 'storage/') || str_starts_with($src, '/storage/')) {
                    // Caminho storage
                    $cleanPath = ltrim($src, '/');
                    $filePath = public_path($cleanPath);
                } elseif (str_contains($src, public_path())) {
                    // Já processado, é caminho completo
                    $filePath = $src;
                }
                
                // Converter para base64 se arquivo existe
                if ($filePath && file_exists($filePath)) {
                    try {
                        $imageData = base64_encode(file_get_contents($filePath));
                        $mimeType = mime_content_type($filePath);
                        return 'src="data:' . $mimeType . ';base64,' . $imageData . '"';
                    } catch (\Exception $e) {
                        \Log::warning("Erro ao converter imagem para base64: {$filePath} - " . $e->getMessage());
                    }
                }
                
                return $matches[0];
            },
            $html
        );


        return $html;
    }

    /**
     * Renderizar HTML com otimizações (método legado - agora usa renderTemplate)
     */
    protected static function renderHtmlOptimized(
        DocumentTemplate $template,
        array $data,
        array $options = []
    ): string {
        return self::renderTemplate($template, $data, true, $options);
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
            // 'fontDir' => null, // Usar fontes do sistema
            'fontCache' => null,

            'defaultPaperOrientation' => 'portrait',
            'marginTop' => '20mm',    // Corrigido de 250mm
            'marginRight' => '15mm',  // Corrigido de 200mm
            'marginBottom' => '20mm', // Corrigido de 250mm
            'marginLeft' => '15mm',   // Corrigido de 200mm
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
     * 🆕 Gerar nome de arquivo dinâmico baseado nos dados
     */
    protected static function generateDynamicFileName(
        DocumentTemplate $template, 
        array $data, 
        array $options = []
    ): string {
        // Se nome customizado foi fornecido nas opções
        if (!empty($options['filename'])) {
            $fileName = $options['filename'];
            if (!str_ends_with($fileName, '.pdf')) {
                $fileName .= '.pdf';
            }
            return $fileName;
        }

        // Gerar nome baseado no tipo de documento e dados
        $fileName = self::buildFileNameFromData($template, $data);
        
        return $fileName . '.pdf';
    }

    /**
     * 🆕 Construir nome baseado no tipo de documento
     */
    protected static function buildFileNameFromData(DocumentTemplate $template, array $data): string
    {
        $date = date('Y-m-d');
        $timestamp = date('His');
        
        // Mapear tipos de documento para prefixos
        $prefixMap = [
            'invoice' => 'Fatura',
            'quote' => 'Cotacao',
            'credit_note' => 'Nota_Credito',
            'debit_note' => 'Nota_Debito',
            'receipt' => 'Recibo',
            'contract' => 'Contrato',
            'proposal' => 'Proposta',
        ];

        $prefix = $prefixMap[$template->type] ?? 'Documento';

        // Tentar extrair número do documento dos dados
        $documentNumber = self::extractDocumentNumber($data, $template->type);

        if ($documentNumber) {
            return "{$prefix}_{$documentNumber}_{$date}";
        }

        // Se não encontrar número, usar cliente + timestamp
        $clientName = self::extractClientName($data);
        if ($clientName) {
            $safeName = self::sanitizeFileName($clientName);
            return "{$prefix}_{$safeName}_{$date}_{$timestamp}";
        }

        // Fallback: usar tipo + timestamp
        return "{$prefix}_{$date}_{$timestamp}";
    }

    /**
     * 🆕 Extrair número do documento dos dados
     */
    protected static function extractDocumentNumber(array $data, string $type): ?string
    {
        $numberFields = [
            'invoice_number',
            'quote_number', 
            'credit_note_number',
            'debit_note_number',
            'receipt_number',
            'contract_number',
            'proposal_number',
            'number',
            'document_number'
        ];

        // Verificar no objeto principal (invoice, quote, etc.)
        foreach ($data as $key => $value) {
            if (is_object($value) || is_array($value)) {
                $object = is_array($value) ? (object)$value : $value;
                
                foreach ($numberFields as $field) {
                    if (isset($object->$field) && !empty($object->$field)) {
                        return self::sanitizeFileName($object->$field);
                    }
                }
            }
        }

        // Verificar diretamente no array de dados
        foreach ($numberFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                return self::sanitizeFileName($data[$field]);
            }
        }

        return null;
    }

    /**
     * 🆕 Extrair nome do cliente dos dados
     */
    protected static function extractClientName(array $data): ?string
    {
        // Verificar em objetos principais
        foreach ($data as $value) {
            if (is_object($value) || is_array($value)) {
                $object = is_array($value) ? (object)$value : $value;
                
                // Verificar se tem cliente relacionado
                if (isset($object->client) && isset($object->client->name)) {
                    return $object->client->name;
                }
                
                // Verificar campos diretos
                if (isset($object->client_name)) {
                    return $object->client_name;
                }
                
                if (isset($object->customer_name)) {
                    return $object->customer_name;
                }
            }
        }

        // Verificar diretamente no array
        if (isset($data['client_name'])) {
            return $data['client_name'];
        }

        if (isset($data['customer_name'])) {
            return $data['customer_name'];
        }

        return null;
    }

    /**
     * 🆕 Sanitizar nome do arquivo
     */
    protected static function sanitizeFileName(string $name): string
    {
        // Remover/substituir caracteres especiais
        $name = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $name);
        
        // Remover múltiplos underscores
        $name = preg_replace('/_+/', '_', $name);
        
        // Remover underscores do início e fim
        $name = trim($name, '_');
        
        // Limitar tamanho
        return substr($name, 0, 50);
    }

    /**
     * Nome de arquivo simples (mantido para compatibilidade)
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
     * Versão ultra-rápida para downloads frequentes (atualizada)
     */
    public static function quickDownload(DocumentTemplate $template, array $data, array $options = []): Response
    {
        // HTML para PDF (com processamento de caminhos)
        $html = self::renderTemplate($template, $data, true);

        // PDF com configurações mínimas
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'DejaVu Sans');
        $pdfOptions->set('isRemoteEnabled', false);
        $pdfOptions->set('dpi', 72);
        $pdfOptions->set('enablePhp', false);
        $pdfOptions->set('debugCss', false);

        $pdfOptions->set('marginTop', '20mm');    // Corrigido
        $pdfOptions->set('marginRight', '15mm');  // Corrigido
        $pdfOptions->set('marginBottom', '20mm'); // Corrigido
        $pdfOptions->set('marginLeft', '15mm');   // Corrigido

        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Nome dinâmico
        $fileName = self::generateDynamicFileName($template, $data, $options);

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
 * Trait para controllers que usam o helper (performance) - Atualizada
 */
trait OptimizedPdfDownload
{
    protected function quickPdfDownload(int $templateId, array $data, array $options = []): Response
    {
        $template = DocumentTemplate::select(['id', 'name', 'html_template', 'css_styles', 'type'])
            ->findOrFail($templateId);

        return DocumentTemplateHelper::quickDownload($template, $data, $options);
    }

    protected function cachedPdfDownload(int $templateId, array $data, array $options = []): Response
    {
        $template = DocumentTemplate::findOrFail($templateId);

        $defaultOptions = [
            'enable_cache' => true,
            'enable_logging' => false
        ];

        return DocumentTemplateHelper::downloadPdfDocument($template, $data, array_merge($defaultOptions, $options));
    }

    /**
     * 🆕 Preview HTML no navegador
     */
    protected function previewHtml(int $templateId, array $data): Response
    {
        $template = DocumentTemplate::findOrFail($templateId);
        return DocumentTemplateHelper::previewInBrowser($template, $data);
    }
}