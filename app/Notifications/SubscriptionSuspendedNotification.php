<?php

// ===== NOTIFICAÇÃO DE SUSPENSÃO DETALHADA =====
// app/Notifications/SubscriptionSuspendedNotification.php

namespace App\Notifications;

use App\Models\Subscription;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Storage;

class SubscriptionSuspendedNotification extends Notification
{
    use Queueable;

    protected $subscription;
    protected $attachNotice;

    public function __construct(Subscription $subscription, $attachNotice = true)
    {
        $this->subscription = $subscription;
        $this->attachNotice = $attachNotice;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('🚨 Importante: Serviço Suspenso - ' . $this->subscription->domain . ' - Ação Necessária')
            ->view('emails.subscription-suspended', [
                'subscription' => $this->subscription,
                'client' => $notifiable,
                'plan' => $this->subscription->plan,
                'company' => $this->getCompanyInfo(),
                'suspensionDate' => $this->subscription->suspended_at,
                'suspensionReason' => $this->subscription->suspension_reason,
                'daysOverdue' => $this->calculateDaysOverdue(),
                'amountDue' => $this->calculateAmountDue(),
                'reactivationSteps' => $this->getReactivationSteps(),
                'gracePeriodEnd' => $this->getGracePeriodEnd()
            ]);

        // Anexar aviso de suspensão se solicitado
        if ($this->attachNotice) {
            $noticePath = $this->generateSuspensionNotice();
            if ($noticePath && Storage::exists($noticePath)) {
                $message->attach(Storage::path($noticePath), [
                    'as' => 'Aviso_Suspensao_' . $this->subscription->domain . '.pdf',
                    'mime' => 'application/pdf',
                ]);
            }
        }

        $this->logEmail($notifiable, 'suspended');

        return $message;
    }

    private function getCompanyInfo()
    {
        return [
            'name' => 'DINTELL, LDA',
            'nuit' => '401170839',
            'address_maputo' => 'Av. Maguiguana nº 137 R/C - Maputo',
            'address_beira' => 'Rua Correia de Brito nº 1697, 1º andar - Beira',
            'country' => 'Moçambique',
            'phone' => '866713342',
            'whatsapp' => '866713342',
            'email' => 'comercial@dintell.co.mz',
            'support_email' => 'suporte@dintell.co.mz',
            'website' => 'www.dintell.co.mz',
            'slogan' => 'beyond technology, intelligence.',
            'bank_name' => 'BCI',
            'bank_account' => '222 038 724 100 01',
            'bank_nib' => '0008 0000 2203 8724 101 13'
        ];
    }

    private function calculateDaysOverdue()
    {
        if ($this->subscription->ends_at && $this->subscription->ends_at < now()) {
            return now()->diffInDays($this->subscription->ends_at);
        }
        return 0;
    }

    private function calculateAmountDue()
    {
        // Se há pagamentos em atraso, calcular valor devido
        if ($this->subscription->ends_at && $this->subscription->ends_at < now()) {
            $periodsOverdue = ceil($this->calculateDaysOverdue() / $this->subscription->plan->billing_cycle_days);
            return $periodsOverdue * $this->subscription->plan->price;
        }
        return $this->subscription->plan->price;
    }

    private function getReactivationSteps()
    {
        return [
            '1. Efetuar o pagamento em atraso',
            '2. Enviar comprovativo de pagamento',
            '3. Aguardar reativação (até 2 horas úteis)',
            '4. Verificar funcionamento do website'
        ];
    }

    private function getGracePeriodEnd()
    {
        // Período de carência de 7 dias após suspensão
        return $this->subscription->suspended_at ?
               $this->subscription->suspended_at->addDays(7) :
               now()->addDays(7);
    }

    private function generateSuspensionNotice()
    {
        try {
            $pdf = app('dompdf.wrapper');

            $html = view('pdfs.suspension-notice', [
                'subscription' => $this->subscription,
                'client' => $this->subscription->client,
                'plan' => $this->subscription->plan,
                'company' => $this->getCompanyInfo(),
                'suspensionDate' => $this->subscription->suspended_at,
                'suspensionReason' => $this->subscription->suspension_reason,
                'noticeNumber' => $this->generateNoticeNumber(),
                'daysOverdue' => $this->calculateDaysOverdue(),
                'amountDue' => $this->calculateAmountDue(),
                'gracePeriodEnd' => $this->getGracePeriodEnd(),
                'reactivationSteps' => $this->getReactivationSteps()
            ])->render();

            $pdf->loadHTML($html);
            $pdf->setPaper('A4', 'portrait');

            $fileName = 'notices/aviso_suspensao_' . $this->subscription->id . '_' . time() . '.pdf';
            Storage::put($fileName, $pdf->output());

            return $fileName;

        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF do aviso de suspensão: ' . $e->getMessage());
            return null;
        }
    }

    private function generateNoticeNumber()
    {
        return sprintf('SUSP-%d-%s',
            $this->subscription->id,
            now()->format('YmdHis')
        );
    }

    private function logEmail($notifiable, $type)
    {
        EmailLog::create([
            'subscription_id' => $this->subscription->id,
            'client_id' => $this->subscription->client_id,
            'to_email' => $notifiable->email,
            'subject' => '🚨 Importante: Serviço Suspenso - ' . $this->subscription->domain . ' - Ação Necessária',
            'type' => $type,
            'content' => 'Notificação detalhada de suspensão de subscrição - Motivo: ' . ($this->subscription->suspension_reason ?? 'Não especificado'),
            'status' => 'sent',
            'sent_at' => now(),
            'has_attachment' => $this->attachNotice,
            'attachment_name' => $this->attachNotice ? 'Aviso_Suspensao_' . $this->subscription->domain . '.pdf' : null
        ]);
    }
}