<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BillingSetting;
use App\Models\Product;
use App\Models\Service;

class BillingSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar configurações iniciais
        $this->createBillingSettings();

        // Criar produtos de exemplo
        $this->createSampleProducts();

        // Criar serviços de exemplo
        $this->createSampleServices();
    }

    private function createBillingSettings()
    {
        BillingSetting::create([
            'company_name' => 'TechSoft Solutions',
            'company_address' => 'Av. Julius Nyerere, 123, Maputo, Moçambique',
            'tax_number' => '400123456',
            'company_phone' => '(84) 1234-5678',
            'company_email' => 'contato@techsoft.co.mz',
            'company_website' => 'https://www.techsoft.co.mz',
            'invoice_prefix' => 'FAT',
            'quote_prefix' => 'COT',
            'next_invoice_number' => 1,
            'next_quote_number' => 1,
            'default_tax_rate' => 17.00,
            'default_payment_terms' => 30,
            'currency' => 'MZN',
            'number_format' => 'comma',
            'send_invoice_emails' => true,
            'send_quote_emails' => true,
            'send_overdue_reminders' => true,
            'reminder_days' => 7,
        ]);
    }

    private function createSampleProducts()
    {
        $products = [
            [
                'name' => 'Sistema de Gestão Escolar',
                'code' => 'SGE001',
                'description' => 'Sistema completo para gestão de escolas com módulos de matrícula, notas, financeiro e comunicação.',
                'price' => 25000.00,
                'cost' => 15000.00,
                'category' => 'software',
                'unit' => 'licenca',
                'stock_quantity' => 10,
                'min_stock_level' => 2,
                'tax_rate' => 17.00,
                'is_active' => true,
            ],
            [
                'name' => 'Sistema de Ponto de Venda (POS)',
                'code' => 'POS001',
                'description' => 'Sistema de PDV com controle de estoque, vendas, relatórios e integração fiscal.',
                'price' => 15000.00,
                'cost' => 8000.00,
                'category' => 'software',
                'unit' => 'licenca',
                'stock_quantity' => 15,
                'min_stock_level' => 3,
                'tax_rate' => 17.00,
                'is_active' => true,
            ],
            [
                'name' => 'Aplicativo Mobile Personalizado',
                'code' => 'APP001',
                'description' => 'Desenvolvimento de aplicativo mobile personalizado para Android e iOS.',
                'price' => 35000.00,
                'cost' => 20000.00,
                'category' => 'software',
                'unit' => 'unidade',
                'stock_quantity' => 5,
                'min_stock_level' => 1,
                'tax_rate' => 17.00,
                'is_active' => true,
            ],
            [
                'name' => 'Licença Microsoft Office 365',
                'code' => 'LIC001',
                'description' => 'Licença anual do Microsoft Office 365 Business Premium.',
                'price' => 1200.00,
                'cost' => 800.00,
                'category' => 'licencas',
                'unit' => 'ano',
                'stock_quantity' => 50,
                'min_stock_level' => 10,
                'tax_rate' => 17.00,
                'is_active' => true,
            ],
            [
                'name' => 'Servidor Dell PowerEdge',
                'code' => 'HW001',
                'description' => 'Servidor Dell PowerEdge T140 com 16GB RAM e 1TB HD.',
                'price' => 45000.00,
                'cost' => 35000.00,
                'category' => 'hardware',
                'unit' => 'unidade',
                'stock_quantity' => 3,
                'min_stock_level' => 1,
                'tax_rate' => 17.00,
                'weight' => 12.5,
                'dimensions' => '43.5 x 17.5 x 36.8 cm',
                'is_active' => true,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }

    private function createSampleServices()
    {
        $services = [
            [
                'name' => 'Desenvolvimento de Sistema Web',
                'code' => 'DEV001',
                'description' => 'Desenvolvimento completo de sistema web personalizado usando Laravel e Vue.js.',
                'hourly_rate' => 150.00,
                'fixed_price' => null,
                'category' => 'desenvolvimento',
                'complexity_level' => 'alta',
                'estimated_hours' => 200,
                'tax_rate' => 17.00,
                'requirements' => [
                    'Reunião inicial para levantamento de requisitos',
                    'Definição de escopo e funcionalidades',
                    'Aprovação do protótipo/wireframes',
                    'Acesso ao ambiente de desenvolvimento',
                    'Aprovação em cada etapa de desenvolvimento'
                ],
                'deliverables' => [
                    'Código fonte completo',
                    'Documentação técnica',
                    'Manual do usuário',
                    'Testes e validação',
                    'Deploy em ambiente de produção',
                    'Treinamento da equipe'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Design de Interface (UI/UX)',
                'code' => 'DES001',
                'description' => 'Design completo de interface para aplicações web e mobile.',
                'hourly_rate' => 100.00,
                'fixed_price' => null,
                'category' => 'design',
                'complexity_level' => 'media',
                'estimated_hours' => 80,
                'tax_rate' => 17.00,
                'requirements' => [
                    'Briefing detalhado do projeto',
                    'Referências visuais e identidade da marca',
                    'Definição de público-alvo',
                    'Aprovação de wireframes'
                ],
                'deliverables' => [
                    'Wireframes/Protótipos',
                    'Layouts finais em alta resolução',
                    'Guia de estilo/Style Guide',
                    'Assets exportados para desenvolvimento'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Consultoria em Tecnologia',
                'code' => 'CON001',
                'description' => 'Consultoria especializada em arquitetura de software e melhores práticas.',
                'hourly_rate' => 200.00,
                'fixed_price' => null,
                'category' => 'consultoria',
                'complexity_level' => 'alta',
                'estimated_hours' => 40,
                'tax_rate' => 17.00,
                'requirements' => [
                    'Reunião inicial para diagnóstico',
                    'Acesso aos dados/sistemas necessários',
                    'Disponibilidade da equipe chave'
                ],
                'deliverables' => [
                    'Relatório de diagnóstico',
                    'Plano de ação detalhado',
                    'Apresentação executiva',
                    'Acompanhamento de implementação'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Manutenção Mensal de Sistema',
                'code' => 'MAN001',
                'description' => 'Serviço mensal de manutenção preventiva e corretiva de sistemas.',
                'hourly_rate' => null,
                'fixed_price' => 2000.00,
                'category' => 'manutencao',
                'complexity_level' => 'baixa',
                'estimated_hours' => 20,
                'tax_rate' => 17.00,
                'requirements' => [
                    'Acesso aos sistemas',
                    'Cronograma de manutenção acordado',
                    'Contato técnico disponível'
                ],
                'deliverables' => [
                    'Relatório mensal de manutenção',
                    'Correções realizadas',
                    'Recomendações de melhorias',
                    'Backup de segurança'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Treinamento em Tecnologia',
                'code' => 'TRE001',
                'description' => 'Treinamento personalizado em tecnologias web e mobile.',
                'hourly_rate' => 80.00,
                'fixed_price' => null,
                'category' => 'treinamento',
                'complexity_level' => 'media',
                'estimated_hours' => 16,
                'tax_rate' => 17.00,
                'requirements' => [
                    'Definição do conteúdo programático',
                    'Sala de treinamento equipada',
                    'Lista de participantes',
                    'Material de apoio fornecido'
                ],
                'deliverables' => [
                    'Conteúdo programático',
                    'Material didático',
                    'Exercícios práticos',
                    'Certificado de participação',
                    'Suporte pós-treinamento (30 dias)'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Suporte Técnico Premium',
                'code' => 'SUP001',
                'description' => 'Suporte técnico premium com atendimento prioritário 24/7.',
                'hourly_rate' => null,
                'fixed_price' => 5000.00,
                'category' => 'suporte',
                'complexity_level' => 'alta',
                'estimated_hours' => 40,
                'tax_rate' => 17.00,
                'requirements' => [
                    'Contrato de suporte assinado',
                    'Contatos técnicos definidos',
                    'Acesso remoto configurado',
                    'SLA acordado'
                ],
                'deliverables' => [
                    'Atendimento 24/7',
                    'Tempo de resposta garantido',
                    'Relatório mensal de incidentes',
                    'Atualizações preventivas',
                    'Monitoramento proativo'
                ],
                'is_active' => true,
            ],
        ];

        foreach ($services as $serviceData) {
            Service::create($serviceData);
        }
    }
}