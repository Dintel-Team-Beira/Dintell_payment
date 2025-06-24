<?php

// QuoteController.php
// app/Http/Controllers/QuoteController.php
namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\Client;
use App\Models\BillingSetting;
use App\Services\BillingCalculatorService;
use App\Services\InvoicePdfService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QuoteController extends Controller
{
    protected $calculator;
    protected $pdfService;

    public function __construct(BillingCalculatorService $calculator, InvoicePdfService $pdfService)
    {
        $this->calculator = $calculator;
        $this->pdfService = $pdfService;
    }

    public function index(Request $request)
    {
        $query = Quote::with('client');

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('date_from')) {
            $query->where('quote_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('quote_date', '<=', $request->date_to);
        }

        $quotes = $query->orderBy('created_at', 'desc')->paginate(15);
        $clients = Client::orderBy('name')->get();

        // Estatísticas
        $stats = [
            'total_quotes' => Quote::count(),
            'total_amount' => Quote::sum('total'),
            'pending_count' => Quote::where('status', 'sent')->count(),
            'accepted_count' => Quote::where('status', 'accepted')->count(),
            'conversion_rate' => $this->getConversionRate()
        ];

        return view('quotes.index', compact('quotes', 'clients', 'stats'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $settings = BillingSetting::getSettings();

        return view('quotes.create', compact('clients', 'settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'quote_date' => 'required|date',
            'valid_until' => 'required|date|after:quote_date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string'
        ]);

        $totals = $this->calculator->calculateTotals($validated['items']);

        $quote = Quote::create([
            'client_id' => $validated['client_id'],
            'quote_date' => $validated['quote_date'],
            'valid_until' => $validated['valid_until'],
            'subtotal' => $totals['subtotal'],
            'tax_amount' => $totals['tax_amount'],
            'total' => $totals['total'],
            'notes' => $validated['notes'],
            'terms_conditions' => $validated['terms_conditions']
        ]);

        foreach ($validated['items'] as $item) {
            $quote->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'] ?? BillingSetting::getSettings()->default_tax_rate
            ]);
        }

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'Cotação criada com sucesso!');
    }

    public function show(Quote $quote)
    {
        $quote->load('client', 'items', 'invoice');
        return view('quotes.show', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        if ($quote->status === 'accepted' && $quote->converted_to_invoice_at) {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'Cotações já convertidas em fatura não podem ser editadas.');
        }

        $quote->load('client', 'items');
        $clients = Client::orderBy('name')->get();
        $settings = BillingSetting::getSettings();

        return view('quotes.edit', compact('quote', 'clients', 'settings'));
    }

    public function update(Request $request, Quote $quote)
    {
        if ($quote->status === 'accepted' && $quote->converted_to_invoice_at) {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'Cotações já convertidas em fatura não podem ser editadas.');
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'quote_date' => 'required|date',
            'valid_until' => 'required|date|after:quote_date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string'
        ]);

        $totals = $this->calculator->calculateTotals($validated['items']);

        $quote->update([
            'client_id' => $validated['client_id'],
            'quote_date' => $validated['quote_date'],
            'valid_until' => $validated['valid_until'],
            'subtotal' => $totals['subtotal'],
            'tax_amount' => $totals['tax_amount'],
            'total' => $totals['total'],
            'notes' => $validated['notes'],
            'terms_conditions' => $validated['terms_conditions']
        ]);

        // Remover itens existentes e criar novos
        $quote->items()->delete();

        foreach ($validated['items'] as $item) {
            $quote->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'] ?? BillingSetting::getSettings()->default_tax_rate
            ]);
        }

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'Cotação atualizada com sucesso!');
    }

    public function destroy(Quote $quote)
    {
        if ($quote->status === 'accepted' && $quote->converted_to_invoice_at) {
            return redirect()->route('quotes.index')
                ->with('error', 'Cotações já convertidas em fatura não podem ser excluídas.');
        }

        $quote->delete();

        return redirect()->route('quotes.index')
            ->with('success', 'Cotação excluída com sucesso!');
    }

    public function convertToInvoice(Quote $quote)
    {
        if (!$quote->canConvertToInvoice()) {
            return back()->with('error', 'Esta cotação não pode ser convertida em fatura.');
        }

        $invoice = $quote->convertToInvoice();

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Cotação convertida em fatura com sucesso!');
    }

    public function updateStatus(Request $request, Quote $quote)
    {
        $request->validate([
            'status' => 'required|in:draft,sent,accepted,rejected,expired'
        ]);

        $quote->update(['status' => $request->status]);

        return back()->with('success', 'Status da cotação atualizado!');
    }

    public function downloadPdf(Quote $quote)
    {
        $pdf = $this->pdfService->generateQuotePdf($quote);

        return $pdf->download("cotacao-{$quote->quote_number}.pdf");
    }

    private function getConversionRate()
    {
        $totalQuotes = Quote::count();
        $convertedQuotes = Quote::whereNotNull('converted_to_invoice_at')->count();

        return $totalQuotes > 0 ? round(($convertedQuotes / $totalQuotes) * 100, 2) : 0;
    }
}

?>