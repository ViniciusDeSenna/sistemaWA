<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Diária</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 20px;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #696cff;
            font-size: 24px;
            text-transform: uppercase;
        }

        .info {
            margin-bottom: 5px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .info p {
            margin: 5px 0;
            font-size: 10px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            font-size: 10px;
        }

        th {
            background: #696cff;
            color: white;
            text-transform: uppercase;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
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
                            <td>{{ mb_strimwidth($rate->companies_name ?? 'Não Informado', 0, 20, '...') }}</td>
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
            <p>Total ({{ $collaboratorName }}) {{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($totalForCollaborator ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}</p>
        </div>

    @endforeach
    
    <div class="footer">
        <p>Total {{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($total ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}</p>
    </div>

    <div class="footer">
        <p>Gerado em: {{ date('d/m/Y') }}</p>
    </div>
</body>
</html>
