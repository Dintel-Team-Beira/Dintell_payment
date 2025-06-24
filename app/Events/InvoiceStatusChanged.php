<?php
//  Evento para auditoria de faturação
// app/Events/InvoiceStatusChanged.php
namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Invoice;

class InvoiceStatusChanged
{
    use Dispatchable, SerializesModels;

    public $invoice;
    public $oldStatus;
    public $newStatus;
    public $user;

    public function __construct(Invoice $invoice, $oldStatus, $newStatus, $user = null)
    {
        $this->invoice = $invoice;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->user = $user ?? auth()->user();
    }
}
