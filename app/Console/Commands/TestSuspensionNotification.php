<?php

// ===== COMANDO PARA TESTAR SUSPENSÃO =====
// app/Console/Commands/TestSuspensionNotification.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Notifications\SubscriptionSuspendedNotification;

class TestSuspensionNotification extends Command
{
    protected $signature = 'test:suspension-notification
                            {subscription_id : ID da subscrição para teste}
                            {--reason= : Motivo da suspensão personalizado}
                            {--attach : Incluir anexo PDF}';

    protected $description = 'Testar notificação de suspensão com email detalhado';

    public function handle()
    {
        $subscriptionId = $this->argument('subscription_id');
        $customReason = $this->option('reason');
        $attachPdf = $this->option('attach');

        $subscription = Subscription::with(['client', 'plan'])->find($subscriptionId);

        if (!$subscription) {
            $this->error('❌ Subscrição não encontrada!');
            return Command::FAILURE;
        }

        $this->info("🔍 Preparando teste de suspensão para:");
        $this->line("   📄 Subscrição: #{$subscription->id}");
        $this->line("   🌐 Domínio: {$subscription->domain}");
        $this->line("   👤 Cliente: {$subscription->client->name}");
        $this->line("   📧 Email: {$subscription->client->email}");
        $this->line("   📦 Plano: {$subscription->plan->name}");
        $this->newLine();

        // Configurar dados de suspensão para teste
        $suspension_reason = $customReason ?: $this->choice(
            'Selecione o motivo da suspensão:',
            [
                'Falta de pagamento - Serviço vencido há mais de 7 dias',
                'Violação dos Termos de Serviço',
                'Uso excessivo de recursos do servidor',
                'Solicitação do cliente',
                'Manutenção técnica prolongada',
                'Conteúdo inadequado detectado'
            ],
            0
        );

        // Simular suspensão
        $oldStatus = $subscription->status;
        $subscription->update([
            'status' => 'suspended',
            'suspended_at' => now(),
            'suspension_reason' => $suspension_reason
        ]);

        $this->info("⚙️  Dados de suspensão configurados:");
        $this->line("   📅 Data: " . now()->format('d/m/Y H:i:s'));
        $this->line("   💡 Motivo: {$suspension_reason}");
        $this->line("   📎 Anexo PDF: " . ($attachPdf ? 'Sim' : 'Não'));
        $this->newLine();

        try {
            $this->info('📧 Enviando notificação de suspensão...');

            // Enviar notificação
            $subscription->client->notify(new SubscriptionSuspendedNotification($subscription, $attachPdf));

            $this->info('✅ Notificação enviada com sucesso!');
            $this->newLine();

            // Mostrar estatísticas
            $this->showSuspensionStats($subscription);

        } catch (\Exception $e) {
            $this->error('❌ Erro ao enviar notificação: ' . $e->getMessage());

            // Restaurar status original em caso de erro
            $subscription->update([
                'status' => $oldStatus,
                'suspended_at' => null,
                'suspension_reason' => null
            ]);

            return Command::FAILURE;
        }

        // Perguntar se quer restaurar o status
        if ($this->confirm('Deseja restaurar o status original da subscrição?', true)) {
            $subscription->update([
                'status' => $oldStatus,
                'suspended_at' => null,
                'suspension_reason' => null
            ]);
            $this->info('🔄 Status original restaurado!');
        }

        return Command::SUCCESS;
    }

    private function showSuspensionStats($subscription)
    {
        $daysOverdue = 0;
        if ($subscription->ends_at && $subscription->ends_at < now()) {
            $daysOverdue = now()->diffInDays($subscription->ends_at);
        }

        $amountDue = $subscription->plan->price;
        if ($daysOverdue > 0) {
            $periodsOverdue = ceil($daysOverdue / $subscription->plan->billing_cycle_days);
            $amountDue = $periodsOverdue * $subscription->plan->price;
        }

        $gracePeriodEnd = now()->addDays(7);

        $this->info('📊 ESTATÍSTICAS DA SUSPENSÃO:');
        $this->table(
            ['Item', 'Valor'],
            [
                ['Status Atual', '🔴 Suspenso'],
                ['Data de Suspensão', now()->format('d/m/Y H:i')],
                ['Dias em Atraso', $daysOverdue > 0 ? "{$daysOverdue} dias" : 'N/A'],
                ['Valor para Reativação', 'MT ' . number_format($amountDue, 2)],
                ['Fim do Período de Carência', $gracePeriodEnd->format('d/m/Y')],
                ['Plano', $subscription->plan->name],
                ['Cliente', $subscription->client->name],
            ]
        );
    }
}
