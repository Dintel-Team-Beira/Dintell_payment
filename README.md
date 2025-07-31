# SUB360 - Sistema Integrado de Gestão Empresarial

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10.x-red?style=for-the-badge&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2+-blue?style=for-the-badge&logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-8.0+-orange?style=for-the-badge&logo=mysql" alt="MySQL">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-cyan?style=for-the-badge&logo=tailwindcss" alt="TailwindCSS">
  <img src="https://img.shields.io/badge/Status-Em%20Desenvolvimento-yellow?style=for-the-badge" alt="Status">
</p>

<p align="center">
  <strong>Sistema completo de faturação e gestão de subscrições desenvolvido pela Dintell</strong>
</p>

---

## 📋 Sobre o Projeto

O **SUB360** é um sistema de gestão empresarial robusto e completo, desenvolvido em Laravel, que integra funcionalidades de faturação, gestão de clientes, controle de subscrições e muito mais. Projetado para empresas que precisam de uma solução escalável e personalizável.

### 🎯 Objetivo

Fornecer uma plataforma unificada para gerenciamento completo de negócios, desde a criação de faturas até o controle de subscrições, oferecendo uma experiência moderna e intuitiva tanto para administradores quanto para clientes.

---

## ⚡ Funcionalidades Principais

### 🧾 Sistema de Faturação Avançado

- **Criação de Faturas Inteligente**
  - Interface drag-and-drop para seleção de produtos/serviços
  - Cálculo automático de impostos (IVA configurável)
  - Suporte a descontos comerciais (percentual ou valor fixo)
  - Múltiplos métodos de pagamento
  - Conversão automática de cotações em faturas

- **Tipos de Documentos**
  - Faturas tradicionais
  - Notas de crédito (devoluções/ajustes)
  - Notas de débito (cobranças adicionais)
  - Cotações com validade configurável

- **Vendas Rápidas**
  - Interface simplificada para vendas à dinheiro
  - Cálculo automático de troco
  - Marcação automática como paga

### 💳 Gestão de Subscrições

- **Planos Flexíveis**
  - Ciclos de cobrança customizáveis (mensal, trimestral, anual)
  - Recursos configuráveis por plano
  - Preços diferenciados por região
  - Períodos de trial configuráveis

- **Automação de Cobrança**
  - Renovação automática de subscrições
  - Notificações de vencimento
  - Suspensão automática por inadimplência
  - Relatórios de receita recorrente

### 👥 Gestão de Clientes

- **Perfis Completos**
  - Informações detalhadas de contato
  - Histórico de transações
  - Documentos anexados
  - Notas internas

- **Comunicação Integrada**
  - Sistema de notificações por email
  - Templates personalizáveis
  - Histórico de comunicações

### 📊 Relatórios e Analytics

- **Dashboard Executivo**
  - Métricas em tempo real
  - Gráficos interativos
  - KPIs personalizados
  - Comparações periódicas

- **Relatórios Financeiros**
  - Fluxo de caixa
  - Contas a receber
  - Análise de receita recorrente
  - Exportação para Excel/PDF

### 🛍️ Catálogo de Produtos/Serviços

- **Gestão de Produtos**
  - Controle de estoque
  - Categorização hierárquica
  - Preços variáveis por cliente
  - Imagens e descrições detalhadas

- **Serviços Profissionais**
  - Preços por hora ou valor fixo
  - Níveis de complexidade
  - Requisitos e entregáveis
  - Estimativas de tempo

### 🔐 Segurança e Compliance

- **Autenticação Robusta**
  - Sistema de roles e permissões
  - Autenticação de dois fatores (2FA)
  - Logs de auditoria
  - Sessões seguras

- **Conformidade Fiscal**
  - Numeração sequencial de faturas
  - Cálculos fiscais automáticos
  - Relatórios para autoridades fiscais
  - Backup automático de dados

---

## 🛠️ Tecnologias Utilizadas

### Backend
- **Laravel 10.x** - Framework PHP robusto e moderno
- **MySQL 8.0+** - Banco de dados relacional
- **PHP 8.2+** - Linguagem de programação
- **Redis** - Cache e sessões (opcional)

### Frontend
- **TailwindCSS** - Framework CSS utilitário
- **Alpine.js** - Framework JavaScript reativo
- **Chart.js** - Gráficos e visualizações
- **Select2** - Componentes avançados de seleção

### Ferramentas de Desenvolvimento
- **Laravel Sail** - Ambiente de desenvolvimento Docker
- **Laravel Mix** - Compilação de assets
- **PHPUnit** - Testes automatizados
- **Laravel Telescope** - Debug e profiling

---

## 📦 Instalação

### Pré-requisitos

```bash
PHP >= 8.2
Composer
Node.js >= 16
MySQL >= 8.0
```

### 1. Clone o Repositório

```bash
git clone https://github.com/dintell/subn360.git
cd subn360
```

### 2. Instale as Dependências

```bash
# Dependências PHP
composer install

# Dependências Node.js
npm install
```

### 3. Configuração do Ambiente

```bash
# Copie o arquivo de ambiente
cp .env.example .env

# Gere a chave da aplicação
php artisan key:generate
```

### 4. Configure o Banco de Dados

Edite o arquivo `.env` com as credenciais do banco:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=subn360
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 5. Execute as Migrações

```bash
# Execute as migrações
php artisan migrate

# Execute os seeders (dados de exemplo)
php artisan db:seed
```

