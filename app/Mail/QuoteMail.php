<?php

namespace App\Mail;

use App\Helpers\DocumentTemplateHelper;
use App\Models\DocumentTemplate;
use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quote;

    public $emailSubject;

    public $emailMessage;


    /**
     * Create a new message instance.
     */
    public function __construct(Quote $quote, $subject, $message=null)
    {
        $this->quote = $quote;
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
            view: 'emails.quote',
            with:[
                'quote'=>$this->quote,
                'customMessage'=>$this->emailMessage,
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
            $template = DocumentTemplate::where('company_id', $this->quote->company_id)
                ->where('type', 'quote')
                ->where('is_selected', true)
                ->first() ?? DocumentTemplate::where('type', 'quote')->where('is_default', true)->first();
            $data = [
                'quote' => $this->quote,
                'company' => $this->quote->company,
                'client' => $this->quote->client,
            ];
            $pdfOutput = DocumentTemplateHelper::generatePdfOutput($template, $data);
            $filename = 'COTAÇÃO-'.$this->quote->quote_number.'.pdf';

            return [
                Attachment::fromData(fn () => $pdfOutput, $filename)
                    ->withMime('application/pdf'),
            ];
        } catch (\Exception $e) {
            \Log::error('PDF generation failed', [
                'quote_id' => $this->quote->id,
                'error' => $e->getMessage(),
            ]);

            return [];
        }

    }
}
