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
                        <label class="form-label" for="sectionSelect_id">Setor Trabalhado</label>
                        <select class="form-control" id="sectionSelect_id" name="sectionSelect_id" disabled>

                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="start">Chegada</label>
                        <input type="datetime-local" class="form-control" id="start" name="start" disabled value="{{ $dailyRate?->start ?? '' }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="end">Saída</label>
                        <input type="datetime-local" class="form-control" id="end" name="end" disabled value="{{ $dailyRate?->end ?? '' }}">
                    </div>
                    <div class="mb-3">
                            <label class="form-label" for="feeding_id">Alimentação</label>
                            <input type="checkbox" class="" id="feeding_id" name="feeding_id" {{ isset($dailyRate) && $dailyRate?->feeding != 0 ? 'checked' : ''}}> R$10,00
                        </div>

                    <div class="mb-3">
                        <label class="form-label" for="total_time">Horas Trabalhadas</label>
                        <input type="text" class="form-control" id="total_time" name="total_time" data-mask="00:00" readonly value="{{ $dailyRate?->total_time ?? '' }}">
                    </div>
                    
                    <input type="text" class="form-control" id="imposto_paid_id" name="imposto_paid_id" hidden readonly value="0">
<!-- --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- -->
                    <div name="" {{ Auth::user()->hasPermissionTo('Visualizar e inserir informações financeiras nas diárias') ? '' : 'hidden' }}>
                        
                        
                        <div class="d-flex">
                            <div class="mb-3 me-3">
                                <label class="form-label" for="employee_pay_id">Colaborador</label>
                                <input type="text" class="form-control money" id="employee_pay_id" readonly name="employee_pay_id" value="">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="leaderComission_id">Comissão</label>
                                <input type="text" class="form-control money" id="leaderComission_id" readonly name="leaderComission_id" value="">
                            </div>
                        </div>
                        <div class="d-flex">
                            <input type="text" name="user_id" hidden value="{{auth()->user()->id}}" />
                            <div class="mb-3 me-3 flex-grow-1">
                                <label class="form-label" for="inss_id">INSS Pago</label>
                                <input type="text" class="form-control money" id="inss_id" readonly name="inss_id" value="">
                            </div>
                            <div class="mb-3" style="flex: 0.5;">
                                <label class="form-label" for="inss_percentage">%INSS</label>
                                <input type="text" class="form-control" id="inss_percentage_id" name="inss_percentage_id" value="7">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label" for="transport_id">Transporte</label>
                            <input type="text" class="form-control money" id="transport_id" name="transport_id" value="{{ $dailyRate?->costs ?? '' }}">
                        </div>
                        


                        <div class="mb-3">
                            <label class="form-label" for="addition">Acréscimos</label>
                            <input type="text" class="form-control money" id="addition" name="addition" value="{{ $dailyRate?->addition ?? '' }}">
                        </div>
                        <div class="d-flex ml-0">
                            <div class="mb-3 me-3">
                                <label class="form-label" for="total">Valor Total Bruto</label>
                                <input type="text" class="form-control money" id="total" name="total" readonly value="{{ $dailyRate?->total ?? '' }}">
                            </div>
                            <div class="mb-3 me-3">
                                <label class="form-label" for="imposto_id">Imposto (%)</label>
                                <input type="number" class="form-control percentage" id="imposto_id" name="imposto_id" value="{{14}}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="total_liq">Valor Total Liquido</label>
                                <input type="text" class="form-control" x-mask:dynamic="$money($input, 'R$')" id="total_liq" name="total_liq" readonly value="{{ $dailyRate?->total ?? '' }}">
                            </div>

                        </div>
                        
                 </div>
