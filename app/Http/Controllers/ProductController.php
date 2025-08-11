<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Lista de produtos
     */
    public function index(Request $request)
    {
        $query = Product::query();

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

        if ($request->filled('status')) {
            $status = $request->get('status');
            switch ($status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'low_stock':
                    $query->whereColumn('stock_quantity', '<=', 'min_stock_level');
                    break;
            }
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->get('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->get('price_max'));
        }

        $products = $query->orderBy('name')->paginate(20);

        return view('products.index', compact('products'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Salvar produto
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:products,code',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'category' => 'required|string|max:50',
            'unit' => 'required|string|max:20',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);

                $validated['company_id'] = auth()->user()->company->id;
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->except(['image']);
        $data['is_active'] = $request->has('is_active');

        // Upload da imagem
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        // Gerar código se não fornecido
        if (empty($data['code'])) {
            $data['code'] = $this->generateCode($data['name']);
        }

        $product = Product::create($data);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'product' => $product]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    /**
     * Visualizar produto
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Formulário de edição
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Atualizar produto
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:products,code,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'category' => 'required|string|max:50',
            'unit' => 'required|string|max:20',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);

                $validated['company_id'] = auth()->user()->company->id;
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->except(['image']);
        $data['is_active'] = $request->has('is_active');

        // Upload da nova imagem
        if ($request->hasFile('image')) {
            // Remover imagem anterior
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Excluir produto
     */
    public function destroy(Product $product)
    {
        // Verificar se produto está sendo usado em faturas/orçamentos
        if ($product->invoiceItems()->exists() || $product->quoteItems()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não pode ser excluído pois está sendo usado em faturas ou orçamentos.'
            ]);
        }

        // Remover imagem
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produto excluído com sucesso!'
        ]);
    }

    /**
     * Duplicar produto
     */
    public function duplicate(Product $product)
    {
        $newProduct = $product->replicate();
        $newProduct->name = $product->name . ' (Cópia)';
        $newProduct->code = $this->generateCode($newProduct->name);
        $newProduct->save();

        return redirect()->route('products.edit', $newProduct)
            ->with('success', 'Produto duplicado com sucesso!');
    }

    /**
     * Alterar status do produto
     */
    public function toggleStatus(Request $request, Product $product): JsonResponse
    {
        $product->update(['is_active' => $request->get('is_active', true)]);

        return response()->json([
            'success' => true,
            'message' => 'Status do produto alterado com sucesso!'
        ]);
    }

    /**
     * Produtos com estoque baixo
     */
    public function lowStock()
    {
        $products = Product::lowStock()->active()->get();

        return view('products.low-stock', compact('products'));
    }

    /**
     * Exportar produtos
     */
    public function export(Request $request)
    {
        $query = Product::query();

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

        $products = $query->get();

        // Aqui você implementaria a exportação para Excel/CSV
        // Por exemplo, usando Laravel Excel ou gerando CSV manualmente

        $fileName = 'produtos_' . now()->format('Y_m_d_H_i_s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [
                'Código', 'Nome', 'Descrição', 'Categoria', 'Preço',
                'Custo', 'Estoque', 'Estoque Mínimo', 'Unidade', 'Status'
            ]);

            // Data
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->code,
                    $product->name,
                    $product->description,
                    $product->category,
                    $product->price,
                    $product->cost,
                    $product->stock_quantity,
                    $product->min_stock_level,
                    $product->unit,
                    $product->is_active ? 'Ativo' : 'Inativo'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API: Buscar produtos
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        $products = Product::active()
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('code', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'type' => 'product',
                    'name' => $product->name,
                    'code' => $product->code,
                    'price' => $product->price,
                    'description' => $product->description,
                    'unit' => $product->unit,
                    'stock_quantity' => $product->stock_quantity
                ];
            });

        return response()->json($products);
    }

    /**
     * API: Estatísticas de produtos
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => Product::count(),
            'active' => Product::active()->count(),
            'inactive' => Product::where('is_active', false)->count(),
            'low_stock' => Product::lowStock()->count(),
            'out_of_stock' => Product::where('stock_quantity', 0)->count(),
            'categories' => Product::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category'),
            'total_value' => Product::sum('price'),
            'avg_price' => Product::avg('price')
        ];

        return response()->json($stats);
    }

    /**
     * Gerar código único para produto
     */
    private function generateCode($name): string
    {
        $words = explode(' ', $name);
        $code = 'PROD';

        if (count($words) >= 2) {
            $code = strtoupper(substr($words[0], 0, 3) . substr($words[1], 0, 3));
        } else {
            $code = strtoupper(substr($words[0], 0, 6));
        }

        // Adicionar número sequencial para garantir unicidade
        $counter = 1;
        $baseCode = $code;

        while (Product::where('code', $code)->exists()) {
            $code = $baseCode . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $code;
    }
}
