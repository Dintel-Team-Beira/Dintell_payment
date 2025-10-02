<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
     /**
     * Criar nova subscrição para uma empresa
     */
    public function createSubscription(
        Company $company,
        Plan $plan,
        array $options = []
    ): CompanySubscription {
        return DB::transaction(function () use ($company, $plan, $options) {
            
            // Determinar datas
            $startsAt = $options['starts_at'] ?? now();
            $billingCycle = $options['billing_cycle'] ?? $plan->billing_cycle;
            $endsAt = $this->calculateEndDate($startsAt, $billingCycle);
            
            // Criar subscrição
            $subscription = CompanySubscription::create([
                'company_id' => $company->id,
                'plan_id' => $plan->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => 'active',//$plan->has_trial ? 'trialing' : 'active',
                'trial_ends_at' => null,//$plan->has_trial ? now()->addDays($plan->trial_days) : null,
                'amount' => $plan->price,
                'currency' => $plan->currency,
                'billing_cycle' => $billingCycle,
                'next_payment_due' => $plan->has_trial ? now()->addDays($plan->trial_days) : $endsAt,
                'auto_renew' => $options['auto_renew'] ?? true,
                'coupon_code' => $options['coupon_code'] ?? null,
                'discount_amount' => $options['discount_amount'] ?? null,
                'discount_percentage' => $options['discount_percentage'] ?? null,
                'notes' => $options['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Salvar snapshot dos limites e features do plano
            $subscription->savePlanSnapshot();
            $subscription->save();

            // Atualizar empresa
            $this->syncCompanyData($company, $subscription);

            // Log da ação
            Log::info("Subscription created", [
                'subscription_id' => $subscription->id,
                'company_id' => $company->id,
                'plan_id' => $plan->id,
            ]);

            return $subscription;
        });
    }

    /**
     * Renovar subscrição
     */
    public function renewSubscription(CompanySubscription $subscription): bool
    {
        if (!$subscription->auto_renew) {
            Log::warning("Attempted to renew subscription with auto_renew disabled", [
                'subscription_id' => $subscription->id,
            ]);
            return false;
        }

        return DB::transaction(function () use ($subscription) {
            $months = match($subscription->billing_cycle) {
                'monthly' => 1,
                'quarterly' => 3,
                'yearly' => 12,
                default => 1,
            };

            $subscription->starts_at = $subscription->ends_at;
            $subscription->ends_at = $subscription->ends_at->addMonths($months);
            $subscription->next_payment_due = $subscription->ends_at;
            $subscription->renewal_count++;
            $subscription->status = 'active';
            $subscription->last_payment_at = now();
            $subscription->save();

            Log::info("Subscription renewed", [
                'subscription_id' => $subscription->id,
                'renewal_count' => $subscription->renewal_count,
            ]);

            return true;
        });
    }

    /**
     * Cancelar subscrição
     */
    public function cancelSubscription(
        CompanySubscription $subscription,
        string $reason = null,
        bool $immediate = false
    ): bool {
        return DB::transaction(function () use ($subscription, $reason, $immediate) {
            
            $subscription->canceled_at = now();
            $subscription->canceled_reason = $reason;
            $subscription->canceled_by = auth()->id();

            if ($immediate) {
                $subscription->status = 'canceled';
                $subscription->ends_at = now();
                
                // Atualizar empresa imediatamente
                $subscription->company->update([
                    'status' => 'inactive',
                ]);
            } else {
                $subscription->cancel_at_period_end = true;
                // Status só muda quando expirar
            }

            $subscription->save();

            Log::info("Subscription canceled", [
                'subscription_id' => $subscription->id,
                'immediate' => $immediate,
                'reason' => $reason,
            ]);

            return true;
        });
    }

    /**
     * Suspender subscrição
     */
    public function suspendSubscription(
        CompanySubscription $subscription,
        string $reason,
        string $message = null,
        string $details = null,
        bool $canAppeal = true
    ): bool {
        return DB::transaction(function () use ($subscription, $reason, $message, $details, $canAppeal) {
            
            $subscription->status = 'suspended';
            $subscription->suspended_at = now();
            $subscription->suspension_reason = $reason;
            $subscription->suspension_message = $message ?? $this->getDefaultSuspensionMessage($reason);
            $subscription->suspension_details = $details;
            $subscription->suspended_by = auth()->id();
            $subscription->can_appeal = $canAppeal;
            $subscription->suspension_count++;
            $subscription->save();

            // Atualizar empresa
            $subscription->company->update([
                'status' => 'suspended',
            ]);

            Log::warning("Subscription suspended", [
                'subscription_id' => $subscription->id,
                'reason' => $reason,
                'suspension_count' => $subscription->suspension_count,
            ]);

            return true;
        });
    }

    /**
     * Reativar subscrição suspensa
     */
    public function reactivateSubscription(
        CompanySubscription $subscription,
        string $notes = null
    ): bool {
        if (!$subscription->isSuspended()) {
            return false;
        }

        return DB::transaction(function () use ($subscription, $notes) {
            
            $subscription->status = 'active';
            $subscription->reactivated_at = now();
            $subscription->reactivated_by = auth()->id();
            
            if ($notes) {
                $previousNotes = $subscription->notes ?? '';
                $subscription->notes = $previousNotes . "\n[" . now()->format('Y-m-d H:i') . "] Reativação: " . $notes;
            }
            
            $subscription->save();

            // Atualizar empresa
            $subscription->company->update([
                'status' => 'active',
            ]);

            Log::info("Subscription reactivated", [
                'subscription_id' => $subscription->id,
            ]);

            return true;
        });
    }

    /**
     * Fazer upgrade de plano
     */
    public function upgradePlan(
        CompanySubscription $subscription,
        Plan $newPlan,
        bool $immediate = true
    ): CompanySubscription {
        // Validar se pode fazer upgrade
        $validation = $this->canChangePlan($subscription, $newPlan, 'upgrade');
        
        if (!$validation['can_change']) {
            throw new \Exception(implode(', ', $validation['errors']));
        }

        return DB::transaction(function () use ($subscription, $newPlan, $immediate) {
            
            // Calcular crédito proporcional do plano atual
            $credit = $this->calculateProportionalCredit($subscription);

            // Criar nova subscrição
            $newSubscription = CompanySubscription::create([
                'company_id' => $subscription->company_id,
                'plan_id' => $newPlan->id,
                'previous_subscription_id' => $subscription->id,
                'is_upgrade' => true,
                'starts_at' => $immediate ? now() : $subscription->ends_at,
                'ends_at' => $immediate ? now()->addMonth() : $subscription->ends_at->copy()->addMonth(),
                'status' => $immediate ? 'active' : 'pending',
                'amount' => $newPlan->price - $credit,
                'currency' => $newPlan->currency,
                'billing_cycle' => $subscription->billing_cycle,
                'next_payment_due' => $immediate ? now()->addMonth() : $subscription->ends_at->copy()->addMonth(),
                'auto_renew' => $subscription->auto_renew,
                'notes' => "Upgrade do plano {$subscription->plan->name} para {$newPlan->name}. Crédito aplicado: " . number_format($credit, 2),
                'created_by' => auth()->id(),
            ]);

            $newSubscription->savePlanSnapshot();
            $newSubscription->save();

            // Cancelar subscrição antiga
            $subscription->status = 'canceled';
            $subscription->canceled_at = now();
            $subscription->canceled_reason = 'Upgrade para plano superior';
            $subscription->canceled_by = auth()->id();
            
            if ($immediate) {
                $subscription->ends_at = now();
            }
            
            $subscription->save();

            // Atualizar empresa
            $this->syncCompanyData($subscription->company, $newSubscription);

            Log::info("Plan upgraded", [
                'old_subscription_id' => $subscription->id,
                'new_subscription_id' => $newSubscription->id,
                'old_plan' => $subscription->plan->name,
                'new_plan' => $newPlan->name,
                'credit_applied' => $credit,
            ]);

            return $newSubscription;
        });
    }

    /**
     * Fazer downgrade de plano
     */
    public function downgradePlan(
        CompanySubscription $subscription,
        Plan $newPlan
    ): CompanySubscription {
        // Validar se pode fazer downgrade
        $validation = $this->canChangePlan($subscription, $newPlan, 'downgrade');
        
        if (!$validation['can_change']) {
            throw new \Exception(implode(', ', $validation['errors']));
        }

        return DB::transaction(function () use ($subscription, $newPlan) {
            
            // Downgrade só entra em vigor no fim do período atual
            $newSubscription = CompanySubscription::create([
                'company_id' => $subscription->company_id,
                'plan_id' => $newPlan->id,
                'previous_subscription_id' => $subscription->id,
                'is_downgrade' => true,
                'starts_at' => $subscription->ends_at,
                'ends_at' => $subscription->ends_at->copy()->addMonth(),
                'status' => 'pending',
                'amount' => $newPlan->price,
                'currency' => $newPlan->currency,
                'billing_cycle' => $subscription->billing_cycle,
                'next_payment_due' => $subscription->ends_at->copy()->addMonth(),
                'auto_renew' => $subscription->auto_renew,
                'notes' => "Downgrade do plano {$subscription->plan->name} para {$newPlan->name}. Entrará em vigor em " . $subscription->ends_at->format('d/m/Y'),
                'created_by' => auth()->id(),
            ]);

            $newSubscription->savePlanSnapshot();
            $newSubscription->save();

            // Marcar subscrição atual para não renovar
            $subscription->auto_renew = false;
            $subscription->cancel_at_period_end = true;
            $subscription->notes = ($subscription->notes ?? '') . "\nDowngrade agendado para " . $subscription->ends_at->format('d/m/Y');
            $subscription->save();

            Log::info("Plan downgraded (scheduled)", [
                'old_subscription_id' => $subscription->id,
                'new_subscription_id' => $newSubscription->id,
                'old_plan' => $subscription->plan->name,
                'new_plan' => $newPlan->name,
                'effective_date' => $subscription->ends_at->format('Y-m-d'),
            ]);

            return $newSubscription;
        });
    }

    /**
     * Verificar subscrições expirando e processar ações
     */
    public function processExpiringSubscriptions(): array
    {
        $results = [
            'checked' => 0,
            'renewed' => 0,
            'expired' => 0,
            'warned' => 0,
        ];

        // Subscrições que vão expirar em breve
        $expiring = CompanySubscription::where('status', 'active')
            ->where('ends_at', '<=', now()->addDays(7))
            ->where('ends_at', '>', now())
            ->get();

        foreach ($expiring as $subscription) {
            $results['checked']++;
            $daysLeft = $subscription->daysUntilExpiration();

            // Avisos progressivos
            if (in_array($daysLeft, [7, 3, 1])) {
                // Aqui você pode enviar emails/notificações
                Log::info("Subscription expiring soon", [
                    'subscription_id' => $subscription->id,
                    'days_left' => $daysLeft,
                ]);
                $results['warned']++;
            }
        }

        // Subscrições que expiraram hoje
        $expired = CompanySubscription::whereIn('status', ['active', 'trialing'])
            ->where('ends_at', '<=', now())
            ->get();

        foreach ($expired as $subscription) {
            if ($subscription->auto_renew) {
                // Tentar renovar
                if ($this->renewSubscription($subscription)) {
                    $results['renewed']++;
                } else {
                    $this->markAsExpired($subscription);
                    $results['expired']++;
                }
            } else {
                $this->markAsExpired($subscription);
                $results['expired']++;
            }
        }

        return $results;
    }

    /**
     * Marcar subscrição como expirada
     */
    protected function markAsExpired(CompanySubscription $subscription): void
    {
        $subscription->status = 'expired';
        $subscription->save();

        // Atualizar empresa
        $subscription->company->update([
            'status' => 'inactive',
        ]);

        Log::info("Subscription expired", [
            'subscription_id' => $subscription->id,
        ]);
    }

    /**
     * Sincronizar dados da empresa com a subscrição
     */
    protected function syncCompanyData(Company $company, CompanySubscription $subscription): void
    {
        $plan = $subscription->plan;

        $company->update([
            'status' => $subscription->status === 'trialing' ? 'trial' : 'active',
            'subscription_plan' => $plan->slug,
            'max_users' => $plan->max_users,
            'max_invoices_per_month' => $plan->max_invoices_per_month,
            'max_clients' => $plan->max_clients,
            'trial_ends_at' => $subscription->trial_ends_at,
        ]);
    }

    /**
     * Calcular crédito proporcional de uma subscrição
     */
    protected function calculateProportionalCredit(CompanySubscription $subscription): float
    {
        if ($subscription->ends_at->isPast()) {
            return 0;
        }

        $totalDays = $subscription->starts_at->diffInDays($subscription->ends_at);
        $remainingDays = now()->diffInDays($subscription->ends_at);
        
        if ($totalDays <= 0) {
            return 0;
        }

        $dailyRate = $subscription->amount / $totalDays;
        $credit = $dailyRate * $remainingDays;

        return round($credit, 2);
    }

    /**
     * Calcular data de término baseada no ciclo de cobrança
     */
    protected function calculateEndDate(Carbon $startDate, string $billingCycle): Carbon
    {
        return match($billingCycle) {
            'monthly' => $startDate->copy()->addMonth(),
            'quarterly' => $startDate->copy()->addMonths(3),
            'yearly' => $startDate->copy()->addYear(),
            default => $startDate->copy()->addMonth(),
        };
    }

    /**
     * Validar se pode mudar de plano
     */
    protected function canChangePlan(
        CompanySubscription $subscription,
        Plan $newPlan,
        string $type
    ): array {
        $errors = [];

        // Verificar se não está suspenso
        if ($subscription->isSuspended()) {
            $errors[] = "Não é possível mudar de plano com conta suspensa";
        }

        // Verificar se o plano está ativo
        if (!$newPlan->is_active) {
            $errors[] = "O plano selecionado não está disponível";
        }

        // Verificar tipo de mudança
        if ($type === 'upgrade' && $newPlan->price <= $subscription->plan->price) {
            $errors[] = "O novo plano deve ser superior ao atual para fazer upgrade";
        }

        if ($type === 'downgrade' && $newPlan->price >= $subscription->plan->price) {
            $errors[] = "O novo plano deve ser inferior ao atual para fazer downgrade";
        }

        // Verificar se já não está no plano
        if ($subscription->plan_id === $newPlan->id) {
            $errors[] = "Você já está neste plano";
        }

        return [
            'can_change' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Mensagens padrão de suspensão
     */
    protected function getDefaultSuspensionMessage(string $reason): string
    {
        return match($reason) {
            'payment_failed' => 'Sua conta foi suspensa devido a pagamento pendente. Regularize sua situação para recuperar o acesso.',
            'terms_violation' => 'Sua conta foi suspensa por violação dos Termos de Uso. Entre em contato com o suporte.',
            'fraud_suspected' => 'Sua conta foi suspensa por segurança. Nossa equipe está analisando atividades suspeitas.',
            'excessive_usage' => 'Sua conta excedeu os limites do plano e foi suspensa. Faça upgrade ou entre em contato.',
            'chargeback' => 'Sua conta foi suspensa devido a um chargeback. Entre em contato com o financeiro.',
            'abuse_detected' => 'Sua conta foi suspensa por uso indevido do sistema. Contate o suporte para esclarecimentos.',
            default => 'Sua conta foi suspensa. Entre em contato com o suporte para mais informações.',
        };
    }
}
