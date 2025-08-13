<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Quote;
use App\Models\Invoice;
use Dompdf\Dompdf;
use Dompdf\Options;

class InvoicePdfService
{
    protected $dompdf;

    public function __construct()
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('chroot', public_path());

        $this->dompdf = new Dompdf($options);
    }

    public function generateQuotePdf(Quote $quote)
    {
        $quote->load(['client', 'items']);
       $company = Company::findOrFail(auth()->user()->company_id);

        $html = view('pdfs.quote', compact('quote', 'company'))->render();

        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        return $this->dompdf->output();
    }

    public function downloadQuotePdf(Quote $quote, $filename = null)
    {
        $filename = $filename ?: 'cotacao_' . $quote->quote_number . '.pdf';
        
        $pdf = $this->generateQuotePdf($quote);

        return response($pdf, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function viewQuotePdf(Quote $quote)
    {
        $pdf = $this->generateQuotePdf($quote);

        return response($pdf, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="cotacao_' . $quote->quote_number . '.pdf"');
    }
}
