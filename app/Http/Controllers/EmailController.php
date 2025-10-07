<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailLog;
use App\Models\Subscription;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
class EmailController extends Controller
{
    //
    /**
     * Display a listing of email logs
     */
    public function index(Request $request)
    {
        $query = EmailLog::with(['subscription', 'client']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('to_email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('client', function($clientQuery) use ($search) {
                      $clientQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('subscription', function($subQuery) use ($search) {
                      $subQuery->where('domain', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Subscription filter
        if ($request->filled('subscription')) {
            $query->where('subscription_id', $request->subscription);
        }

        $emailLogs = $query->latest()->paginate(20)->withQueryString();

        // Get filter options
        $subscriptions = Subscription::select('id', 'domain')->orderBy('domain')->get();
        $emailTypes = EmailLog::select('type')->distinct()->orderBy('type')->pluck('type');

        // Statistics
        $stats = $this->getEmailStats();

        return view('email_logs.index', compact('emailLogs', 'subscriptions', 'emailTypes', 'stats'));
    }

    /**
     * Display the specified email log
     */
    public function show(string $tenant, EmailLog $emailLog)
    {
        $emailLog->load(['subscription.plan', 'client']);

        return view('email_logs.show', compact('emailLog'));
    }

    /**
     * Resend a failed email
     */
    public function resend(string $tenant, EmailLog $emailLog)
    {
        if ($emailLog->status !== 'failed') {
            return back()->withErrors(['error' => 'Apenas emails com falha podem ser reenviados.']);
        }

        try {
            // Reset status to queued for resend
            $emailLog->update([
                'status' => 'queued',
                'error_message' => null
            ]);

            // Here you would trigger your notification system again
            // This depends on your notification implementation
            $this->triggerNotificationResend($emailLog);

            return back()->with('success', 'Email reenviado com sucesso!');

        } catch (\Exception $e) {
            $emailLog->markAsFailed($e->getMessage());
            return back()->withErrors(['error' => 'Erro ao reenviar email: ' . $e->getMessage()]);
        }
    }

    /**
     * Bulk resend failed emails
     */
    public function bulkResend(Request $request)
    {
        $validated = $request->validate([
            'email_ids' => 'required|array',
            'email_ids.*' => 'exists:email_logs,id'
        ]);

        $failedEmails = EmailLog::whereIn('id', $validated['email_ids'])
                               ->where('status', 'failed')
                               ->get();

        $resent = 0;
        $errors = [];

        foreach ($failedEmails as $emailLog) {
            try {
                $emailLog->update([
                    'status' => 'queued',
                    'error_message' => null
                ]);

                $this->triggerNotificationResend($emailLog);
                $resent++;

            } catch (\Exception $e) {
                $emailLog->markAsFailed($e->getMessage());
                $errors[] = "Erro no email {$emailLog->id}: " . $e->getMessage();
            }
        }

        $message = "Reenviados {$resent} emails com sucesso";
        if (!empty($errors)) {
            $message .= ". Erros: " . implode(', ', $errors);
        }

        return back()->with('success', $message);
    }

    /**
     * Delete old email logs
     */
    public function cleanup(Request $request)
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $deleted = EmailLog::where('created_at', '<', now()->subDays($validated['days']))->delete();

        return back()->with('success', "Removidos {$deleted} registros de email antigos.");
    }

    /**
     * Export email logs
     */
    public function export(Request $request)
    {
        $query = EmailLog::with(['subscription', 'client']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $emailLogs = $query->latest()->get();

        $csvData = [];
        $csvData[] = ['ID', 'Data', 'Destinatário', 'Assunto', 'Tipo', 'Status', 'Cliente', 'Domínio', 'Erro'];

        foreach ($emailLogs as $log) {
            $csvData[] = [
                $log->id,
                $log->created_at->format('d/m/Y H:i'),
                $log->to_email,
                $log->subject,
                $log->type,
                $log->status,
                $log->client->name ?? 'N/A',
                $log->subscription->domain ?? 'N/A',
                $log->error_message ?? ''
            ];
        }

        $filename = 'email_logs_' . now()->format('Y-m-d_H-i') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get email statistics
     */
    private function getEmailStats()
    {
        return [
            'total' => EmailLog::count(),
            'sent' => EmailLog::sent()->count(),
            'failed' => EmailLog::failed()->count(),
            'queued' => EmailLog::queued()->count(),
            'today' => EmailLog::whereDate('created_at', today())->count(),
            'this_week' => EmailLog::where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => EmailLog::where('created_at', '>=', now()->startOfMonth())->count(),
            'success_rate' => $this->calculateSuccessRate(),
            'by_type' => EmailLog::select('type', DB::raw('count(*) as count'))
                                 ->groupBy('type')
                                 ->orderBy('count', 'desc')
                                 ->get(),
            'recent_failures' => EmailLog::failed()
                                        ->with(['subscription', 'client'])
                                        ->latest()
                                        ->limit(5)
                                        ->get()
        ];
    }

    /**
     * Calculate email success rate
     */
    private function calculateSuccessRate()
    {
        $total = EmailLog::count();
        if ($total === 0) return 100;

        $sent = EmailLog::sent()->count();
        return round(($sent / $total) * 100, 1);
    }

    /**
     * Trigger notification resend
     */
    private function triggerNotificationResend(EmailLog $emailLog)
    {
        if (!$emailLog->subscription || !$emailLog->client) {
            throw new \Exception('Subscription or client not found for email log');
        }

        switch ($emailLog->type) {
            case 'suspended':
                $emailLog->client->notify(new \App\Notifications\SubscriptionSuspendedNotification($emailLog->subscription));
                break;
            case 'activated':
                $emailLog->client->notify(new \App\Notifications\SubscriptionActivatedNotification($emailLog->subscription));
                break;
            case 'expiring':
                $emailLog->client->notify(new \App\Notifications\SubscriptionExpiringNotification($emailLog->subscription));
                break;
            case 'payment':
                $emailLog->client->notify(new \App\Notifications\PaymentReceivedNotification($emailLog->subscription, $emailLog->subscription->amount_paid));
                break;
            case 'renewed':
                $emailLog->client->notify(new \App\Notifications\SubscriptionRenewedNotification($emailLog->subscription));
                break;
            default:
                throw new \Exception('Unknown email type for resend: ' . $emailLog->type);
        }
    }


    /**
     * API endpoint for email statistics
     */
    public function apiStats()
    {
        $stats = $this->getEmailStats();
        return response()->json($stats);
    }

    /**
     * Test email functionality
     */
    public function test(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:test,suspended,activated,expiring,payment'
        ]);

        try {
            // Create a test email log
            $emailLog = EmailLog::create([
                'subscription_id' => null,
                'client_id' => null,
                'to_email' => $validated['email'],
                'subject' => 'Email de Teste - ' . config('app.name'),
                'type' => 'test',
                'content' => 'Este é um email de teste do sistema.',
                'status' => 'queued'
            ]);

            // Send test email (implement your mail sending logic here)
            // Mail::to($validated['email'])->send(new TestMail());

            $emailLog->markAsSent();

            return response()->json([
                'success' => true,
                'message' => 'Email de teste enviado com sucesso!'
            ]);

        } catch (\Exception $e) {
            if (isset($emailLog)) {
                $emailLog->markAsFailed($e->getMessage());
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar email de teste: ' . $e->getMessage()
            ], 500);
        }
    }
        /**
     * Display a listing of email logs
     */
    public function logs(Request $request)
    {
        $query = EmailLog::with(['subscription', 'client']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('to_email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('client', function($clientQuery) use ($search) {
                      $clientQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('subscription', function($subQuery) use ($search) {
                      $subQuery->where('domain', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Subscription filter
        if ($request->filled('subscription')) {
            $query->where('subscription_id', $request->subscription);
        }

        $emailLogs = $query->latest()->paginate(20)->withQueryString();

        // Get filter options
        $subscriptions = Subscription::select('id', 'domain')->orderBy('domain')->get();
        $emailTypes = EmailLog::select('type')->distinct()->orderBy('type')->pluck('type');

        // Statistics
        $stats = $this->getEmailStats();

        return view('email_logs.index', compact('emailLogs', 'subscriptions', 'emailTypes', 'stats'));

}
}