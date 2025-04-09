<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Diária</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9fafb;
            color: #1e293b;
        }

        h1, h2 {
            text-align: center;
            color: #1e293b;
            margin-bottom: 20px;
            font-size: 22px;
        }
        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            background-color: white;
            border: 3px solid #1e293b; /* Borda externa grossa e escura */
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border-radius: 6px;
            overflow: hidden;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #cbd5e1; /* Cor intermediária nas bordas internas */
            font-size: 13px;
        }

        th {
            background-color: #3b82f6;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f1f5f9;
        }
        .company-header {
            background-color:rgb(136, 161, 201);
            color: white;
            font-weight: bold;
        }

        .total-footer {
            background-color: #e2e8f0; 
            color: #1e40af;            
            font-weight: bold;
            font-size: 13px;
            border-top: 2px solid #1e293b;
        }

        .total {
            font-weight: bold;
            color:rgb(255, 255, 255);
            font-size: 16px;
        }

        .commission-table th, .commission-table td {
            padding: 10px;
            border: 1px solid #e2e8f0;
        }

        .commission-table th {
            background-color: #3b82f6;
            color: white;
        }

        .commission-table tr:nth-child(even) {
            background-color: #f1f5f9;
        }

        .table-container {
            margin-top: 10px;
        }

        .collaborator-row td {
            font-size: 12px;
        }

        .disabled-field {
            color: #64748b;
            background-color: #cbd5e1;
        }

        .pay-amount {
            color: #16a34a;
            font-weight: bold;
        }

        .section-name {
            color: #1e293b;
            font-weight: bold;
        }

    </style>
</head>
<body>
    <h1>Relatório de Diária</h1>

    @php($total = 0)
    <table>
        <thead>
            <tr>
                <th>Setor</th>
                <th>Início</th>
                <th>Fim</th>
                <th>Tempo Total</th>
                <th>Valor</th>
            </tr>
        </thead>
        
        @foreach ($finalData as $company)
            <tr class="company-header">
                <th class="company-header" colspan="5" style="text-align: center; font-size: 18px;">{{ $company["company_name"] }}</th> 
            </tr>

            @foreach ($company["collaborators"] as $collaborator)
                <tr>
                    <th colspan="5" style="text-align: center; font-size: 16px;">{{ $collaborator["collaborator_name"] }}</th>
                </tr>

                <tbody>
                    @foreach ($collaborator["sections"] as $section)
                        @foreach ($section["daily_rates"] as $daily_rate)
                            <tr class="collaborator-row">
                                <td class="section-name">
                                    {{ mb_strimwidth($section["section_name"] ?? 'Não Informado', 0, 20, '...') }}
                                </td>

                                <td>
                                    {{ isset($daily_rate["start"]) ? Carbon\Carbon::parse($daily_rate["start"])->format('d/m/Y H:i:s') : '--/--/-- --:--:--' }}
                                </td>

                                <td class="{{ isset($daily_rate['end']) ? '' : 'disabled-field' }}">
                                    {{ isset($daily_rate["end"]) ? Carbon\Carbon::parse($daily_rate["end"])->format('d/m/Y H:i:s') : '--/--/-- --:--:--' }}
                                </td>

                                <td class="{{ isset($daily_rate['total_time']) ? '' : 'disabled-field' }}">
                                    {{ $daily_rate["total_time"] ?? '--:--:--' }}
                                </td>

                                <td class="pay-amount">
                                    {{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($daily_rate["pay_amount"] ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>

                <tfoot>
                    <tr class="total-footer">
                        <th class="total-footer" colspan="4">Pix: {{ $collaborator["pix_key"] }}</th>
                        <th class="total-footer" colspan="1">Valor total: {{ $collaborator["total_pay"] }}</th>
                    </tr>
                </tfoot>

                @php($total += $collaborator["total_pay"])
            @endforeach
        @endforeach
    </table>

    <p class="total">Valor total: {{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($total ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}</p>

    <h2 class="total">Comissão dos Líderes</h2>

    <div class="table-container">
        <table class="commission-table">
            <thead>
                <tr>
                    <th>Líder</th>
                    <th>Comissão</th>
                    <th>Chave Pix</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($leaderCommissions as $item)
                    <tr>
                        <td>{{ mb_strimwidth($item->leader_name ?? 'Não Informado', 0, 20, '...') }}</td>
                        <td>{{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($item->total_leader_comission ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}</td>
                        <td>{{ $item->leader_pix_key ?? 'Não Informado' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
