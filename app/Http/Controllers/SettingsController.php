<?php

namespace App\Http\Controllers;

use App\Models\BillingSetting;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Página principal de configurações
     */
    public function index()
    {
        $settings = BillingSetting::getSettings();

        return view('settings.index', compact('settings'));
    }

    /**
     * Configurações da empresa
     */
    public function company()
    {
        $settings = BillingSetting::getSettings();

        return view('settings.company', compact('settings'));
    }

    /**
     * Atualizar configurações da empresa
     */
    public function updateCompany(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:500',
            'tax_number' => 'nullable|string|max:50',
            'company_phone' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
            'company_website' => 'nullable|url|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $validated['company_id'] = auth()->user()->company->id;
        $settings = BillingSetting::getSettings();

        $data = $request->only([
            'company_name',
            'company_address',
            'tax_number',
            'company_phone',
            'company_email',
            'company_website'
        ]);

        // Upload do logo se fornecido
        if ($request->hasFile('company_logo')) {
            $logoPath = $request->file('company_logo')->store('logos', 'public');
            $data['company_logo'] = $logoPath;
        }

        $settings->update($data);

        return back()->with('success', 'Configurações da empresa atualizadas com sucesso!');
    }

    /**
     * Configurações de faturamento
     */
    public function billing()
    {
        $settings = BillingSetting::getSettings();

        return view('settings.billing', compact('settings'));
    }

    /**
     * Atualizar configurações de faturamento
     */
    public function updateBilling(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_prefix' => 'required|string|max:10',
            'quote_prefix' => 'required|string|max:10',
            'next_invoice_number' => 'required|integer|min:1',
            'next_quote_number' => 'required|integer|min:1',
            'default_tax_rate' => 'required|numeric|min:0|max:100',
            'default_payment_terms' => 'nullable|integer|min:1',
            'late_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'currency' => 'required|string|max:3',
            'number_format' => 'required|in:dot,comma'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $validated['company_id'] = auth()->user()->company->id;
        $settings = BillingSetting::getSettings();

        $settings->update($request->only([
            'invoice_prefix',
            'quote_prefix',
            'next_invoice_number',
            'next_quote_number',
            'default_tax_rate',
            'default_payment_terms',
            'late_fee_percentage',
            'currency',
            'number_format'
        ]));

        return back()->with('success', 'Configurações de faturamento atualizadas com sucesso!');
    }

    /**
     * Configurações de impostos
     */
    public function taxes()
    {
        $settings = BillingSetting::getSettings();

        return view('settings.taxes', compact('settings'));
    }

    /**
     * Atualizar configurações de impostos
     */
    public function updateTaxes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'default_tax_rate' => 'required|numeric|min:0|max:100',
            'tax_name' => 'nullable|string|max:50',
            'tax_registration' => 'nullable|string|max:100',
            'include_tax_in_price' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $settings = BillingSetting::getSettings();

        $settings->update($request->only([
            'default_tax_rate',
            'tax_name',
            'tax_registration',
            'include_tax_in_price'
        ]));

        return back()->with('success', 'Configurações de impostos atualizadas com sucesso!');
    }

    /**
     * Configurações de notificações
     */
    public function notifications()
    {
        $settings = BillingSetting::getSettings();

        return view('settings.notifications', compact('settings'));
    }

    /**
     * Atualizar configurações de notificações
     */
    public function updateNotifications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'send_invoice_emails' => 'boolean',
            'send_quote_emails' => 'boolean',
            'send_overdue_reminders' => 'boolean',
            'reminder_days' => 'nullable|integer|min:1|max:90',
            'email_template_invoice' => 'nullable|string',
            'email_template_quote' => 'nullable|string',
            'email_template_reminder' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $settings = BillingSetting::getSettings();

        $settings->update($request->only([
            'send_invoice_emails',
            'send_quote_emails',
            'send_overdue_reminders',
            'reminder_days',
            'email_template_invoice',
            'email_template_quote',
            'email_template_reminder'
        ]));

        return back()->with('success', 'Configurações de notificações atualizadas com sucesso!');
    }

    /**
     * Página de produtos
     */
    public function products()
    {
        $products = Product::orderBy('name')->paginate(20);

        return view('settings.products', compact('products'));
    }

    /**
     * Página de serviços
     */
    public function services()
    {
        $services = Service::orderBy('name')->paginate(20);

        return view('settings.services', compact('services'));
    }

    /**
     * Reset configurações para padrão
     */
    public function reset()
    {
        $settings = BillingSetting::getSettings();

        $settings->update([
            'company_name' => 'Sua Empresa',
            'company_address' => 'Endereço da empresa',
            'invoice_prefix' => 'FAT',
            'quote_prefix' => 'COT',
            'default_tax_rate' => 17.00,
            'currency' => 'MZN',
            'number_format' => 'comma'
        ]);

        return back()->with('success', 'Configurações resetadas para o padrão!');
    }

    /**
     * Backup das configurações
     */
    public function backup()
    {
        $settings = BillingSetting::getSettings();

        $backup = [
            'billing_settings' => $settings->toArray(),
            'products' => Product::all()->toArray(),
            'services' => Service::all()->toArray(),
            'backup_date' => now()->format('Y-m-d H:i:s')
        ];

        $fileName = 'backup_configuracoes_' . now()->format('Y_m_d_H_i_s') . '.json';

        return response()->json($backup)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * API: Buscar produtos/serviços
    //  * APi
     */
    public function searchItems(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all'); // 'products', 'services', 'all'

        $results = collect();

        if ($type === 'products' || $type === 'all') {
            $products = Product::where('name', 'LIKE', "%{$query}%")
                ->orWhere('code', 'LIKE', "%{$query}%")
                ->limit(10)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'type' => 'product',
                        'name' => $product->name,
                        'code' => $product->code,
                        'price' => $product->price,
                        'description' => $product->description
                    ];
                });

            $results = $results->merge($products);
        }

        if ($type === 'services' || $type === 'all') {
            $services = Service::where('name', 'LIKE', "%{$query}%")
                ->orWhere('code', 'LIKE', "%{$query}%")
                ->limit(10)
                ->get()
                ->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'type' => 'service',
                        'name' => $service->name,
                        'code' => $service->code,
                        'price' => $service->hourly_rate,
                        'description' => $service->description
                    ];
                });

            $results = $results->merge($services);
        }

        return response()->json($results->take(20));
    }

    /**
     * Exibir preview das configurações de email
     */
    public function emailPreview(Request $request)
    {
        try {
            // Verificar se é uma requisição AJAX para preview dinâmico
            if ($request->ajax()) {
                $template = $request->input('template', 'invoice');
                $sampleData = $this->getSampleDataForTemplate($template);

                $view = view("emails.templates.{$template}", $sampleData)->render();

                return response()->json([
                    'success' => true,
                    'html' => $view
                ]);
            }

            // Buscar configurações atuais de email
            $emailSettings = [
                'smtp_host' => config('mail.mailers.smtp.host'),
                'smtp_port' => config('mail.mailers.smtp.port'),
                'smtp_username' => config('mail.mailers.smtp.username'),
                'smtp_encryption' => config('mail.mailers.smtp.encryption'),
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
            ];

            // Templates disponíveis
            $templates = [
                'invoice' => 'Fatura',
                'quote' => 'Cotação',
                'credit_note' => 'Nota de Crédito',
                'debit_note' => 'Nota de Débito',
                'payment_reminder' => 'Lembrete de Pagamento',
                'welcome' => 'Boas-vindas'
            ];

            return view('admin.settings.email.preview', compact('emailSettings', 'templates'));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao carregar preview: ' . $e->getMessage()]);
        }
    }

    /**
     * Configurações de email do sistema
     */
    public function emailSettings()
    {
        $settings = [
            'smtp_host' => config('mail.mailers.smtp.host'),
            'smtp_port' => config('mail.mailers.smtp.port'),
            'smtp_username' => config('mail.mailers.smtp.username'),
            'smtp_encryption' => config('mail.mailers.smtp.encryption'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];

        return view('admin.settings.email.index', compact('settings'));
    }

    /**
     * Atualizar configurações de email
     */
    public function updateEmailSettings(Request $request)
    {
        $request->validate([
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer|between:1,65535',
            'smtp_username' => 'required|email|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'required|in:tls,ssl,null',
            'from_address' => 'required|email|max:255',
            'from_name' => 'required|string|max:255',
        ]);

        try {
            // Atualizar configurações no .env
            $this->updateEnvFile([
                'MAIL_HOST' => $request->smtp_host,
                'MAIL_PORT' => $request->smtp_port,
                'MAIL_USERNAME' => $request->smtp_username,
                'MAIL_PASSWORD' => $request->smtp_password ?: config('mail.mailers.smtp.password'),
                'MAIL_ENCRYPTION' => $request->smtp_encryption === 'null' ? null : $request->smtp_encryption,
                'MAIL_FROM_ADDRESS' => $request->from_address,
                'MAIL_FROM_NAME' => $request->from_name,
            ]);

            // Limpar cache de configuração
            \Artisan::call('config:clear');

            return redirect()->route('admin.settings.email.index')
                           ->with('success', 'Configurações de email atualizadas com sucesso!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao atualizar configurações: ' . $e->getMessage()]);
        }
    }

    /**
     * Enviar email de teste
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
            'template' => 'required|string'
        ]);

        try {
            $sampleData = $this->getSampleDataForTemplate($request->template);

            \Mail::send("emails.templates.{$request->template}", $sampleData, function ($message) use ($request) {
                $message->to($request->test_email)
                       ->subject('Email de Teste - ' . config('app.name'));
            });

            return response()->json([
                'success' => true,
                'message' => 'Email de teste enviado com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar email: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Gerar dados de exemplo para templates
     */
    private function getSampleDataForTemplate($template)
    {
        switch ($template) {
            case 'invoice':
                return [
                    'invoice' => (object) [
                        'invoice_number' => 'FAT-2025-001',
                        'total' => 1500.00,
                        'due_date' => now()->addDays(30)->format('d/m/Y'),
                        'client' => (object) [
                            'name' => 'Empresa Exemplo Lda',
                            'email' => 'exemplo@empresa.com'
                        ]
                    ]
                ];

            case 'quote':
                return [
                    'quote' => (object) [
                        'quote_number' => 'COT-2025-001',
                        'total' => 2500.00,
                        'valid_until' => now()->addDays(15)->format('d/m/Y'),
                        'client' => (object) [
                            'name' => 'Cliente Exemplo',
                            'email' => 'cliente@exemplo.com'
                        ]
                    ]
                ];

            default:
                return [
                    'company' => config('app.name'),
                    'user' => (object) [
                        'name' => 'Usuário Exemplo',
                        'email' => 'usuario@exemplo.com'
                    ]
                ];
        }
    }

    /**
     * Atualizar arquivo .env
     */
    private function updateEnvFile($data)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            $value = is_null($value) ? '' : $value;
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        file_put_contents($envFile, $envContent);
    }
}
