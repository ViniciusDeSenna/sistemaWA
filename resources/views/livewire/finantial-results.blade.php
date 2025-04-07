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
        <div class="card-footer d-flex justify-content-end gap-3">

        <button wire:click="gerarRelatorioFinanceiro" class="btn btn-primary me-1">
            Extrato Financeiro
        </button>

            <button class="btn btn-primary me-1 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cadastrarCusto" aria-expanded="{{ $costCollapseOpen ? 'true' : 'false' }}" aria-controls="cadastrarCusto">
                Cadastrar Custo
            </button>

        </div>
    </div>

    <!-- Exibe mensagens de sucesso -->
    @if (session()->has('success'))
        <div class="mt-3">
            <x-alert type="success" fade dismissible>
                {{ session('success') }}
            </x-alert>
        </div>
    @endif

    <!-- Exibe mensagens de erro -->
    @if (session()->has('error'))
        <div class="mt-3">
            <x-alert type="danger" fade dismissible>
                {{ session('error') }}
            </x-alert>
        </div>
    @endif

    <!-- Cadastro de Custos -->
    <div class="collapse mt-5 {{ $costCollapseOpen ? 'show' : '' }}" id="cadastrarCusto">
        <x-card title="Cadastro de Custo">
            <div class="row">
                
                <div class="col mb-3" wire:ignore>
                    <label for="cost.category_id" class="form-label">Categoria</label>
                    <select id="costCategory" class="form-select"></select>
                    @error('cost.category_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col mb-3">
                    <label for="costDate" class="form-label">Data</label>
                    <input type="date" wire:model="cost.date" class="form-control" id="costDate">
                    @error('cost.date') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col mb-3">
                    <label for="costValue" class="form-label">Valor</label>
                    <input type="text" wire:model="cost.value" class="form-control" id="costValue" placeholder="R$ 00,00" >
                    @error('cost.value') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col mb-3">
                    <label for="costDescription" class="form-label">Descrição</label>
                    <input type="text" wire:model="cost.description" class="form-control" id="costDescription" placeholder="Descrição" >
                    @error('cost.description') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

            </div>
            <div class="d-flex justify-content-end gap-3">
                <button class="btn btn-primary me-1 collapsed" wire:click="saveCusto" type="button" data-bs-toggle="collapse" data-bs-target="#cadastrarCusto" aria-expanded="false" aria-controls="cadastrarCusto">
                    Salvar
                </button>
            </div>
        </x-card>
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

@script
<script>
     $(document).ready(function() {
        costCadegorySelect2({{ Js::from($costCategories) }});
        $('#costCategory').on('change', function(event){
            @this.$set('cost.category_id', event.target.value);
            @this.$set('costCollapseOpen', true);
        })

        Livewire.on('costCadegorySelect2', ({ costCategories }) => {
            let itens = [...costCategories];
            costCadegorySelect2(itens);
        });
    });

    function costCadegorySelect2(itens) {
        
        itens.unshift({ id: '', name: 'Selecione uma categoria', selected: true });

        let newData = itens.map(element => ({
            id: element.id,
            text: element.name,
            selected: element.selected || false
        }));

        let select = $('#costCategory');
 
        select.empty().select2({
            theme: 'bootstrap-5',
            tags: true,
            data: newData,
        });
    }
</script>
@endscript