### 6. Compile os Assets

```bash
# Para desenvolvimento
npm run dev

# Para produção
npm run build
```

### 7. Inicie o Servidor

```bash
php artisan serve
```

Acesse: `http://localhost:8000`

---

## 🚀 Deployment

### Usando Laravel Sail (Docker)

```bash
# Inicie os containers
./vendor/bin/sail up -d

# Execute as migrações
./vendor/bin/sail artisan migrate --seed
```

### Deployment em Produção

1. **Configuração do Servidor**
   - Apache/Nginx configurado
   - PHP 8.2+ com extensões necessárias
   - MySQL/PostgreSQL configurado

2. **Variáveis de Ambiente**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://seudominio.com
   ```

3. **Otimizações**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan queue:restart
   ```

---

## 📚 Documentação da API

### Endpoints Principais

#### Faturas
```http
GET /api/invoices - Lista faturas
POST /api/invoices - Cria nova fatura
GET /api/invoices/{id} - Detalhes da fatura
PUT /api/invoices/{id} - Atualiza fatura
DELETE /api/invoices/{id} - Remove fatura
```

#### Clientes
```http
GET /api/clients - Lista clientes
POST /api/clients - Cria novo cliente
GET /api/clients/{id} - Detalhes do cliente
PUT /api/clients/{id} - Atualiza cliente
```

#### Produtos/Serviços
```http
GET /api/products - Lista produtos
GET /api/services - Lista serviços
GET /api/products/active - Produtos ativos
GET /api/services/active - Serviços ativos
```

### Autenticação

O sistema utiliza Laravel Sanctum para autenticação da API:

```http
POST /api/login
Headers: Content-Type: application/json
Body: {
  "email": "user@example.com",
  "password": "password"
}
```

---

## 🧪 Testes

### Executar Testes

```bash
# Todos os testes
php artisan test

# Testes específicos
php artisan test --filter=InvoiceTest

# Testes com cobertura
php artisan test --coverage
```

### Estrutura de Testes

```
tests/
├── Feature/
│   ├── InvoiceManagementTest.php
│   ├── ClientManagementTest.php
│   └── SubscriptionTest.php
└── Unit/
    ├── Models/
    ├── Services/
    └── Helpers/
```

---

## 🤝 Contribuição

### Como Contribuir

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

### Padrões de Código

- Seguir PSR-12 para PHP
- Usar convenções do Laravel
- Documentar métodos complexos
- Escrever testes para novas funcionalidades

### Issues e Bugs

Para reportar bugs ou solicitar funcionalidades, utilize o sistema de Issues do GitHub com as seguintes labels:

- `bug` - Para bugs confirmados
- `feature` - Para novas funcionalidades
- `enhancement` - Para melhorias
- `documentation` - Para melhorias na documentação

---

## 📈 Roadmap

### Versão 1.2 (Em Desenvolvimento)
- [ ] API REST completa
- [ ] App mobile (React Native)
- [ ] Integração com gateways de pagamento
- [ ] Sistema de aprovações workflow

### Versão 1.3 (Planejado)
- [ ] Inteligência artificial para previsões
- [ ] Integração com contabilidade
- [ ] Multi-idioma
- [ ] Modo offline

### Versão 2.0 (Futuro)
- [ ] Arquitetura microserviços
- [ ] Suporte multi-tenant
- [ ] Marketplace de plugins
- [ ] PWA completo

---

## 📄 Licença

Este projeto está licenciado sob a Licença MIT. Veja o arquivo [LICENSE](LICENSE) para detalhes.

---

## 👨‍💻 Equipe de Desenvolvimento

### Dintell - Soluções Tecnológicas

**Team Leader & Full Stack Developer**
- **Arnaldo Tomo** - *Desenvolvedor Full Stack especializado em Laravel e React Native*
  - 📧 Email: arnaldo@dintell.co.mz
  - 🐙 GitHub: [@arnaldotomo](https://github.com/arnaldotomo)
  - 💼 LinkedIn: [Arnaldo Tomo](https://linkedin.com/in/arnaldotomo)

### Especializações da Equipe
- **Backend Development**: Laravel, PHP, MySQL, API REST
- **Frontend Development**: TailwindCSS, Alpine.js, React Native
- **DevOps**: Docker, CI/CD, Cloud Deployment
- **UX/UI Design**: Figma, Adobe Creative Suite
- **Project Management**: Agile, Scrum, Kanban

---

## 🌟 Agradecimentos

- **Laravel Community** - Pela framework extraordinária
- **TailwindCSS Team** - Pelo framework CSS utilitário
- **Clientes e Beta Testers** - Pelo feedback valioso
- **Open Source Community** - Pelas bibliotecas utilizadas

---

## 📞 Suporte

### Documentação Adicional
- [Wiki do Projeto](https://github.com/dintell/subn360/wiki)
- [FAQ](https://github.com/dintell/subn360/wiki/FAQ)
- [Troubleshooting](https://github.com/dintell/subn360/wiki/Troubleshooting)

### Contato
- 📧 **Email**: suporte@dintell.co.mz
- 🌐 **Website**: [https://dintell.co.mz](https://dintell.co.mz)


---

<p align="center">
  <strong>Desenvolvido com ❤️ pela equipe Dintell</strong>
</p>

<p align="center">
  <i>"Transformando ideias em soluções tecnológicas que fazem a diferença"</i>
</p>
