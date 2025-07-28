<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SampleCompaniesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@sfs.co.mz')->first();

        // Empresa 1 - TechStart (Trial)
        $techstart = Company::create([
            'name' => 'TechStart Lda',
            'slug' => 'techstart',
            'email' => 'admin@techstart.co.mz',
            'phone' => '+258 84 123 4567',
            'address' => 'Av. Julius Nyerere, 1234, Maputo',
            'city' => 'Maputo',
            'country' => 'Moçambique',
            'tax_number' => '100123456',
            'currency' => 'MZN',
            'default_tax_rate' => 17.00,
            'bank_accounts' => [
                [
                    'name' => 'BCI',
                    'account_number' => '123456789',
                    'nib' => '0008 0000 1234 5678 901 12'
                ]
            ],
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(15),
            'subscription_plan' => 'basic',
            'max_users' => 1,
            'max_invoices_per_month' => 50,
            'max_clients' => 100,
            'monthly_fee' => 500.00,
            'custom_domain_enabled' => false,
            'api_access_enabled' => false,
            'feature_flags' => [
                'advanced_reports' => false,
                'multi_currency' => false,
                'api_access' => false,
                'custom_branding' => false,
            ],
            'created_by' => $adminUser->id,
        ]);

        // Criar usuário para TechStart
        User::create([
            'name' => 'João Silva',
            'email' => 'joao@techstart.co.mz',
            'password' => Hash::make('password'),
            'company_id' => $techstart->id,
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Empresa 2 - InnovaCorp (Active - Premium)
        $innovacorp = Company::create([
            'name' => 'InnovaCorp Sarl',
            'slug' => 'innovacorp',
            'email' => 'geral@innovacorp.co.mz',
            'phone' => '+258 82 987 6543',
            'address' => 'Rua da Paz, 567, Matola',
            'city' => 'Matola',
            'country' => 'Moçambique',
            'tax_number' => '200987654',
            'currency' => 'MZN',
            'default_tax_rate' => 17.00,
            'bank_accounts' => [
                [
                    'name' => 'Standard Bank',
                    'account_number' => '987654321',
                    'nib' => '0009 0000 9876 5432 109 87'
                ],
                [
                    'name' => 'Millennium BIM',
                    'account_number' => '555666777',
                    'nib' => '0001 0000 5556 6677 705 55'
                ]
            ],
            'status' => 'active',
            'subscription_plan' => 'premium',
            'max_users' => 5,
            'max_invoices_per_month' => 200,
            'max_clients' => 500,
            'monthly_fee' => 1500.00,
            'custom_domain_enabled' => false,
            'api_access_enabled' => true,
            'feature_flags' => [
                'advanced_reports' => true,
                'multi_currency' => true,
                'api_access' => true,
                'custom_branding' => false,
            ],
            'total_revenue' => 45000.00,
            'total_invoices' => 150,
            'total_clients' => 25,
            'current_users_count' => 3,
            'current_month_invoices' => 12,
            'last_payment_at' => now()->subDays(15),
            'next_payment_due' => now()->addDays(15),
            'created_by' => $adminUser->id,
        ]);

        // Criar usuários para InnovaCorp
        User::create([
            'name' => 'Maria Santos',
            'email' => 'maria@innovacorp.co.mz',
            'password' => Hash::make('password'),
            'company_id' => $innovacorp->id,
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Carlos Fernandes',
            'email' => 'carlos@innovacorp.co.mz',
            'password' => Hash::make('password'),
            'company_id' => $innovacorp->id,
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Empresa 3 - MegaSoft (Enterprise)
        $megasoft = Company::create([
            'name' => 'MegaSoft Moçambique',
            'slug' => 'megasoft',
            'domain' => 'faturacao.megasoft.co.mz',
            'email' => 'financeiro@megasoft.co.mz',
            'phone' => '+258 21 123 456',
            'address' => 'Av. Samora Machel, 2890, Maputo',
            'city' => 'Maputo',
            'country' => 'Moçambique',
            'tax_number' => '300456789',
            'currency' => 'MZN',
            'default_tax_rate' => 17.00,
            'bank_accounts' => [
                [
                    'name' => 'BCI',
                    'account_number' => '111222333',
                    'nib' => '0008 0000 1112 2233 301 11'
                ],
                [
                    'name' => 'Standard Bank',
                    'account_number' => '444555666',
                    'nib' => '0009 0000 4445 5566 604 44'
                ]
            ],
            'mpesa_number' => '84 123 4567',
            'status' => 'active',
            'subscription_plan' => 'enterprise',
            'max_users' => 999,
            'max_invoices_per_month' => 999999,
            'max_clients' => 999999,
            'monthly_fee' => 3000.00,
            'custom_domain_enabled' => true,
            'api_access_enabled' => true,
            'feature_flags' => [
                'advanced_reports' => true,
                'multi_currency' => true,
                'api_access' => true,
                'custom_branding' => true,
                'priority_support' => true,
            ],
            'total_revenue' => 250000.00,
            'total_invoices' => 850,
            'total_clients' => 120,
            'current_users_count' => 8,
            'current_month_invoices' => 75,
            'last_payment_at' => now()->subDays(5),
            'next_payment_due' => now()->addDays(25),
            'created_by' => $adminUser->id,
        ]);

        // Criar usuários para MegaSoft
        User::create([
            'name' => 'Ana Pereira',
            'email' => 'ana@megasoft.co.mz',
            'password' => Hash::make('password'),
            'company_id' => $megasoft->id,
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Pedro Costa',
            'email' => 'pedro@megasoft.co.mz',
            'password' => Hash::make('password'),
            'company_id' => $megasoft->id,
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Empresa 4 - StartupHub (Trial Expirando)
        $startuphub = Company::create([
            'name' => 'StartupHub Incubadora',
            'slug' => 'startuphub',
            'email' => 'info@startuphub.co.mz',
            'phone' => '+258 85 555 1234',
            'address' => 'Centro de Incubação, Polana, Maputo',
            'city' => 'Maputo',
            'country' => 'Moçambique',
            'tax_number' => '400789123',
            'currency' => 'MZN',
            'default_tax_rate' => 17.00,
            'bank_accounts' => [
                [
                    'name' => 'Millennium BIM',
                    'account_number' => '777888999',
                    'nib' => '0001 0000 7778 8899 907 77'
                ]
            ],
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(3), // Expira em 3 dias
            'subscription_plan' => 'basic',
            'max_users' => 1,
            'max_invoices_per_month' => 50,
            'max_clients' => 100,
            'monthly_fee' => 500.00,
            'custom_domain_enabled' => false,
            'api_access_enabled' => false,
            'feature_flags' => [
                'advanced_reports' => false,
                'multi_currency' => false,
                'api_access' => false,
                'custom_branding' => false,
            ],
            'total_revenue' => 8500.00,
            'total_invoices' => 25,
            'total_clients' => 8,
            'current_users_count' => 1,
            'current_month_invoices' => 5,
            'created_by' => $adminUser->id,
        ]);

        // Criar usuário para StartupHub
        User::create([
            'name' => 'Sofia Machado',
            'email' => 'sofia@startuphub.co.mz',
            'password' => Hash::make('password'),
            'company_id' => $startuphub->id,
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Empresa 5 - DigitalPro (Suspensa)
        $digitalpro = Company::create([
            'name' => 'DigitalPro Solutions',
            'slug' => 'digitalpro',
            'email' => 'admin@digitalpro.co.mz',
            'phone' => '+258 86 999 8888',
            'address' => 'Av. 24 de Julho, 1500, Maputo',
            'city' => 'Maputo',
            'country' => 'Moçambique',
            'tax_number' => '500111222',
            'currency' => 'MZN',
            'default_tax_rate' => 17.00,
            'status' => 'suspended',
            'subscription_plan' => 'premium',
            'max_users' => 5,
            'max_invoices_per_month' => 200,
            'max_clients' => 500,
            'monthly_fee' => 1500.00,
            'metadata' => [
                'suspension_reason' => 'Falta de pagamento',
                'suspended_at' => now()->subDays(10)->toISOString(),
            ],
            'created_by' => $adminUser->id,
        ]);

        User::create([
            'name' => 'Roberto Lima',
            'email' => 'roberto@digitalpro.co.mz',
            'password' => Hash::make('password'),
            'company_id' => $digitalpro->id,
            'role' => 'admin',
            'is_active' => false, // Desativado devido à suspensão
            'email_verified_at' => now(),
        ]);

        // Atualizar estatísticas das empresas
        collect([$techstart, $innovacorp, $megasoft, $startuphub, $digitalpro])->each(function ($company) {
            $company->updateUsageStats();
        });

        $this->command->info('Empresas de exemplo criadas com sucesso!');
        $this->command->line('');
        $this->command->line('Empresas criadas:');
        $this->command->line('1. TechStart (Trial) - techstart.seudominio.com');
        $this->command->line('2. InnovaCorp (Active Premium) - innovacorp.seudominio.com');
        $this->command->line('3. MegaSoft (Enterprise) - faturacao.megasoft.co.mz');
        $this->command->line('4. StartupHub (Trial expirando) - startuphub.seudominio.com');
        $this->command->line('5. DigitalPro (Suspensa) - digitalpro.seudominio.com');
        $this->command->line('');
        $this->command->line('Credenciais dos usuários: senha padrão "password"');
    }
}
