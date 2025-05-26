<?php
// database/seeders/DemoDataSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // Criar usuário admin
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@submanager.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now()
        ]);

        // Criar planos de exemplo
        $plans = [
            [
                'name' => 'Básico',
                'slug' => 'basico',
                'description' => 'Plano ideal para pequenos websites',
                'price' => 500.00,
                'billing_cycle' => 'monthly',
                'features' => ['1 domínio', 'Suporte básico', 'API Access'],
                'max_domains' => 1
            ],
            [
                'name' => 'Profissional',
                'slug' => 'profissional',
                'description' => 'Para empresas que precisam de mais recursos',
                'price' => 1200.00,
                'billing_cycle' => 'monthly',
                'features' => ['5 domínios', 'Suporte prioritário', 'API Access', 'Analytics'],
                'max_domains' => 5
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Solução completa para grandes empresas',
                'price' => 5000.00,
                'billing_cycle' => 'monthly',
                'features' => ['Domínios ilimitados', 'Suporte 24/7', 'API Access', 'Analytics', 'White-label'],
                'max_domains' => 999
            ]
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::create($planData);
        }

        // Criar usuários de exemplo e subscrições
        $demoUsers = [
            ['name' => 'João Silva', 'email' => 'joao@exemplo.com', 'domain' => 'joaosilva.com'],
            ['name' => 'Maria Santos', 'email' => 'maria@exemplo.com', 'domain' => 'mariasantos.com'],
            ['name' => 'Pedro Costa', 'email' => 'pedro@exemplo.com', 'domain' => 'pedrocosta.com'],
        ];

        foreach ($demoUsers as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('password123'),
                'email_verified_at' => now()
            ]);

            Subscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => SubscriptionPlan::inRandomOrder()->first()->id,
                'domain' => $userData['domain'],
                'status' => collect(['active', 'active', 'active', 'suspended'])->random(),
                'starts_at' => now()->subDays(rand(1, 30)),
                'ends_at' => now()->addDays(rand(30, 365)),
                'amount_paid' => rand(500, 5000),
                'last_payment_date' => now()->subDays(rand(1, 30))
            ]);
        }

        $this->command->info('✅ Dados de demonstração criados com sucesso!');
        $this->command->info('📧 Login: admin@submanager.com');
        $this->command->info('🔑 Senha: password123');
    }
}