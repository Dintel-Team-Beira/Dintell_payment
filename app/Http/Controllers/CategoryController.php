<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
          $query = Category::with(['parent', 'children'])
                        ->withCount(['products', 'services']);

        // Pesquisa
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtro por tipo
        if ($type = $request->get('type')) {
            $query->ofType($type);
        }

        // Filtro por status
        if ($request->has('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->active();
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filtro: apenas principais ou subcategorias
        if ($request->get('level') === 'main') {
            $query->main();
        } elseif ($request->get('level') === 'sub') {
            $query->subcategories();
        }

        // Ordenação
        $sortField = $request->get('sort', 'order');
        $sortDirection = $request->get('direction', 'asc');
        
        if ($sortField === 'items_count') {
            // Ordenar por total de itens (produtos + serviços)
            $query->orderByRaw('(products_count + services_count) ' . $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $categories = $query->paginate($request->get('per_page', 15));

        // Estatísticas
        $stats = [
            'total_categories' => Category::count(),
            'active_categories' => Category::active()->count(),
            'product_categories' => Category::where('type', 'product')->orWhere('type', 'both')->count(),
            'service_categories' => Category::where('type', 'service')->orWhere('type', 'both')->count(),
        ];
        return view('categories.index', compact('categories', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         // Categorias principais para dropdown de "Categoria Pai"
        $parentCategories = Category::main()->active()->ordered()->get();
        
        return view('categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->where('company_id', auth()->user()->company_id)],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:product,service,both'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'icon' => ['nullable', 'string', 'max:50'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        try {
            $category = Category::create($validated);

            return redirect()
                ->route('categories.index')
                ->with('success', 'Categoria criada com sucesso!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar categoria: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
         $category->load(['parent', 'children', 'products', 'services']);
        
        // Produtos e serviços paginados
        $products = $category->products()
                            ->with('category')
                            ->active()
                            ->paginate(10, ['*'], 'products_page');
                            
        $services = $category->services()
                            ->with('category')
                            ->active()
                            ->paginate(10, ['*'], 'services_page');

        return view('categories.show', compact('category', 'products', 'services'));
 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $descendantIds = $category->getDescendantIds();
        $excludeIds = array_merge([$category->id], $descendantIds);
        
        $parentCategories = Category::main()
                                    ->active()
                                    ->whereNotIn('id', $excludeIds)
                                    ->ordered()
                                    ->get();

        return view('categories.edit', compact('category', 'parentCategories'));
  
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
          $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 
                Rule::unique('categories')
                    ->where('company_id', auth()->user()->company_id)
                    ->ignore($category->id)
            ],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:product,service,both'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'icon' => ['nullable', 'string', 'max:50'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        // Validar se pode ser movida para o novo pai (evitar loops)
        if (isset($validated['parent_id']) && !$category->canBeMovedTo($validated['parent_id'])) {
            return back()
                ->withInput()
                ->with('error', 'Não é possível mover esta categoria para a categoria selecionada (criaria um loop).');
        }

        try {
            $category->update($validated);

            return redirect()
                ->route('categories.index')
                ->with('success', 'Categoria atualizada com sucesso!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar categoria: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
         // Verificar se tem produtos ou serviços associados
        $hasProducts = $category->products()->exists();
        $hasServices = $category->services()->exists();
        $hasChildren = $category->children()->exists();

        if ($hasProducts || $hasServices || $hasChildren) {
            $message = 'Não é possível excluir esta categoria pois ';
            $reasons = [];
            
            if ($hasProducts) $reasons[] = 'tem produtos associados';
            if ($hasServices) $reasons[] = 'tem serviços associados';
            if ($hasChildren) $reasons[] = 'tem subcategorias';
            
            $message .= implode(', ', $reasons) . '.';

            return back()->with('error', $message);
        }

        try {
            $category->delete();

            return redirect()
                ->route('categories.index')
                ->with('success', 'Categoria excluída com sucesso!');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir categoria: ' . $e->getMessage());
        }
    }
}
