<?php

// API Controller para integração mobile
// app/Http/Controllers/Api/BillingApiController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\QuoteResource;

class BillingApiController extends Controller
{
    public function invoices(Request $request)
    {
        $query = Invoice::with('client', 'items');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $invoices = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return InvoiceResource::collection($invoices);
    }

    public function invoice(Invoice $invoice)
    {
        $invoice->load('client', 'items', 'quote');
        return new InvoiceResource($invoice);
    }

    public function quotes(Request $request)
    {
        $query = Quote::with('client', 'items');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $quotes = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return QuoteResource::collection($quotes);
    }

    public function markInvoiceAsPaid(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0'
        ]);

        $amount = $request->amount ?? $invoice->remaining_amount;
        $invoice->markAsPaid($amount);

        return response()->json([
            'message' => 'Pagamento registrado com sucesso',
            'invoice' => new InvoiceResource($invoice->fresh())
        ]);
    }

    public function convertQuoteToInvoice(Quote $quote)
    {
        if (!$quote->canConvertToInvoice()) {
            return response()->json([
                'message' => 'Esta cotação não pode ser convertida em fatura'
            ], 422);
        }

        $invoice = $quote->convertToInvoice();

        return response()->json([
            'message' => 'Cotação convertida em fatura com sucesso',
            'invoice' => new InvoiceResource($invoice)
        ]);
    }

    public function dashboard()
    {
        $stats = [
            'total_invoices' => Invoice::count(),
            'total_quotes' => Quote::count(),
            'pending_amount' => Invoice::whereIn('status', ['sent', 'overdue'])->sum('total'),
            'paid_amount_this_month' => Invoice::where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->sum('total'),
            'overdue_count' => Invoice::where('status', 'overdue')->count(),
            'recent_invoices' => InvoiceResource::collection(
                Invoice::with('client')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
            ),
            'recent_quotes' => QuoteResource::collection(
                Quote::with('client')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
            )
        ];

        return response()->json($stats);
    }
}
