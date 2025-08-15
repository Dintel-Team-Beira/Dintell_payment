<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF; // Para exportação de relatórios

class AdminSupportController extends Controller
{
    /**
     * Dashboard de suporte administrativo
     */
    public function index()
    {
        $stats = $this->getBasicStats();

        $recentTickets = SupportTicket::with(['user', 'company'])
            ->latest()
            ->limit(10)
            ->get();

        $urgentTickets = SupportTicket::with(['user', 'company'])
            ->where('priority', 'urgent')
            ->whereIn('status', ['open', 'pending'])
            ->latest()
            ->get();

        return view('admin.support.index', compact('stats', 'recentTickets', 'urgentTickets'));
    }

    /**
     * Lista todos os tickets com filtros
     */
    public function tickets(Request $request)
    {
        $query = SupportTicket::with(['user', 'company', 'assignedTo', 'replies']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('assigned_to')) {
            if ($request->assigned_to === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('ticket_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                               ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Export functionality
        if ($request->filled('export') && $request->export === 'true') {
            return $this->exportTickets($query->get());
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = $this->getBasicStats();
        $agents = User::where('is_super_admin', true)->get();

        return view('admin.support.tickets.index', compact('tickets', 'stats', 'agents'));
    }

    /**
     * Visualizar ticket específico (admin)
     */
    public function showTicket($id)
    {
        $ticket = SupportTicket::with(['user', 'company', 'assignedTo', 'replies.user'])
            ->findOrFail($id);

        // Marcar como visualizado pelo admin
        $ticket->markAsViewed(Auth::user());

        $agents = User::where('is_super_admin', true)->get();

        return view('admin.support.tickets.show', compact('ticket', 'agents'));
    }

    /**
     * Responder ticket (admin)
     */
    public function replyTicket(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'message' => 'required|string|min:5',
                'is_internal' => 'sometimes|boolean',
                'status' => 'sometimes|in:open,pending,in_progress,resolved,closed',
                'attachments' => 'sometimes|array|max:5',
                'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,txt'
            ]);

            $ticket = SupportTicket::findOrFail($id);
            $user = Auth::user();

            // Processar anexos
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('support-attachments', 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType()
                    ];
                }
            }

            $reply = $ticket->addReply(
                $validated['message'],
                $user,
                $validated['is_internal'] ?? false,
                $attachments
            );

            // Atualizar status se fornecido
            if ($request->filled('status')) {
                $ticket->update(['status' => $validated['status']]);

                if ($validated['status'] === 'resolved') {
                    $ticket->update(['resolved_at' => now()]);
                } elseif ($validated['status'] === 'closed') {
                    $ticket->close('Fechado pelo administrador');
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Resposta enviada com sucesso!',
                'reply' => [
                    'id' => $reply->id,
                    'message' => $reply->message,
                    'user_name' => $reply->user->name,
                    'created_at' => $reply->created_at->diffForHumans(),
                    'is_internal' => $reply->is_internal
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar resposta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atribuir ticket a um agente
     */
    public function assignTicket(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'assigned_to' => 'required|exists:users,id'
            ]);

            $ticket = SupportTicket::findOrFail($id);
            $agent = User::findOrFail($validated['assigned_to']);

            $ticket->assignTo($agent);

            return response()->json([
                'success' => true,
                'message' => "Ticket atribuído para {$agent->name}!"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atribuir ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar status do ticket
     */
    public function updateTicketStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:open,pending,in_progress,resolved,closed',
                'comment' => 'sometimes|string|max:500'
            ]);

            $ticket = SupportTicket::findOrFail($id);
            $oldStatus = $ticket->status;

            $ticket->update(['status' => $validated['status']]);

            if ($validated['status'] === 'resolved') {
                $ticket->resolve($validated['comment'] ?? 'Ticket resolvido pelo administrador');
            } elseif ($validated['status'] === 'closed') {
                $ticket->close($validated['comment'] ?? 'Ticket fechado pelo administrador');
            }

            // Log da mudança
            $ticket->replies()->create([
                'user_id' => Auth::id(),
                'message' => "Status alterado de '{$oldStatus}' para '{$validated['status']}'" .
                           ($validated['comment'] ? ". Comentário: {$validated['comment']}" : ''),
                'is_internal' => true,
                'is_system' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status atualizado com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Relatórios de suporte
     */
    public function reports(Request $request)
    {
        $period = (int) $request->input('period', 30); // dias
        $startDate = now()->subDays($period);

        $data = [
            'tickets_created' => SupportTicket::where('created_at', '>=', $startDate)->count(),
            'tickets_resolved' => SupportTicket::where('resolved_at', '>=', $startDate)->count(),
            'avg_response_time' => $this->getAverageResponseTime($startDate),
            'satisfaction_rate' => $this->getSatisfactionRate($startDate),
            'tickets_by_category' => $this->getTicketsByCategory($startDate),
            'tickets_by_priority' => $this->getTicketsByPriority($startDate),
            'agent_performance' => $this->getAgentPerformance($startDate)
        ];

        // Export functionality
        if ($request->filled('export')) {
            if ($request->export === 'pdf') {
                return $this->exportReportPDF($data, $period);
            } elseif ($request->export === 'agents') {
                return $this->exportAgentReport($data['agent_performance']);
            }
        }

        return view('admin.support.reports', compact('data', 'period'));
    }

    /**
     * Check for updates (AJAX endpoint)
     */
    public function checkUpdates()
    {
        $newTickets = SupportTicket::where('created_at', '>=', now()->subMinutes(5))->count();
        $urgentTickets = SupportTicket::where('priority', 'urgent')
                                   ->whereIn('status', ['open', 'pending'])
                                   ->count();

        return response()->json([
            'new_tickets' => $newTickets,
            'urgent_tickets' => $urgentTickets
        ]);
    }

    /**
     * Métodos auxiliares privados
     */
    private function getBasicStats()
    {
        return [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::whereIn('status', ['open', 'pending'])->count(),
            'closed' => SupportTicket::where('status', 'closed')->count(),
            'high_priority' => SupportTicket::whereIn('priority', ['high', 'urgent'])->count(),
            'unassigned' => SupportTicket::whereNull('assigned_to')->whereIn('status', ['open', 'pending'])->count(),
            'pending' => SupportTicket::where('status', 'pending')->count(),
        ];
    }

    private function getAverageResponseTime($startDate)
    {
        $tickets = SupportTicket::whereNotNull('first_response_at')
            ->where('created_at', '>=', $startDate)
            ->select('created_at', 'first_response_at')
            ->get();

        if ($tickets->isEmpty()) {
            return 'N/A';
        }

        $totalMinutes = $tickets->sum(function ($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->first_response_at);
        });

        $averageMinutes = $totalMinutes / $tickets->count();
        return round($averageMinutes / 60, 1) . ' horas';
    }

    private function getSatisfactionRate($startDate)
    {
        $ratedTickets = SupportTicket::whereNotNull('satisfaction_rating')
            ->where('closed_at', '>=', $startDate)
            ->get();

        if ($ratedTickets->isEmpty()) {
            return 'N/A';
        }

        $averageRating = $ratedTickets->avg('satisfaction_rating');
        return round(($averageRating / 5) * 100) . '%';
    }

    private function getTicketsByCategory($startDate)
    {
        return SupportTicket::where('created_at', '>=', $startDate)
            ->groupBy('category')
            ->selectRaw('category, count(*) as count')
            ->pluck('count', 'category')
            ->toArray();
    }

    private function getTicketsByPriority($startDate)
    {
        return SupportTicket::where('created_at', '>=', $startDate)
            ->groupBy('priority')
            ->selectRaw('priority, count(*) as count')
            ->pluck('count', 'priority')
            ->toArray();
    }

    private function getAgentPerformance($startDate)
    {
        $agents = User::where('is_super_admin', true)->get();
        $performance = [];

        foreach ($agents as $agent) {
            $tickets = SupportTicket::where('assigned_to', $agent->id)
                ->where('created_at', '>=', $startDate)
                ->get();

            if ($tickets->count() > 0) {
                $resolvedTickets = $tickets->where('status', 'resolved')->count();
                $avgSatisfaction = $tickets->whereNotNull('satisfaction_rating')->avg('satisfaction_rating');

                $performance[] = [
                    'name' => $agent->name,
                    'total_tickets' => $tickets->count(),
                    'resolved_tickets' => $resolvedTickets,
                    'avg_satisfaction' => $avgSatisfaction ? round($avgSatisfaction, 1) : null
                ];
            }
        }

        return $performance;
    }

    private function exportTickets($tickets)
    {
        $filename = 'tickets_export_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($tickets) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'Número do Ticket',
                'Assunto',
                'Status',
                'Prioridade',
                'Cliente',
                'Email do Cliente',
                'Empresa',
                'Atribuído para',
                'Criado em',
                'Última Atividade'
            ]);

            // Data
            foreach ($tickets as $ticket) {
                fputcsv($file, [
                    $ticket->ticket_number,
                    $ticket->subject,
                    ucfirst($ticket->status),
                    ucfirst($ticket->priority),
                    $ticket->user->name,
                    $ticket->user->email,
                    $ticket->company->name ?? 'N/A',
                    $ticket->assignedTo->name ?? 'Não Atribuído',
                    $ticket->created_at->format('d/m/Y H:i'),
                    $ticket->last_activity_at ? $ticket->last_activity_at->format('d/m/Y H:i') : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportReportPDF($data, $period)
    {
        $pdf = PDF::loadView('admin.support.reports.pdf', compact('data', 'period'));
        return $pdf->download('relatorio_suporte_' . date('Y-m-d') . '.pdf');
    }

    private function exportAgentReport($agentData)
    {
        $filename = 'agent_performance_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($agentData) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'Agente',
                'Total de Tickets',
                'Tickets Resolvidos',
                'Taxa de Resolução (%)',
                'Satisfação Média'
            ]);

            // Data
            foreach ($agentData as $agent) {
                $resolutionRate = $agent['total_tickets'] > 0 ?
                    ($agent['resolved_tickets'] / $agent['total_tickets'] * 100) : 0;

                fputcsv($file, [
                    $agent['name'],
                    $agent['total_tickets'],
                    $agent['resolved_tickets'],
                    number_format($resolutionRate, 1),
                    $agent['avg_satisfaction'] ? number_format($agent['avg_satisfaction'], 1) : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
