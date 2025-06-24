<?php

// Resource para API - Quote
// app/Http/Resources/QuoteResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'quote_number' => $this->quote_number,
            'client' => [
                'id' => $this->client->id,
                'name' => $this->client->name,
                'email' => $this->client->email
            ],
            'quote_date' => $this->quote_date->format('Y-m-d'),
            'valid_until' => $this->valid_until->format('Y-m-d'),
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'subtotal' => (float) $this->subtotal,
            'tax_amount' => (float) $this->tax_amount,
            'total' => (float) $this->total,
            'is_expired' => $this->isExpired(),
            'can_convert_to_invoice' => $this->canConvertToInvoice(),
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
            'accepted' => 'Aceita',
            'rejected' => 'Rejeitada',
            'expired' => 'Expirada'
        ];

        return $labels[$this->status] ?? $this->status;
    }
}