<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $emailSubject;
    public $emailMessage;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice, $subject, $message = null)
    {
        $this->invoice = $invoice;
        $this->emailSubject = $subject;
        $this->emailMessage = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Gerar o PDF da fatura
        $pdf = Pdf::loadView('pdfs.invoice', [
            'invoice' => $this->invoice,
            'company' => $this->invoice->company
        ]);

        $filename = 'fatura-' . $this->invoice->invoice_number . '.pdf';

        return $this->view('emails.invoice')
                    ->subject($this->emailSubject)
                    ->with([
                        'invoice' => $this->invoice,
                        'company' => $this->invoice->company,
                        'customMessage' => $this->emailMessage
                    ])
                    ->attachData($pdf->output(), $filename, [
                        'mime' => 'application/pdf',
                    ]);
    }
}