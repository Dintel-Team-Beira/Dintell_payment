<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Quote;
use Illuminate\Http\Request;

class TemplatePreviewController extends Controller
{
    //
    public function show($type)
    {

        $company = auth()->user()->company;
        
         if ($type === 'invoice') {
            $invoice = Invoice::with(['client', 'items'])->first(); // ou criar dados fictícios
            return view('pdfs.invoice', compact('invoice', 'company'));
        } elseif ($type === 'quote') {
            $quote = Quote::with(['client', 'items'])->first(); // ou criar dados fictícios
            return view('pdfs.quote', compact('quote', 'company'));
        }
       
        return abort(404, 'Template not found');
    }
}
