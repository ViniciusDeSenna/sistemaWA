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
            margin-bottom: 15px;
            padding: 15px;
            background: #e9ecef;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 2px solid #4C73FF; /* Borda externa mais destacada */
        }

        .info p {
            margin: 5px 0;
            font-size: 14px;
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
            border: 2px solid #4C73FF; /* Borda externa mais destacada */
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
            letter-spacing: 1px;
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
            font-size: 14px;
            color: #555;
        }

        .collaborator-row {
            background-color: #d1e3f1; /* Cor padronizada para o fundo do nome do colaborador */
            font-weight: bold;
            font-size: 16px;
        }

        .collaborator-summary {
            background-color: #f0f8ff;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            border-top: 2px solid #4C73FF; /* Borda superior mais destacada */
        }

        .collaborator-summary strong {
            font-size: 18px;
            color: #333;
        }
    </style>
</head>
<body>
    <h1>Relatório de Diária</h1>

    @php($total = 0)
    @foreach($dailyRate as $collaboratorId => $rates)
        
        @php($companyName = $rates[0]['company_name'] ?? 'Não informado')
        
        <div class="info">
            <p><strong>Estabelecimento: </strong> {{ $companyName }}</p>
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
                    @php($groupedByCollaborator = $rates->groupBy('collaborator_id'))
                    @foreach($groupedByCollaborator as $collaboratorId => $Sections)
                        @php($collaboratorName = $Sections->first()->collaborators_name ?? 'Não Informado')
                        @php($collaboratorPixKey = $Sections->first()->pix_key ?? 'Não Informado')
                        @php($totalForCollaborator = 0)

                        <!-- Collaborator Name Row -->
                        <tr class="collaborator-row">
                            <td colspan="3">
                                <strong>{{ $collaboratorName }}</strong>
                            </td>
                        </tr>

                        @foreach ($Sections as $rate)
                            @php($totalForCollaborator += $rate->pay_amount)
                            @php($total += $rate->pay_amount)

                            <tr>
                                <td>{{ mb_strimwidth($rate->section_name ?? 'Não Informado', 0, 20, '...') }}</td>
                                <td>{{ isset($rate->start) ? Carbon\Carbon::parse($rate->start)->format('d/m/Y H:i:s') : '--/--/-- --:--:--' }}</td>
                                <td>{{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($rate->pay_amount ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}</td>
                            </tr>
                        @endforeach

                        <!-- Collaborator Summary Row -->
                        <tr class="collaborator-summary">
                            <td colspan="3">
                                <strong>Chave Pix:</strong> {{ $collaboratorPixKey }} - <strong>{{ $collaboratorName }}</strong> - 
                                <strong>Total a Pagar:</strong> 
                                {{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($totalForCollaborator ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}
                            </td>
                        </tr>

                        <tr>
                            <th colspan="3"></th>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @endforeach
    
    <div class="footer">
        <p>Total Geral: {{ $user->can('Visualizar e inserir informações financeiras nas diárias') ? App\BlueUtils\Money::format($total ?? '0', 'R$ ', 2, ',', '.') : 'R$ --,--' }}</p>
    </div>

    <div class="footer">
        <p>Gerado em: {{ date('d/m/Y') }}</p>
    </div>
</body>
</html>
