<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    /**
     * Lista de serviços
     */
    public function index(Request $request)
    {
        $query = Service::query();

        // Filtros
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        if ($request->filled('complexity')) {
            $query->where('complexity_level', $request->get('complexity'));
        }

        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('price_min')) {
            $query->where(function($q) use ($request) {
                $q->where('hourly_rate', '>=', $request->get('price_min'))
                  ->orWhere('fixed_price', '>=', $request->get('price_min'));
            });
        }

        if ($request->filled('price_max')) {
            $query->where(function($q) use ($request) {
                $q->where('hourly_rate', '<=', $request->get('price_max'))
                  ->orWhere('fixed_price', '<=', $request->get('price_max'));
            });
        }

        $services = $query->orderBy('name')->paginate(20);
        $categories = Category::where('type', 'service')
                        ->orWhere('type', 'both')
                        ->orderBy('name')
                        ->get();

        return view('services.index', compact('services', 'categories'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        $categories = Category::where('type', 'service')
                        ->orWhere('type', 'both')
                        ->orderBy('name')
                        ->get();
        return view('services.create',compact('categories'));
    }

    /**
     * Salvar serviço
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:services,code',
            'description' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'fixed_price' => 'nullable|numeric|min:0',
            // 'category' => 'required|string|max:50',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'estimated_hours' => 'nullable|numeric|min:0',
            'complexity_level' => 'required|in:baixa,media,alta',
            // 'requirements' => 'nullable|array',
            'requirements' => 'nullable|string',
            'deliverables' => 'nullable|array',
            'is_active' => 'boolean',
            'category' => 'required|exists:categories,id',
        ]);
        $validated['company_id'] = auth()->user()->company->id;
        // Validação customizada: deve ter pelo menos hourly_rate ou fixed_price
        $validator->after(function ($validator) use ($request) {
            if (!$request->filled('hourly_rate') && !$request->filled('fixed_price')) {
                $validator->errors()->add('pricing', 'Deve ser informado pelo menos o preço por hora ou preço fixo.');
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['category_id'] = $request->get('category');
        unset($data['category']);
        // Gerar código se não fornecido
        if (empty($data['code'])) {
            $data['code'] = $this->generateCode($data['name']);
        }

        // Processar requirements e deliverables
        if ($request->filled('requirements_text')) {
            $data['requirements'] = array_filter(explode("\n", $request->get('requirements_text')));
        }

        if ($request->filled('deliverables_text')) {
            $data['deliverables'] = array_filter(explode("\n", $request->get('deliverables_text')));
        }

        $service = Service::create($data);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'service' => $service]);
        }

        return redirect()->route('services.index')
            ->with('success', 'Serviço criado com sucesso!');
    }

    /**
     * Visualizar serviço
     */
    public function show(string $tenant, Service $service)
    {
        return view('services.show', compact('service'));
    }

    /**
     * Formulário de edição
     */
    public function edit(string $tenant, Service $service)
    {
        // dd($service);
        $categories = Category::where('type', 'service')
                        ->orWhere('type', 'both')
                        ->orderBy('name')
                        ->get();
        return view('services.edit', compact('service','categories'));
    }

    /**
     * Atualizar serviço
     */
    public function update(Request $request, Service $service)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:services,code,' . $service->id,
            'description' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'fixed_price' => 'nullable|numeric|min:0',
            // 'category' => 'required|string|max:50',
            'category' => 'required|exists:categories,id',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'estimated_hours' => 'nullable|numeric|min:0',
            'complexity_level' => 'required|in:baixa,media,alta',
            'requirements' => 'nullable|string',
            'deliverables' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        $validated['company_id'] = auth()->user()->company->id;
        // Validação customizada: deve ter pelo menos hourly_rate ou fixed_price
        $validator->after(function ($validator) use ($request) {
            if (!$request->filled('hourly_rate') && !$request->filled('fixed_price')) {
                $validator->errors()->add('pricing', 'Deve ser informado pelo menos o preço por hora ou preço fixo.');
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['category_id'] = $request->get('category');

        // Processar requirements e deliverables
        if ($request->filled('requirements_text')) {
            $data['requirements'] = array_filter(explode("\n", $request->get('requirements_text')));
        }

        if ($request->filled('deliverables_text')) {
            $data['deliverables'] = array_filter(explode("\n", $request->get('deliverables_text')));
        }

        unset($data['category']);
        $service->update($data);
        $data['category_id'] = $request->get('category');
        return redirect()->route('services.index')
            ->with('success', 'Serviço atualizado com sucesso!');
    }

    /**
     * Excluir serviço
     */
    public function destroy(string $tenant,Service $service)
    {
        // Verificar se serviço está sendo usado em faturas/orçamentos
        if ($service->invoiceItems()->exists() || $service->quoteItems()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Serviço não pode ser excluído pois está sendo usado em faturas ou orçamentos.'
            ]);
        }

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Serviço excluído com sucesso!'
        ]);
    }

    /**
     * Duplicar serviço
     */
    public function duplicate(string $tenant, Service $service)
    {
        $newService = $service->replicate();
        $newService->name = $service->name . ' (Cópia)';
        $newService->code = $this->generateCode($newService->name);
        $newService->save();

        return redirect()->route('services.edit', $newService)
            ->with('success', 'Serviço duplicado com sucesso!');
    }

    /**
     * Alterar status do serviço
     */
    public function toggleStatus(Request $request, string $tenant, Service $service): JsonResponse
    {
        $service->update(['is_active' => $request->get('is_active', true)]);

        return response()->json([
            'success' => true,
            'message' => 'Status do serviço alterado com sucesso!'
        ]);
    }

    /**
     * Exportar serviços
     */
    public function export(Request $request)
    {
        $query = Service::query();

        // Aplicar mesmos filtros da listagem
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        if ($request->filled('complexity')) {
            $query->where('complexity_level', $request->get('complexity'));
        }

        $services = $query->get();

        $fileName = 'servicos_' . now()->format('Y_m_d_H_i_s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($services) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [
                'Código', 'Nome', 'Descrição', 'Categoria', 'Preço/Hora',
                'Preço Fixo', 'Horas Estimadas', 'Complexidade', 'Status'
            ]);

            // Data
            foreach ($services as $service) {
                fputcsv($file, [
                    $service->code,
                    $service->name,
                    $service->description,
                    $service->category,
                    $service->hourly_rate,
                    $service->fixed_price,
                    $service->estimated_hours,
                    $service->complexity_level,
                    $service->is_active ? 'Ativo' : 'Inativo'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API: Buscar serviços
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        $services = Service::active()
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('code', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'type' => 'service',
                    'name' => $service->name,
                    'code' => $service->code,
                    'hourly_rate' => $service->hourly_rate,
                    'fixed_price' => $service->fixed_price,
                    'estimated_cost' => $service->estimated_cost,
                    'description' => $service->description,
                    'estimated_hours' => $service->estimated_hours,
                    'complexity_level' => $service->complexity_level
                ];
            });

        return response()->json($services);
    }

    /**
     * API: Estatísticas de serviços
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => Service::count(),
            'active' => Service::active()->count(),
            'inactive' => Service::where('is_active', false)->count(),
            'by_category' => Service::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category'),
            'by_complexity' => Service::selectRaw('complexity_level, COUNT(*) as count')
                ->groupBy('complexity_level')
                ->pluck('count', 'complexity_level'),
            'avg_hourly_rate' => Service::whereNotNull('hourly_rate')->avg('hourly_rate'),
            'avg_fixed_price' => Service::whereNotNull('fixed_price')->avg('fixed_price'),
            'total_estimated_hours' => Service::sum('estimated_hours')
        ];

        return response()->json($stats);
    }

    /**
     * API: Calcular preço do serviço
     */
    public function calculatePrice(Request $request, Service $service): JsonResponse
    {
        $hours = $request->get('hours', $service->estimated_hours);
        $price = $service->calculatePrice($hours);

        return response()->json([
            'price' => $price,
            'hours' => $hours,
            'hourly_rate' => $service->hourly_rate,
            'fixed_price' => $service->fixed_price,
            'pricing_type' => $service->fixed_price > 0 ? 'fixed' : 'hourly'
        ]);
    }

    /**
     * API: Obter templates de requisitos e entregáveis
     */
    public function getTemplates(Request $request): JsonResponse
    {
        $category = $request->get('category');

        $templates = [
            'desenvolvimento' => [
                'requirements' => [
                    'Reunião de levantamento de requisitos',
                    'Definição de escopo e funcionalidades',
                    'Aprovação do protótipo/wireframes',
                    'Acesso ao ambiente de desenvolvimento',
                    'Definição de prazos e marcos',
                    'Aprovação em cada etapa de desenvolvimento'
                ],
                'deliverables' => [
                    'Código fonte completo',
                    'Documentação técnica',
                    'Manual do usuário',
                    'Testes e validação',
                    'Deploy em ambiente de produção',
                    'Treinamento da equipe',
                    'Suporte pós-entrega (30 dias)'
                ]
            ],
            'design' => [
                'requirements' => [
                    'Briefing detalhado do projeto',
                    'Referências visuais e identidade da marca',
                    'Definição de público-alvo',
                    'Aprovação de wireframes',
                    'Feedback em até 2 dias úteis'
                ],
                'deliverables' => [
                    'Wireframes/Protótipos',
                    'Layouts finais em alta resolução',
                    'Guia de estilo/Style Guide',
                    'Assets exportados para desenvolvimento',
                    'Revisões incluídas (até 3 rodadas)'
                ]
            ],
            'consultoria' => [
                'requirements' => [
                    'Reunião inicial para diagnóstico',
                    'Acesso aos dados/sistemas necessários',
                    'Disponibilidade da equipe chave',
                    'Definição de objetivos e KPIs'
                ],
                'deliverables' => [
                    'Relatório de diagnóstico',
                    'Plano de ação detalhado',
                    'Apresentação executiva',
                    'Acompanhamento de implementação',
                    'Relatório final com resultados'
                ]
            ]
        ];

        return response()->json($templates[$category] ?? Service::getDefaultRequirements());
    }

    /**
     * Gerar código único para serviço
     */
    private function generateCode($name): string
    {
        $words = explode(' ', $name);
        $code = 'SERV';

        if (count($words) >= 2) {
            $code = strtoupper(substr($words[0], 0, 3) . substr($words[1], 0, 3));
        } else {
            $code = strtoupper(substr($words[0], 0, 6));
        }

        // Adicionar número sequencial para garantir unicidade
        $counter = 1;
        $baseCode = $code;

        while (Service::where('code', $code)->exists()) {
            $code = $baseCode . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $code;
    }

    /**
 * Duplicar serviço
 */

/**
 * Exportar dados do serviço específico
 */
public function exportSingle(Service $service)
{
    $fileName = 'servico_' . $service->code . '_' . now()->format('Y_m_d_H_i_s') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    ];

    $callback = function() use ($service) {
        $file = fopen('php://output', 'w');

        // Header
        fputcsv($file, [
            'Campo', 'Valor'
        ]);

        // Data
        $data = [
            ['Código', $service->code],
            ['Nome', $service->name],
            ['Descrição', $service->description],
            ['Categoria', $service->category],
            ['Preço por Hora', $service->hourly_rate ? 'MT ' . number_format($service->hourly_rate, 2) : '-'],
            ['Preço Fixo', $service->fixed_price ? 'MT ' . number_format($service->fixed_price, 2) : '-'],
            ['Horas Estimadas', $service->estimated_hours ?: '-'],
            ['Complexidade', ucfirst($service->complexity_level)],
            ['Status', $service->is_active ? 'Ativo' : 'Inativo'],
            ['Requisitos', $service->requirements ?: '-'],
            ['Tags', $service->tags ?: '-'],
            ['Criado em', $service->created_at->format('d/m/Y H:i')],
            ['Atualizado em', $service->updated_at->format('d/m/Y H:i')],
        ];

        foreach ($data as $row) {
            fputcsv($file, $row);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}
