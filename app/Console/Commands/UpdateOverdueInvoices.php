<?php
// Comando Artisan para atualizar status de faturas vencidas
// app/Console/Commands/UpdateOverdueInvoices.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use Carbon\Carbon;

class UpdateOverdueInvoices extends Command
{
    protected $signature = 'invoices:update-overdue';
    protected $description = 'Atualiza o status de faturas vencidas';

    public function handle()
    {
        $overdueCount = Invoice::where('status', 'sent')
            ->where('due_date', '<', Carbon::now())
            ->update(['status' => 'overdue']);

        $this->info("$overdueCount faturas marcadas como vencidas.");

        return 0;
    }
}