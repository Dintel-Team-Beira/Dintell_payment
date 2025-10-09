<?php

namespace App\Mail;

use App\Helpers\DocumentTemplateHelper;
use App\Models\DocumentTemplate;
use App\Models\Receipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

      public $receipt;
    public $emailSubject;
    public $emailMessage;
    /**
     * Create a new message instance.
     */
    public function __construct(Receipt $receipt, $subject, $message = null)
    {
         $this->receipt = $receipt;
        $this->emailSubject = $subject;
        $this->emailMessage = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.receipt',
            with: [
                'receipt' => $this->receipt,
                'customMessage' => $this->emailMessage,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        try {
            $template = DocumentTemplate::where('company_id', $this->receipt->company_id)
                ->where('type', 'receipt')
                ->where('is_selected', true)
                ->first() 
                ?? DocumentTemplate::where('type', 'receipt')
                    ->where('is_default', true)
                    ->first();

            if (!$template) {
                \Log::warning('Nenhum template de recibo encontrado', [
                    'company_id' => $this->receipt->company_id,
                    'receipt_id' => $this->receipt->id
                ]);
                return [];
            }

            $data = [
                'receipt' => $this->receipt,
                'company' => $this->receipt->company,
                'client' => $this->receipt->client,
            ];

            $pdfOutput = DocumentTemplateHelper::generatePdfOutput($template, $data);
            $filename = 'RECIBO-' . $this->receipt->receipt_number . '.pdf';

            return [
                Attachment::fromData(fn () => $pdfOutput, $filename)
                    ->withMime('application/pdf'),
            ];

        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF do recibo para email', [
                'receipt_id' => $this->receipt->id,
                'company_id' => $this->receipt->company_id ?? null,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
