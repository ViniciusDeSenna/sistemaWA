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

        .sector-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 25px;
            padding: 8px;
            background-color: #dbe4ff;
            border-radius: 8px;
            border: 2px solid #4C73FF;
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

        .sector-summary, .company-summary {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            background-color: #4C73FF;
            color: white;
            border-radius: 8px;
            margin-top: 10px;
        }

        .company-summary {
            background-color: #28a745;
            border: 2px solid #155d27;
        }

        /* Ajuste para remoção de artefato entre título e setor */
        .company-break {
            page-break-after: always;
        }

        .sector-title, .company-summary {
            margin-top: 0;
        }
    </style>
</head>
<body>
    <h1>Relatório de Diária</h1>
    @php($totalGeralDiarias = 0)
    @php($totalGeralMinutos = 0)

    @foreach($dailyRate as $collaboratorId => $rates)

        @php($companyName = $rates[0]['company_name'] ?? 'Não informado')

        <div class="info">
            {{ $companyName }}
        </div>

        @php($groupedBySector = $rates->groupBy('section_name'))

        @php($totalDiariasEmpresa = 0)
        @php($totalMinutosEmpresa = 0)

        @foreach($groupedBySector as $sectorName => $sectorRates)
            @php($isHourly = $sectorRates->whereNotNull('end')->isNotEmpty())

            <div class="table-container">
                <div class="sector-title">{{ $sectorName }}</div>
                <table>
                    <thead>
                        <tr>
                            <th>Nome do Colaborador</th>
                            <th>Data de Início</th>
                            @if($isHourly)
                                <th>Data de Saída</th>
                                <th>Tempo Total</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php($totalForSectorDiarias = 0)
                        @php($totalForSectorMinutos = 0)

                        @foreach($sectorRates as $rate)
                            @if($isHourly)
                                @php($minutos = \Carbon\Carbon::parse($rate->start)->diffInMinutes(\Carbon\Carbon::parse($rate->end)))
                                @php($totalForSectorMinutos += $minutos)
                                <tr>
                                    <td>{{ $rate->collaborators_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($rate->start)->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($rate->end)->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ floor($minutos / 60) }}:{{ sprintf('%02d', $minutos % 60) }}</td>
                                </tr>
                            @else
                                @php($totalForSectorDiarias += 1)
                                <tr>
                                    <td>{{ $rate->collaborators_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($rate->start)->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
                <div class="sector-summary">
                    @if($isHourly)
                        Total de Horas no Setor {{ $sectorName }}:
                        {{ floor($totalForSectorMinutos / 60) }}:{{ sprintf('%02d', $totalForSectorMinutos % 60) }}
                    @else
                        Total de Diárias no Setor {{ $sectorName }}: {{ $totalForSectorDiarias }}
                    @endif
                </div>

                @php($totalDiariasEmpresa += $totalForSectorDiarias)
                @php($totalMinutosEmpresa += $totalForSectorMinutos)
            </div>
        @endforeach

        <div class="company-summary">
            @if($totalMinutosEmpresa > 0)
                <p>Total de Horas na Empresa {{ $companyName }}:
                    {{ floor($totalMinutosEmpresa / 60) }}:{{ sprintf('%02d', $totalMinutosEmpresa % 60) }}
                </p>
            @endif
            @if($totalDiariasEmpresa > 0)
                <p>Total de Diárias na Empresa {{ $companyName }}: {{ $totalDiariasEmpresa }}</p>
            @endif
        </div>

        @php($totalGeralDiarias += $totalDiariasEmpresa)
        @php($totalGeralMinutos += $totalMinutosEmpresa)

        @if(!$loop->last)
            <div class="company-break"></div>
        @endif
    @endforeach

    <div class="footer">
        @if($totalGeralMinutos > 0)
            <p><strong>Total Geral de Horas: </strong>
                {{ floor($totalGeralMinutos / 60) }}:{{ sprintf('%02d', $totalGeralMinutos % 60) }}
            </p>
        @endif
        @if($totalGeralDiarias > 0)
            <p><strong>Total Geral de Diárias: </strong> {{ $totalGeralDiarias }}</p>
        @endif
    </div>

</body>
</html>