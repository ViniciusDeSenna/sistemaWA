<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Financeiro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 20px;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            color: #4C73FF;
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .info {
            margin-bottom: 10px;
            padding: 15px;
            background: #e9ecef;
            border-radius: 8px;
            border: 2px solid #4C73FF;
        }

        .info p {
            margin: 5px 0;
            font-size: 12px;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            border-radius: 8px;
            border: 2px solid #4C73FF;
        }

        th, td {
            padding: 12px;
            text-align: center;
            font-size: 12px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #4C73FF;
            color: white;
            text-transform: uppercase;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e1e1e1;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 12px;
            color: #555;
        }
    </style>
</head>
<body>
    <h1>Relatório Financeiro</h1>

    @php($total = 0)

    @foreach($dailyRate as $collaboratorId => $rates)

        @php($collaboratorName = $rates[0]['collaborators_name'] ?? 'Não Informado')
        @php($totalForCollaborator = 0)
    
        <div class="info">
            <p><strong>Colaborador:</strong> {{ $collaboratorName }}</p>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Estabelecimento</th>
                        <th>Início</th>
                        <th>Fim</th>
                        <th>Tempo Total</th>
                        <th>Valor da Diária</th>
                        <th>Custo</th>
                        <th>Acréscimo</th>
                        <th>Participação do Colaborador</th>
                        <th>Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rates as $rate)

                        @php($totalForCollaborator += $rate->total)
                        @php($total += $rate->total)

                        <tr>
                            <td>{{ mb_strimwidth($rate->companies_name ?? 'Não Informado', 0, 30, '...') }}</td>
                            <td>{{ isset($rate->start) ? Carbon\Carbon::parse($rate->start)->format('d/m/Y H:i:s') : '--/--/-- --:--:--' }}</td>
                            <td>{{ isset($rate->end) ? Carbon\Carbon::parse($rate->end)->format('d/m/Y H:i:s') : '--/--/-- --:--:--' }}</td>
                            <td>{{ $rate->total_time }}</td>
                            <td>{{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($rate->hourly_rate * App\BlueUtils\Time::convertTimeToDecimal($rate->total_time) ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}</td>
                            <td>{{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($rate->costs ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}</td>
                            <td>{{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($rate->addition ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}</td>
                            <td>{{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($rate->collaborator_participation ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}</td>
                            <td>{{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($rate->total ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="footer">
            <p><strong>Total ({{ $collaboratorName }}):</strong> {{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($totalForCollaborator ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}</p>
        </div>

    @endforeach
    
    <div class="footer">
        <p><strong>Total Geral:</strong> {{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($total ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}</p>
    </div>

    <div class="footer">
        <p>Gerado em: {{ date('d/m/Y') }}</p>
    </div>
</body>
</html>
