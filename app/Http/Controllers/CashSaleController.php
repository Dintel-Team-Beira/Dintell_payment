<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashSaleController extends Controller
{
    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('cash-sales.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'cash_received' => 'required|numeric|min:0',
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
            if (($validated['discount_percentage'] ?? 0) > 0) {
                $discountAmount = ($subtotal + $taxAmount) * ($validated['discount_percentage'] / 100);
            }

            $total = $subtotal + $taxAmount - $discountAmount;

            // Verificar se o valor recebido é suficiente
            if ($validated['cash_received'] < $total) {
                throw new \Exception('Valor recebido é insuficiente');
            }

            $change = $validated['cash_received'] - $total;

            // Criar venda à dinheiro
            $cashSale = Invoice::create([
                'document_type' => Invoice::TYPE_INVOICE,
                'client_id' => $validated['client_id'],
                'invoice_date' => now(),
                'due_date' => now(),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_percentage' => $validated['discount_percentage'] ?? 0,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'is_cash_sale' => true,
                'payment_method' => Invoice::PAYMENT_CASH,
                'cash_received' => $validated['cash_received'],
                'change_given' => $change,
                'paid_amount' => $total,
                'status' => 'paid',
                'paid_at' => now(),
                'notes' => $validated['notes']
            ]);

            // Criar itens
            foreach ($validated['items'] as $item) {
                $cashSale->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'total_price' => $item['quantity'] * $item['unit_price']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'invoice_id' => $cashSale->id,
                'invoice_number' => $cashSale->invoice_number,
                'total' => $total,
                'change' => $change,
                'redirect_url' => route('invoices.show', $cashSale)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function quickSale()
    {
        // Vista para vendas rápidas (POS-like)
        return view('cash-sales.quick-sale');
    }
}
