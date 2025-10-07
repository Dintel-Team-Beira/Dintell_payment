<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Receipt;
use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiptController extends Controller
{
     protected $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    /**
     * Listar todos os recibos
     */
    public function index(Request $request)
    {
        $company = auth()->user()->company;
        $query = Receipt::where('company_id', $company->id)->with(['invoice', 'client', 'issuedBy']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to . ' 23:59:59');
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('receipt_number', 'like', '%' . $request->search . '%')
                  ->orWhere('transaction_reference', 'like', '%' . $request->search . '%')
                  ->orWhere('notes', 'like', '%' . $request->search . '%')
                  ->orWhereHas('client', function($clientQuery) use ($request) {
                      $clientQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Ordenação
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $receipts = $query->orderBy($sortField, $sortDirection)
                          ->paginate($request->get('per_page', 15));

        $clients = Client::where('company_id', $company->id)->orderBy('name')->get();
        $stats = $this->receiptService->getReceiptStats($company->id);

        return view('receipts.index', compact('receipts', 'clients', 'stats'));
    }

    /**
     * Mostrar detalhes de um recibo
     */
    public function show(Receipt $receipt)
    {
        $receipt->load(['invoice', 'client', 'company', 'issuedBy']);
        
        return view('receipts.show', compact('receipt'));
    }

    /**
     * Cancelar um recibo
     */
    public function cancel(Request $request, Receipt $receipt)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            $this->receiptService->cancelReceipt($receipt, $request->reason);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Recibo cancelado com sucesso!'
                ]);
            }

            return back()->with('success', 'Recibo cancelado com sucesso!');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao cancelar recibo: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erro ao cancelar recibo: ' . $e->getMessage());
        }
    }

    /**
     * Download do PDF do recibo
     */
    public function downloadPdf(Receipt $receipt)
    {
        try {
            return $this->receiptService->generateReceiptPdf($receipt);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao gerar PDF: ' . $e->getMessage());
        }
    }

    /**
     * Estatísticas de recibos (API)
     */
    public function stats(Request $request)
    {
        $companyId = $request->get('company_id');
        $stats = $this->receiptService->getReceiptStats($companyId);

        return response()->json($stats);
    }

    /**
     * Duplicar recibo (criar nova versão)
     */
    public function duplicate(Receipt $receipt)
    {
        try {
            DB::beginTransaction();

            // Validar se é possível duplicar
            if ($receipt->isCancelled()) {
                throw new \Exception('Não é possível duplicar um recibo cancelado');
            }

            // Criar nova versão
            $newReceipt = $receipt->replicate([
                'receipt_number' // será gerado automaticamente
            ]);
            
            $newReceipt->notes = ($newReceipt->notes ?? '') . "\n\nDuplicado do recibo: {$receipt->receipt_number}";
            $newReceipt->payment_date = now();
            $newReceipt->save();

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Recibo duplicado com sucesso!',
                    'receipt' => [
                        'id' => $newReceipt->id,
                        'receipt_number' => $newReceipt->receipt_number,
                        'show_url' => route('receipts.show', $newReceipt)
                    ]
                ]);
            }

            return redirect()->route('receipts.show', $newReceipt)
                ->with('success', 'Recibo duplicado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao duplicar recibo: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erro ao duplicar recibo: ' . $e->getMessage());
        }
    }

    /**
     * Exportar recibos
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        
        // Implementar exportação conforme necessário
        // Similar ao que já existe para faturas
        
        return back()->with('info', 'Funcionalidade de exportação será implementada em breve.');
    }
                
}
