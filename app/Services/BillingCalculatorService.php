<?php
// Serviço para cálculos de faturação
// app/Services/BillingCalculatorService.php
namespace App\Services;

use App\Models\BillingSetting;

class BillingCalculatorService
{
    public function calculateTotals(array $items)
    {
        $subtotal = 0;
        $taxAmount = 0;
        $settings = BillingSetting::getSettings();

        foreach ($items as $item) {
            $itemSubtotal = $item['quantity'] * $item['unit_price'];
            $taxRate = $item['tax_rate'] ?? $settings->default_tax_rate;
            $itemTax = $itemSubtotal * ($taxRate / 100);

            $subtotal += $itemSubtotal;
            $taxAmount += $itemTax;
        }

        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'total' => round($subtotal + $taxAmount, 2)
        ];
    }

    public function calculateItemTotal(float $quantity, float $unitPrice, float $taxRate = null)
    {
        $settings = BillingSetting::getSettings();
        $taxRate = $taxRate ?? $settings->default_tax_rate;

        $subtotal = $quantity * $unitPrice;
        $tax = $subtotal * ($taxRate / 100);

        return [
            'subtotal' => round($subtotal, 2),
            'tax' => round($tax, 2),
            'total' => round($subtotal + $tax, 2)
        ];
    }

    public function recalculateDocument($document)
    {
        $subtotal = 0;
        $taxAmount = 0;

        foreach ($document->items as $item) {
            $itemSubtotal = $item->quantity * $item->unit_price;
            $itemTax = $itemSubtotal * ($item->tax_rate / 100);

            $subtotal += $itemSubtotal;
            $taxAmount += $itemTax;
        }

        $document->update([
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'total' => round($subtotal + $taxAmount, 2)
        ]);

        return $document;
    }
}