<?php
// app/Console/Commands/CleanOldApiLogs.php

namespace App\Console\Commands;

use App\Models\ApiLog;
use Illuminate\Console\Command;

class CleanOldApiLogs extends Command
{
    protected $signature = 'logs:clean {--days=90 : Número de dias para manter}';
    protected $description = 'Remove logs antigos da API';

    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        $count = ApiLog::where('created_at', '<', $cutoffDate)->count();

        if ($count === 0) {
            $this->info('✅ Nenhum log antigo encontrado.');
            return;
        }

        if ($this->confirm("Deseja remover {$count} logs com mais de {$days} dias?")) {
            ApiLog::where('created_at', '<', $cutoffDate)->delete();
            $this->info("✅ {$count} logs removidos com sucesso!");
        } else {
            $this->info('❌ Operação cancelada.');
        }
    }
}