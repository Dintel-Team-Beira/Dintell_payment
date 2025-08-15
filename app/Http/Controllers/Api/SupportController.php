<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    /**
     * Lista todos os tickets do usuário autenticado
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();

            $tickets = SupportTicket::where('user_id', $user->id)
                ->orWhere('company_id', $user->company_id)
                ->with(['user', 'replies'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($ticket) {
                    return [
                        'id' => $ticket->id,
                        'ticket_number' => $ticket->ticket_number,
                        'subject' => $ticket->subject,
                        'description' => $ticket->description,
                        'status' => $ticket->status,
                        'priority' => $ticket->priority,
                        'category' => $ticket->category,
                        'created_at' => $ticket->created_at->format('d/m/Y H:i'),
                        'updated_at' => $ticket->updated_at->format('d/m/Y H:i'),
                        'replies_count' => $ticket->replies->count(),
                        'last_reply' => $ticket->replies->last() ? [
                            'message' => Str::limit($ticket->replies->last()->message, 100),
                            'created_at' => $ticket->replies->last()->created_at->diffForHumans()
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'tickets' => $tickets
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao listar tickets: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Erro ao carregar tickets'
            ], 500);
        }
    }

    /**
     * Mostra um ticket específico com suas mensagens
     */
    public function show($ticketId)
    {
        try {
            $user = auth()->user();

            $ticket = SupportTicket::where('id', $ticketId)
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->orWhere('company_id', $user->company_id);
                })
                ->with(['user', 'replies.user'])
                ->first();

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ticket não encontrado'
                ], 404);
            }

            // Formatar dados do ticket
            $ticketData = [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'description' => $ticket->description,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'category' => $ticket->category,
                'created_at' => $ticket->created_at->format('d/m/Y H:i'),
                'updated_at' => $ticket->updated_at->format('d/m/Y H:i'),
                'user' => [
                    'name' => $ticket->user->name,
                    'email' => $ticket->user->email,
                    'avatar' => $ticket->user->avatar ?? null
                ],
                'messages' => $ticket->replies->map(function ($reply) {
                    return [
                        'id' => $reply->id,
                        'message' => $reply->message,
                        'created_at' => $reply->created_at->format('d/m/Y H:i'),
                        'user' => [
                            'name' => $reply->user->name,
                            'avatar' => $reply->user->avatar ?? null,
                            'is_admin' => $reply->user->is_super_admin ?? false
                        ],
                        'is_internal' => $reply->is_internal,
                        'is_status_change' => $reply->is_status_change,
                        'metadata' => $reply->metadata
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'ticket' => $ticketData
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao carregar ticket: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Erro ao carregar ticket'
            ], 500);
        }
    }

    /**
     * Cria um novo ticket de suporte
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:technical,billing,general,feature_request',
            'priority' => 'required|in:low,medium,high,urgent'
        ]);

        try {
            DB::beginTransaction();

            $user = auth()->user();

            $ticket = SupportTicket::create([
                'ticket_number' => $this->generateTicketNumber(),
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'subject' => $request->subject,
                'description' => $request->description,
                'category' => $request->category,
                'priority' => $request->priority,
                'status' => 'open'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ticket criado com sucesso!',
                'ticket' => [
                    'id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'subject' => $ticket->subject,
                    'status' => $ticket->status
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar ticket: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Erro ao criar ticket'
            ], 500);
        }
    }

    /**
     * Envia uma mensagem para um ticket
     */
    public function sendMessage(Request $request, $ticketId)
    {
        $request->validate([
            'message' => 'required|string|min:1'
        ]);

        try {
            $user = auth()->user();

            $ticket = SupportTicket::where('id', $ticketId)
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->orWhere('company_id', $user->company_id);
                })
                ->first();

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ticket não encontrado'
                ], 404);
            }

            if ($ticket->status === 'closed') {
                return response()->json([
                    'success' => false,
                    'error' => 'Não é possível enviar mensagens para tickets fechados'
                ], 400);
            }

            DB::beginTransaction();

            $reply = TicketReply::create([
                'support_ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'message' => $request->message,
                'is_internal' => false
            ]);

            // Atualizar status do ticket se estiver resolvido
            if ($ticket->status === 'resolved') {
                $ticket->update(['status' => 'open']);
            }

            $ticket->touch(); // Atualizar updated_at

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mensagem enviada com sucesso!',
                'reply' => [
                    'id' => $reply->id,
                    'message' => $reply->message,
                    'created_at' => $reply->created_at->format('d/m/Y H:i'),
                    'user' => [
                        'name' => $user->name,
                        'avatar' => $user->avatar ?? null
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao enviar mensagem: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Erro ao enviar mensagem'
            ], 500);
        }
    }

    /**
     * Fecha um ticket
     */
    public function close($ticketId)
    {
        try {
            $user = auth()->user();

            $ticket = SupportTicket::where('id', $ticketId)
                ->where('user_id', $user->id)
                ->first();

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ticket não encontrado'
                ], 404);
            }

            $ticket->update(['status' => 'closed']);

            return response()->json([
                'success' => true,
                'message' => 'Ticket fechado com sucesso!'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao fechar ticket: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Erro ao fechar ticket'
            ], 500);
        }
    }

    /**
     * Avalia um ticket
     */
    public function rate(Request $request, $ticketId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:1000'
        ]);

        try {
            $user = auth()->user();

            $ticket = SupportTicket::where('id', $ticketId)
                ->where('user_id', $user->id)
                ->first();

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ticket não encontrado'
                ], 404);
            }

            $ticket->update([
                'satisfaction_rating' => $request->rating,
                'satisfaction_feedback' => $request->feedback
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Avaliação registrada com sucesso!'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao avaliar ticket: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Erro ao registrar avaliação'
            ], 500);
        }
    }

    /**
     * Gera um número único para o ticket
     */
    private function generateTicketNumber()
    {
        do {
            $number = 'TK-' . date('Y') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (SupportTicket::where('ticket_number', $number)->exists());

        return $number;
    }
}
