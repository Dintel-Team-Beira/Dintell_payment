<?php

namespace App\Http\Controllers;

use App\Helpers\DocumentTemplateHelper;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use App\Models\Quote;
use App\Models\BillingSetting;
use App\Models\DocumentTemplate;
use App\Models\Receipt;
use App\Services\BillingCalculatorService;
use App\Services\InvoicePdfService;
use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    protected $calculator;
    protected $pdfService;
    protected $receiptService;

    public function __construct(BillingCalculatorService $calculator = null, InvoicePdfService $pdfService = null, ReceiptService $receiptService = null)
    {
        $this->calculator = $calculator;
        $this->pdfService = $pdfService;
    }

    public function index(Request $request)
    {
        $query = Invoice::with(['client', 'items', 'quote']);

        // dd($request->all());
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

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice_number', 'like', '%' . $request->search . '%')
                    ->orWhere('notes', 'like', '%' . $request->search . '%')
                    ->orWhereHas('client', function ($clientQuery) use ($request) {
                        $clientQuery->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('email', 'like', '%' . $request->search . '%');
                    });
            });
        }

        // Ordenação
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        if ($sortField === 'client_name') {
            $query->join('clients', 'invoices.client_id', '=', 'clients.id')
                ->orderBy('clients.name', $sortDirection)
                ->select('invoices.*');
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $invoices = $query->paginate($request->get('per_page', 15));
        $clients = Client::orderBy('name')->get();

        // Estatísticas avançadas
        $stats = $this->getInvoiceStats();

        return view('invoices.index', compact('invoices', 'clients', 'stats'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $settings = BillingSetting::getSettings();


        // Verificar se é venda à dinheiro
        $isCashSale = request()->get('cash_sale', false);
        $user = auth()->user();

        $company = $user->company;
        $excededUsage = false;
        if ($company->plan_id && $company->plan) {
            // $invoiceUsage = $company->getInvoiceUsage(); 
            $invoiceUsage = $company->getInvoiceUsageFeatured();
            if ($invoiceUsage['exceeded']) {
                $excededUsage = true;
            }
        }

        return view('invoices.create', compact('clients', 'settings', 'isCashSale', 'excededUsage'));
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'client_id' => 'required|exists:clients,id',
    //         'quote_id' => 'nullable|exists:quotes,id',
    //         'invoice_date' => 'required|date',
    //         'payment_terms_days' => 'required|numeric|min:0|max:365',
    //         'items' => 'required|array|min:1',
    //         'items.*.description' => 'required|string|max:255',
    //         'items.*.quantity' => 'required|numeric|min:0.01',
    //         'items.*.unit_price' => 'required|numeric|min:0',
    //         'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
    //         'notes' => 'nullable|string',
    //         'terms_conditions' => 'nullable|string'
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         // Garantir que payment_terms_days seja um número
    //         $paymentTermsDays = (int) $validated['payment_terms_days'];

    //         // Calcular totais
    //         $totals = $this->calculateInvoiceTotals($validated['items']);

    //         // Calcular data de vencimento corretamente
    //         $dueDate = Carbon::parse($validated['invoice_date'])
    //             ->addDays($paymentTermsDays);

    //         // Criar fatura
    //         $invoice = Invoice::create([
    //             'client_id' => $validated['client_id'],
    //             'quote_id' => $validated['quote_id'] ?? null,
    //             'invoice_date' => $validated['invoice_date'],
    //             'due_date' => $dueDate,
    //             'payment_terms_days' => $paymentTermsDays,
    //             'subtotal' => $totals['subtotal'],
    //             'tax_amount' => $totals['tax_amount'],
    //             'total' => $totals['total'],
    //             'status' => 'draft',
    //             'notes' => $validated['notes'],
    //             'terms_conditions' => $validated['terms_conditions']
    //         ]);

    //         // Criar itens da fatura
    //         foreach ($validated['items'] as $itemData) {
    //             $this->createInvoiceItem($invoice, $itemData);
    //         }

    //         DB::commit();

    //         return redirect()->route('invoices.show', $invoice)
    //             ->with('success', 'Fatura criada com sucesso!');

    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         \Log::error('Erro ao criar fatura: ' . $e->getMessage(), [
    //             'request_data' => $request->all(),
    //             'exception' => $e
    //         ]);

    //         return back()->withInput()
    //             ->with('error', 'Erro ao criar fatura: ' . $e->getMessage());
    //     }
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'quote_id' => 'nullable|exists:quotes,id',
            'invoice_date' => 'required|date',
            'payment_terms_days' => 'required|numeric|min:0|max:365',
            // 'payment_method' => 'required|in:cash,bank_transfer,check,credit_card,other',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'is_cash_sale' => 'nullable|boolean',
            'cash_received' => 'nullable|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Calcular totais
            $totals = $this->calculateInvoiceTotals($validated['items']);

            // Calcular desconto
            $discountAmount = $validated['discount_amount'] ?? 0;
            if (($validated['discount_percentage'] ?? 0) > 0) {
                $discountAmount = ($totals['subtotal'] + $totals['tax_amount']) * ($validated['discount_percentage'] / 100);
            }

            $total = $totals['subtotal'] + $totals['tax_amount'] - $discountAmount;

            // Calcular data de vencimento
            $paymentTermsDays = (int) $validated['payment_terms_days'];
            $dueDate = Carbon::parse($validated['invoice_date'])->addDays($paymentTermsDays);

            // Criar fatura
            $invoiceData = [
                'client_id' => $validated['client_id'],
                'quote_id' => $validated['quote_id'] ?? null,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $dueDate,
                'payment_terms_days' => $paymentTermsDays,
                // 'payment_method' => $validated['payment_method'],
                'subtotal' => $totals['subtotal'],
                'tax_amount' => $totals['tax_amount'],
                'discount_percentage' => $validated['discount_percentage'] ?? 0,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'status' => 'draft',
                'notes' => $validated['notes'],
                'terms_conditions' => $validated['terms_conditions'],
                'document_type' => Invoice::TYPE_INVOICE,
                'company_id' => auth()->user()->company->id
            ];

            // Se é venda à dinheiro
            if ($validated['is_cash_sale'] ?? false) {
                $cashReceived = $validated['cash_received'] ?? $total;

                if ($cashReceived < $total) {
                    throw new \Exception('Valor recebido insuficiente para venda à dinheiro');
                }

                $invoiceData['is_cash_sale'] = true;
                $invoiceData['cash_received'] = $cashReceived;
                $invoiceData['change_given'] = $cashReceived - $total;
                $invoiceData['paid_amount'] = $total;
                $invoiceData['status'] = 'paid';
                $invoiceData['paid_at'] = now();
            }

            $invoice = Invoice::create($invoiceData);

            // Criar itens da fatura
            foreach ($validated['items'] as $itemData) {
                $this->createInvoiceItem($invoice, $itemData);
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Fatura criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erro ao criar fatura: ' . $e->getMessage());
        }
    }

    private function calculateInvoiceTotals($items)
    {
        $subtotal = 0;
        $taxAmount = 0;

        foreach ($items as $item) {
            $quantity = (float) $item['quantity'];
            $unitPrice = (float) $item['unit_price'];
            $taxRate = (float) ($item['tax_rate'] ?? 0);

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

    private function createInvoiceItem($invoice, $itemData)
    {
        $quantity = (float) $itemData['quantity'];
        $unitPrice = (float) $itemData['unit_price'];
        $taxRate = (float) ($itemData['tax_rate'] ?? 0);

        $subtotal = $quantity * $unitPrice;
        $taxAmount = $subtotal * ($taxRate / 100);
        $total = $subtotal + $taxAmount;

        return $invoice->items()->create([
            'description' => $itemData['description'],
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'tax_rate' => $taxRate,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'company_id' => $invoice->company_id
        ]);
    }


    /**
     * Display the specified resource.
     * ⭐ SEM TYPE HINT - aceita tanto Invoice quanto string/int
     */
    public function show($invoice)
    {
        // Se for string/int, buscar o objeto
        if (!$invoice instanceof Invoice) {
            $invoice = Invoice::with(['client', 'items', 'quote'])->findOrFail($invoice);
        }

        // Se já for objeto, garantir que relacionamentos estão carregados
        if (!$invoice->relationLoaded('client')) {
            $invoice->load(['client', 'items', 'quote']);
        }

        // Verificar se está vencida e atualizar status automaticamente
        if ($invoice->status === 'sent' && method_exists($invoice, 'isOverdue') && $invoice->isOverdue()) {
            $invoice->update(['status' => 'overdue']);
        }

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Faturas pagas não podem ser editadas.');
        }

        $invoice->load(['client', 'items']);
        $clients = Client::orderBy('name')->get();
        $settings = BillingSetting::getSettings();

        return view('invoices.edit', compact('invoice', 'clients', 'settings'));
    }

    // public function update(Request $request, Invoice $invoice)
    // {
    //     if ($invoice->status === 'paid') {
    //         return redirect()->route('invoices.show', $invoice)
    //             ->with('error', 'Faturas pagas não podem ser editadas.');
    //     }

    //     $validated = $request->validate([
    //         'client_id' => 'required|exists:clients,id',
    //         'invoice_date' => 'required|date',
    //         'payment_terms_days' => 'required|numeric|min:0|max:365',
    //         'items' => 'required|array|min:1',
    //         'items.*.description' => 'required|string|max:255',
    //         'items.*.quantity' => 'required|numeric|min:0.01',
    //         'items.*.unit_price' => 'required|numeric|min:0',
    //         'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
    //         'notes' => 'nullable|string',
    //         'terms_conditions' => 'nullable|string'
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         // Calcular novos totais
    //         $totals = $this->calculateInvoiceTotals($validated['items']);
    //         $dueDate = Carbon::parse($validated['invoice_date'])
    //             ->addDays($validated['payment_terms_days']);

    //         // Atualizar fatura
    //         $invoice->update([
    //             'client_id' => $validated['client_id'],
    //             'invoice_date' => $validated['invoice_date'],
    //             'due_date' => $dueDate,
    //             'payment_terms_days' => $validated['payment_terms_days'],
    //             'subtotal' => $totals['subtotal'],
    //             'tax_amount' => $totals['tax_amount'],
    //             'total' => $totals['total'],
    //             'notes' => $validated['notes'],
    //             'terms_conditions' => $validated['terms_conditions']
    //         ]);

    //         // Remover itens existentes
    //         $invoice->items()->delete();

    //         // Criar novos itens
    //         foreach ($validated['items'] as $itemData) {
    //             $this->createInvoiceItem($invoice, $itemData);
    //         }

    //         DB::commit();

    //         return redirect()->route('invoices.show', $invoice)
    //             ->with('success', 'Fatura atualizada com sucesso!');

    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return back()->withInput()
    //             ->with('error', 'Erro ao atualizar fatura: ' . $e->getMessage());
    //     }
    // }
    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Faturas pagas não podem ser editadas.');
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'payment_terms_days' => 'required|numeric|min:0|max:365',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string'
        ]);
        $validated['company_id'] = auth()->user()->company->id;
        try {
            DB::beginTransaction();

            // Calcular novos totais
            $totals = $this->calculateInvoiceTotals($validated['items']);

            // Garantir que payment_terms_days é inteiro
            $paymentTerms = (int)$validated['payment_terms_days'];

            $dueDate = Carbon::parse($validated['invoice_date'])
                ->addDays($paymentTerms);

            // Atualizar campos individualmente para evitar problemas
            $invoice->client_id = $validated['client_id'];
            $invoice->invoice_date = $validated['invoice_date'];
            $invoice->due_date = $dueDate;
            $invoice->payment_terms_days = $paymentTerms;
            $invoice->subtotal = $totals['subtotal'];
            $invoice->tax_amount = $totals['tax_amount'];
            $invoice->total = $totals['total'];
            $invoice->notes = $validated['notes'];
            $invoice->terms_conditions = $validated['terms_conditions'];

            $invoice->save();

            // Remover itens existentes
            $invoice->items()->delete();

            // Criar novos itens
            foreach ($validated['items'] as $itemData) {
                $this->createInvoiceItem($invoice, $itemData);
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Fatura atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log detalhado do erro
            \Log::error('Erro ao atualizar fatura', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return back()->withInput()
                ->with('error', 'Erro ao atualizar fatura: ' . $e->getMessage());
        }
    }
    public function destroy(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.index')
                ->with('error', 'Faturas pagas não podem ser excluídas.');
        }

        try {
            DB::beginTransaction();

            $invoice->items()->delete();
            $invoice->delete();

            DB::commit();

            return redirect()->route('invoices.index')
                ->with('success', 'Fatura excluída com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('invoices.index')
                ->with('error', 'Erro ao excluir fatura: ' . $e->getMessage());
        }
    }


    // public function markAsPaid(Request $request, Invoice $invoice)
    // {
    //     $request->validate([
    //         'amount' => 'nullable|numeric|min:0.01|max:' . $invoice->remaining_amount
    //     ]);

    //     try {
    //         $amount = $request->amount ?? $invoice->remaining_amount;
    //         $invoice->markAsPaid($amount);

    //         if ($request->expectsJson()) {
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Pagamento registrado com sucesso!'
    //             ]);
    //         }

    //         return back()->with('success', 'Pagamento registrado com sucesso!');
    //     } catch (\Exception $e) {
    //         if ($request->expectsJson()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Erro ao registrar pagamento: ' . $e->getMessage()
    //             ], 500);
    //         }

    //         return back()->with('error', 'Erro ao registrar pagamento: ' . $e->getMessage());
    //     }
    // }

    // Método markAsPaid simplificado no InvoiceController

    public function markAsPaid(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0.01|max:' . $invoice->total
        ]);

        try {
            DB::beginTransaction();

            $amount = $request->amount ?? $invoice->total;

            // Atualizar a fatura
            $invoice->markAsPaid($amount);

            $receipt = null;

            // Gerar recibo automaticamente se a fatura estiver totalmente paga
            if ($invoice->status === 'paid') {
                try {
                    $receipt = Receipt::create([
                        'invoice_id' => $invoice->id,
                        'client_id' => $invoice->client_id,
                        'amount_paid' => $amount,
                          'company_id'=>$invoice->company_id,
                        'payment_method' => Receipt::PAYMENT_OTHER, // método genérico
                        'payment_date' => now(),
                        'notes' => "Recibo gerado automaticamente para fatura {$invoice->invoice_number}",
                        'issued_by' => auth()->user()->id,
                    ]);
                } catch (\Exception $e) {
                    // Log do erro mas não falhar a operação
                    \Log::warning('Erro ao gerar recibo automaticamente', [
                        'invoice_id' => $invoice->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            $message = 'Pagamento registrado com sucesso!';
            if ($receipt) {
                $message .= ' Recibo ' . $receipt->receipt_number . ' foi gerado automaticamente.';
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'invoice' => [
                        'id' => $invoice->id,
                        'status' => $invoice->status,
                        'paid_amount' => $invoice->paid_amount,
                        'remaining_amount' => $invoice->remaining_amount
                    ],
                    'receipt' => $receipt ? [
                        'id' => $receipt->id,
                        'receipt_number' => $receipt->receipt_number,
                        'download_url' => route('receipts.download-pdf', $receipt)
                    ] : null
                ]);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao registrar pagamento: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erro ao registrar pagamento: ' . $e->getMessage());
        }
    }

    // Novo método para gerar recibo manual
    public function generateReceipt(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0.01|max:' . $invoice->remaining_amount,
            'payment_method' => 'required|in:cash,bank_transfer,check,credit_card,mobile_money,other',
            'transaction_reference' => 'nullable|string|max:255',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
            'update_invoice' => 'nullable|boolean'
        ]);

        try {
            if (!$this->receiptService) {
                throw new \Exception('Serviço de recibos não disponível');
            }

            $paymentData = [
                'amount_paid' => $request->amount_paid,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date ? \Carbon\Carbon::parse($request->payment_date) : now(),
                'transaction_reference' => $request->transaction_reference,
                'notes' => $request->notes,
                'update_invoice' => $request->update_invoice ?? true,
            ];

            $receipt = $this->receiptService->generateManualReceipt($invoice, $paymentData);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Recibo gerado com sucesso!',
                    'receipt' => [
                        'id' => $receipt->id,
                        'receipt_number' => $receipt->receipt_number,
                        'amount' => $receipt->formatted_amount,
                        'download_url' => route('receipts.download-pdf', $receipt)
                    ]
                ]);
            }

            return back()->with('success', 'Recibo ' . $receipt->receipt_number . ' gerado com sucesso!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar recibo: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erro ao gerar recibo: ' . $e->getMessage());
        }
    }

    // Método para listar recibos de uma fatura
    public function receipts(Invoice $invoice)
    {
        $receipts = $invoice->receipts()
            ->with(['issuedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        if (request()->expectsJson()) {
            return response()->json([
                'receipts' => $receipts->map(function ($receipt) {
                    return [
                        'id' => $receipt->id,
                        'receipt_number' => $receipt->receipt_number,
                        'amount_paid' => $receipt->formatted_amount,
                        'payment_method' => $receipt->payment_method_label,
                        'payment_date' => $receipt->formatted_payment_date,
                        'status' => $receipt->status,
                        'issued_by' => $receipt->issuedBy ? $receipt->issuedBy->name : 'Sistema',
                        'download_url' => route('receipts.download-pdf', $receipt)
                    ];
                })
            ]);
        }

        return view('invoices.receipts', compact('invoice', 'receipts'));
    }
    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate([
            'status' => 'required|in:draft,sent,paid,overdue,cancelled'
        ]);

        $oldStatus = $invoice->status;
        $newStatus = $request->status;

        // Regras de negócio para mudança de status
        if ($oldStatus === 'paid' && $newStatus !== 'paid') {
            return back()->with('error', 'Faturas pagas não podem ter o status alterado.');
        }

        $updateData = ['status' => $newStatus];

        // Se marcar como enviada, registrar timestamp
        if ($newStatus === 'sent' && $oldStatus !== 'sent') {
            $updateData['sent_at'] = now();
        }

        $invoice->update($updateData);

        $statusMessages = [
            'draft' => 'Fatura salva como rascunho',
            'sent' => 'Fatura marcada como enviada',
            'paid' => 'Fatura marcada como paga',
            'overdue' => 'Fatura marcada como vencida',
            'cancelled' => 'Fatura cancelada'
        ];

        return back()->with('success', $statusMessages[$newStatus] ?? 'Status atualizado!');
    }

    public function duplicate(Invoice $invoice)
    {
        try {
            DB::beginTransaction();

            // Duplicar a fatura usando apenas campos que sabemos que existem
            $newInvoice = $invoice->replicate([
                'invoice_number', // será gerado automaticamente
                'paid_amount',
                'paid_at'
            ]);

            // Tratar payment_terms_days como número
            $paymentTerms = is_numeric($invoice->payment_terms_days)
                ? (int)$invoice->payment_terms_days
                : 30;

            // Definir apenas campos que temos certeza que existem
            $newInvoice->status = 'draft';
            $newInvoice->invoice_date = Carbon::today();
            $newInvoice->due_date = Carbon::today()->addDays($paymentTerms);
            $newInvoice->paid_amount = 0;
            $newInvoice->paid_at = null;

            $newInvoice->save();

            // Duplicar itens da fatura
            if ($invoice->items && $invoice->items->count() > 0) {
                foreach ($invoice->items as $item) {
                    $newItem = $item->replicate();
                    $newItem->invoice_id = $newInvoice->id;
                    $newItem->save();
                }
            }

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Fatura duplicada com sucesso!',
                    'invoice_id' => $newInvoice->id,
                    'redirect_url' => route('invoices.edit', $newInvoice)
                ]);
            }

            return redirect()->route('invoices.edit', $newInvoice)
                ->with('success', 'Fatura duplicada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Erro ao duplicar fatura', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao duplicar fatura: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erro ao duplicar fatura: ' . $e->getMessage());
        }
    }

    public function downloadPdf(Invoice $invoice)
    {
        try {
            if ($this->pdfService) {
                return $this->pdfService->downloadInvoicePdf($invoice);
            }


            // Fallback se o serviço não estiver disponível
            return $this->generateSimplePdf($invoice);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao gerar PDF: ' . $e->getMessage());
        }
    }

    /**
     * Enviar fatura por email
     */
    public function sendByEmail(Request $request, Invoice $invoice)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'nullable|string|max:1000'
        ]);

        try {
            // Enviar email com PDF anexado
            Mail::to($request->email)->send(new InvoiceMail(
                $invoice,
                $request->subject,
                $request->message
            ));

            // Atualizar status da fatura
            $invoice->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);

            // Log da ação (opcional)
            \Log::info('Fatura enviada por email', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'email' => $request->email,
                'sent_at' => now()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Fatura enviada por email com sucesso!',
                    'data' => [
                        'invoice_status' => $invoice->status,
                        'sent_at' => $invoice->sent_at->format('d/m/Y H:i')
                    ]
                ]);
            }

            return back()->with('success', 'Fatura enviada por email com sucesso!');
        } catch (\Exception $e) {
            // Log do erro
            \Log::error('Erro ao enviar fatura por email', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao enviar email: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erro ao enviar email: ' . $e->getMessage());
        }
    }

    /**
     * Teste de configuração de email (método auxiliar)
     */
    public function testEmailConfig()
    {
        try {
            // Teste simples de configuração
            $config = config('mail');

            return response()->json([
                'success' => true,
                'message' => 'Configuração de email válida',
                'mailer' => $config['default'] ?? 'N/A',
                'host' => $config['mailers'][$config['default']]['host'] ?? 'N/A'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro na configuração de email: ' . $e->getMessage()
            ], 500);
        }
    }

    // API Methods para AJAX
    public function getClientQuotes(Client $client)
    {
        $quotes = $client->quotes()
            ->where('status', 'accepted')
            ->whereNull('converted_to_invoice_at')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'quote_number', 'total', 'status']);

        return response()->json($quotes->map(function ($quote) {
            return [
                'id' => $quote->id,
                'quote_number' => $quote->quote_number,
                'total' => $quote->total,
                'status_label' => $quote->status_label ?? $quote->status
            ];
        }));
    }

    public function getQuoteItems(Quote $quote)
    {
        $items = $quote->items()->get();

        return response()->json([
            'quote' => [
                'id' => $quote->id,
                'quote_number' => $quote->quote_number,
                'total' => $quote->total
            ],
            'items' => $items->map(function ($item) {
                return [
                    'name' => $item->name,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'tax_rate' => $item->tax_rate
                ];
            })
        ]);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:invoices,id',
            'status' => 'required|in:sent,paid,cancelled'
        ]);

        try {
            $invoices = Invoice::whereIn('id', $request->invoice_ids)->get();
            $updateCount = 0;

            foreach ($invoices as $invoice) {
                if ($invoice->status !== 'paid' || $request->status === 'paid') {
                    $updateData = ['status' => $request->status];

                    if ($request->status === 'sent' && $invoice->status !== 'sent') {
                        $updateData['sent_at'] = now();
                    }

                    $invoice->update($updateData);
                    $updateCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "{$updateCount} faturas atualizadas com sucesso!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar faturas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDownloadPdf(Request $request)
    {
        $request->validate([
            'invoice_ids' => 'required|string'
        ]);

        $invoiceIds = explode(',', $request->invoice_ids);
        $invoices = Invoice::whereIn('id', $invoiceIds)->with('client', 'items')->get();

        // Implementar download em lote de PDFs
        // Por enquanto, retornar erro
        return response()->json([
            'success' => false,
            'message' => 'Download em lote ainda não implementado'
        ], 501);
    }

    // Private Methods
    // private function calculateInvoiceTotals(array $items)
    // {
    //     $subtotal = 0;
    //     $taxAmount = 0;

    //     foreach ($items as $item) {
    //         $quantity = floatval($item['quantity']);
    //         $unitPrice = floatval($item['unit_price']);
    //         $taxRate = floatval($item['tax_rate'] ?? 0);

    //         $itemSubtotal = $quantity * $unitPrice;
    //         $itemTax = $itemSubtotal * ($taxRate / 100);

    //         $subtotal += $itemSubtotal;
    //         $taxAmount += $itemTax;
    //     }

    //     return [
    //         'subtotal' => $subtotal,
    //         'tax_amount' => $taxAmount,
    //         'total' => $subtotal + $taxAmount
    //     ];
    // }

    // private function createInvoiceItem(Invoice $invoice, array $itemData)
    // {
    //     return InvoiceItem::create([
    //         'invoice_id' => $invoice->id,
    //         'description' => $itemData['description'],
    //         'quantity' => $itemData['quantity'],
    //         'unit_price' => $itemData['unit_price'],
    //         'tax_rate' => $itemData['tax_rate'] ?? 0,
    //     ]);
    // }

    private function getInvoiceStats()
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        $today = Carbon::today();

        $stats = [
            // Total de faturas
            'total_invoices' => Invoice::count(),

            // Faturas pendentes (status = 'draft' ou due_date ainda não passou)
            'total_pending' => Invoice::whereIn('status', ['draft', 'sent'])
                ->where('due_date', '>=', $today)
                ->sum('total'),
            'pending_count' => Invoice::whereIn('status', ['draft', 'sent'])
                ->where('due_date', '>=', $today)
                ->count(),

            // Faturas vencidas (due_date passou e não foram pagas)
            'total_overdue' => Invoice::whereIn('status', ['draft', 'sent'])
                ->where('due_date', '<', $today)
                ->sum('total'),
            'count_overdue' => Invoice::whereIn('status', ['draft', 'sent'])
                ->where('due_date', '<', $today)
                ->count(),

            // Faturas pagas este mês
            'total_paid_this_month' => Invoice::where('status', 'paid')
                ->whereMonth('updated_at', $currentMonth->month) // ou use paid_at se existir
                ->whereYear('updated_at', $currentMonth->year)
                ->sum('total'),
            'paid_count_this_month' => Invoice::where('status', 'paid')
                ->whereMonth('updated_at', $currentMonth->month)
                ->whereYear('updated_at', $currentMonth->year)
                ->count(),
        ];

        // Calcular média de dias para pagamento
        $paidInvoices = Invoice::where('status', 'paid')
            ->whereNotNull('updated_at') // ou paid_at se tiver essa coluna
            ->get();

        $totalDays = 0;
        $count = 0;

        foreach ($paidInvoices as $invoice) {
            if ($invoice->updated_at && $invoice->invoice_date) {
                // Se tiver paid_at, use: $invoice->paid_at
                $days = Carbon::parse($invoice->invoice_date)->diffInDays($invoice->updated_at);
                $totalDays += $days;
                $count++;
            }
        }

        $stats['avg_payment_days'] = $count > 0 ? round($totalDays / $count) : 0;

        // Calcular crescimento comparado ao mês anterior
        $lastMonthStats = [
            'total_invoices' => Invoice::whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->count(),
            'total_amount' => Invoice::whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->sum('total')
        ];

        $currentMonthStats = [
            'total_invoices' => Invoice::whereMonth('created_at', $currentMonth->month)
                ->whereYear('created_at', $currentMonth->year)
                ->count(),
            'total_amount' => Invoice::whereMonth('created_at', $currentMonth->month)
                ->whereYear('created_at', $currentMonth->year)
                ->sum('total')
        ];

        // Calcular percentual de crescimento
        if ($lastMonthStats['total_invoices'] > 0) {
            $stats['invoices_growth'] = (($currentMonthStats['total_invoices'] - $lastMonthStats['total_invoices']) / $lastMonthStats['total_invoices']) * 100;
        } else {
            $stats['invoices_growth'] = $currentMonthStats['total_invoices'] > 0 ? 100 : 0;
        }

        if ($lastMonthStats['total_amount'] > 0) {
            $stats['amount_growth'] = (($currentMonthStats['total_amount'] - $lastMonthStats['total_amount']) / $lastMonthStats['total_amount']) * 100;
        } else {
            $stats['amount_growth'] = $currentMonthStats['total_amount'] > 0 ? 100 : 0;
        }

        return $stats;
    }
    private function generateSimplePdf(Invoice $invoice)
    {
        $company = auth()->user()->company;
        // dd($invoice->items);
        // // Implementação simples de PDF se o serviço não estiver disponível
        // $html = view('pdfs.invoice', compact('invoice','company'))->render();

        // // Usar DomPDF ou similar
        // $pdf = app('dompdf.wrapper');
        // $pdf->loadHTML($html);
        // return $pdf->download("fatura-{$invoice->invoice_number}.pdf");

        //USANDO O TEMPLATE
        $data = compact('invoice', 'company');
        $template = DocumentTemplate::where('company_id', $company->id)->where('type', 'invoice')->where('is_selected', true)->first();
        return DocumentTemplateHelper::downloadPdfDocument($template, $data);
    }
}
