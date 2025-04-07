<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Extrato Financeiro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background-color: #fff;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #4C73FF;
        }

        .periodo {
            text-align: center;
            margin-bottom: 40px;
            font-size: 14px;
        }

        .dia {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }

        .dia-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            padding: 6px 10px;
            font-size: 13px;
            margin-left: 20px;
        }

        .ganho {
            color: #DAA520;
        }

        .custo:nth-child(even) {
            background-color: #f9f9f9;
        }

        .custo:nth-child(odd) {
            background-color: #efefef;
        }

        .custo {
            color: #C0392B;
        }

        .lucro {
            color: #27AE60;
            font-weight: bold;
            margin-top: 10px;
            margin-left: 20px;
        }

        .descricao {
            font-size: 11px;
            color: #666;
            margin-left: 20px;
        }

        .totais-dia {
            border: 1px solid #ccc;
            padding: 10px;
            margin-top: 15px;
            margin-left: 20px;
            border-radius: 6px;
            background-color: #f5f5f5;
        }

        .totais-dia .item {
            margin-left: 0;
        }

        .total-geral {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ccc;
            font-size: 14px;
        }

        .total-linha {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            padding: 3px 0;
        }

    </style>
</head>
<body>

<h1>Extrato Financeiro</h1>
<p class="periodo">Período: {{ $periodo }}</p>

@foreach ($dias as $dia)
    <div class="dia">
        <div class="dia-title">{{ $dia['data'] }}</div>

        @if (count($dia['itens']) === 1 && $dia['itens'][0]['nome'] === 'Sem movimentações')
            <p style="font-style: italic; color: #888; margin-left: 20px;">Nenhuma movimentação registrada.</p>
        @else
            @php
                $agrupados = [];
                $totalGanhoDia = 0;
                $totalCustoDia = 0;

                foreach ($dia['itens'] as $item) {
                    $nome = $item['nome'];
                    $valor = floatval(str_replace(['.', ','], ['', '.'], str_replace('R$', '', $item['valor'])));
                    $tipo = $item['tipo'] ?? '';

                    $agrupados[$tipo][$nome] = ($agrupados[$tipo][$nome] ?? 0) + $valor;

                    if ($tipo === 'ganho') $totalGanhoDia += $valor;
                    if ($tipo === 'custo') $totalCustoDia += $valor;
                }

                $lucroDia = $totalGanhoDia - $totalCustoDia;
            @endphp

            @foreach (['ganho', 'custo'] as $tipo)
                @if (!empty($agrupados[$tipo]))
                    @php $index = 0; @endphp
                    @foreach ($agrupados[$tipo] as $nome => $valor)
                        <div class="item {{ $tipo }}">
                            <div>{{ $nome }}</div>
                            <div>R$ {{ $tipo === 'ganho' ? '+' : '-' }}{{ number_format($valor, 2, ',', '.') }}</div>
                        </div>
                        @php $index++; @endphp
                    @endforeach
                @endif
            @endforeach

            <div class="totais-dia">
                <div class="item ganho">
                    <strong>Total de Ganhos:</strong>
                    <div>R$ +{{ number_format($totalGanhoDia, 2, ',', '.') }}</div>
                </div>
                <div class="item custo">
                    <strong>Total de Custos:</strong>
                    <div>R$ -{{ number_format($totalCustoDia, 2, ',', '.') }}</div>
                </div>
                <div class="item lucro">
                    <strong>Lucro do dia:</strong>
                    <div>R$ {{ number_format($lucroDia, 2, ',', '.') }}</div>
                </div>
            </div>
        @endif
    </div>
@endforeach

<div class="total-geral">
    <div class="total-linha ganho">
        <div>Total de Ganhos:</div>
        <div>R$ +{{ $totais['ganhos'] }}</div>
    </div>
    <div class="total-linha custo">
        <div>Total de Custos:</div>
        <div>R$ -{{ $totais['custos'] }}</div>
    </div>
    <div class="total-linha lucro">
        <div>Lucro Total:</div>
        <div>R$ {{ $totais['lucro'] }}</div>
    </div>
</div>

</body>
</html>
