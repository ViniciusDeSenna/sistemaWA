<x-app-layout>

    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Cadastrando Diária</h5>
            </div> 
            <div class="card-body">
                <form id="form-hourly-rate">

                    <div class="mb-3">
                        <label class="form-label" for="collaborator_id">Colaborador</label>
                        <select class="form-control" id="collaborator_id" name="collaborator_id">
                            <option value="" disabled selected>Selecione um colaborador</option>
                            @foreach ($collaborators as $colaborator)
                                <option value="{{ $colaborator->id }}" {{ ($dailyRate?->collaborator_id ?? 0) == $colaborator->id ? 'selected' : '' }}>
                                    {{ $colaborator->name }}
                                </option>                            
                            @endforeach
                        </select>
                    </div>
                
                    <div class="mb-3">
                        <label class="form-label" for="company_id">Empresa</label>
                        <select class="form-control" id="company_id" name="company_id">
                            <option value="" disabled selected>Selecione uma empresa</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}" {{ ($dailyRate?->collaborator_id ?? 0) == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                
                    <div class="mb-3">
                        <label class="form-label" for="start">Chegada</label>
                        <input type="datetime-local" class="form-control" id="start" name="start" value="{{ $dailyRate?->start ?? '' }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="start_interval">Chegada Intervalo</label>
                        <input type="datetime-local" class="form-control" id="start_interval" name="start_interval" value="{{ $dailyRate?->start_interval ?? '' }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="end_interval">Saida Intervalo</label>
                        <input type="datetime-local" class="form-control" id="end_interval" name="end_interval" value="{{ $dailyRate?->end_interval ?? '' }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="end">Saída</label>
                        <input type="datetime-local" class="form-control" id="end" name="end" value="{{ $dailyRate?->end ?? '' }}">
                    </div>
                
                    <div class="mb-3">
                        <label class="form-label" for="daily_total_time">Quantidade de Horas Trabalhadas</label>
                        <input type="text" class="form-control" id="daily_total_time" name="daily_total_time" data-mask="00:00" readonly value="{{ $dailyRate?->daily_total_time ?? '' }}">
                    </div>
                
                    @can('Visualizar e inserir informações financeiras nas diárias')
                        <div class="mb-3">
                            <label class="form-label" for="hourly_rate">Valor por Hora</label>
                            <input type="text" class="form-control money" id="hourly_rate" name="hourly_rate" value="{{ $dailyRate?->hourly_rate ?? '' }}">
                        </div>
                    @endcan

                    @can('Visualizar e inserir informações financeiras nas diárias')
                        <div class="mb-3">
                            <label class="form-label" for="costs">Gastos</label>
                            <input type="text" class="form-control money" id="costs" name="costs" value="{{ $dailyRate?->costs ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="costs_description">Descrição dos Gastos</label>
                            <textarea class="form-control" id="costs_description" name="costs_description" rows="4">{!! $dailyRate?->costs_description ?? '' !!}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="addition">Acréscimos</label>
                            <input type="text" class="form-control money" id="addition" name="addition" value="{{ $dailyRate?->addition ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="addition_description">Descrição dos Acréscimos</label>
                            <textarea class="form-control" id="addition_description" name="addition_description" rows="4">{!! $dailyRate?->addition_description ?? '' !!}</textarea>
                        </div>
                    
                        <div class="mb-3">
                            <label class="form-label" for="total">Valor Total</label>
                            <input type="text" class="form-control money" id="total" name="total" readonly value="{{ $dailyRate?->total ?? '' }}">
                        </div>
                    @endcan
                
                    <div class="mb-3">
                        <label class="form-label" for="pix_key">Chave Pix para pagamento</label>
                        <input type="text" class="form-control" id="pix_key" name="pix_key" value="{{ $dailyRate?->pix_key ?? '' }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="observation">Observação</label>
                        <textarea class="form-control" id="observation" name="observation" rows="4">{!! $dailyRate?->observation ?? '' !!}</textarea>
                    </div>
                </form>
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center">
                @if ($dailyRate?->id ?? false)
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary right" style="margin-right: 0%" onclick="update({{ $dailyRate?->id ?? null }})">Salvar</button>
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
    function post() {
        $.ajax({
            url: '{{ route('daily-rate.store') }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: $('#form-hourly-rate').serialize(),
            success: function(response) {
                Swal.fire({
                    title: response?.title ?? 'Sucesso!',
                    text: response?.message ?? 'Sucesso na ação!',
                    icon: response?.type ?? 'success'
                }).then((result) => {
                    $('#form-hourly-rate')[0].reset();

                    window.location.reload();
                });
            },
            error: function(response) {
                response = JSON.parse(response.responseText);
                Swal.fire({
                    title: response?.title ?? 'Oops!',
                    html: response?.message?.replace(/\n/g, '<br>') ?? 'Erro na ação!',
                    icon: response?.type ?? 'error'
                });
            }
        });
    }

    function update(id) {
        $.ajax({
            url: "{{ route('daily-rate.update', '') }}" + '/' + id,
            type: 'PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: $('#form-hourly-rate').serialize(),
            success: function(response) {
                Swal.fire({
                    title: response?.title ?? 'Sucesso!',
                    text: response?.message ?? 'Sucesso na ação!',
                    icon: response?.type ?? 'success'
                }).then((result) => {
                    $('#form-hourly-rate')[0].reset();

                    window.location.reload();
                });
            },
            error: function(response) {
                response = JSON.parse(response.responseText);
                Swal.fire({
                    title: response?.title ?? 'Oops!',
                    html: response?.message?.replace(/\n/g, '<br>') ?? 'Erro na ação!',
                    icon: response?.type ?? 'error'
                });
            }
        });
    }

    function getHourlyRate(callback) {
        try {
            let value = Number($('#hourly_rate').val().replace('.', '').replace(',', '.'));

            if (value === 0) {
                let company = $('#company_id').val();

                if (company === null) {
                    callback(0);
                    return;
                }

                $.ajax({
                    url: "{{ route('companies.hourly-rate', '') }}" + '/' + company,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        callback(Number(response));
                    },
                    error: function() {
                        callback(0);
                    }
                });

            } else {
                callback(value);
            }
        } catch {
            callback(0);
        }
    }

    function getPixKey() {
        let value = $('#pix_key').val();

        if (value === "") {
            let collaborator = $('#collaborator_id').val();

            $.ajax({
                url: "{{ route('collaborators.pix-key', '') }}" + '/' + collaborator,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#pix_key').val(response)
                },
            });
        }
    }

    function difHourly(start, end) {
        try {

            if (start == "") return 0;
            if (end == "") return 0;
            
            let startDate = new Date(start); // Example start datetime
            let endDate = new Date(end);   // Example end datetime

            let diffInMilliseconds = endDate - startDate; // Difference in milliseconds

            // Convert to different units
            let diffInSeconds = diffInMilliseconds / 1000;
            let diffInMinutes = diffInSeconds / 60;
            let diffInHours = diffInMinutes / 60;

            return diffInHours ?? 0;

        } catch {
            return 0;
        }
    }

    function formatTime(value) {
        let hours = Math.floor(value); // Obtém a parte inteira como horas
        let minutes = Math.round((value % 1) * 60); // Converte a parte decimal para minutos

        // Garante que o formato seja sempre HH:MM
        return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
    }

    function calcular() {
        // Chama getHourlyRate com um callback para lidar com o valor retornado
        getHourlyRate(function(hourlyRate) {

            // Pega o horário de início até fim e calcula quantas horas deu, faz x valor por hora se não existir início ou fim valor é = 0
            let startDate = $('#form-hourly-rate input[name="start"]').val();
            let endDate = $('#form-hourly-rate input[name="end"]').val();
            let workedHourly = difHourly(startDate, endDate);

            $('#form-hourly-rate input[name="daily_total_time"]').val(formatTime(workedHourly));

            let startIntervalDate = $('#form-hourly-rate input[name="start_interval"]').val();
            let endIntervalDate = $('#form-hourly-rate input[name="end_interval"]').val();
            let intervaledHourly = difHourly(startIntervalDate, endIntervalDate);

            // Soma o resto com acréscimos - gastos para descobrir quanto a empresa vai receber
            let addition = Number($('#form-hourly-rate input[name="addition"]').val().replace('.', '').replace(',', '.'));
            let costs = Number($('#form-hourly-rate input[name="costs"]').val().replace('.', '').replace(',', '.'));


            // Calcula o total
            let total = ((hourlyRate * (workedHourly - intervaledHourly)) + addition) - costs;

            // Informa o valor no total
            if (total < 0) {
                return 0;
            }

            // Aqui você pode adicionar o valor no campo de total, se necessário
            $('#form-hourly-rate input[name="total"]').val(total);
        });
    }


    $(document).ready(function () {
        $('#form-hourly-rate').on('input change', function () {
            calcular();
        });

        $('#collaborator_id').select2({
            theme: 'bootstrap-5'
        });
        $('#company_id').select2({
            theme: 'bootstrap-5'
        });

        $('.money').mask('#.###.###.##0,00', {
            reverse: true,
            translation: {
                '#': {
                pattern: /-?\d/,
                optional: true
                }
            },
            placeholder: "R$ 0,00"
        });

        getPixKey();
    });

</script>