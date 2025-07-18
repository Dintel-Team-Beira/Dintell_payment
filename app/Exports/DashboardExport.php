<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class DashboardExport implements WithMultipleSheets
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            'Resumo' => new SummarySheet($this->data),
            'Faturas' => new InvoicesSheet($this->data['invoices']),
            'Orçamentos' => new QuotesSheet($this->data['quotes']),
            'Clientes' => new ClientsSheet($this->data['clients']),
            'Performance' => new PerformanceSheet($this->data['performance']),
        ];
    }
}

class SummarySheet implements FromCollection, WithHeadings, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect([
            [
                'Período: ' . $this->data['period']['start'] . ' - ' . $this->data['period']['end'],
                '',
                '',
                ''
            ],
            [
                'Gerado em: ' . $this->data['generated_at'],
                '',
                '',
                ''
            ],
            ['', '', '', ''],
            ['FATURAS', '', '', ''],
            ['Total de Faturas', $this->data['invoices']['total_invoices'], '', ''],
            ['Valor Total', number_format($this->data['invoices']['total_value'], 2, ',', '.') . ' MT', '', ''],
            ['Faturas Pagas', $this->data['invoices']['paid_invoices'], '', ''],
            ['Valor Pago', number_format($this->data['invoices']['paid_value'], 2, ',', '.') . ' MT', '', ''],
            ['Taxa de Pagamento', $this->data['invoices']['payment_rate'] . '%', '', ''],
            ['', '', '', ''],
            ['ORÇAMENTOS', '', '', ''],
            ['Total de Orçamentos', $this->data['quotes']['total_quotes'], '', ''],
            ['Valor Total', number_format($this->data['quotes']['total_value'], 2, ',', '.') . ' MT', '', ''],
            ['Orçamentos Aceitos', $this->data['quotes']['accepted_quotes'], '', ''],
            ['Taxa de Conversão', $this->data['quotes']['conversion_rate'] . '%', '', ''],
            ['', '', '', ''],
            ['CLIENTES', '', '', ''],
            ['Total de Clientes', $this->data['clients']['total_clients'], '', ''],
            ['Clientes Ativos', $this->data['clients']['active_clients'], '', ''],
            ['Novos Clientes', $this->data['clients']['new_clients'], '', ''],
            ['Taxa de Retenção', $this->data['clients']['client_retention_rate'] . '%', '', ''],
        ]);
    }

    public function headings(): array
    {
        return ['Métrica', 'Valor', '', ''];
    }

    public function title(): string
    {
        return 'Resumo';
    }
}

class InvoicesSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['recent_invoices'])->map(function($invoice) {
            return [
                $invoice['invoice_number'],
                $invoice['client_name'],
                number_format($invoice['total'], 2, ',', '.'),
                $invoice['status'],
                $invoice['created_at']
            ];
        });
    }

    public function headings(): array
    {
        return ['Número', 'Cliente', 'Valor (MT)', 'Status', 'Data'];
    }

    public function title(): string
    {
        return 'Faturas';
    }
}

class QuotesSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['recent_quotes'])->map(function($quote) {
            return [
                $quote['quote_number'],
                $quote['client_name'],
                number_format($quote['total'], 2, ',', '.'),
                $quote['status'],
                $quote['created_at']
            ];
        });
    }

    public function headings(): array
    {
        return ['Número', 'Cliente', 'Valor (MT)', 'Status', 'Data'];
    }

    public function title(): string
    {
        return 'Orçamentos';
    }
}

class ClientsSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['top_clients'])->map(function($client) {
            return [
                $client['name'],
                $client['email'],
                $client['total_invoices'],
                number_format($client['total_value'], 2, ',', '.'),
                $client['last_invoice'] ?? 'N/A'
            ];
        });
    }

    public function headings(): array
    {
        return ['Nome', 'Email', 'Faturas', 'Valor Total (MT)', 'Última Fatura'];
    }

    public function title(): string
    {
        return 'Clientes';
    }
}

class PerformanceSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $collection = collect([
            ['Receita Atual', number_format($this->data['current_revenue'], 2, ',', '.') . ' MT'],
            ['Receita Anterior', number_format($this->data['previous_revenue'], 2, ',', '.') . ' MT'],
            ['Crescimento da Receita', $this->data['revenue_growth'] . '%'],
            ['Faturas Atuais', $this->data['current_invoices']],
            ['Faturas Anteriores', $this->data['previous_invoices']],
            ['Crescimento de Faturas', $this->data['invoice_growth'] . '%'],
            ['Média de Dias para Pagamento', $this->data['average_days_to_payment']],
            ['', ''],
            ['TENDÊNCIA MENSAL', ''],
        ]);

        foreach ($this->data['monthly_trend'] as $month) {
            $collection->push([
                $month['month'],
                number_format($month['revenue'], 2, ',', '.') . ' MT (' . $month['invoices'] . ' faturas)'
            ]);
        }

        return $collection;
    }

    public function headings(): array
    {
        return ['Métrica', 'Valor'];
    }

    public function title(): string
    {
        return 'Performance';
    }
}
