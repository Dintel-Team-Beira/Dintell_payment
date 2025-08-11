<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Support\Facades\Auth;

class AdminSupportController extends Controller
{
    /**
     * Display support tickets dashboard.
     */
    public function tickets(Request $request)
    {
        $query = SupportTicket::with(['user', 'replies'])
            ->latest();

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

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function ($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                               ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $tickets = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'open')->count(),
            'pending' => SupportTicket::where('status', 'pending')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
            'closed' => SupportTicket::where('status', 'closed')->count(),
            'avg_response_time' => $this->getAverageResponseTime(),
            'satisfaction_rate' => $this->getSatisfactionRate()
        ];

        return view('admin.support.tickets', compact('tickets', 'stats'));
    }

    /**
     * Show specific ticket.
     */
    public function showTicket(SupportTicket $ticket)
    {
        $ticket->load(['user', 'replies.user', 'attachments']);

        // Marcar como visualizado pelo admin
