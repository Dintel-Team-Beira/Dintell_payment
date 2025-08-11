<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminSupportController extends Controller
{
    /**
     * Lista de tickets de suporte.
     */
    public function tickets(Request $request)
    {
        $query = $this->getTicketsQuery();

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('company')) {
            $query->whereHas('company', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->company . '%');
            });
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('ticket_number', 'like', '%' . $request->search . '%');
            });
        }

        $tickets = $query->with(['company', 'user', 'assignedTo', 'replies'])
                         ->orderBy('created_at', 'desc')
                         ->paginate(20);

        // Estatísticas
        $stats = [
            'total' => $this->getTicketsQuery()->count(),
            'open' => $this->getTicketsQuery()->whereIn('status', ['open', 'pending'])->count(),
            'closed' => $this->getTicketsQuery()->where('status', 'closed')->count(),
            'high_priority' => $this->getTicketsQuery()->where('priority', 'high')->count(),
        ];

        return view('admin.support.tickets.index', compact('tickets', 'stats'));
    }

    /**
     * Exibe um ticket específico.
     */
    public function showTicket($ticketId)
    {
        $ticket = $this->getTicketById($ticketId);

        if (!$ticket) {
            return redirect()->route('admin.support.tickets')->with('error', 'Ticket não encontrado.');
        }

        // Marcar ticket como visualizado
        $this->markTicketAsViewed($ticket);

        return view('admin.support.tickets.show', compact('ticket'));
    }

    /**
     * Responde a um ticket.
     */
    public function replyTicket(Request $request, $ticketId)
    {
        $request->validate([
            'message' => 'required|string|min:10',
            'status' => 'sometimes|in:open,pending,closed,resolved'
        ]);

        $ticket = $this->getTicketById($ticketId);

        if (!$ticket) {
            return response()->json(['error' => 'Ticket não encontrado'], 404);
        }

        try {
            // Criar resposta
            $reply = $this->createTicketReply($ticket, $request->message, auth()->user());

            // Atualizar status se fornecido
            if ($request->filled('status')) {
                $this->updateTicketStatus($ticket, $request->status);
            }

            // Atualizar última atividade
            $this->updateTicketActivity($ticket);

            return response()->json([
                'success' => true,
                'message' => 'Resposta enviada com sucesso!',
                'reply' => $this->formatReply($reply)
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao enviar resposta: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Atualiza o status de um ticket.
     */
    public function updateTicketStatus(Request $request, $ticketId)
    {
        $request->validate([
            'status' => 'required|in:open,pending,closed,resolved',
            'reason' => 'sometimes|string|max:500'
        ]);

        $ticket = $this->getTicketById($ticketId);

        if (!$ticket) {
            return response()->json(['error' => 'Ticket não encontrado'], 404);
        }

        try {
            $oldStatus = $ticket['status'];
            $this->updateTicketStatus($ticket, $request->status, $request->reason);

            // Log da mudança de status
            $this->logStatusChange($ticket, $oldStatus, $request->status, $request->reason);

            return response()->json([
                'success' => true,
                'message' => 'Status atualizado com sucesso!',
                'new_status' => $request->status
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar status: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mock data - Em um projeto real, isso viria do banco de dados
     */
    private function getTicketsQuery()
    {
        // Simulação de query - substitua pela sua implementação real
        return collect([
            [
                'id' => 1,
                'ticket_number' => 'TK-2024-001',
                'subject' => 'Problema com faturação',
                'description' => 'Cliente relatando problema ao gerar faturas',
                'status' => 'open',
                'priority' => 'high',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subHours(3),
                'company' => ['id' => 1, 'name' => 'Empresa ABC'],
                'user' => ['id' => 1, 'name' => 'João Silva', 'email' => 'joao@empresa.com'],
                'assigned_to' => null,
                'replies_count' => 3
            ],
            [
                'id' => 2,
                'ticket_number' => 'TK-2024-002',
                'subject' => 'Solicitação de nova funcionalidade',
                'description' => 'Gostaria de adicionar relatórios customizados',
                'status' => 'pending',
                'priority' => 'medium',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subHours(1),
                'company' => ['id' => 2, 'name' => 'Tech Solutions'],
                'user' => ['id' => 2, 'name' => 'Maria Santos', 'email' => 'maria@tech.com'],
                'assigned_to' => ['id' => 1, 'name' => 'Admin'],
                'replies_count' => 1
            ],
            [
                'id' => 3,
                'ticket_number' => 'TK-2024-003',
                'subject' => 'Dúvida sobre integração',
                'description' => 'Como integrar com sistema externo?',
                'status' => 'closed',
                'priority' => 'low',
                'created_at' => Carbon::now()->subWeek(),
                'updated_at' => Carbon::now()->subDays(2),
                'company' => ['id' => 3, 'name' => 'Inovação Ltda'],
                'user' => ['id' => 3, 'name' => 'Pedro Costa', 'email' => 'pedro@inovacao.com'],
                'assigned_to' => ['id' => 1, 'name' => 'Admin'],
                'replies_count' => 5
            ]
        ]);
    }

    private function getTicketById($id)
    {
        $tickets = $this->getTicketsQuery();
        $ticket = $tickets->firstWhere('id', $id);

        if ($ticket) {
            // Adicionar respostas mock
            $ticket['replies'] = [
                [
                    'id' => 1,
                    'message' => 'Obrigado por entrar em contato. Estamos analisando seu problema.',
                    'user' => ['name' => 'Suporte Admin', 'avatar' => null],
                    'created_at' => Carbon::now()->subHours(2),
                    'is_admin' => true
                ],
                [
                    'id' => 2,
                    'message' => 'Consegui reproduzir o problema. Estamos trabalhando na correção.',
                    'user' => ['name' => 'Suporte Admin', 'avatar' => null],
                    'created_at' => Carbon::now()->subHour(),
                    'is_admin' => true
                ]
            ];
        }

        return $ticket ? collect($ticket) : null;
    }

    private function markTicketAsViewed($ticket)
    {
        // Implementar lógica para marcar como visualizado
        // Exemplo: DB::table('ticket_views')->updateOrInsert([...]);
    }

    private function createTicketReply($ticket, $message, $user)
    {
        // Implementar criação de resposta
        return [
            'id' => rand(1000, 9999),
            'message' => $message,
            'user' => [
                'name' => $user->name,
                'avatar' => $user->avatar ?? null
            ],
            'created_at' => Carbon::now(),
            'is_admin' => true
        ];
    }

    private function updateTicketStatus($ticket, $status, $reason = null)
    {
        // Implementar atualização de status
        // Exemplo: DB::table('tickets')->where('id', $ticket['id'])->update(['status' => $status]);
    }

    private function updateTicketActivity($ticket)
    {
        // Implementar atualização de última atividade
        // Exemplo: DB::table('tickets')->where('id', $ticket['id'])->update(['updated_at' => now()]);
    }

    private function logStatusChange($ticket, $oldStatus, $newStatus, $reason = null)
    {
        // Implementar log de mudança de status
        // Exemplo: criar registro na tabela ticket_logs
    }

    private function formatReply($reply)
    {
        return [
            'id' => $reply['id'],
            'message' => $reply['message'],
            'user_name' => $reply['user']['name'],
            'created_at' => $reply['created_at']->diffForHumans(),
            'is_admin' => $reply['is_admin']
        ];
    }
}
