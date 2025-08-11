<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminHelpController extends Controller
{
    /**
     * Show documentation.
     */
    public function documentation(Request $request)
    {
        $section = $request->get('section', 'getting-started');
        $docs = $this->getDocumentationContent($section);
        $navigation = $this->getDocumentationNavigation();

        return view('admin.help.documentation', compact('docs', 'navigation', 'section'));
    }

    /**
     * Show API documentation.
     */
    public function apiDocs()
    {
        $endpoints = $this->getApiEndpoints();
        $authentication = $this->getApiAuthentication();
        $examples = $this->getApiExamples();

        return view('admin.help.api-docs', compact('endpoints', 'authentication', 'examples'));
    }

    /**
     * Show changelog.
     */
    public function changelog()
    {
        $versions = $this->getChangelogVersions();

        return view('admin.help.changelog', compact('versions'));
    }

    /**
     * Get documentation content.
     */
    private function getDocumentationContent($section)
    {
        $docs = [
            'getting-started' => [
                'title' => 'Primeiros Passos',
                'content' => $this->getGettingStartedContent()
            ],
            'user-management' => [
                'title' => 'Gestão de Usuários',
                'content' => $this->getUserManagementContent()
            ],
            'plans-management' => [
                'title' => 'Gestão de Planos',
                'content' => $this->getPlansManagementContent()
            ],
            'billing-settings' => [
                'title' => 'Configurações de Faturação',
                'content' => $this->getBillingSettingsContent()
            ],
            'system-settings' => [
                'title' => 'Configurações do Sistema',
                'content' => $this->getSystemSettingsContent()
            ],
            'security' => [
                'title' => 'Segurança',
                'content' => $this->getSecurityContent()
            ],
            'backup-restore' => [
                'title' => 'Backup e Restauração',
                'content' => $this->getBackupRestoreContent()
            ],
            'monitoring' => [
                'title' => 'Monitoramento',
                'content' => $this->getMonitoringContent()
            ],
            'troubleshooting' => [
                'title' => 'Resolução de Problemas',
                'content' => $this->getTroubleshootingContent()
            ]
        ];

        return $docs[$section] ?? $docs['getting-started'];
    }

    /**
     * Get documentation navigation.
     */
    private function getDocumentationNavigation()
    {
        return [
            'Início' => [
                'getting-started' => 'Primeiros Passos'
            ],
            'Gestão' => [
                'user-management' => 'Gestão de Usuários',
                'plans-management' => 'Gestão de Planos'
            ],
            'Configurações' => [
                'billing-settings' => 'Configurações de Faturação',
                'system-settings' => 'Configurações do Sistema',
                'security' => 'Segurança'
            ],
            'Manutenção' => [
                'backup-restore' => 'Backup e Restauração',
                'monitoring' => 'Monitoramento'
            ],
            'Suporte' => [
                'troubleshooting' => 'Resolução de Problemas'
            ]
        ];
    }

    /**
     * Get API endpoints.
     */
    private function getApiEndpoints()
    {
        return [
            'Authentication' => [
                [
                    'method' => 'POST',
                    'endpoint' => '/api/auth/login',
                    'description' => 'Autenticar usuário',
                    'parameters' => [
                        'email' => 'string|required',
                        'password' => 'string|required'
                    ],
                    'response' => [
                        'token' => 'string',
                        'user' => 'object',
                        'expires_at' => 'datetime'
                    ]
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/auth/logout',
                    'description' => 'Fazer logout',
                    'parameters' => [],
                    'response' => [
                        'message' => 'string'
                    ]
                ]
            ],
            'Plans' => [
                [
                    'method' => 'GET',
                    'endpoint' => '/api/plans',
                    'description' => 'Listar planos disponíveis',
                    'parameters' => [
                        'active' => 'boolean|optional'
                    ],
                    'response' => [
                        'data' => 'array',
                        'meta' => 'object'
                    ]
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/plans/{id}',
                    'description' => 'Obter detalhes de um plano',
                    'parameters' => [],
                    'response' => [
                        'data' => 'object'
                    ]
                ]
            ],
            'Companies' => [
                [
                    'method' => 'GET',
                    'endpoint' => '/api/companies',
                    'description' => 'Listar empresas',
                    'parameters' => [
                        'page' => 'integer|optional',
                        'per_page' => 'integer|optional'
                    ],
                    'response' => [
                        'data' => 'array',
                        'meta' => 'object'
                    ]
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/companies',
                    'description' => 'Criar nova empresa',
                    'parameters' => [
                        'name' => 'string|required',
                        'email' => 'string|required',
                        'plan_id' => 'integer|required'
                    ],
                    'response' => [
                        'data' => 'object'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get API authentication info.
     */
    private function getApiAuthentication()
    {
        return [
            'type' => 'Bearer Token',
            'description' => 'Use o token JWT obtido no endpoint de login',
            'header' => 'Authorization: Bearer {token}',
            'example' => 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...'
        ];
    }

    /**
     * Get API examples.
     */
    private function getApiExamples()
    {
        return [
            'login' => [
                'title' => 'Login de Usuário',
                'request' => [
                    'method' => 'POST',
                    'url' => '/api/auth/login',
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ],
                    'body' => [
                        'email' => 'admin@exemplo.com',
                        'password' => 'password123'
                    ]
                ],
                'response' => [
                    'status' => 200,
                    'body' => [
                        'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...',
                        'user' => [
                            'id' => 1,
                            'name' => 'Administrador',
                            'email' => 'admin@exemplo.com'
                        ],
                        'expires_at' => '2024-12-31T23:59:59Z'
                    ]
                ]
            ],
            'list_plans' => [
                'title' => 'Listar Planos',
                'request' => [
                    'method' => 'GET',
                    'url' => '/api/plans?active=true',
                    'headers' => [
                        'Authorization' => 'Bearer {token}',
                        'Accept' => 'application/json'
                    ]
                ],
                'response' => [
                    'status' => 200,
                    'body' => [
                        'data' => [
                            [
                                'id' => 1,
                                'name' => 'Plano Básico',
                                'price' => 0,
                                'billing_cycle' => 'monthly'
                            ]
                        ],
                        'meta' => [
                            'total' => 3,
                            'per_page' => 15
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get changelog versions.
     */
    private function getChangelogVersions()
    {
        return [
            [
                'version' => '2.1.0',
                'date' => '2024-08-11',
                'type' => 'major',
                'changes' => [
                    'added' => [
                        'Sistema completo de planos de subscrição',
                        'Dashboard de monitoramento em tempo real',
                        'Sistema de logs avançado',
                        'API REST completa'
                    ],
                    'improved' => [
                        'Interface de usuário redesenhada',
                        'Performance do sistema otimizada',
                        'Segurança aprimorada'
                    ],
                    'fixed' => [
                        'Correção de bugs no sistema de faturação',
                        'Problemas de sincronização resolvidos'
                    ]
                ]
            ],
            [
                'version' => '2.0.5',
                'date' => '2024-07-28',
                'type' => 'patch',
                'changes' => [
                    'fixed' => [
                        'Correção de vulnerabilidade de segurança',
                        'Bug no envio de emails corrigido',
                        'Problemas de cache resolvidos'
                    ]
                ]
            ],
            [
                'version' => '2.0.4',
                'date' => '2024-07-15',
                'type' => 'patch',
                'changes' => [
                    'improved' => [
                        'Otimização do banco de dados',
                        'Melhoria na velocidade de carregamento'
                    ],
                    'fixed' => [
                        'Correções menores na interface',
                        'Problemas de compatibilidade resolvidos'
                    ]
                ]
            ],
            [
                'version' => '2.0.0',
                'date' => '2024-06-01',
                'type' => 'major',
                'changes' => [
                    'added' => [
                        'Nova arquitetura do sistema',
                        'Suporte multi-empresa',
                        'Sistema de configurações avançado'
                    ],
                    'changed' => [
                        'Interface completamente redesenhada',
                        'Migração para Laravel 11'
                    ],
                    'removed' => [
                        'Funcionalidades legadas removidas'
                    ]
                ]
            ]
        ];
    }

    // Content methods for different documentation sections
    private function getGettingStartedContent()
    {
        return "
# Bem-vindo ao SFS - Sistema de Faturação e Subscrição

O SFS é uma plataforma completa para gestão de faturação e subscrições, desenvolvida especificamente para o mercado moçambicano.

## Funcionalidades Principais

### 📊 Dashboard Administrativo
- Visão geral do sistema em tempo real
- Métricas de performance e uso
- Alertas e notificações importantes

### 👥 Gestão de Usuários
- Criação e edição de usuários
- Controle de permissões e roles
- Histórico de atividades

### 📋 Gestão de Planos
- Criação de planos flexíveis
- Configuração de limitações
- Preços em MZN e outras moedas

### ⚙️ Configurações
- Configurações de sistema
- Configurações de faturação
- Configurações de email
- Configurações de backup
- Configurações de segurança

## Primeiros Passos

1. **Acesse o painel administrativo** em `/admin`
2. **Configure as configurações básicas** em Configurações > Sistema
3. **Crie os planos** em Gestão > Planos
4. **Configure a faturação** em Configurações > Faturação
5. **Teste o sistema** criando uma empresa de teste

## Suporte

Para suporte técnico, consulte a seção de Resolução de Problemas ou entre em contato com nossa equipe.
        ";
    }

    private function getUserManagementContent()
    {
        return "
# Gestão de Usuários

## Criando Usuários

Para criar um novo usuário:

1. Acesse **Gestão > Usuários > Novo Usuário**
2. Preencha os dados obrigatórios
3. Defina o role (Usuário, Admin, Gerente)
4. Configure o status (Ativo/Inativo)
5. Salve as alterações

## Roles e Permissões

### Administrador
- Acesso total ao sistema
- Pode criar, editar e excluir qualquer dados
- Acesso às configurações do sistema

### Gerente
- Pode gerenciar usuários da sua empresa
- Acesso limitado às configurações
- Pode visualizar relatórios

### Usuário
- Acesso básico ao sistema
- Pode apenas visualizar dados da sua empresa
- Não pode alterar configurações

## Autenticação de Dois Fatores

Para maior segurança, recomenda-se ativar a autenticação de dois fatores:

1. Acesse **Configurações > Segurança**
2. Ative a opção '2FA Enabled'
3. Configure os métodos permitidos
4. Defina se é obrigatório para administradores
        ";
    }

    private function getPlansManagementContent()
    {
        return "
# Gestão de Planos

## Criando um Plano

### Informações Básicas
- **Nome**: Nome do plano (ex: Básico, Profissional)
- **Descrição**: Descrição detalhada das funcionalidades
- **Preço**: Valor em MZN ou outra moeda
- **Ciclo**: Mensal, Trimestral ou Anual

### Limitações
Configure os limites do plano:
- Máximo de usuários
- Máximo de empresas
- Faturas por mês
- Máximo de clientes
- Máximo de produtos
- Armazenamento (MB)

### Funcionalidades
Selecione as funcionalidades incluídas:
- Faturação básica
- Gestão de clientes
- Relatórios avançados
- Backup automático
- API access
- Suporte prioritário

### Período de Teste
- Ative o período de teste gratuito
- Defina a duração em dias

## Gerenciando Planos

### Ativação/Desativação
Use o toggle para ativar ou desativar planos sem excluí-los.

### Plano Popular
Marque um plano como 'Popular' para destacá-lo na listagem.

### Duplicação
Use a função duplicar para criar variações de planos existentes.

## Boas Práticas

1. **Plano Gratuito**: Sempre tenha um plano gratuito para teste
2. **Limitações Graduais**: Aumente as limitações progressivamente
3. **Preços Competitivos**: Pesquise o mercado antes de definir preços
4. **Funcionalidades Claras**: Seja específico sobre o que cada plano inclui
        ";
    }

    private function getBillingSettingsContent()
    {
        return "
# Configurações de Faturação

## Configurações de Moeda

### Moeda Padrão
- **MZN**: Metical Moçambicano (recomendado)
- **USD**: Dólar Americano
- **EUR**: Euro
- **ZAR**: Rand Sul-Africano

### Formatação
- **Símbolo**: MT para MZN
- **Posição**: Antes ou depois do valor
- **Casas Decimais**: Normalmente 2
- **Separador de Milhares**: Vírgula (,)

## Configurações de Impostos

### IVA (Imposto sobre Valor Acrescentado)
- **Taxa Padrão**: 16% em Moçambique
- **NUIT**: Número de identificação fiscal da empresa
- **Preços Incluem IVA**: Se os preços já incluem o imposto

## Configurações de Fatura

### Numeração
- **Prefixo**: FAT (recomendado)
- **Número Inicial**: 1
- **Prazo de Pagamento**: 30 dias (padrão)

### Métodos de Pagamento
- **Dinheiro**: Pagamento em dinheiro
- **Transferência Bancária**: Para conta bancária
- **M-Pesa**: Pagamento móvel
- **e-Mola**: Carteira digital
- **Cheque**: Pagamento por cheque

## Dados Bancários

Configure suas informações bancárias para transferências:
- Nome do banco
- Número da conta
- IBAN (se aplicável)
- Código SWIFT (para transferências internacionais)

## Exemplo de Configuração

```
Moeda: MZN
Símbolo: MT
Posição: Depois (100,00 MT)
IVA: 16%
Prefixo: FAT
Métodos: Dinheiro, M-Pesa, Transferência
```
        ";
    }

    private function getSystemSettingsContent()
    {
        return "
# Configurações do Sistema

## Configurações Gerais

### Informações da Aplicação
- **Nome**: Nome do seu sistema
- **Descrição**: Breve descrição
- **Logo**: Upload do logotipo
- **Favicon**: Ícone do navegador

### Localização
- **Fuso Horário**: Africa/Maputo (recomendado)
- **Idioma**: Português
- **Formato de Data**: dd/mm/yyyy

## Configurações de Usuário

### Registro
- **Permitir Registro**: Habilitar/desabilitar novos registros
- **Verificação de Email**: Exigir verificação de email
- **Máximo de Usuários por Empresa**: Limite padrão

### Sessão
- **Tempo de Sessão**: Duração da sessão em minutos
- **Logout Automático**: Tempo de inatividade

## Modo de Manutenção

### Ativação
Use o modo de manutenção para:
- Realizar atualizações
- Manutenção do sistema
- Correções críticas

### Configuração
- **Mensagem**: Mensagem exibida aos usuários
- **Acesso Admin**: Administradores ainda podem acessar

## Otimização do Sistema

### Cache
- **Limpar Cache**: Remove cache de configurações
- **Cache de Views**: Acelera carregamento de páginas
- **Cache de Rotas**: Otimiza roteamento

### Otimização
- **Otimizar Sistema**: Executa comandos de otimização
- **Compilar Assets**: Compila CSS e JavaScript

## Informações do Sistema

Visualize informações importantes:
- Versão do PHP
- Versão do Laravel
- Servidor web
- Limite de memória
- Espaço em disco
        ";
    }

    private function getSecurityContent()
    {
        return "
# Configurações de Segurança

## Políticas de Senha

### Requisitos Mínimos
- **Comprimento**: Mínimo 8 caracteres
- **Letras Maiúsculas**: Pelo menos uma
- **Letras Minúsculas**: Pelo menos uma
- **Números**: Pelo menos um
- **Símbolos**: Recomendado

### Expiração
- **Dias para Expirar**: 0 = nunca expira
- **Prevenir Reutilização**: Últimas 5 senhas

## Autenticação de Dois Fatores (2FA)

### Métodos Disponíveis
- **App Autenticador**: Google Authenticator, Authy
- **SMS**: Código via mensagem
- **Email**: Código via email

### Configuração
- **Obrigatório para Admins**: Recomendado
- **Período de Recuperação**: Códigos de backup

## Proteção contra Ataques

### Limitação de Tentativas
- **Máximo de Tentativas**: 5 (recomendado)
- **Duração do Bloqueio**: 15 minutos
- **Limitação de Taxa**: Por IP

### Lista Branca de IPs
- **Apenas para Admins**: Restringir acesso admin
- **IPs Permitidos**: Lista de IPs confiáveis
- **Formato CIDR**: Suporte a ranges (192.168.1.0/24)

## Logs de Auditoria

### Eventos Registrados
- **Logins Bem-sucedidos**: Todos os acessos
- **Tentativas Falhadas**: Tentativas de invasão
- **Mudanças de Senha**: Alterações de credenciais
- **Ações Administrativas**: Todas as ações de admin
- **Exportação de Dados**: Downloads e exports
- **Mudanças de Permissão**: Alterações de roles

### Retenção
- **Período**: 90 dias (recomendado)
- **Arquivamento**: Para logs antigos

## Recomendações de Segurança

### Básicas
1. **Senhas Fortes**: Use senhas complexas
2. **2FA**: Ative para todos os administradores
3. **Atualizações**: Mantenha o sistema atualizado
4. **Backups**: Faça backups regulares

### Avançadas
1. **HTTPS**: Use sempre conexões seguras
2. **Firewall**: Configure firewall no servidor
3. **Monitoramento**: Monitore logs regularmente
4. **Acesso Limitado**: Princípio do menor privilégio

### Checklist de Segurança
- [ ] 2FA ativado para admins
- [ ] Políticas de senha configuradas
- [ ] Logs de auditoria ativados
- [ ] Backups automáticos funcionando
- [ ] Sistema atualizado
- [ ] HTTPS configurado
- [ ] Firewall ativo
        ";
    }

    private function getBackupRestoreContent()
    {
        return "
# Backup e Restauração

## Configuração de Backup Automático

### Frequência
- **Diário**: Recomendado para dados críticos
- **Semanal**: Para sistemas com baixa atividade
- **Mensal**: Para dados históricos

### Horário
- **Madrugada**: 02:00 (menor tráfego)
- **Evitar Horários de Pico**: Durante horário comercial

### Retenção
- **30 dias**: Padrão recomendado
- **Máximo de Backups**: 10 arquivos

## Conteúdo do Backup

### Base de Dados
- **Todas as Tabelas**: Dados completos
- **Estrutura**: Schema da base de dados
- **Índices**: Para performance

### Arquivos
- **Uploads de Usuário**: Documentos e imagens
- **Configurações**: Arquivos de configuração
- **Logs**: Opcionalmente incluir

## Locais de Armazenamento

### Local
- **Servidor**: Armazenamento no próprio servidor
- **Prós**: Rápido e simples
- **Contras**: Risco se servidor falhar

### FTP
- **Servidor Remoto**: Backup em servidor externo
- **Configuração**: Host, usuário, senha, caminho
- **Prós**: Backup off-site
- **Contras**: Depende de conectividade

### Cloud
- **Google Drive**: Integração direta
- **Dropbox**: Sincronização automática
- **Prós**: Altamente disponível
- **Contras**: Pode ter custos

## Processo de Restauração

### Antes de Restaurar
1. **Parar o Sistema**: Modo manutenção
2. **Backup Atual**: Fazer backup antes da restauração
3. **Verificar Integridade**: Do arquivo de backup

### Passos da Restauração
1. **Selecionar Backup**: Escolher arquivo correto
2. **Confirmar Ação**: Processo irreversível
3. **Aguardar Conclusão**: Pode demorar alguns minutos
4. **Verificar Sistema**: Testar funcionalidades

### Após Restauração
1. **Testar Funcionalidades**: Verificar se tudo funciona
2. **Verificar Dados**: Conferir integridade dos dados
3. **Reativar Sistema**: Sair do modo manutenção

## Monitoramento de Backup

### Notificações
- **Sucesso**: Confirmar backups bem-sucedidos
- **Falha**: Alertas imediatos sobre problemas
- **Emails**: Para administradores

### Verificações
- **Integridade**: Verificar se arquivos não estão corrompidos
- **Tamanho**: Monitorar tamanho dos backups
-
