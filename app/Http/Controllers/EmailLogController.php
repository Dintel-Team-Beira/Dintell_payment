<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use App\Models\Subscription;
use App\Models\Client;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    public function index(Request $request)
    {
        $query = EmailLog::with(['subscription', 'client'])
                        ->latest();

        // Filtros
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('email')) {
            $query->where('to_email', 'like', '%'.$request->email.'%');
        }

        $emailLogs = $query->paginate(25);

        return view('email-logs.index', compact('emailLogs'));
    }

    public function show(EmailLog $emailLog)
    {
        return view('email-logs.show', compact('emailLog'));
    }

    public function resend(EmailLog $emailLog)
    {
        if ($emailLog->status === 'sent') {
            return back()->with('warning', 'Este e-mail já foi enviado com sucesso.');
        }

        // Aqui você implementaria a lógica para reenviar o e-mail
        // Exemplo simplificado:
        try {
            // Lógica de envio de e-mail...
            $emailLog->markAsSent();
            return back()->with('success', 'E-mail reenviado com sucesso.');
        } catch (\Exception $e) {
            $emailLog->markAsFailed($e->getMessage());
            return back()->with('error', 'Falha ao reenviar e-mail: '.$e->getMessage());
        }
    }

    public function forSubscription(Subscription $subscription)
    {
        $emailLogs = $subscription->emailLogs()
                                ->latest()
                                ->paginate(15);

        return view('email-logs.index', [
            'emailLogs' => $emailLogs,
            'subscription' => $subscription
        ]);
    }

    public function forClient(Client $client)
    {
        $emailLogs = $client->emailLogs()
                          ->latest()
                          ->paginate(15);

        return view('email-logs.index', [
            'emailLogs' => $emailLogs,
            'client' => $client
        ]);
    }
}