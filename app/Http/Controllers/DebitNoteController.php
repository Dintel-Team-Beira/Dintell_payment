<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebitNoteController extends Controller
{
    public function index(Request $request)
    {
        $debitNotes = Invoice::debitNotes()
            ->with(['client', 'relatedInvoice'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('debit-notes.index', compact('debitNotes'));
    }

    public function create(Request $request)
    {
        $invoiceId = $request->get('invoice_id');
        $invoice = null;

        if ($invoiceId) {
            $invoice = Invoice::findOrFail($invoiceId);
        }

        $clients = Client::orderBy('name')->get();

        return view('debit-notes.create', compact('invoice', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'related_invoice_id' => 'nullable|exists:invoices,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
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

            $total = $subtotal + $taxAmount;

            // Criar nota de débito
            $debitNote = Invoice::create([
                'document_type' => Invoice::TYPE_DEBIT_NOTE,
                'client_id' => $validated['client_id'],
                'related_invoice_id' => $validated['related_invoice_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'status' => 'sent',
                'adjustment_reason' => $validated['adjustment_reason'],
                'notes' => $validated['notes'],
                'payment_method' => Invoice::PAYMENT_OTHER
            ]);

            // Criar itens
            foreach ($validated['items'] as $item) {
                $debitNote->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'total_price' => $item['quantity'] * $item['unit_price']
                ]);
            }

            DB::commit();

            return redirect()->route('debit-notes.show', $debitNote)
                ->with('success', 'Nota de débito criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erro ao criar nota de débito: ' . $e->getMessage());
        }
    }

    public function show(Invoice $debitNote)
    {
        if (!$debitNote->isDebitNote()) {
            abort(404);
        }

        $debitNote->load(['client', 'items', 'relatedInvoice']);

        return view('debit-notes.show', compact('debitNote'));
    }
}
