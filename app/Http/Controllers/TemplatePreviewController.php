<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use App\Models\Invoice;
use App\Models\Quote;
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
            ->get(['id', 'name', 'is_default', 'created_at', 'updated_at']);

        return response()->json($templates);
    }

    //
    public function show($templateId)
    {
        $template = DocumentTemplate::findOrFail($templateId);
        $company = $template->company;
        
        // return '<pre>'.print_r($template).'</pre>';
        // Dados fictícios ou reais dependendo do tipo
        if ($template->type === 'invoice') {
            $invoice = Invoice::where('company_id', $company->id)->where('id',14)->first();//$this->createSampleInvoice($company);
            // $html = $this->renderTemplate($template->html_template, compact('invoice', 'company'));
            $data = compact('invoice', 'company');
        } elseif ($template->type === 'quote') {
            $quote = Quote::where('company_id', $company->id)->first();//::$this->createSampleQuote($company);
            $data = compact('quote', 'company');
            // $html = $this->renderTemplate($template->html_template, compact('quote', 'company'));
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
}
