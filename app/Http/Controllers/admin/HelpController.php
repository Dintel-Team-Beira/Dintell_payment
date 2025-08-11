<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class AdminHelpController extends Controller
{
    /**
     * Documentação do sistema.
     */
    public function documentation()
    {
        $sections = $this->getDocumentationSections();
        $recentUpdates = $this->getRecentDocumentationUpdates();

        return view('admin.help.documentation', compact('sections', 'recentUpdates'));
    }

    /**
     * Documentação da API.
     */
    public function apiDocs()
    {
        $endpoints = $this->getApiEndpoints();
        $authentication = $this->getApiAuthenticationGuide();
        $examples = $this->getApiExamples();

        return view('admin.help.api-docs', compact('endpoints', 'authentication', 'examples'));
    }

    /**
     * Changelog do sistema.
     */
    public function changelog()
    {
        $versions = $this->getSystemVersions();
        $currentVersion = $this->getCurrentVersion();

        return view('admin.help.changelog', compact('versions', 'currentVersion'));
    }

    /**
     * Seções da documentação (mock data).
     */
    private function getDocumentationSections()
    {
        return [
            [
                'id' => 'getting-started',
                'title' => 'Começando',
                'description' => 'Guia básico para começar a usar o sistema',
                'icon' => 'fas fa-play',
                'articles' => [
                    ['title' => 'Configuração Inicial', 'url' => '#setup', 'updated_at' => Carbon::now()->subDays(2)],
                    ['title' => 'Primeiros Passos', 'url' => '#first-steps', 'updated_at' => Carbon::now()->subDays(1)],
                    ['title' => 'Interface do Sistema', 'url' => '#interface', 'updated_at' => Carbon::now()->subWeek()],
                ]
            ],
            [
                'id' => 'companies',
                'title' => 'Gestão de Empresas',
                'description' => 'Como gerenciar empresas no sistema',
                'icon' => 'fas fa-building',
                'articles' => [
                    ['title' => 'Criar Nova Empresa', 'url' => '#create-company', 'updated_at' => Carbon::now()->subDays(3)],
                    ['title' => 'Configurações da Empresa', 'url' => '#company-settings', 'updated_at' => Carbon::now()->subDays(2)],
                    ['title' => 'Suspender/Ativar Empresa', 'url' => '#company-status', 'updated_at' => Carbon::now()->subWeek()],
                    ['title' => 'Impersonificação', 'url' => '#impersonation', 'updated_at' => Carbon::now()->subDays(5)],
                ]
            ],
            [
                'id' => 'users',
                'title' => 'Gestão de Usuários',
                'description' => 'Gerenciamento de usuários do sistema',
                'icon' => 'fas fa-users',
                'articles' => [
                    ['title' => 'Criar Usuários', 'url' => '#create-users', 'updated_at' => Carbon::now()->subDays(4)],
                    ['title' => 'Permissões e Roles', 'url' => '#permissions', 'updated_at' => Carbon::now()->subWeek()],
                    ['title' => 'Resetar Senhas', 'url' => '#reset-passwords', 'updated_at' => Carbon::now()->subDays(6)],
                ]
            ],
            [
                'id' => 'monitoring',
                'title' => 'Monitoramento',
                'description' => 'Como usar as ferramentas de monitoramento',
                'icon' => 'fas fa-chart-line',
                'articles' => [
                    ['title' => 'Dashboard de Performance', 'url' => '#performance', 'updated_at' => Carbon::now()],
                    ['title' => 'Health Check', 'url' => '#health-check', 'updated_at' => Carbon::now()],
                    ['title' => 'Métricas e Alertas', 'url' => '#metrics', 'updated_at' => Carbon::now()->subHour()],
                ]
            ],
            [
                'id' => 'support',
                'title' => 'Sistema de Suporte',
                'description' => 'Como gerenciar tickets de suporte',
                'icon' => 'fas fa-life-ring',
                'articles' => [
                    ['title' => 'Gerenciar Tickets', 'url' => '#tickets', 'updated_at' => Carbon::now()->subDays(1)],
                    ['title' => 'Responder Clientes', 'url' => '#replies', 'updated_at' => Carbon::now()->subDays(2)],
                    ['title' => 'Relatórios de Suporte', 'url' => '#support-reports', 'updated_at' => Carbon::now()->subWeek()],
                ]
            ],
            [
                'id' => 'troubleshooting',
                'title' => 'Solução de Problemas',
                'description' => 'Como resolver problemas comuns',
                'icon' => 'fas fa-wrench',
                'articles' => [
                    ['title' => 'Problemas de Conexão', 'url' => '#connection-issues', 'updated_at' => Carbon::now()->subDays(3)],
                    ['title' => 'Erros Comuns', 'url' => '#common-errors', 'updated_at' => Carbon::now()->subWeek()],
                    ['title' => 'Performance Lenta', 'url' => '#slow-performance', 'updated_at' => Carbon::now()->subDays(5)],
                ]
            ]
        ];
    }

    /**
     * Atualizações recentes da documentação.
     */
    private function getRecentDocumentationUpdates()
    {
        return [
            [
                'title' => 'Dashboard de Performance',
                'section' => 'Monitoramento',
                'type' => 'new',
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'Health Check',
                'section' => 'Monitoramento',
                'type' => 'new',
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'Primeiros Passos',
                'section' => 'Começando',
                'type' => 'updated',
                'updated_at' => Carbon::now()->subDays(1)
            ],
            [
                'title' => 'Configurações da Empresa',
                'section' => 'Gestão de Empresas',
                'type' => 'updated',
                'updated_at' => Carbon::now()->subDays(2)
            ]
        ];
    }

    /**
     * Endpoints da API (mock data).
     */
    private function getApiEndpoints()
    {
        return [
            [
                'group' => 'Verificação de Domínios',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/v1/check/{domain}',
                        'description' => 'Verificação rápida de status do domínio',
                        'parameters' => ['domain' => 'string - Domínio a verificar'],
                        'response' => '{"status": "active", "expires_at": "2024-12-31"}'
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/v1/status/{domain}',
                        'description' => 'Status detalhado do domínio',
                        'parameters' => ['domain' => 'string - Domínio a verificar'],
                        'response' => '{"status": "active", "plan": "premium", "usage": {...}}'
                    ]
                ]
            ],
            [
                'group' => 'Analytics',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/v1/analytics/{domain}',
                        'description' => 'Analytics avançado do domínio',
                        'parameters' => ['domain' => 'string - Domínio a analisar'],
                        'response' => '{"requests": 1500, "errors": 2, "avg_response": 120}'
                    ]
                ]
            ],
            [
                'group' => 'Health Check',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/v1/health',
                        'description' => 'Status de saúde do sistema',
                        'parameters' => [],
                        'response' => '{"status": "healthy", "services": {...}}'
                    ]
                ]
            ]
        ];
    }

    /**
     * Guia de autenticação da API.
     */
    private function getApiAuthenticationGuide()
    {
        return [
            'type' => 'API Key',
            'description' => 'Autenticação via API Key no header Authorization',
            'example' => 'Authorization: Bearer your-api-key-here',
            'obtain' => 'Obtenha sua API Key nas configurações da empresa'
        ];
    }

    /**
     * Exemplos de uso da API.
     */
    private function getApiExamples()
    {
        return [
            [
                'title' => 'Verificar Status de Domínio',
                'language' => 'curl',
                'code' => 'curl -X GET "https://sfs.com/api/v1/check/exemplo.com" \
  -H "Authorization: Bearer your-api-key" \
  -H "Content-Type: application/json"'
            ],
            [
                'title' => 'Verificar Status de Domínio',
                'language' => 'php',
                'code' => '$client = new GuzzleHttp\Client();
$response = $client->get("https://sfs.com/api/v1/check/exemplo.com", [
    "headers" => [
        "Authorization" => "Bearer your-api-key",
        "Content-Type" => "application/json"
    ]
]);

$data = json_decode($response->getBody(), true);'
            ],
            [
                'title' => 'Verificar Status de Domínio',
                'language' => 'javascript',
                'code' => 'fetch("https://sfs.com/api/v1/check/exemplo.com", {
    method: "GET",
    headers: {
        "Authorization": "Bearer your-api-key",
        "Content-Type": "application/json"
    }
})
.then(response => response.json())
.then(data => console.log(data));'
            ]
        ];
    }

    /**
     * Versões do sistema (mock data).
     */
    private function getSystemVersions()
    {
        return [
            [
                'version' => '2.1.0',
                'release_date' => Carbon::now(),
                'type' => 'major',
                'changes' => [
                    ['type' => 'new', 'description' => 'Sistema completo de monitoramento com dashboards de performance e health check'],
                    ['type' => 'new', 'description' => 'Sistema de tickets de suporte integrado'],
                    ['type' => 'improved', 'description' => 'Interface administrativa redesenhada'],
                    ['type' => 'improved', 'description' => 'Performance geral do sistema otimizada'],
                    ['type' => 'fixed', 'description' => 'Correção de bugs na gestão de empresas']
                ]
            ],
            [
                'version' => '2.0.1',
                'release_date' => Carbon::now()->subWeeks(2),
                'type' => 'patch',
                'changes' => [
                    ['type' => 'fixed', 'description' => 'Correção de problema na autenticação de API'],
                    ['type' => 'fixed', 'description' => 'Correção de bug na criação de relatórios'],
                    ['type' => 'improved', 'description' => 'Melhorias na validação de formulários']
                ]
            ],
            [
                'version' => '2.0.0',
                'release_date' => Carbon::now()->subMonth(),
                'type' => 'major',
                'changes' => [
                    ['type' => 'new', 'description' => 'Nova arquitetura multi-tenant'],
                    ['type' => 'new', 'description' => 'Sistema de subscrições renovado'],
                    ['type' => 'new', 'description' => 'Dashboard administrativo completo'],
                    ['type' => 'improved', 'description' => 'Performance geral melhorada em 40%'],
                    ['type' => 'breaking', 'description' => 'Alterações na estrutura da API (v1 deprecada)']
                ]
            ],
            [
                'version' => '1.9.5',
                'release_date' => Carbon::now()->subMonths(2),
                'type' => 'minor',
                'changes' => [
                    ['type' => 'new', 'description' => 'Relatórios de uso por empresa'],
                    ['type' => 'improved', 'description' => 'Interface de gestão de usuários'],
                    ['type' => 'fixed', 'description' => 'Múltiplas correções de bugs menores']
                ]
            ]
        ];
    }

    /**
     * Versão atual do sistema.
     */
    private function getCurrentVersion()
    {
        return [
            'version' => '2.1.0',
            'environment' => config('app.env'),
            'php_version' => phpversion(),
            'laravel_version' => app()->version()
        ];
    }
}
