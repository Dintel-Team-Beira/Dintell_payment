<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpar planos existentes
        SubscriptionPlan::truncate();

        // Criar planos baseados na Dintell
        $plans = [
            [
                'name' => 'Start-Up',
                'slug' => 'start-up',
                'description' => 'Plano ideal para pequenas empresas e projetos pessoais que estão começando online',
                'price' => 2950.00,
                'setup_fee' => 9989.00,
                'billing_cycle' => 'monthly',
                'billing_cycle_days' => 30,
                'features' => [
                    'Website de 1 Página (Landing Page)',
                    'Hospedagem básica',
                    'Até 5 Contas de Email',
                    'Domínio grátis (.com)',
                    'Certificado SSL Grátis',
                    'Suporte Incluído'
                ],
                'is_active' => true,
                'is_featured' => false,
                'max_domains' => 1,
                'max_storage_gb' => 5,
                'max_bandwidth_gb' => 50,
                'color_theme' => '#3B82F6',
                'trial_days' => 0,
                'sort_order' => 1
            ],
            [
                'name' => 'Growth',
                'slug' => 'growth',
                'description' => 'Perfeito para empresas em crescimento que precisam de mais recursos e funcionalidades',
                'price' => 3450.00,
                'setup_fee' => 14949.00,
                'billing_cycle' => 'monthly',
                'billing_cycle_days' => 30,
                'features' => [
                    'Website Até 5 Páginas',
                    'Hospedagem Premium',
                    'Até 10 Contas de Email',
                    'Domínio grátis (.com / .co.mz)',
                    'Certificado SSL Grátis',
                    'Suporte Incluído'
                ],
                'is_active' => true,
                'is_featured' => true, // Plano em destaque
                'max_domains' => 1,
                'max_storage_gb' => 15,
                'max_bandwidth_gb' => 100,
                'color_theme' => '#10B981',
                'trial_days' => 7,
                'sort_order' => 2
            ],
            [
                'name' => 'Excellence',
                'slug' => 'excellence',
                'description' => 'Solução completa para empresas estabelecidas que buscam excelência online',
                'price' => 4850.00,
                'setup_fee' => 19849.00,
                'billing_cycle' => 'monthly',
                'billing_cycle_days' => 30,
                'features' => [
                    'Website até 10 páginas',
                    'Hospedagem Premium',
                    'Contas de Email ilimitadas',
                    'Domínio grátis (.com / .co.mz / .org / .co.mz)',
                    'Certificado SSL Grátis',
                    'Formulários e Integração com redes sociais',
                    'Painel de Controle cPanel',
                    'Suporte Incluído'
                ],
                'is_active' => true,
                'is_featured' => false,
                'max_domains' => 3,
                'max_storage_gb' => 50,
                'max_bandwidth_gb' => 500,
                'color_theme' => '#F59E0B',
                'trial_days' => 14,
                'sort_order' => 3
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Solução personalizada para grandes empresas com necessidades específicas',
                'price' => 8500.00,
                'setup_fee' => 25000.00,
                'billing_cycle' => 'monthly',
                'billing_cycle_days' => 30,
                'features' => [
                    'Website ilimitado de páginas',
                    'Hospedagem Premium Dedicada',
                    'Contas de Email ilimitadas',
                    'Múltiplos domínios incluídos',
                    'Certificado SSL Premium',
                    'Integração completa com redes sociais',
                    'Painel de Controle cPanel Avançado',
                    'Analytics e Relatórios',
                    'Backup automático diário',
                    'Suporte Prioritário 24/7',
                    'Consultoria técnica incluída'
                ],
                'is_active' => true,
                'is_featured' => false,
                'max_domains' => 10,
                'max_storage_gb' => 200,
                'max_bandwidth_gb' => 2000,
                'color_theme' => '#8B5CF6',
                'trial_days' => 30,
                'sort_order' => 4
            ],
            [
                'name' => 'Anual Growth',
                'slug' => 'anual-growth',
                'description' => 'Plano Growth com desconto especial para pagamento anual',
                'price' => 35000.00, // ~15% desconto
                'setup_fee' => 14949.00,
                'billing_cycle' => 'yearly',
                'billing_cycle_days' => 365,
                'features' => [
                    'Website Até 5 Páginas',
                    'Hospedagem Premium',
                    'Até 10 Contas de Email',
                    'Domínio grátis (.com / .co.mz)',
                    'Certificado SSL Grátis',
                    'Suporte Incluído',
                    '2 meses grátis no pagamento anual'
                ],
                'is_active' => true,
                'is_featured' => false,
                'max_domains' => 1,
                'max_storage_gb' => 15,
                'max_bandwidth_gb' => 100,
                'color_theme' => '#059669',
                'trial_days' => 14,
                'sort_order' => 5
            ],
            [
                'name' => 'Lifetime Special',
                'slug' => 'lifetime-special',
                'description' => 'Oferta especial vitalícia - pague uma vez e use para sempre',
                'price' => 150000.00,
                'setup_fee' => 0.00,
                'billing_cycle' => 'lifetime',
                'billing_cycle_days' => 0,
                'features' => [
                    'Website até 15 páginas',
                    'Hospedagem Premium vitalícia',
                    'Contas de Email ilimitadas',
                    'Domínios grátis incluídos',
                    'Certificado SSL Premium',
                    'Todas as integrações incluídas',
                    'Suporte vitalício',
                    'Atualizações gratuitas',
                    'Sem mensalidades'
                ],
                'is_active' => false, // Desativado por padrão
                'is_featured' => false,
                'max_domains' => 5,
                'max_storage_gb' => 100,
                'max_bandwidth_gb' => 1000,
                'color_theme' => '#DC2626',
                'trial_days' => 0,
                'sort_order' => 6
            ]
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::create($planData);
        }

        $this->command->info('Planos de subscrição criados com sucesso!');
    }
}