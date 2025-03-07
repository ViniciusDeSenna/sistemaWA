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
                        <label class="form-label" for="end">Saída</label>
                        <input type="datetime-local" class="form-control" id="end" name="end" value="{{ $dailyRate?->end ?? '' }}">
                    </div>
                
                    <div class="mb-3">
                        <label class="form-label" for="total_time">Quantidade de Horas Trabalhadas</label>
                        <input type="text" class="form-control" id="total_time" name="total_time" data-mask="00:00" readonly value="{{ $dailyRate?->total_time ?? '' }}">
                    </div>
                
                    @can('Visualizar e inserir informações financeiras nas diárias')
                        <div class="mb-3">
                            <label class="form-label" for="hourly_rate">Valor por Hora</label>
                            <input type="text" class="form-control money" id="hourly_rate" name="hourly_rate" value="{{$dailyRate?->hourly_rate ?? '' }}">
                        </div>
                 
                        <div class="mb-3">
                            <label class="form-label" for="costs">Gastos</label>
                            <input type="text" class="form-control money" id="costs" name="costs" value="{{ number_format($dailyRate?->costs ?? '0', 2, ".", ",") }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="costs_description">Descrição dos Gastos</label>
                            <textarea class="form-control" id="costs_description" name="costs_description" rows="4">{!! $dailyRate?->costs_description ?? '' !!}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="addition">Acréscimos</label>
                            <input type="text" class="form-control money" id="addition" name="addition" value="{{ number_format($dailyRate?->addition ?? '0', 2, ".", ",") }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="addition_description">Descrição dos Acréscimos</label>
                            <textarea class="form-control" id="addition_description" name="addition_description" rows="4">{!! $dailyRate?->addition_description ?? '' !!}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="addition">Participação do Colaborador</label>
                            <input type="text" class="form-control money" id="collaborator_participation" name="collaborator_participation" value="{{ number_format($dailyRate?->collaborator_participation ?? '0', 2, ".", ",") }}">
                        </div>
                    
                        <div class="mb-3">
                            <label class="form-label" for="total">Valor Total</label>
                            <input type="text" class="form-control money" id="total" name="total" readonly value="{{ number_format($dailyRate?->total ?? '0', 2, ".", ",") }}">
                        </div>
                    @endcan

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
            let value = $('#hourly_rate').val();

            // Se o valor não for fornecido
            if (value === "") {
                let company = $('#company_id').val();

                // Se não houver empresa selecionada, retornamos 0
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
                        // Converte a resposta para o formato correto de moeda e insere no campo
                        let rate = response.replace('.', '').replace(',', '.'); // Garantindo que a vírgula seja convertida para ponto
                        $('#hourly_rate').val(rate.replace('.', ',')); // Exibe o valor com a vírgula de volta

                        // Converte para número e verifica se o valor é válido
                        let hourlyRate = parseFloat(rate);
                        if (isNaN(hourlyRate)) {
                            callback(0);
                        } else {
                            callback(hourlyRate);
                        }
                    },
                    error: function() {
                        // Se a requisição falhar, retornamos 0
                        callback(0);
                    }
                });

            } else {
                // Caso já tenha valor no campo, converte para número
                let numericValue = value.replace(',', '.');
                let hourlyRate = parseFloat(numericValue);

                // Se o valor não for válido, chamamos o callback com 0
                if (isNaN(hourlyRate)) {
                    callback(0);
                } else {
                    callback(hourlyRate);
                }
            }
        } catch (error) {
            // Registra o erro para facilitar o diagnóstico
            console.error('Erro ao obter o valor da taxa horária:', error);
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

    $(document).ready(function () {
        $('#form-hourly-rate').on('input change', function () {
            calcular();
        });

        // Inicializando o select2 para os campos de colaborador e empresa
        $('#collaborator_id').select2({
            theme: 'bootstrap-5'
        });
        $('#company_id').select2({
            theme: 'bootstrap-5'
        });

        // Máscara para campos de moeda (R$ 0,00)
        $('.money').mask('000.000.000.000.000,00', {
            reverse: true,
            placeholder: "R$ 0,00"
        });

        // Máscara para o campo de horas (00:00)
        $('#total_time').mask('00:00');
    });

    function calcular() {
        // Obtendo os valores dos campos com a máscara aplicada
        let hourlyRate = $('#hourly_rate').val().replace(/\./g, '').replace(',', '.'); // Convertendo para formato numérico
        let costs = $('#costs').val().replace(/\./g, '').replace(',', '.'); // Convertendo para formato numérico
        let addition = $('#addition').val().replace(/\./g, '').replace(',', '.'); // Convertendo para formato numérico
        let collaboratorParticipation = $('#collaborator_participation').val().replace(/\./g, '').replace(',', '.'); // Convertendo para formato numérico

        // Obtendo o horário de início e fim para calcular as horas trabalhadas
        let startDate = $('#form-hourly-rate input[name="start"]').val();
        let endDate = $('#form-hourly-rate input[name="end"]').val();
        
        // Calculando as horas trabalhadas
        let workedHourly = difHourly(startDate, endDate);

        // Atualizando o campo de total_time com o valor calculado
        $('#total_time').val(formatTime(workedHourly));

        // Calculando o total (considerando valores numéricos)
        let total = (((parseFloat(hourlyRate) * workedHourly) + parseFloat(addition)) - parseFloat(costs)) - parseFloat(collaboratorParticipation);

        // Atualizando o campo de total com o valor calculado
        $('#total').val(formatCurrency(total));
    }

    
    // Função para formatar o valor como moeda
    function formatCurrency(value) {
        return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    function difHourly(start, end) {
        try {
            if (start == "" || end == "") return 0;
            
            let startDate = new Date(start);
            let endDate = new Date(end);
            let diffInMilliseconds = endDate - startDate;
            let diffInHours = diffInMilliseconds / (1000 * 60 * 60);
            
            return diffInHours;
        } catch {
            return 0;
        }
    }

    function formatTime(value) {
        let hours = Math.floor(value);
        let minutes = Math.round((value % 1) * 60);
        return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
    }

</script>