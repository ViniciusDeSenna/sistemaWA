<div class="container mt-3">
    <div class="card shadow-lg border-0">
        <h5 class="card-header text-white bg-primary text-center py-3">
            Resultados Financeiros
        </h5>

        <!-- Botões de filtro aprimorados -->
        <div class="card-body bg-light p-3 text-center">
            <div class="btn-group">
                <button class="btn btn-primary" wire:click="setFilter('day')">Dia</button>
                <button class="btn btn-primary" wire:click="setFilter('week')">Semana</button>
                <button class="btn btn-primary" wire:click="setFilter('month')">Mês</button>
                <button class="btn btn-primary" wire:click="setFilter('year')">Ano</button>
            </div>
        </div>

        <!-- Cards de Resumo Financeiro -->
        <div class="card-body bg-light">
            <div class="d-flex justify-content-between gap-3">
                <div class="card shadow-sm text-center flex-fill m-0 p-2">
                    <h6 class="text-warning mb-1 fs-5">
                        R$ {{ number_format($total_earned, 2, ',', '.') }}
                    </h6>
                    <p class="small mb-0">Faturamento</p>
                </div>
                <div class="card shadow-sm text-center flex-fill m-0 p-2">
                    <h6 class="text-danger mb-1 fs-5">
                        R$ {{ number_format($total_costs, 2, ',', '.') }}
                    </h6>
                    <p class="small mb-0">Custo</p>
                </div>
                <div class="card shadow-sm text-center flex-fill m-0 p-2">
                    <h6 class="text-success mb-1 fs-5">
                        R$ {{ number_format($total_earned - $total_costs, 2, ',', '.') }}
                    </h6>
                    <p class="small mb-0">Lucro</p>
                </div>
            </div>
        </div>
        

        <!-- Navegação de filtros -->
        <div class="card-body bg-light text-center">
            <h6 class="mt-2 text-muted">Período Selecionado</h6>
            <h5 class="fw-bold">{{ date('d/M/Y', strtotime($start)) }} ↔ {{ date('d/M/Y', strtotime($end)) }}</h5>
            <div class="d-flex w-100 gap-2">
                <button class="btn btn-outline-primary flex-fill py-3" wire:click="navigatePeriod('previous')">
                    <span class="tf-icons bx bx-left-arrow-alt"></span> Período Anterior
                </button>
                <button class="btn btn-outline-primary flex-fill py-3" wire:click="navigatePeriod('next')">
                    Próximo Período <span class="tf-icons bx bx-right-arrow-alt"></span>
                </button>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="card-footer d-flex flex-column gap-3 w-100">
            <livewire:add-cost />
            <button class="btn btn-secondary w-100" wire:click="gerarRelatorioFinanceiro">
                Relatório Financeiro
            </button>
        </div>    
    </div>

    
    <!-- Seções -->
    <h5 class="text-center mt-5">Setores</h5>
    @foreach ($sections_array as $section)
        <div class="mb-3">
            <x-card>
                <h6 class="fw-bold">{{ $section['name'] }}</h6>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Diárias:</span>
                    <span>{{ $section['dailyRateCount'] }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Faturamento:</span>
                    <span>R$ {{ number_format($section['totalEarned'], 2, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Custos:</span>
                    <span>R$ {{ number_format($section['totalCost'], 2, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Lucro:</span>
                    <span>R$ {{ number_format($section['totalProfit'], 2, ',', '.') }}</span>
                </div>
            </x-card>
        </div>
    @endforeach
    
    <!-- Empresas -->
    <h5 class="text-center mt-5">Empresas</h5>
    @foreach ($companies_array as $company)
        <div class="mb-3">
            <x-card>
                <h6 class="fw-bold">{{ $company['name'] }}</h6>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Diárias:</span>
                    <span>{{ $company['dailyRateCount'] }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Faturamento:</span>
                    <span>R$ {{ number_format($company['totalEarned'], 2, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Custos:</span>
                    <span>R$ {{ number_format($company['totalCost'], 2, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Lucro:</span>
                    <span>R$ {{ number_format($company['totalProfit'], 2, ',', '.') }}</span>
                </div>
            </x-card>
        </div>
    @endforeach

    <!-- Cidades -->
    <h5 class="text-center mt-5">Cidades</h5>
    @foreach ($cities_array as $city)
        <div class="mb-3">
            <x-card>
                <h6 class="fw-bold">{{ $city['name'] }}</h6>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Diárias:</span>
                    <span>{{ $city['dailyRateCount'] }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Faturamento:</span>
                    <span>R$ {{ number_format($city['totalEarned'], 2, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Custos:</span>
                    <span>R$ {{ number_format($city['totalCost'], 2, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Lucro:</span>
                    <span>R$ {{ number_format($city['totalProfit'], 2, ',', '.') }}</span>
                </div>
            </x-card>
        </div>
    @endforeach
</div>

</div>