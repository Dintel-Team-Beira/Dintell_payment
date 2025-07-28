<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Criar o primeiro super admin
        User::firstOrCreate(
            ['email' => 'arnaldo.tomo@dintell.co.mz'],
            [
                'name' => 'Administrador SFS',
                'email' => 'arnaldo.tomo@dintell.co.mz',
                'password' => Hash::make('Admin@123'),
                'is_super_admin' => true,
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
                'timezone' => 'Africa/Maputo',
                'language' => 'pt',
            ]
        );

        // Criar admin de backup
        User::firstOrCreate(
            ['email' => 'arnaldo@sdintell.co.mz'],
            [
                'name' => 'Arnaldo Tomo',
                'email' => 'arnaldo@sdintell.co.mz',
                'password' => Hash::make('Dev@123'),
                'is_super_admin' => true,
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
                'timezone' => 'Africa/Maputo',
                'language' => 'pt',
            ]
        );

        $this->command->info('Super administradores criados com sucesso!');
        $this->command->line('');
        $this->command->line('Credenciais:');
        $this->command->line('arnaldo.tomo@dintell.co.mz | Senha: Admin@123');
        $this->command->line('arnaldo@sdintell.co.mz | Senha: Dev@123');
        $this->command->line('');
        $this->command->line('Acesse: http://seudominio.com/admin/login');
    }
}
