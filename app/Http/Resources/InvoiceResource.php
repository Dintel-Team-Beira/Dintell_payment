<?php

// Resource para API - Invoice
// app/Http/Resources/InvoiceResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'client' => [
                'id' => $this->client->id,
                'name' => $this->client->name,
                'email' => $this->client->email
            ],
            'invoice_date' => $this->invoice_date->format('Y-m-d'),
            'due_date' => $this->due_date->format('Y-m-d'),
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'subtotal' => (float) $this->subtotal,
            'tax_amount' => (float) $this->tax_amount,
            'total' => (float) $this->total,
            'paid_amount' => (float) $this->paid_amount,
            'remaining_amount' => (float) $this->remaining_amount,
            'is_overdue' => $this->isOverdue(),
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'tax_rate' => (float) $item->tax_rate,
                        'total' => (float) $item->total
                    ];
                });
            }),
            'notes' => $this->notes,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }

    private function getStatusLabel()
    {
        $labels = [
            'draft' => 'Rascunho',
            'sent' => 'Enviada',
            'paid' => 'Paga',
            'overdue' => 'Vencida',
            'cancelled' => 'Cancelada'
        ];

        return $labels[$this->status] ?? $this->status;
    }
}
