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
                            <label class="form-label" for="addition">Participação do Colaborador</label>
                            <input type="text" class="form-control money" id="collaborator_participation" name="collaborator_participation" value="{{ $dailyRate?->collaborator_participation ?? '' }}">
                        </div>
                    
                        <div class="mb-3">
                            <label class="form-label" for="total">Valor Total</label>
                            <input type="text" class="form-control money" id="total" name="total" readonly value="{{ $dailyRate?->total ?? '' }}">
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
    $(document).ready(function () {
        $('#form-hourly-rate input:not([name="company_id"])').on('input change', function () {
            calcular();
        });

        $('#company_id').on('input change', function () {
            getHourlyRate();
        });


        $('#collaborator_id').select2({
            theme: 'bootstrap-5'
        });

        $('#company_id').select2({
            theme: 'bootstrap-5'
        });

        let moneyMask = new Inputmask("R$ 999,99", {
            numericInput: true,
            rightAlign: false,
            prefix: "R$ ",
            groupSeparator: ".",
            radixPoint: ",",
            autoGroup: true,
            unmaskAsNumber: true,
            allowMinus: true
        });
        moneyMask.mask('.money');
    });

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
        let company = $('#company_id').val();
        $.ajax({
            url: "{{ route('companies.hourly-rate', '') }}" + '/' + company,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#hourly_rate').val(response);
                calcular();
                return;
            },
        });
    }

    function calcular() {
        // Obtendo os valores dos campos com a máscara aplicada
        let hourlyRate = Number((parseFloat(document.getElementById('hourly_rate').inputmask.unmaskedvalue()) || 0) / 100).toFixed(2);
        let costs = Number((parseFloat(document.getElementById('costs').inputmask.unmaskedvalue()) || 0) / 100).toFixed(2);
        let addition = Number((parseFloat(document.getElementById('addition').inputmask.unmaskedvalue()) || 0) / 100).toFixed(2);
        let collaboratorParticipation = Number((parseFloat(document.getElementById('collaborator_participation').inputmask.unmaskedvalue()) || 0) / 100).toFixed(2);


        // Obtendo o horário de início e fim para calcular as horas trabalhadas
        let startDate = $('#form-hourly-rate input[name="start"]').val();
        let endDate = $('#form-hourly-rate input[name="end"]').val();
        
        // Calculando as horas trabalhadas
        let workedHourly = difHourly(startDate, endDate);

        // Atualizando o campo de total_time com o valor calculado
        $('#total_time').val(formatTime(workedHourly));

        // Calculando o total (considerando valores numéricos)
        let total = (((hourlyRate * workedHourly) + addition) - costs) - collaboratorParticipation;
        console.log(total);
        // Atualizando o campo de total com o valor calculado
        $('#total').val(parseFloat(total).toFixed(2));
    }

    function difHourly(start, end) {
        try {
            if (start == "" || end == "") return 0;
            
            let startDate = new Date(start);
            let endDate = new Date(end);  

            let diffInMilliseconds = endDate - startDate; 

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

    
</script>