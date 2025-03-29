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
            border: 2px solid #4C73FF;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color: #222;
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
            border: 2px solid #4C73FF;
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

        .company-summary {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            background-color: #28a745;
            color: white;
            border-radius: 8px;
            margin-top: 10px;
            border: 2px solid #155d27;
        }
    </style>
</head>
<body>
    <h1>Relatório de Diária</h1>

    @php($totalGeral = 0)
    
    @foreach($dailyRate as $companyName => $rates)
        <div class="info">{{ $companyName }}</div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Colaborador</th>
                        <th>Setor</th>
                        <th>Data</th>
                        <th>Valor Pago</th>
                    </tr>
                </thead>
                <tbody>
                    @php($totalEmpresa = 0)
                    @foreach($rates as $rate)
                        @php($totalEmpresa += $rate->pay_amount)
                        @php($totalGeral += $rate->pay_amount)
                        <tr>
                            <td>{{ $rate->collaborators_name }}</td>
                            <td>{{ $rate->section_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($rate->start)->format('d/m/Y H:i:s') }}</td>
                            <td>{{ App\BlueUtils\Money::format($rate->pay_amount, 'R$ ', 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="company-summary">Total da Empresa: {{ App\BlueUtils\Money::format($totalEmpresa, 'R$ ', 2, ',', '.') }}</div>
    @endforeach

    <div class="footer">
        <p><strong>Total Geral:</strong> {{ App\BlueUtils\Money::format($totalGeral, 'R$ ', 2, ',', '.') }}</p>
        <p>Gerado em: {{ date('d/m/Y') }}</p>
    </div>
</body>
</html>
