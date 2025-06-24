<?php
// Job para envio automático de lembretes de vencimento
// app/Jobs/SendInvoiceReminders.php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Invoice;
use App\Mail\InvoiceReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendInvoiceReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Faturas que vencem em 3 dias
        $upcomingInvoices = Invoice::where('status', 'sent')
            ->whereBetween('due_date', [
                Carbon::now()->addDays(3)->startOfDay(),
                Carbon::now()->addDays(3)->endOfDay()
            ])
            ->with('client')
            ->get();

        // Faturas vencidas há 1 dia
        $overdueInvoices = Invoice::where('status', 'overdue')
            ->whereBetween('due_date', [
                Carbon::now()->subDays(1)->startOfDay(),
                Carbon::now()->subDays(1)->endOfDay()
            ])
            ->with('client')
            ->get();

        // Enviar lembretes
        foreach ($upcomingInvoices as $invoice) {
            if ($invoice->client->email) {
                Mail::to($invoice->client->email)
                    ->send(new InvoiceReminderMail($invoice, 'upcoming'));
            }
        }

        foreach ($overdueInvoices as $invoice) {
            if ($invoice->client->email) {
                Mail::to($invoice->client->email)
                    ->send(new InvoiceReminderMail($invoice, 'overdue'));
            }
        }
    }
}