<!-- --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- -->

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
    let companySections = [];
    let selectedSection;
    let selectedCollaborator;
    $(document).ready(function () {

        loadSectionInfo();
        calcular();

        $('#inss_percentage_id').on('input change', function(){
            calcular();
        });
        $('#addition').on('input change', function(){
            calcular();
        });
        $('#collaborator_id').on('input change', function(){
            getSelectedColaborator($(this).val());
        });

        $('#transport_id').on('input change', function(){
            calcular();
        });
        $('#transport_id').on('input change', function(){
            calcular();
        });
        $('#imposto_id').on('input change', function(){
            calcular();
        });
        $('#form-hourly-rate input:not([name="company_id"])').on('input change', function () {
          //  calcular();
        });

        $('#company_id').on('input change', function () {
            getCompanySections($(this).val());
            //getHourlyRate();
        });
        $('#sectionSelect_id').on('input change', function () {
            selectedSection = companySections.find(item => item.section_id === Number($(this).val()));
            loadSectionInfo();
            //getHourlyRate();
            calcular();

        });
        $('#feeding_id').on('input change', function () {
            calcular();

        });
        $('#start').on('input change', function () {
            if (selectedSection.perHour === 1){
                calcular();

            }
        });
        $('#end').on('input change', function () {
            if (selectedSection.perHour === 1){
                calcular();

            }
        });


        $('#collaborator_id').select2({
            theme: 'bootstrap-5'
        });

        $('#company_id').select2({
            theme: 'bootstrap-5'
        });

        let moneyMask = new Inputmask("R$ 99999,99", {
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
                response = JSON.parse(response.responseText);s
                Swal.fire({
                    title: response?.title ?? 'Oops!',
                    html: response?.message?.replace(/\n/, '<br>') ?? 'Erro na ação!',
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

function loadSectionInfo(){
    if (selectedSection){
        document.getElementById("start").disabled = false;
        if (selectedSection.perHour === 1){
            if (document.getElementById("end").disabled){
                document.getElementById("end").disabled = false;
    
            }
            document.getElementById("employee_pay_id").value = 0;
    
            //document.getElementById("end").hidden = false;
            
            //document.getElementById("total_time").hidden = false;
        }else{
            //document.getElementById("employee_pay_id").value = selectedSection.employeePay;
            
            document.getElementById("end").disabled = true;
            //document.getElementById("end").hidden = true;
            //document.getElementById("total_time").hidden = true;
            
        }
        //document.getElementById("leaderComission_id").value = selectedSection.leaderComission * 100;
    
    } else {
        document.getElementById("start").disabled = true;
        document.getElementById("end").disabled = true;
        document.getElementById("employee_pay_id").value = '';
        document.getElementById("transport_id").value = '';
        document.getElementById("leaderComission_id").value = '';
        document.getElementById("total").value = '';
        document.getElementById("total_liq").value = '';

    }    

}

    function getSectionNameById(id) {
        let sections = @json($sections);
        const section = sections.find(item => item.id === id);
        return section ? section.name : 'ID não encontrado';
}
    function getSelectedColaborator(colaboratorId){
        $.ajax({
            url: "/get-colaborator/" + colaboratorId,
            type: "GET",
            dataType: "json",
            success: function (colaborador) {
                selectedCollaborator = colaborador;
                calcular();
            },
            error: function (xhr) {
                console.error("Erro ao buscar setores:", xhr.responseText);
            }
        });
    }

    function getCompanySections(companyId){
  
        $.ajax({
            url: "/get-company-sections/" + companyId,
            type: "GET",
            dataType: "json",
            success: function (sections) {
                let select = $("#sectionSelect_id");
                select.empty();
                select.append('<option value="" disabled selected>Selecione um setor</option>');
                companySections = sections;
                if (sections.length > 0) {
                    sections.forEach(function (section) {
                        select.append(`<option value="${section.section_id}">${getSectionNameById(section.section_id)}</option>`);
                    });
                    selectedSection = null;
                    loadSectionInfo();
                    select.prop("disabled", false); 
                } else {
                    select.append('<option value="" disabled>Nenhum setor encontrado</option>');
                    select.prop("disabled", true);
                }
            },
            error: function (xhr) {
                console.error("Erro ao buscar setores:", xhr.responseText);
            }
        });
    }

    function calculate_pay_perHour(value_per_hour){
        let startDate = $('#form-hourly-rate input[name="start"]').val();
        let endDate = $('#form-hourly-rate input[name="end"]').val();
        
        let workedHourly = difHourly(startDate, endDate);
        if (workedHourly <= 0){
            $('#total_time').val(formatTime(0));
            if (workedHourly < 0){
                $('#total_time').val("Horarios invalidos, Saída antecede entrada");

            }

            return 0;
        } else{
            $('#total_time').val(formatTime(workedHourly));

            return workedHourly * value_per_hour;
        }

    }

    function calcular() {

        if (selectedSection == null || selectedCollaborator == null) return;
        
        let transport = Number(((parseFloat(document.getElementById('transport_id').inputmask.unmaskedvalue()) || 0) / 100).toFixed(2));
        let feeding = (document.getElementById('feeding_id').checked ? 10 : 0);
        let addition = Number(((parseFloat(document.getElementById('addition').inputmask.unmaskedvalue()) || 0) / 100).toFixed(2));
        let leaderComission = selectedSection.leaderComission;
        let earned = selectedSection.earned;
        
        let pay_amount = selectedSection.employeePay;
        if (selectedCollaborator.is_leader === 1) {
            leaderComission = 0;
            pay_amount = selectedSection.leaderPay;
        }else if (selectedCollaborator.is_extra === 1) {
            pay_amount = selectedSection.extra;
        }
        if (selectedSection.perHour === 1)  {
            pay_amount = calculate_pay_perHour(pay_amount);
            earned = calculate_pay_perHour(selectedSection.earned);
        }
        $('#employee_pay_id').val((pay_amount + feeding).toFixed(2));
        
        let inss_percentage = parseFloat(document.getElementById('inss_percentage_id').value) || 0;
        let inss_discount = (pay_amount * inss_percentage) / (100 - inss_percentage)
        document.getElementById('inss_id').value = parseFloat(inss_discount).toFixed(2);
        
        let tax = ((parseFloat(document.getElementById('imposto_id').value) || 0) / 100);
        console.log("imposto: ", tax);
        
        let total = ((earned) + addition).toFixed(2);
        let total_liq = (total * (1-tax) - (pay_amount + feeding) - transport - inss_discount - leaderComission).toFixed(2);
        
        $('#imposto_paid_id').val((pay_amount * tax).toFixed(2));
        $("#leaderComission_id").val(leaderComission.toFixed(2));
        $('#total').val(parseFloat(total).toFixed(2));
        $('#total_liq').val(parseFloat(total_liq).toFixed(2));
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