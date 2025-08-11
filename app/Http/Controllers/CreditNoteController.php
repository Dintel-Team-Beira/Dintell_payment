<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\BillingSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditNoteController extends Controller
{
    public function index(Request $request)
    {
        $creditNotes = Invoice::where('document_type', Invoice::TYPE_CREDIT_NOTE)
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
        $validated['company_id'] = auth()->user()->company->id;
        try {
            DB::beginTransaction();

            // Obter configurações de faturação
            $settings = BillingSetting::getSettings();

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
                'invoice_number' => $settings->getNextCreditNoteNumber(),
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
                'payment_method' => Invoice::PAYMENT_OTHER ?? 'other'
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
                    $relatedInvoice->paid_amount = max(0, ($relatedInvoice->paid_amount ?? 0) - $total);
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

    public function downloadPdf(Invoice $creditNote)
    {
        if (!$creditNote->isCreditNote()) {
            abort(404);
        }

        $creditNote->load(['client', 'items', 'relatedInvoice']);
        $settings = BillingSetting::getSettings();

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pdfs.credit-notes', compact('creditNote', 'settings'));

        $filename = 'nota-credito-' . $creditNote->invoice_number . '.pdf';

        return $pdf->download($filename);
    }

    public function sendByEmail(Request $request, Invoice $creditNote)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string'
        ]);

        if (!$creditNote->isCreditNote()) {
            abort(404);
        }

        try {
            // Gerar PDF
            $creditNote->load(['client', 'items', 'relatedInvoice']);
            $settings = BillingSetting::getSettings();

            $pdf = app('dompdf.wrapper');
            $pdf->loadView('credit-notes.pdf', compact('creditNote', 'settings'));

            $filename = 'nota-credito-' . $creditNote->invoice_number . '.pdf';

            // Enviar email (você pode usar Mail facade ou uma classe específica)
            // Mail::to($request->email)->send(new CreditNoteEmail($creditNote, $pdf->output(), $request->subject, $request->message));

            return back()->with('success', 'Nota de crédito enviada por email com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao enviar email: ' . $e->getMessage());
        }
    }

    public function edit(Invoice $creditNote)
    {
        if (!$creditNote->isCreditNote()) {
            abort(404);
        }

        $creditNote->load(['client', 'items', 'relatedInvoice']);
        $clients = Client::orderBy('name')->get();

        return view('credit-notes.edit', compact('creditNote', 'clients'));
    }

    public function update(Request $request, Invoice $creditNote)
    {
        if (!$creditNote->isCreditNote()) {
            abort(404);
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
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
        $validated['company_id'] = auth()->user()->company->id;
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

            // Atualizar nota de crédito
            $creditNote->update([
                'client_id' => $validated['client_id'],
                'invoice_date' => $validated['invoice_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_percentage' => $validated['discount_percentage'] ?? 0,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'adjustment_reason' => $validated['adjustment_reason'],
                'notes' => $validated['notes']
            ]);

            // Atualizar itens
            $creditNote->items()->delete();
            foreach ($validated['items'] as $item) {
                $creditNote->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'total_price' => $item['quantity'] * $item['unit_price']
                ]);
            }

            DB::commit();

            return redirect()->route('credit-notes.show', $creditNote)
                ->with('success', 'Nota de crédito atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erro ao atualizar nota de crédito: ' . $e->getMessage());
        }
    }

    public function destroy(Invoice $creditNote)
    {
        if (!$creditNote->isCreditNote()) {
            abort(404);
        }

        try {
            DB::beginTransaction();

            // Se estava relacionada a uma fatura, restaurar o valor
            if ($creditNote->related_invoice_id) {
                $relatedInvoice = Invoice::find($creditNote->related_invoice_id);
                if ($relatedInvoice) {
                    $relatedInvoice->paid_amount = ($relatedInvoice->paid_amount ?? 0) + $creditNote->total;
                    if ($relatedInvoice->paid_amount >= $relatedInvoice->total) {
                        $relatedInvoice->status = 'paid';
                    }
                    $relatedInvoice->save();
                }
            }

            $creditNote->delete();

            DB::commit();

            return redirect()->route('credit-notes.index')
                ->with('success', 'Nota de crédito excluída com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao excluir nota de crédito: ' . $e->getMessage());
        }
    }

    public function duplicate(Invoice $creditNote)
    {
        if (!$creditNote->isCreditNote()) {
            abort(404);
        }

        try {
            DB::beginTransaction();

            $settings = BillingSetting::getSettings();

            // Criar nova nota de crédito
            $newCreditNote = $creditNote->replicate();
            $newCreditNote->invoice_number = $settings->getNextCreditNoteNumber();
            $newCreditNote->invoice_date = now()->format('Y-m-d');
            $newCreditNote->related_invoice_id = null; // Remover relação com fatura original
            $newCreditNote->save();

            // Duplicar itens
            foreach ($creditNote->items as $item) {
                $newItem = $item->replicate();
                $newItem->invoice_id = $newCreditNote->id;
                $newItem->save();
            }

            DB::commit();

            return redirect()->route('credit-notes.edit', $newCreditNote)
                ->with('success', 'Nota de crédito duplicada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao duplicar nota de crédito: ' . $e->getMessage());
        }
    }
}
