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
    <h1>Relatório de Diária</h1>

    @php($total = 0)

    @foreach($dailyRate as $collaboratorId => $rates)

        @php($collaboratorName = $rates[0]['collaborators_name'])
        @php($totalCollaborator = 0)
    
        <div class="info">
            <p><strong>Colaborador:</strong> {{ $collaboratorName }}</p>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Estabelecimento</th>
                        <th>Início</th>
                        <th>Início Intervalo</th>
                        <th>Fim Intervalo</th>
                        <th>Fim</th>
                        <th>Tempo Total</th>
                        <th>Valor da Diária</th>
                        <th>Custo</th>
                        <th>Acréscimos</th>
                        <th>Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rates as $rate)

                        @php($totalCollaborator += $rate->daily_rate_total)
                        @php($total += $rate->daily_rate_total)

                        <tr>
                            <td>{{ mb_strimwidth($rate->companies_name ?? '', 0, 20, '...') }}</td>
                            <td>{{ isset($rate->daily_rate_start) ? Carbon\Carbon::parse($rate->daily_rate_start)->format('d/m/Y H:i:s') : '--/--/-- --:--:--' }}</td>
                            <td>{{ isset($rate->daily_rate_start_interval) ? Carbon\Carbon::parse($rate->daily_rate_start_interval)->format('d/m/Y H:i:s') : '--/--/-- --:--:--' }}</td>
                            <td>{{ isset($rate->daily_rate_end_interval) ? Carbon\Carbon::parse($rate->daily_rate_end_interval)->format('d/m/Y H:i:s') : '--/--/-- --:--:--' }}</td>
                            <td>{{ isset($rate->daily_rate_end) ? Carbon\Carbon::parse($rate->daily_rate_end)->format('d/m/Y H:i:s') : '--/--/-- --:--:--' }}</td>
                            <td>{{ $rate->daily_rate_daily_total_time }}</td>
                            <td>{{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($rate->daily_rate_hourly_rate ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}</td>
                            <td>{{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($rate->daily_rate_addition ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}</td>
                            <td>{{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($rate->daily_rate_costs ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}</td>
                            <td>{{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($rate->daily_rate_total ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="footer">
            <p>Total ({{ $collaboratorName }}) {{ App\BlueUtils\Money::format($totalCollaborator ?? '0', 'R$ ', 2, ',', '.') }}</p>
        </div>

    @endforeach
    
    <div class="footer">
        <p>Total {{ App\BlueUtils\Money::format($total ?? '0', 'R$ ', 2, ',', '.') }}</p>
    </div>

    <div class="footer">
        <p>Gerado em: 06/03/2025</p>
    </div>
</body>
</html>
