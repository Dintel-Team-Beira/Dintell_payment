<?php

namespace App\Console\Commands;

use App\Models\Plan;
use Illuminate\Console\Command;

class ManagePlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plans:manage
                            {action : Action to perform (list|create|update|delete|reset)}
                            {--slug= : Plan slug for update/delete operations}
                            {--name= : Plan name}
                            {--price= : Plan price}
                            {--users= : Max users (null for unlimited)}
                            {--active : Set plan as active}
                            {--popular : Set plan as popular}
                            {--force : Force operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage subscription plans from command line';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'list':
                $this->listPlans();
                break;
            case 'create':
                $this->createPlan();
                break;
            case 'update':
                $this->updatePlan();
                break;
            case 'delete':
                $this->deletePlan();
                break;
            case 'reset':
                $this->resetPlans();
                break;
            default:
                $this->error("Invalid action: {$action}");
                $this->info('Available actions: list, create, update, delete, reset');
                return 1;
        }

        return 0;
    }

    private function listPlans()
    {
        $plans = Plan::orderBy('sort_order')->orderBy('price')->get();

        if ($plans->isEmpty()) {
            $this->warn('No plans found in database.');
            $this->info('Run: php artisan db:seed --class=PlansSeeder');
            return;
        }

        $this->info("📋 Subscription Plans ({$plans->count()} total)");
        $this->newLine();

        $tableData = [];
        foreach ($plans as $plan) {
            $tableData[] = [
                $plan->id,
                $plan->name,
                $plan->slug,
                $plan->formatted_price,
                $plan->billing_cycle_text,
                $plan->max_users ?? '∞',
                $plan->max_invoices_per_month ?? '∞',
                $plan->is_active ? '✅' : '❌',
                $plan->is_popular ? '⭐' : '-',
                $plan->companies()->count() . ' empresas'
            ];
        }

        $this->table([
            'ID', 'Nome', 'Slug', 'Preço', 'Ciclo', 'Usuários', 'Faturas/mês', 'Ativo', 'Popular', 'Uso'
        ], $tableData);

        // Estatísticas
        $this->newLine();
        $this->info('📊 Estatísticas:');
        $this->info("• Planos ativos: " . $plans->where('is_active', true)->count());
        $this->info("• Planos populares: " . $plans->where('is_popular', true)->count());
        $this->info("• Planos gratuitos: " . $plans->where('price', 0)->count());
        $this->info("• Planos mensais: " . $plans->where('billing_cycle', 'monthly')->count());
        $this->info("• Planos anuais: " . $plans->where('billing_cycle', 'yearly')->count());
    }

    private function createPlan()
    {
        $this->info('🆕 Criando novo plano...');

        $data = [
            'name' => $this->option('name') ?: $this->ask('Nome do plano'),
            'price' => $this->option('price') ?: $this->ask('Preço (0 para gratuito)', '0'),
            'max_users' => $this->option('users') ?: $this->ask('Máximo de usuários (deixe vazio para ilimitado)') ?: null,
        ];

        // Gerar slug
        $data['slug'] = \Str::slug($data['name']);

        // Verificar se slug já existe
        if (Plan::where('slug', $data['slug'])->exists()) {
            $this->error("Plano com slug '{$data['slug']}' já existe!");
            return;
        }

        // Dados adicionais
        $data['description'] = $this->ask('Descrição do plano');
        $data['currency'] = 'MZN';
        $data['billing_cycle'] = $this->choice('Ciclo de cobrança', ['monthly', 'yearly'], 0);
        $data['is_active'] = $this->option('active') || $this->confirm('Plano ativo?', true);
        $data['is_popular'] = $this->option('popular') || $this->confirm('Plano popular?', false);
        $data['max_invoices_per_month'] = $this->ask('Máximo de faturas por mês (deixe vazio para ilimitado)') ?: null;
        $data['max_clients'] = $this->ask('Máximo de clientes (deixe vazio para ilimitado)') ?: null;
        $data['trial_days'] = $this->ask('Dias de trial', '0');
        $data['has_trial'] = $data['trial_days'] > 0;

        // Features básicas
        $data['features'] = [
            'Faturação básica',
            'Gestão de clientes',
            'Relatórios básicos',
            'Suporte por email'
        ];

        try {
            $plan = Plan::create($data);
            $this->info("✅ Plano '{$plan->name}' criado com sucesso!");
            $this->info("   Slug: {$plan->slug}");
            $this->info("   ID: {$plan->id}");
        } catch (\Exception $e) {
            $this->error("❌ Erro ao criar plano: " . $e->getMessage());
        }
    }

    private function updatePlan()
    {
        $slug = $this->option('slug') ?: $this->ask('Slug do plano a ser atualizado');

        $plan = Plan::where('slug', $slug)->first();

        if (!$plan) {
            $this->error("Plano com slug '{$slug}' não encontrado!");
            return;
        }

        $this->info("📝 Atualizando plano: {$plan->name}");

        $updates = [];

        if ($name = $this->option('name')) {
            $updates['name'] = $name;
        }

        if ($price = $this->option('price')) {
            $updates['price'] = $price;
        }

        if ($users = $this->option('users')) {
            $updates['max_users'] = $users === 'null' ? null : intval($users);
        }

        if ($this->option('active')) {
            $updates['is_active'] = true;
        }

        if ($this->option('popular')) {
            $updates['is_popular'] = true;
        }

        if (empty($updates)) {
            $this->warn('Nenhuma atualização especificada.');
            return;
        }

        try {
            $plan->update($updates);
            $this->info("✅ Plano '{$plan->name}' atualizado com sucesso!");

            foreach ($updates as $key => $value) {
                $this->info("   {$key}: {$value}");
            }
        } catch (\Exception $e) {
            $this->error("❌ Erro ao atualizar plano: " . $e->getMessage());
        }
    }

    private function deletePlan()
    {
        $slug = $this->option('slug') ?: $this->ask('Slug do plano a ser deletado');

        $plan = Plan::where('slug', $slug)->first();

        if (!$plan) {
            $this->error("Plano com slug '{$slug}' não encontrado!");
            return;
        }

        $companiesCount = $plan->companies()->count();

        if ($companiesCount > 0) {
            $this->error("❌ Não é possível deletar o plano '{$plan->name}'!");
            $this->error("   {$companiesCount} empresa(s) estão usando este plano.");
            $this->info("   Mova as empresas para outro plano antes de deletar.");
            return;
        }

        $this->warn("⚠️  Você está prestes a deletar o plano: {$plan->name}");

        if (!$this->option('force') && !$this->confirm('Tem certeza?', false)) {
            $this->info('Operação cancelada.');
            return;
        }

        try {
            $planName = $plan->name;
            $plan->delete();
            $this->info("✅ Plano '{$planName}' deletado com sucesso!");
        } catch (\Exception $e) {
            $this->error("❌ Erro ao deletar plano: " . $e->getMessage());
        }
    }

    private function resetPlans()
    {
        $this->warn('⚠️  Esta operação vai DELETAR TODOS os planos e recriar os padrão!');

        $companiesWithPlans = \DB::table('companies')
            ->whereNotNull('plan_id')
            ->count();

        if ($companiesWithPlans > 0) {
            $this->error("❌ Existem {$companiesWithPlans} empresa(s) com planos atribuídos!");
            $this->error("   Não é possível resetar os planos.");
            return;
        }

        if (!$this->option('force') && !$this->confirm('Tem certeza que deseja continuar?', false)) {
            $this->info('Operação cancelada.');
            return;
        }

        try {
            $this->info('🔄 Deletando planos existentes...');
            Plan::truncate();

            $this->info('📝 Criando planos padrão...');
            $this->call('db:seed', ['--class' => 'PlansSeeder']);

            $this->info('✅ Planos resetados com sucesso!');
            $this->newLine();
            $this->listPlans();
        } catch (\Exception $e) {
            $this->error("❌ Erro ao resetar planos: " . $e->getMessage());
        }
    }
}
