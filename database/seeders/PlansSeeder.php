<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpar planos existentes
        Plan::truncate();

        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Perfeito para freelancers e pequenos negócios que estão começando',
                'price' => 0,
                'currency' => 'MZN',
                'billing_cycle' => 'monthly',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
                'max_users' => 1,
                'max_companies' => 1,
                'max_invoices_per_month' => 25,
                'max_clients' => 50,
                'max_products' => 25,
                'max_storage_mb' => 100,
                'features' => [
                    'Faturação básica',
                    'Gestão de clientes limitada',
                    'Relatórios básicos',
                    'Suporte por email',
                    '1 usuário',
                    '25 faturas/mês',
                    '50 clientes máximo'
                ],
                'limitations' => [
                    'no_api_access' => true,
                    'no_custom_domain' => true,
                    'basic_support_only' => true
                ],
                'trial_days' => 0,
                'has_trial' => false,
                'color' => '#6B7280',
                'icon' => 'user',
                'metadata' => [
                    'recommended_for' => 'Freelancers',
                    'support_level' => 'basic'
                ]
            ],
            [
                'name' => 'Básico',
                'slug' => 'basic',
                'description' => 'Ideal para pequenos negócios com necessidades básicas de faturação',
                'price' => 500,
                'currency' => 'MZN',
                'billing_cycle' => 'monthly',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 2,
                'max_users' => 2,
                'max_companies' => 1,
                'max_invoices_per_month' => 50,
                'max_clients' => 100,
                'max_products' => 50,
                'max_storage_mb' => 250,
                'features' => [
                    'Faturação básica',
                    'Gestão de clientes',
                    'Gestão de produtos',
                    'Relatórios básicos',
                    'Suporte por email',
                    '2 usuários',
                    '50 faturas/mês',
                    '100 clientes',
                    'Backup básico'
                ],
                'limitations' => [
                    'no_api_access' => true,
                    'no_custom_domain' => true,
                    'basic_reports_only' => true
                ],
                'trial_days' => 7,
                'has_trial' => true,
                'color' => '#10B981',
                'icon' => 'building',
                'metadata' => [
                    'recommended_for' => 'Pequenos negócios',
                    'support_level' => 'standard'
                ]
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Para empresas em crescimento que precisam de recursos avançados',
                'price' => 1500,
                'currency' => 'MZN',
                'billing_cycle' => 'monthly',
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 3,
                'max_users' => 5,
                'max_companies' => 2,
                'max_invoices_per_month' => 200,
                'max_clients' => 500,
                'max_products' => 200,
                'max_storage_mb' => 1024,
                'features' => [
                    'Todas as funcionalidades do Básico',
                    'Faturação avançada',
                    'API access básica',
                    'Relatórios avançados',
                    'Múltiplas empresas',
                    'Suporte prioritário',
                    '5 usuários',
                    '200 faturas/mês',
                    '500 clientes',
                    'Backup automático',
                    'Integração com pagamentos',
                    'Templates personalizados'
                ],
                'limitations' => [
                    'limited_api_calls' => 1000,
                    'no_white_label' => true
                ],
                'trial_days' => 14,
                'has_trial' => true,
                'color' => '#3B82F6',
                'icon' => 'star',
                'metadata' => [
                    'recommended_for' => 'Empresas em crescimento',
                    'support_level' => 'priority',
                    'most_popular' => true
                ]
            ],
            [
                'name' => 'Empresarial',
                'slug' => 'enterprise',
                'description' => 'Solução completa para grandes empresas com necessidades complexas',
                'price' => 3000,
                'currency' => 'MZN',
                'billing_cycle' => 'monthly',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 4,
                'max_users' => 15,
                'max_companies' => 5,
                'max_invoices_per_month' => 1000,
                'max_clients' => 2000,
                'max_products' => 1000,
                'max_storage_mb' => 5120,
                'features' => [
                    'Todas as funcionalidades do Premium',
                    'API completa ilimitada',
                    'Múltiplas empresas',
                    'Relatórios personalizados',
                    'Integrações avançadas',
                    'Suporte 24/7',
                    'Gerente de conta dedicado',
                    '15 usuários',
                    '1000 faturas/mês',
                    '2000 clientes',
                    'Backup em tempo real',
                    'White label disponível',
                    'Customizações avançadas',
                    'Treinamento incluído'
                ],
                'limitations' => [],
                'trial_days' => 30,
                'has_trial' => true,
                'color' => '#7C3AED',
                'icon' => 'building-office',
                'metadata' => [
                    'recommended_for' => 'Grandes empresas',
                    'support_level' => '24x7',
                    'includes_training' => true,
                    'dedicated_manager' => true
                ]
            ],
            [
                'name' => 'Ultimate',
                'slug' => 'ultimate',
                'description' => 'Plano sem limites para corporações e grandes volumes',
                'price' => 7500,
                'currency' => 'MZN',
                'billing_cycle' => 'monthly',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 5,
                'max_users' => null, // Ilimitado
                'max_companies' => null, // Ilimitado
                'max_invoices_per_month' => null, // Ilimitado
                'max_clients' => null, // Ilimitado
                'max_products' => null, // Ilimitado
                'max_storage_mb' => null, // Ilimitado
                'features' => [
                    'Todas as funcionalidades do Empresarial',
                    'Usuários ilimitados',
                    'Empresas ilimitadas',
                    'Faturas ilimitadas',
                    'Clientes ilimitados',
                    'Produtos ilimitados',
                    'Armazenamento ilimitado',
                    'API completamente ilimitada',
                    'Customizações sob medida',
                    'Integração personalizada',
                    'Suporte premium 24/7',
                    'SLA garantido',
                    'Backup em múltiplas regiões',
                    'Consultoria inclusa',
                    'Implementação assistida'
                ],
                'limitations' => [],
                'trial_days' => 30,
                'has_trial' => true,
                'color' => '#F59E0B',
                'icon' => 'rocket-launch',
                'metadata' => [
                    'recommended_for' => 'Corporações',
                    'support_level' => 'premium',
                    'includes_consulting' => true,
                    'custom_implementation' => true,
                    'sla_guaranteed' => true
                ]
            ],
            // Planos anuais com desconto
            [
                'name' => 'Premium Anual',
                'slug' => 'premium-annual',
                'description' => 'Plano Premium com desconto anual (2 meses grátis)',
                'price' => 15000, // 10 meses pelo preço de 12
                'currency' => 'MZN',
                'billing_cycle' => 'yearly',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 6,
                'max_users' => 5,
                'max_companies' => 2,
                'max_invoices_per_month' => 200,
                'max_clients' => 500,
                'max_products' => 200,
                'max_storage_mb' => 1024,
                'features' => [
                    'Todas as funcionalidades do Premium',
                    '2 meses grátis',
                    'Desconto de 17%',
                    'Prioridade em atualizações'
                ],
                'limitations' => [
                    'limited_api_calls' => 1000,
                    'no_white_label' => true
                ],
                'trial_days' => 30,
                'has_trial' => true,
                'color' => '#3B82F6',
                'icon' => 'calendar',
                'metadata' => [
                    'recommended_for' => 'Empresas comprometidas',
                    'support_level' => 'priority',
                    'discount_percentage' => 17,
                    'billing_cycle_text' => 'Anual (2 meses grátis)'
                ]
            ],
            [
                'name' => 'Empresarial Anual',
                'slug' => 'enterprise-annual',
                'description' => 'Plano Empresarial com desconto anual (3 meses grátis)',
                'price' => 27000, // 9 meses pelo preço de 12
                'currency' => 'MZN',
                'billing_cycle' => 'yearly',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 7,
                'max_users' => 15,
                'max_companies' => 5,
                'max_invoices_per_month' => 1000,
                'max_clients' => 2000,
                'max_products' => 1000,
                'max_storage_mb' => 5120,
                'features' => [
                    'Todas as funcionalidades do Empresarial',
                    '3 meses grátis',
                    'Desconto de 25%',
                    'Consultoria anual inclusa'
                ],
                'limitations' => [],
                'trial_days' => 30,
                'has_trial' => true,
                'color' => '#7C3AED',
                'icon' => 'calendar-days',
                'metadata' => [
                    'recommended_for' => 'Grandes empresas',
                    'support_level' => '24x7',
                    'discount_percentage' => 25,
                    'billing_cycle_text' => 'Anual (3 meses grátis)',
                    'includes_annual_consulting' => true
                ]
            ]
        ];

        foreach ($plans as $planData) {
            Plan::create($planData);
        }

        $this->command->info('✅ Planos de assinatura criados com sucesso!');
        $this->command->info('📊 Total de planos criados: ' . count($plans));

        // Mostrar resumo dos planos criados
        $this->command->table(
            ['Nome', 'Preço', 'Ciclo', 'Usuários', 'Popular'],
            Plan::select('name', 'price', 'billing_cycle', 'max_users', 'is_popular')
                ->get()
                ->map(function ($plan) {
                    return [
                        $plan->name,
                        $plan->formatted_price,
                        $plan->billing_cycle_text,
                        $plan->max_users ?? 'Ilimitado',
                        $plan->is_popular ? '⭐' : '-'
                    ];
                })
                ->toArray()
        );
    }
}
