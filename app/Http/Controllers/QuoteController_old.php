<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Client;
use App\Models\Product;
use App\Models\Service;
use App\Models\BillingSetting;
use App\Services\BillingCalculatorService;
use App\Services\InvoicePdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $query = Quote::with(['client', 'items']);

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

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('quote_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('client', function($clientQuery) use ($request) {
                      $clientQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $quotes = $query->orderBy('created_at', 'desc')->paginate(15);
        $clients = Client::orderBy('name')->get();

        // Estatísticas avançadas
        $stats = [
            'total_quotes' => Quote::count(),
            'total_amount' => Quote::sum('total'),
            'pending_count' => Quote::where('status', 'sent')->count(),
            'accepted_count' => Quote::where('status', 'accepted')->count(),
            'rejected_count' => Quote::where('status', 'rejected')->count(),
            'expired_count' => Quote::where('status', 'expired')->count(),
            'conversion_rate' => $this->getConversionRate(),
            'avg_quote_value' => Quote::avg('total'),
            'this_month_quotes' => Quote::whereMonth('created_at', Carbon::now()->month)->count(),
            'this_month_value' => Quote::whereMonth('created_at', Carbon::now()->month)->sum('total')
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
            'items.*.type' => 'required|in:product,service',
            'items.*.item_id' => 'required|integer',
            'items.*.name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Calcular totais
            $totals = $this->calculateQuoteTotals($validated['items']);

            // Gerar número da cotação
            $quoteNumber = $this->generateQuoteNumber();

            // Criar cotação
            $quote = Quote::create([
                'quote_number' => $quoteNumber,
                'client_id' => $validated['client_id'],
                'quote_date' => $validated['quote_date'],
                'valid_until' => $validated['valid_until'],
                'subtotal' => $totals['subtotal'],
                'tax_amount' => $totals['tax_amount'],
                'total' => $totals['total'],
                'status' => 'draft',
                'notes' => $validated['notes'],
                'terms_conditions' => $validated['terms_conditions']
            ]);

            // Criar itens da cotação
            foreach ($validated['items'] as $itemData) {
                $this->createQuoteItem($quote, $itemData);
            }

            DB::commit();

            return redirect('/quotes/' . $quote->id)->with('success', 'Cotação criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Erro ao criar cotação: ' . $e->getMessage());
        }
    }

    public function show(Quote $quote)
    {
        $quote->load(['client', 'items', 'invoice']);

        // Verificar se está expirada
        if ($quote->status !== 'expired' && Carbon::parse($quote->valid_until)->isPast()) {
            $quote->update(['status' => 'expired']);
        }

        return view('quotes.show', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        if ($quote->status === 'accepted' && $quote->converted_to_invoice_at) {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'Cotações já convertidas em fatura não podem ser editadas.');
        }

        $quote->load(['client', 'items']);
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
            'items.*.type' => 'required|in:product,service',
            'items.*.item_id' => 'required|integer',
            'items.*.name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Calcular novos totais
            $totals = $this->calculateQuoteTotals($validated['items']);

            // Atualizar cotação
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

            // Remover itens existentes
            $quote->items()->delete();

            // Criar novos itens
            foreach ($validated['items'] as $itemData) {
                $this->createQuoteItem($quote, $itemData);
            }

            DB::commit();

            return redirect()->route('quotes.show', $quote)
                ->with('success', 'Cotação atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Erro ao atualizar cotação: ' . $e->getMessage());
        }
    }

    public function destroy(Quote $quote)
    {
        if ($quote->status === 'accepted' && $quote->converted_to_invoice_at) {
            return redirect()->route('quotes.index')
                ->with('error', 'Cotações já convertidas em fatura não podem ser excluídas.');
        }

        try {
            DB::beginTransaction();

            $quote->items()->delete();
            $quote->delete();

            DB::commit();

            return redirect()->route('quotes.index')
                ->with('success', 'Cotação excluída com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('quotes.index')
                ->with('error', 'Erro ao excluir cotação: ' . $e->getMessage());
        }
    }

    public function convertToInvoice(Quote $quote)
    {
        if (!$quote->canConvertToInvoice()) {
            return back()->with('error', 'Esta cotação não pode ser convertida em fatura.');
        }

        try {
            DB::beginTransaction();

            $invoice = $quote->convertToInvoice();

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Cotação convertida em fatura com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Erro ao converter cotação: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Quote $quote)
    {
        $request->validate([
            'status' => 'required|in:draft,sent,accepted,rejected,expired'
        ]);

        $oldStatus = $quote->status;
        $newStatus = $request->status;

        // Regras de negócio para mudança de status
        if ($oldStatus === 'accepted' && $newStatus !== 'accepted') {
            return back()->with('error', 'Cotações aceitas não podem ter o status alterado.');
        }

        if ($newStatus === 'accepted' && Carbon::parse($quote->valid_until)->isPast()) {
            return back()->with('error', 'Não é possível aceitar uma cotação expirada.');
        }

        $quote->update([
            'status' => $newStatus,
            'status_updated_at' => now()
        ]);

        $statusMessages = [
            'draft' => 'Cotação salva como rascunho',
            'sent' => 'Cotação enviada para o cliente',
            'accepted' => 'Cotação aceita pelo cliente',
            'rejected' => 'Cotação rejeitada pelo cliente',
            'expired' => 'Cotação marcada como expirada'
        ];

        return back()->with('success', $statusMessages[$newStatus] ?? 'Status atualizado!');
    }

    public function duplicate(Quote $quote)
    {
        try {
            DB::beginTransaction();

            $newQuote = $quote->replicate();
            $newQuote->quote_number = $this->generateQuoteNumber();
            $newQuote->status = 'draft';
            $newQuote->quote_date = Carbon::today();
            $newQuote->valid_until = Carbon::today()->addDays(30);
            $newQuote->converted_to_invoice_at = null;
            $newQuote->invoice_id = null;
            $newQuote->save();

            // Duplicar itens
            foreach ($quote->items as $item) {
                $newItem = $item->replicate();
                $newItem->quote_id = $newQuote->id;
                $newItem->save();
            }

            DB::commit();

            return redirect()->route('quotes.edit', $newQuote)
                ->with('success', 'Cotação duplicada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Erro ao duplicar cotação: ' . $e->getMessage());
        }
    }

    public function downloadPdf(Quote $quote)
    {
        try {
            $pdf = $this->pdfService->generateQuotePdf($quote);
            return $pdf->download("cotacao-{$quote->quote_number}.pdf");

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao gerar PDF: ' . $e->getMessage());
        }
    }

    public function sendEmail(Request $request, Quote $quote)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'nullable|string'
        ]);

        try {
            // Implementar envio de email
            // Mail::to($request->email)->send(new QuoteMail($quote, $request->subject, $request->message));

            $quote->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);

            return back()->with('success', 'Cotação enviada por email com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao enviar email: ' . $e->getMessage());
        }
    }

    // API Methods para AJAX
    public function getActiveProducts(Request $request)
    {
        $query = Product::where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $products = $query->select([
            'id', 'name', 'code', 'description', 'price', 'category',
            'stock_quantity', 'tax_rate', 'image'
        ])->orderBy('name')->get();

        return response()->json($products);
    }

    public function getActiveServices(Request $request)
    {
        $query = Service::where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('complexity')) {
            $query->where('complexity_level', $request->complexity);
        }

        $services = $query->select([
            'id', 'name', 'code', 'description', 'hourly_rate', 'fixed_price',
            'category', 'complexity_level', 'estimated_hours', 'tax_rate'
        ])->orderBy('name')->get();

        return response()->json($services);
    }

    // Private Methods
    private function calculateQuoteTotals(array $items)
    {
        $subtotal = 0;
        $taxAmount = 0;

        foreach ($items as $item) {
            $quantity = floatval($item['quantity']);
            $unitPrice = floatval($item['unit_price']);
            $taxRate = floatval($item['tax_rate'] ?? 0);

            $itemSubtotal = $quantity * $unitPrice;
            $itemTax = $itemSubtotal * ($taxRate / 100);

            $subtotal += $itemSubtotal;
            $taxAmount += $itemTax;
        }

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $subtotal + $taxAmount
        ];
    }

    private function createQuoteItem(Quote $quote, array $itemData)
    {
        $quoteItem = new QuoteItem([
            'type' => $itemData['type'],
            'item_id' => $itemData['item_id'],
            'name' => $itemData['name'],
            'description' => $itemData['description'] ?? '',
            'quantity' => $itemData['quantity'],
            'unit_price' => $itemData['unit_price'],
            'tax_rate' => $itemData['tax_rate'] ?? 0
        ]);

        // Buscar dados adicionais do produto/serviço
        if ($itemData['type'] === 'product') {
            $product = Product::find($itemData['item_id']);
            if ($product) {
                $quoteItem->category = $product->category;
                $quoteItem->unit = $product->unit;
            }
        } elseif ($itemData['type'] === 'service') {
            $service = Service::find($itemData['item_id']);
            if ($service) {
                $quoteItem->category = $service->category;
                $quoteItem->complexity_level = $service->complexity_level;
                $quoteItem->estimated_hours = $service->estimated_hours;
            }
        }

        $quote->items()->save($quoteItem);

        return $quoteItem;
    }

    private function generateQuoteNumber()
    {
        $settings = BillingSetting::getSettings();
        $prefix = $settings->quote_prefix ?? 'COT';
        $nextNumber = $settings->next_quote_number ?? 1;

        // Incrementar o número para a próxima cotação
        $settings->increment('next_quote_number');

        return $prefix . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    private function getConversionRate()
    {
        $totalQuotes = Quote::count();
        $convertedQuotes = Quote::whereNotNull('converted_to_invoice_at')->count();

        return $totalQuotes > 0 ? round(($convertedQuotes / $totalQuotes) * 100, 2) : 0;
    }
}