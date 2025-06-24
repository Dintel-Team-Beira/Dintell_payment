<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\BillingSetting;
use App\Services\BillingCalculatorService;
use App\Services\InvoicePdfService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InvoiceController extends Controller
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

        // return "Hello Word";
        $query = Invoice::with('client');

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('date_from')) {
            $query->where('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('invoice_date', '<=', $request->date_to);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(15);
        $clients = Client::orderBy('name')->get();

        // Estatísticas para dashboard
        $stats = [
            'total_pending' => Invoice::where('status', 'sent')->sum('total'),
            'total_overdue' => Invoice::where('status', 'overdue')->sum('total'),
            'total_paid_this_month' => Invoice::where('status', 'paid')
                ->whereMonth('paid_at', Carbon::now()->month)
                ->sum('total'),
            'count_overdue' => Invoice::where('status', 'overdue')->count()
        ];

        return view('invoices.index', compact('invoices', 'clients', 'stats'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $settings = BillingSetting::getSettings();

        return view('invoices.create', compact('clients', 'settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'payment_terms_days' => 'required|numeric|min:0|max:365',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string'
        ]);

        $totals = $this->calculator->calculateTotals($validated['items']);
        $dueDate = Carbon::parse($validated['invoice_date'])
            ->addDays($validated['payment_terms_days']);

        $invoice = Invoice::create([
            'client_id' => $validated['client_id'],
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $dueDate,
            'payment_terms_days' => $validated['payment_terms_days'],
            'subtotal' => $totals['subtotal'],
            'tax_amount' => $totals['tax_amount'],
            'total' => $totals['total'],
            'notes' => $validated['notes'],
            'terms_conditions' => $validated['terms_conditions']
        ]);

        foreach ($validated['items'] as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'] ?? BillingSetting::getSettings()->default_tax_rate
            ]);
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Fatura criada com sucesso!');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('client', 'items', 'quote');
        return view('invoices.show', compact('invoice'));
    }

    public function markAsPaid(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0|max:' . $invoice->remaining_amount
        ]);

        $amount = $request->amount ?? $invoice->remaining_amount;
        $invoice->markAsPaid($amount);

        return back()->with('success', 'Pagamento registrado com sucesso!');
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate([
            'status' => 'required|in:draft,sent,paid,overdue,cancelled'
        ]);

        $invoice->update(['status' => $request->status]);

        return back()->with('success', 'Status da fatura atualizado!');
    }

    public function downloadPdf(Invoice $invoice)
    {
        $pdf = $this->pdfService->generateInvoicePdf($invoice);

        return $pdf->download("fatura-{$invoice->invoice_number}.pdf");
    }

    public function sendByEmail(Invoice $invoice)
    {
        // Implementar envio por email
        // Mail::to($invoice->client->email)->send(new InvoiceMail($invoice));

        $invoice->update(['status' => 'sent']);

        return back()->with('success', 'Fatura enviada por email!');
    }
}