<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditNoteController extends Controller
{
    public function index(Request $request)
    {
        $creditNotes = Invoice::creditNotes()
            ->with(['client', 'relatedInvoice'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('credit-notes.index', compact('creditNotes'));
    }

    public function create(Request $request)
    {
        $invoiceId = $request->get('invoice_id');
        $invoice = null;

        if ($invoiceId) {
            $invoice = Invoice::findOrFail($invoiceId);
        }

        $clients = Client::orderBy('name')->get();

        return view('credit-notes.create', compact('invoice', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'related_invoice_id' => 'nullable|exists:invoices,id',
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'adjustment_reason' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Calcular totais
            $subtotal = 0;
            $taxAmount = 0;

            foreach ($validated['items'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $itemTax = $itemSubtotal * (($item['tax_rate'] ?? 0) / 100);

                $subtotal += $itemSubtotal;
                $taxAmount += $itemTax;
            }

            // Calcular desconto
            $discountAmount = $validated['discount_amount'] ?? 0;
            if ($validated['discount_percentage'] ?? 0 > 0) {
                $discountAmount = ($subtotal + $taxAmount) * ($validated['discount_percentage'] / 100);
            }

            $total = $subtotal + $taxAmount - $discountAmount;

            // Criar nota de crédito
            $creditNote = Invoice::create([
                'document_type' => Invoice::TYPE_CREDIT_NOTE,
                'client_id' => $validated['client_id'],
                'related_invoice_id' => $validated['related_invoice_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['invoice_date'], // Notas de crédito não têm vencimento
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_percentage' => $validated['discount_percentage'] ?? 0,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'status' => 'paid', // Notas de crédito são automaticamente processadas
                'adjustment_reason' => $validated['adjustment_reason'],
                'notes' => $validated['notes'],
                'payment_method' => Invoice::PAYMENT_OTHER
            ]);

            // Criar itens
            foreach ($validated['items'] as $item) {
                $creditNote->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'total_price' => $item['quantity'] * $item['unit_price']
                ]);
            }

            // Se está relacionada a uma fatura, atualizar o saldo
            if ($creditNote->related_invoice_id) {
                $relatedInvoice = Invoice::find($creditNote->related_invoice_id);
                if ($relatedInvoice) {
                    // Reduzir o valor pago da fatura original
                    $relatedInvoice->paid_amount = max(0, $relatedInvoice->paid_amount - $total);
                    if ($relatedInvoice->paid_amount < $relatedInvoice->total) {
                        $relatedInvoice->status = 'sent';
                    }
                    $relatedInvoice->save();
                }
            }

            DB::commit();

            return redirect()->route('credit-notes.show', $creditNote)
                ->with('success', 'Nota de crédito criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erro ao criar nota de crédito: ' . $e->getMessage());
        }
    }

    public function show(Invoice $creditNote)
    {
        if (!$creditNote->isCreditNote()) {
            abort(404);
        }

        $creditNote->load(['client', 'items', 'relatedInvoice']);

        return view('credit-notes.show', compact('creditNote'));
    }
}
