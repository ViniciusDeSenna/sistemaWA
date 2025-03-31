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
            padding: 0;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            color: #696cff;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #696cff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .table-container {
            margin-top: 10px;
        }

        .collaborator-row td {
            font-size: 14px;
        }

        tfoot {
            font-weight: bold;
            background-color: #f4f4f4;
        }

        tfoot th {
            background-color: #696cff;
            color: white;
        }

        .total {
            font-weight: bold;
            color: #696cff;
        }

        .commission-table th, .commission-table td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        .commission-table th {
            background-color: #696cff;
            color: white;
        }

        .commission-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <h1>Relatório de Diária</h1>

    @php($total = 0)
    <table>
        @foreach ($finalData as $company)
            <thead>
                <tr>
                    <th colspan="3" style="text-align: center; font-size: 18px;">{{ $company["company_name"] }}</th>
                </tr>
            </thead>

            @foreach ($company["collaborators"] as $collaborator)
                <thead>
                    <tr>
                        <th colspan="3" style="text-align: center; font-size: 16px;">{{ $collaborator["collaborator_name"] }}</th>
                    </tr>
                </thead>

                <thead>
                    <tr>
                        <th>Setor</th>
                        <th>Data</th>
                        <th>Valor</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($collaborator["sections"] as $section)
                        @foreach ($section["daily_rates"] as $daily_rate)
                            <tr class="collaborator-row">
                                <td>{{ mb_strimwidth($section["section_name"] ?? 'Não Informado', 0, 20, '...') }}</td>
                                <td>{{ isset($daily_rate["start"]) ? Carbon\Carbon::parse($daily_rate["start"])->format('d/m/Y H:i:s') : '--/--/-- --:--:--' }}</td>
                                <td>{{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($daily_rate["pay_amount"] ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="2">Pix: {{ $collaborator["pix_key"] }}</th>
                        <th colspan="1">Valor total: {{ $collaborator["total_pay"] }}</th>
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
