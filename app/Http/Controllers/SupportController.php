<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SupportController extends Controller
{
    /**
     * Lista tickets do usuário atual
     */
    public function myTickets(Request $request)
    {
        $user = Auth::user();

        $query = SupportTicket::where('user_id', $user->id)
            ->with(['replies' => function($q) {
                $q->latest()->limit(1);
            }]);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->latest()->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'tickets' => $tickets->map(function ($ticket) {
                    return [
                        'id' => $ticket->id,
                        'ticket_number' => $ticket->ticket_number,
                        'subject' => $ticket->subject,
                        'status' => $ticket->status,
                        'priority' => $ticket->priority,
                        'category' => $ticket->category,
                        'created_at' => $ticket->created_at,
                        'last_reply' => $ticket->replies->first()?->created_at,
                        'has_unread' => !$ticket->hasBeenViewedBy(Auth::user())
                    ];
                })
            ]);
        }

        return view('support.my-tickets', compact('tickets'));
    }

    /**
     * Criar novo ticket
     */
    public function store(Request $request)
    {

        return $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'category' => 'required|in:technical,billing,general,feature_request,bug_report',
            'priority' => 'required|in:low,medium,high,urgent',
            'attachments' => 'sometimes|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,txt'
        ]);
        try {
            $validated = $request->validate([
                'subject' => 'required|string|max:255',
                'description' => 'required|string|min:10',
                'category' => 'required|in:technical,billing,general,feature_request,bug_report',
                'priority' => 'required|in:low,medium,high,urgent',
                'attachments' => 'sometimes|array|max:5',
                'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,txt'
            ]);

            $user = Auth::user();

            // Processar anexos se houver
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

            // Coletar metadados do sistema
            $metadata = [
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'url' => $request->headers->get('referer'),
                'created_via' => 'popup'
            ];

            $ticket = SupportTicket::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'subject' => $validated['subject'],
                'description' => $validated['description'],
                'category' => $validated['category'],
                'priority' => $validated['priority'],
                'attachments' => $attachments,
                'metadata' => $metadata,
                'status' => 'open'
            ]);

            // Notificar administradores
            $this->notifyAdmins($ticket);

            return response()->json([
                'success' => true,
                'message' => 'Ticket criado com sucesso!',
                'ticket_number' => $ticket->ticket_number,
                'ticket_id' => $ticket->id
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Visualizar ticket específico
     */
    public function show($id)
    {
        $user = Auth::user();

        $ticket = SupportTicket::with(['replies.user', 'assignedTo', 'user'])
            ->where('id', $id)
            ->where(function($query) use ($user) {
                // Usuario pode ver seus próprios tickets ou admin pode ver todos
                $query->where('user_id', $user->id)
                      ->orWhere(function($q) use ($user) {
                          if ($user->is_super_admin) {
                              $q->whereNotNull('id');
                          }
                      });
            })
            ->firstOrFail();

        // Marcar como visualizado
        $ticket->markAsViewed($user);

        return view('support.show', compact('ticket'));
    }

    /**
     * Adicionar resposta ao ticket
     */
    public function addReply(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'message' => 'required|string|min:5',
                'attachments' => 'sometimes|array|max:3',
                'attachments.*' => 'file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx,txt'
            ]);

            $user = Auth::user();

            $ticket = SupportTicket::where('id', $id)
                ->where(function($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->orWhere(function($q) use ($user) {
                              if ($user->is_super_admin) {
                                  $q->whereNotNull('id');
                              }
                          });
                })
                ->firstOrFail();

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
                false, // não é interno
                $attachments
            );

            // Se o ticket estava resolvido e o cliente respondeu, reabrir
            if ($ticket->status === 'resolved' && $ticket->user_id === $user->id) {
                $ticket->update(['status' => 'open']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Resposta adicionada com sucesso!',
                'reply' => [
                    'id' => $reply->id,
                    'message' => $reply->message,
                    'user_name' => $reply->user->name,
                    'created_at' => $reply->created_at->diffForHumans(),
                    'attachments' => $reply->attachments
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar resposta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Avaliar ticket (satisfação)
     */
    public function rateSatisfaction(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:500'
            ]);

            $user = Auth::user();

            $ticket = SupportTicket::where('id', $id)
                ->where('user_id', $user->id)
                ->where('status', 'closed')
                ->firstOrFail();

            $ticket->update([
                'satisfaction_rating' => $validated['rating'],
                'satisfaction_comment' => $validated['comment']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Avaliação registrada com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar avaliação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fechar ticket (apenas o usuário proprietário)
     */
    public function close(Request $request, $id)
    {
        try {
            $user = Auth::user();

            $ticket = SupportTicket::where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $comment = $request->input('comment', 'Ticket fechado pelo usuário');
            $ticket->close($comment);

            return response()->json([
                'success' => true,
                'message' => 'Ticket fechado com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fechar ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reabrir ticket
     */
    public function reopen($id)
    {
        try {
            $user = Auth::user();

            $ticket = SupportTicket::where('id', $id)
                ->where('user_id', $user->id)
                ->whereIn('status', ['closed', 'resolved'])
                ->firstOrFail();

            $ticket->update([
                'status' => 'open',
                'resolved_at' => null,
                'closed_at' => null
            ]);

            $ticket->replies()->create([
                'user_id' => $user->id,
                'message' => 'Ticket reaberto pelo usuário',
                'is_system' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ticket reaberto com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao reabrir ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter estatísticas do usuário
     */
    public function getUserStats()
    {
        $user = Auth::user();

        $stats = [
            'total' => SupportTicket::where('user_id', $user->id)->count(),
            'open' => SupportTicket::where('user_id', $user->id)->whereIn('status', ['open', 'pending', 'in_progress'])->count(),
            'resolved' => SupportTicket::where('user_id', $user->id)->where('status', 'resolved')->count(),
            'closed' => SupportTicket::where('user_id', $user->id)->where('status', 'closed')->count(),
            'avg_resolution_time' => $this->getAverageResolutionTime($user->id),
            'satisfaction_avg' => SupportTicket::where('user_id', $user->id)
                ->whereNotNull('satisfaction_rating')
                ->avg('satisfaction_rating')
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Buscar na base de conhecimento/FAQ
     */
    public function searchKnowledgeBase(Request $request)
    {
        $query = $request->input('q', '');

        if (empty($query)) {
            return response()->json([
                'success' => true,
                'results' => []
            ]);
        }

        // Simular busca na base de conhecimento
        $faqData = $this->getFAQData();

        $results = collect($faqData)->filter(function($item) use ($query) {
            return str_contains(strtolower($item['question']), strtolower($query)) ||
                   str_contains(strtolower($item['answer']), strtolower($query));
        })->values();

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }

    /**
     * Download de anexo
     */
    public function downloadAttachment($ticketId, $attachmentIndex)
    {
        $user = Auth::user();

        $ticket = SupportTicket::where('id', $ticketId)
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere(function($q) use ($user) {
                          if ($user->is_super_admin) {
                              $q->whereNotNull('id');
                          }
                      });
            })
            ->firstOrFail();

        if (!isset($ticket->attachments[$attachmentIndex])) {
            abort(404, 'Anexo não encontrado');
        }

        $attachment = $ticket->attachments[$attachmentIndex];

        if (!Storage::disk('public')->exists($attachment['path'])) {
            abort(404, 'Arquivo não encontrado');
        }

        return Storage::disk('public')->download(
            $attachment['path'],
            $attachment['name']
        );
    }

    /**
     * Métodos privados auxiliares
     */
    private function notifyAdmins(SupportTicket $ticket)
    {
        // Implementar notificação para administradores
        // Por exemplo: email, notificação no dashboard, etc.

        $admins = User::where('is_super_admin', true)->get();

        foreach ($admins as $admin) {
            // Aqui você pode implementar:
            // - Envio de email
            // - Notificação push
            // - Notificação no sistema
            // - Slack/Teams notification
        }
    }

    private function getAverageResolutionTime($userId)
    {
        $tickets = SupportTicket::where('user_id', $userId)
            ->whereNotNull('resolved_at')
            ->select('created_at', 'resolved_at')
            ->get();

        if ($tickets->isEmpty()) {
            return null;
        }

        $totalMinutes = $tickets->sum(function ($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->resolved_at);
        });

        $averageMinutes = $totalMinutes / $tickets->count();

        if ($averageMinutes < 60) {
            return round($averageMinutes) . ' min';
        } elseif ($averageMinutes < 1440) {
            return round($averageMinutes / 60, 1) . ' h';
        } else {
            return round($averageMinutes / 1440, 1) . ' dias';
        }
    }

    private function getFAQData()
    {
        return [
            [
                'id' => 1,
                'question' => 'Como criar uma nova fatura?',
                'answer' => 'Para criar uma nova fatura, acesse o menu "Faturas" e clique em "Nova Fatura". Preencha os dados do cliente e adicione os produtos ou serviços.',
                'category' => 'Faturas',
                'helpful_count' => 25
            ],
            [
                'id' => 2,
                'question' => 'Como alterar minha senha?',
                'answer' => 'Vá em "Meu Perfil" > "Configurações" > "Alterar Senha". Digite sua senha atual e a nova senha duas vezes.',
                'category' => 'Conta',
                'helpful_count' => 18
            ],
            [
                'id' => 3,
                'question' => 'Como posso acompanhar meus pagamentos?',
                'answer' => 'No dashboard principal, você encontra um resumo dos pagamentos. Para mais detalhes, acesse "Relatórios" > "Financeiro".',
                'category' => 'Pagamentos',
                'helpful_count' => 32
            ],
            [
                'id' => 4,
                'question' => 'Como adicionar novos usuários à minha empresa?',
                'answer' => 'Acesse "Configurações" > "Usuários" > "Adicionar Usuário". Preencha os dados e defina as permissões.',
                'category' => 'Usuários',
                'helpful_count' => 15
            ],
            [
                'id' => 5,
                'question' => 'Como exportar relatórios?',
                'answer' => 'Na seção de relatórios, clique no botão "Exportar" e escolha o formato desejado (PDF, Excel, CSV).',
                'category' => 'Relatórios',
                'helpful_count' => 22
            ],
            [
                'id' => 6,
                'question' => 'O que fazer se esqueci minha senha?',
                'answer' => 'Na tela de login, clique em "Esqueci minha senha" e siga as instruções enviadas para seu email.',
                'category' => 'Conta',
                'helpful_count' => 40
            ],
            [
                'id' => 7,
                'question' => 'Como configurar notificações por email?',
                'answer' => 'Vá em "Meu Perfil" > "Notificações" e configure quais eventos você deseja receber por email.',
                'category' => 'Configurações',
                'helpful_count' => 12
            ],
            [
                'id' => 8,
                'question' => 'Como cancelar uma subscrição?',
                'answer' => 'Acesse "Configurações" > "Subscrição" > "Cancelar Subscrição". Confirme a ação.',
                'category' => 'Subscrição',
                'helpful_count' => 8
            ]
        ];
    }
}

