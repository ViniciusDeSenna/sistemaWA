<x-app-layout>

    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Cadastrando Diária</h5>
            </div> 
            <div class="card-body">
                <form id="form-hourly-rate">

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
                        <input type="time" class="form-control" id="arrival-time" name="start" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="departure-time">Hora de Saída</label>
                        <input type="time" class="form-control" id="departure-time" name="end" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="diary-date">Dia da Diária</label>
                        <input type="date" class="form-control" id="diary-date" name="diary_date" required>
                    </div>
                
                    <div class="mb-3">
                        <label class="form-label" for="worked-hours">Quantidade de Horas Trabalhadas</label>
                        <input type="text" class="form-control money" id="worked-hours" name="total_time" data-mask="00:00" readonly required>
                    </div>
                
                    @can('Visualizar e inserir informações financeiras nas diárias')
                        <div class="mb-3">
                            <label class="form-label" for="hourly-rate">Valor por Hora</label>
                            <input type="number" class="form-control money" id="hourly-rate" name="hourly_rate" required>
                        </div>
                    @endcan

                    @can('Visualizar e inserir informações financeiras nas diárias')
                        <div class="mb-3">
                            <label class="form-label" for="hourly-rate">Gastos</label>
                            <input type="number" class="form-control money" id="costs" name="costs" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="costs-description">Descrição dos Gastos</label>
                            <textarea class="form-control" id="costs-description" name="costs-description" rows="4"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="hourly-rate">Acréscimos</label>
                            <input type="number" class="form-control money" id="addition" name="addition" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="addition-description">Descrição dos Acréscimos</label>
                            <textarea class="form-control" id="addition-description" name="addition-description" rows="4"></textarea>
                        </div>
                    
                        <div class="mb-3">
                            <label class="form-label" for="total-value">Valor Total</label>
                            <input type="number" class="form-control money" id="total-value" name="total_value" readonly required>
                        </div>
                    @endcan
                
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
    function getHourlyRate() {
        let value = Number($('#hourly-rate').val().replace('.', '').replace(',', '.'));

        if (value === 0) {

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

    function difHourly(hora1, hora2) {
        // Converter as horas para objetos Date
        var [h1, m1] = hora1.split(":").map(Number);
        var [h2, m2] = hora2.split(":").map(Number);

        // Criar objetos Date fictícios apenas para cálculo
        const data1 = new Date(0, 0, 0, h1, m1);
        const data2 = new Date(0, 0, 0, h2, m2);

        // Calcular a diferença em milissegundos
        let diferencaMs = Math.abs(data2 - data1);

        // Converter para horas e minutos
        const horas = Math.floor(diferencaMs / (1000 * 60 * 60));
        const minutos = Math.floor((diferencaMs % (1000 * 60 * 60)) / (1000 * 60));

        return Number(`${horas}.${minutos}`);
    }

    function formatTime(value) {
        let hours = Math.floor(value); // Obtém a parte inteira como horas
        let minutes = Math.round((value % 1) * 60); // Converte a parte decimal para minutos

        // Garante que o formato seja sempre HH:MM
        return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
    }

    function calcular() {
        // Pega o valor por hora, se nao existir pega o valor da empresa, insere no calculo e calcula com ela. Se não existir valor é 0
        let hourlyRate = getHourlyRate()

        // pega o horario de inicio até fim e calcula quantas horas deu, faz x valor por hora se nao existir inicio ou fim valor é = 0
        let hourlyStart = $('#form-hourly-rate input[name="start"]').val();
        let hourlyEnd = $('#form-hourly-rate input[name="end"]').val();
        let workedHourly = difHourly(hourlyStart, hourlyEnd);

        $('#form-hourly-rate input[name="total_time"]').val(formatTime(workedHourly));

        // soma o resto com acrescimos - gastos para descobrir quanto que a impresa vai receber
        let addition = Number($('#form-hourly-rate input[name="addition"]').val());
        let costs = Number($('#form-hourly-rate input[name="costs"]').val());
        
        let total = ((hourlyRate * workedHourly) + addition) - costs;

        //informa o valor no total
        $('#form-hourly-rate input[name="total_value"]').val(total);
    }

    $(document).ready(function () {
        $('#form-hourly-rate').on('input change', function () {
            calcular();
        });
    });

</script>