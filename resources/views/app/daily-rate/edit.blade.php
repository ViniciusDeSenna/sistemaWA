<x-app-layout>

    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Cadastrando Diária</h5>
            </div> 
            <div class="card-body">
                <form id="form-edit-jobs">

                    <div class="mb-3">
                        <label class="form-label" for="collaborator-select">Colaborador</label>
                        <select class="form-control" id="collaborator-select" name="collaborator">
                            <option value="" disabled selected>Selecione um colaborador</option>
                            @foreach ($collaborators as $colaborator)
                                <option value="{{ $colaborator->id }}">{{ $colaborator->name }}</option>
                            @endforeach
                        </select>
                    </div>
                
                    <div class="mb-3">
                        <label class="form-label" for="company-select">Empresa</label>
                        <select class="form-control" id="company-select" name="company">
                            <option value="" disabled selected>Selecione uma empresa</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                
                    <div class="mb-3">
                        <label class="form-label" for="arrival-time">Hora de Chegada</label>
                        <input type="time" class="form-control" id="arrival-time" name="arrival_time" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="departure-time">Hora de Saída</label>
                        <input type="time" class="form-control" id="departure-time" name="departure_time" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="diary-date">Dia da Diária</label>
                        <input type="date" class="form-control" id="diary-date" name="diary_date" required>
                    </div>
                
                    <div class="mb-3">
                        <label class="form-label" for="worked-hours">Quantidade de Horas Trabalhadas</label>
                        <input type="number" class="form-control" id="worked-hours" name="worked_hours" readonly required>
                    </div>
                
                    <div class="mb-3">
                        <label class="form-label" for="hourly-rate">Valor por Hora</label>
                        <input type="number" class="form-control" id="hourly-rate" name="hourly_rate" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="hourly-rate">Gastos</label>
                        <input type="number" class="form-control" id="hourly-rate" name="hourly_rate" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="expense-description">Descrição dos Gastos</label>
                        <textarea class="form-control" id="expense-description" name="expense_description" rows="4"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="hourly-rate">Acréscimos</label>
                        <input type="number" class="form-control" id="acrescimos" name="acrescimos" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="expense-description">Descrição dos Acréscimos</label>
                        <textarea class="form-control" id="acrescimos-description" name="acrescimos-description" rows="4"></textarea>
                    </div>
                
                    <div class="mb-3">
                        <label class="form-label" for="total-value">Valor Total</label>
                        <input type="number" class="form-control" id="total-value" name="total_value" readonly required>
                    </div>
                
                    <div class="mb-3">
                        <label class="form-label" for="pix-key">Chave Pix para pagamento</label>
                        <input type="text" class="form-control" id="pix-key" name="pix_key" required>
                    </div>
                </form>
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center">
                @if ($establishment?->id ?? false)
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary right" style="margin-right: 0%" onclick="update({{ $establishment?->id ?? null }})">Salvar</button>
                        </div>
                    @else
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary right" style="margin-right: 0%"onclick="post()">Salvar</button>
                        </div>
                    @endif
            </div> 
        </div>
    </div>

</x-app-layout>

<script>
  

    document.getElementById('arrival-time').setAttribute('lang', 'pt-BR');
    document.getElementById('departure-time').setAttribute('lang', 'pt-BR');

    function getHourlyRate() {
        let value = $('#hourly-rate').val();

        if (Number(value) === 0) {

            let company = $('#company-select').val();

            if (company === null) {
                return 0;
            }

            $.ajax({
                url: "{{ route('companies.hourly-rate', '') }}" + '/' + company,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    return Number(response);
                },
                error: function(response) {
                    return 0;
                }
            });

        } else {
            return value;
        }
    }

    function calcular() {
        // Pega o valor por hora, se nao existir pega o valor da empresa, insere no calculo e calcula com ela. Se não existir valor é 0
        let hourlyRate = getHourlyRate()
        // pega o horario de inicio até fim e calcula quantas horas deu, faz x valor por hora se nao existir inicio ou fim valor é = 0
        // soma o resto com acrescimos - gastos para descobrir quanto que a impresa vai receber
        //informa o valor no total
    }
</script>