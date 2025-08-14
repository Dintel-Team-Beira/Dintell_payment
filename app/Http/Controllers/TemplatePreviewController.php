<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use App\Models\Invoice;
use App\Models\Quote;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;

class TemplatePreviewController extends Controller
{

    public function list($type)
    {
        $companyId = auth()->user()->company_id; // ou como você obtém a empresa

        $templates = DocumentTemplate::where('type', $type)
            ->where('company_id', $companyId)
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get(['id', 'name', 'is_default', 'is_selected', 'created_at', 'updated_at']);

        return response()->json($templates);
    }

    //
    public function show($templateId)
    {
        $template = DocumentTemplate::findOrFail($templateId);
        $company = $template->company;

        // Dados fictícios ou reais dependendo do tipo
        if ($template->type === 'invoice') {
            $invoice = Invoice::where('company_id', $company->id)->where('id', 14)->first();
            $data = compact('invoice', 'company');
        } elseif ($template->type === 'quote') {
            $quote = Quote::where('company_id', $company->id)->first();
            $data = compact('quote', 'company');
        }


        $html = Blade::render($template->html_template, $data);

        // Aplicar CSS se existir
        if ($template->css_styles) {
            $cssStyles = $template->css_styles;
            $html = "<style>{$cssStyles}</style>" . $html;
        }

        return response($html)->header('Content-Type', 'text/html');
    }

    private function renderTemplate($htmlTemplate, $data)
    {
        // Método simples de substituição de variáveis
        // Em produção, você pode usar Blade::render() ou similar
        extract($data);
        // dd($data);


        ob_start();
        eval('?>' . $htmlTemplate);
        return ob_get_clean();
    }

    public function download($templateId)
    {
        /*
        BAIXAR EM HTML
        $template = DocumentTemplate::findOrFail($templateId);
        $html = $this->show($templateId)->getContent();

        $fileName = "{$template->name}.html";
        return response($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
        */

        // BAIXAR EM PDF
        $template = DocumentTemplate::findOrFail($templateId);
        $company = $template->company;
        if ($template->type === 'invoice') {
            $invoice = Invoice::with(['client', 'items'])->where('company_id', $company->id)->first();
            if (!$invoice) {
                $invoice = $this->createSampleInvoice($company);
            }
            $data = compact('invoice', 'company');
        } elseif ($template->type === 'quote') {
            $quote = Quote::with(['client', 'items'])->where('company_id', $company->id)->first();
            if (!$quote) {
                $quote = $this->createSampleQuote($company);
            }
            $data = compact('quote', 'company');
        }

        // Renderizar template
        $html = Blade::render($template->html_template, $data);
        if ($template->css_styles) {
            $cssStyles =  $template->css_styles;
            $html = "<style>{$cssStyles}</style>" . $html;
        }
        // Configurar dompdf
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        // $options->set('chroot', public_path());

        $dompdf = new Dompdf($options);

        // Carregar HTML
        $dompdf->loadHtml($html);

        // Definir tamanho e orientação
        $dompdf->setPaper('A4', 'portrait');

        // Renderizar PDF
        $dompdf->render();

        // Nome do arquivo
        $fileName = $template->name . '_' . now()->format('Y-m-d') . '.pdf';

        // Download
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache'
        ]);
    }
    private function sanitizeFileName($filename)
    {
        // Remover caracteres especiais e espaços
        $filename = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $filename);
        return $filename;
    }
    public function selectTemplate(Request $request, $idTemplate)
    {
        $template = DocumentTemplate::findOrFail($idTemplate);
        $companyId = auth()->user()->company_id;
        // dd($companyId);

        // Desmarcar todos os outros templates como selecionados
        DocumentTemplate::where('company_id', $companyId)
            ->where('is_selected', true)
            ->update(['is_selected' => false]);

        // Marcar o template atual como selecionado
        $template->is_selected = true;
        $template->save();

        return response()->json(['message' => 'Template selecionado com sucesso.']);
    }
}
