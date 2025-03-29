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
            margin-bottom: 20px;
            padding: 15px;
            background: #e9ecef;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #4C73FF;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: center;
            font-size: 14px;
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

        .collaborator-row {
            background-color: #d1e3f1;
            font-weight: bold;
            font-size: 16px;
        }

        .collaborator-summary {
            background-color: #f0f8ff;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            border-top: 2px solid #4C73FF;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
    <h1>Relatório de Diária</h1>

    @php($total = 0)
    @php($userCommissions = [])

    @foreach($dailyRate as $collaboratorId => $rates)
        <div class="info">
            <p><strong>Estabelecimento:</strong> {{ $rates[0]['company_name'] ?? 'Não informado' }}</p>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Setor trabalhado</th>
                        <th>Data</th>
                        <th>Quantia paga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rates->groupBy('collaborator_id') as $collaboratorId => $Sections)
                        @php
                            $collaboratorName = $Sections->first()->collaborators_name ?? 'Não Informado';
                            $collaboratorPixKey = $Sections->first()->pix_key ?? 'Não Informado';
                            $totalForCollaborator = 0;
                        @endphp

                        <tr class="collaborator-row">
                            <td colspan="3"><strong>{{ $collaboratorName }}</strong></td>
                        </tr>

                        @foreach ($Sections as $rate)
                            @php
                                $totalForCollaborator += $rate->pay_amount;
                                $total += $rate->pay_amount;
                            @endphp

                            <tr>
                                <td>{{ mb_strimwidth($rate->section_name ?? 'Não Informado', 0, 20, '...') }}</td>
                                <td>{{ isset($rate->start) ? Carbon\Carbon::parse($rate->start)->format('d/m/Y H:i:s') : '--/--/-- --:--:--' }}</td>
                                <td>{{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($rate->pay_amount ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}</td>
                            </tr>

                            @php
                                $userId = $rate->user_id;
                                if (!isset($userCommissions[$userId])) {
                                    $userCommissions[$userId] = [
                                        'user_name' => $rate->user_name ?? 'Desconhecido',
                                        'user_pix_key' => $rate->user_pix_key ?? 'Não Informado',
                                        'user_id' => $userId,
                                        'total_comission' => 0
                                    ];
                                }
                                $userCommissions[$userId]['total_comission'] += $rate->leader_comission;
                            @endphp
                        @endforeach

                        <tr class="collaborator-summary">
                            <td colspan="3">
                            <td>{{ App\BlueUtils\Money::format($user['total_comission'] ?? '0', 'R$ ', 2, ',', '.') }}</td>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <h2>Comissões</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Chave Pix</th>
                    <th>Total a Pagar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($userCommissions as $user)
                    <tr>
                        <td>{{ $user['user_name'] }}</td>
                        <td>{{ $user['user_pix_key'] }}</td>
                        <td>{{ App\BlueUtils\Money::format($user['total_comission'] ?? '0', 'R$ ', 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p><strong>Total Geral:</strong> {{ App\BlueUtils\Money::format($total, 'R$ ', 2, ',', '.') }}</p>
        <p>Gerado em: {{ date('d/m/Y') }}</p>
    </div>
</body>
</html>
