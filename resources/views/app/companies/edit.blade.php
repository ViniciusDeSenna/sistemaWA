<x-app-layout>
    <div class="container">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Cadastrando Estabelecimento</h5>
            </div> 
            <div class="card-body">
                <form id="form-edit-establishment">
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-fullname">Nome</label>
                        <input type="text" class="form-control" id="basic-default-fullname" name="name" placeholder="WA Merchandising e Terceirização" value="{{ $establishment?->name ?? ''}}" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-fullname">CNPJ</label>
                        <input type="text" class="form-control cnpj" id="basic-default-fullname" name="document" placeholder="XX.XXX.XXX/XXXX-XX" value="{{ $establishment?->document ?? ''}}" />
                    </div>

<!--                    <div class="mb-3">
                            <label class="form-label" for="basic-default-text">Rede Pertencente</label>
                            <input type="text" class="form-control" id="category" name="category" placeholder="Rede" value="{{ $establishment?->chain_of_stores ?? ''}}" />
                    </div>                    
-->
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-text">Setores</label><br>
                        <div class="d-flex align-items-center gap-2">
                            <select class="form-select" id="sectionSelect"> 
                                <option value="empty">Selecione um setor</option>
                                @foreach ($sections as $setor)
                                    <option value="{{ $setor->id }}" data-nome="{{ $setor->name }}">{{ $setor->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addLabelForSection()">+</button>
                        </div>
                        <div class="w-100" id="accordionDiv" style="background-color:#CCE0FF">
                            <div id="selectedSections" class="w-100"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-message">Observação</label>
                        <textarea id="basic-default-message" class="form-control" placeholder="Alguma observação?" name="observation">{!! $establishment?->observation ?? '' !!}</textarea>
                    </div>
                </form>
            </div>
    
            <div class="card-footer">
                @if ($establishment?->id ?? false)
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary right" onclick="update({{ $establishment?->id ?? null }})">Salvar</button>
                    </div>
                @else
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary right" onclick="post()">Salvar</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    
    let establishment = @json($establishment ?? null);
    let companySections = @json($companySections ?? []);
    let sections = @json($sections ?? null);

    function createSectionCard(collapseId, setorId, setorNome, earned, employee_pay, leader_pay, comission){
        return `
                <div class="accordion-item card-body mb-1 w-100">
                    <h2 class="accordion-header" id="heading${setorId}">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="false" aria-controls="${collapseId}">
                            <strong>Setor:</strong> ${setorNome}
                        </button>
                    </h2>

                    <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="heading${setorId}" data-bs-parent="#accordionDiv">

                    <div class="accordion-body d-flex justify-content-end">
                            <input type="hidden" id="setores[${setorId}][id]" value="${setorId}">
                            <label class="form-label mb-3">Recebido: 
                                <input type="number" class="form-control number" id="setores[${setorId}][earned]" value="${earned ?? 1000}">
                            </label>
                            <label class="form-label mb-1">Diária: 
                                <input type="number" class="form-control number" id="setores[${setorId}][diaria]" value="${employee_pay ?? 100}">
                            </label>
                            <label class="form-label mb-1">Líder: 
                                <input type="number" class="form-control number" id="setores[${setorId}][lider]" value="${leader_pay ?? 122}">
                            </label>
                            <label class="form-label mb-3">Comissão: 
                                <input type="number" class="form-control number" id="setores[${setorId}][comissao]" value="${comission ?? 8}">
                            </label>
                        </div>
                        <div class="d-flex justify-content-center d-flex bd-highlight">
                            @if ($establishment?->id ?? false)
                                <button type="button" class="btn btn-info mt-2 p-2 flex-fill bd-highlight" onclick="saveSection('${setorId}', '${setorNome}', {{ $establishment?->id }})">Salvar</button>
                                <button type="button" class="btn btn-danger mt-2 p-2 flex-fill bd-highlight" onclick="removeSection('${setorId}', '${setorNome}', {{ $establishment?->id }})">Remover</button>
                                
                            @else
                                <button type="button" style="#63666A" class="mt-2 p-2 flex-fill bd-highlight" onclick="" disabled>Salvar</button>
                                <button type="button" class="btn btn-danger mt-2 p-2 flex-fill bd-highlight" onclick="removeSection('${setorId}', '${setorNome}', {{ 0 }})">Remover</button>

                            @endif
                        </div>
                        </div>
                </div>
            `;
    }

    function loadExistingRegisteredSections(companySections, sections){
        let setorSelect = document.getElementById("sectionSelect");
        //let setorId = setorSelect.value;
        let setorNome = setorSelect.options[setorSelect.selectedIndex].dataset.nome;       
        



        for (let setSection of companySections) {
            let div = document.createElement("div");
            div.style.backgroundColor = "#E6F7FF";
            div.className = "mb-1 pt-1 form-label rounded-3 d-flex flex-column align-items-center";
            div.id = `setor-${setSection?.id}`;

            let collapseId = "collapse" + setSection?.id;
            let sectioName = sections.find(section => section.id === setSection.section_id);
            let sectionContent = createSectionCard(collapseId, setSection.section_id, sectioName.name, setSection.earned, setSection.employeePay, setSection.leaderPay, setSection.leaderComission);
            
            console.log(setSection.section_id);
            //setorSelect.options[setSection.section_id].remove();
            for (let i = 0; i < setorSelect.options.length; i++) {
                if (setorSelect.options[i].value == setSection.section_id) {
                    setorSelect.remove(i); 
                    break;  
                }
            }
            if (sectionContent) {
                div.innerHTML = sectionContent;
                document.getElementById("selectedSections").appendChild(div);
            } else {
                console.warn("loadExistingRegisteredSections returned empty content");
            }
        }    
    
    }
    
    function addLabelForSection() {
        
        let setorSelect = document.getElementById("sectionSelect");
        let setorId = setorSelect.value;
        let setorNome = setorSelect.options[setorSelect.selectedIndex].dataset.nome;

        if (setorId === "empty") {
            alert("Por favor, selecione um setor antes de adicionar.");
            return;
        }

        let div = document.createElement("div");
        div.style.backgroundColor = "#E6F7FF";
        div.className = "mb-1 pt-1 form-label rounded-3 d-flex flex-column align-items-center";
        //div.style.border = "2px solid #CCE0FF";
        div.id = `setor-${setorId}`;

        let collapseId = 'collapse' + setorId; 

        div.innerHTML = createSectionCard(collapseId, setorId, setorNome);

        document.getElementById("selectedSections").appendChild(div);

        setorSelect.options[setorSelect.selectedIndex].remove();
    }

    function saveSection(sectionId, sectionName, establishmentId) {
        // Extração dos valores dos campos
        let employeePay = document.getElementById(`setores[${sectionId}][diaria]`).value;
        let leaderPay = document.getElementById(`setores[${sectionId}][lider]`).value;
        let comission = document.getElementById(`setores[${sectionId}][comissao]`).value;
        let earned = document.getElementById(`setores[${sectionId}][earned]`).value;

        console.log(sectionId);
        // Enviar dados via AJAX
        $.ajax({
            url: '{{ route('companyHasSection.storeObject') }}',  // URL da rota
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Garantir CSRF token
            },
            data: {
                establishment_id: establishmentId,
                section_id: sectionId,
                employee_pay: employeePay,
                leader_pay: leaderPay,
                leaderComission: comission,
                earned: earned
            },
            success: function(response) {
                Swal.fire({
                    title: response?.title ?? 'Sucesso!',
                    text: response?.message ?? 'Sucesso na ação!',
                    icon: response?.type ?? 'success'
                }).then((result) => {
                    //window.location.reload();
                });
            },
            error: function(response) {
                response = JSON.parse(response.responseText); // Captura o erro
                Swal.fire({
                    title: response?.title ?? 'Oops!',
                    html: response?.message?.replace(/\n/g, '<br>') ?? 'Erro na ação!',
                    icon: response?.type ?? 'error'
                });
            }
        });
    }

    function removeSection(sectionId, sectionName, establishmentID) {
        let sectionDiv = document.getElementById(`setor-${sectionId}`);
        if (sectionDiv) {
            sectionDiv.remove();
        }

        let setorSelect = document.getElementById("sectionSelect");
        let option = document.createElement("option");
        option.value = sectionId;
        option.textContent = sectionName;
        option.dataset.nome = sectionName;

        setorSelect.appendChild(option);

        if (establishmentID) {
            $.ajax({
                url: `{{ route('companyHasSection.remove') }}`, 
                type: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                data: {
                    establishment_id: establishmentID,
                    section_id: sectionId
                },
                success: function(response) {
                    Swal.fire({
                    title: response?.title ?? 'Sucesso!',
                    text: response?.message ?? 'Sucesso na ação!',
                    icon: response?.type ?? 'success'
                }).then((result) => {
                    window.location.reload();
                });
                },
                error: function(response) {
                    Swal.fire({
                        title: "Erro!",
                        text: "Não foi possível remover o setor.",
                        icon: "error"
                    });
                }
            });
        }
    }


    function getSections(companyId){
        let sections = @json($sections);
        let sectionsData = [];

        for (let section of sections) {
            let sectionId = section.id;

            let employeePayElement = document.getElementById(`setores[${sectionId}][diaria]`);
            let leaderPayElement = document.getElementById(`setores[${sectionId}][lider]`);
            let comissionElement = document.getElementById(`setores[${sectionId}][comissao]`);
            let earnedElement = document.getElementById(`setores[${sectionId}][earned]`);
            
            // Check if the elements exist before accessing the 'value' property
            if (employeePayElement && leaderPayElement && comissionElement && earnedElement) {
                let employeePay = employeePayElement.value;
                let leaderPay = leaderPayElement.value;
                let comission = comissionElement.value;
                let earned = earnedElement.value;
                console.log(employeePay, leaderPay, comission, earned);
                
                // Ensure none of the values are null or empty
                if (employeePay && leaderPay && comission && earned) {
                    sectionsData.push({
                        company_id: companyId,
                        section_id: sectionId,
                        employeePay: employeePay,
                        leaderPay: leaderPay,
                        leaderComission: comission,
                        earned: earned
                    });
                }
            }
        }
            
            
        console.log(sectionsData);
        $.ajax({
            url: '{{ route('companyHasSection.storeArray') }}',  // URL da rota
            type: 'POST',
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  // Garantir CSRF token
            },
            data: { sections: sectionsData },
            success: function(response) {
                Swal.fire({
                    title: response?.title ?? 'Sucesso!',
                    text: response?.message ?? 'Sucesso na ação!',
                    icon: response?.type ?? 'success'
                }).then((result) => {
                    //window.location.reload();
                });
            },
            error: function(response) {
                response = JSON.parse(response.responseText); // Captura o erro
                Swal.fire({
                    title: response?.title ?? 'Oops!',
                    html: response?.message?.replace(/\n/g, '<br>') ?? 'Erro na ação!',
                    icon: response?.type ?? 'error'
                });
            }
        });
    }

    function post() {
        
 
        $.ajax({
            url: '{{ route('companies.store') }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: $('#form-edit-establishment').serialize(),
            success: function(response) {
                Swal.fire({
                    title: response?.title ?? 'Sucesso!',
                    text: response?.message ?? 'Sucesso na ação!',
                    icon: response?.type ?? 'success'
                }).then(() => {
                    //$('#form-edit-establishment')[0].reset();
                    
                    if (response.company_id) {
                        getSections(response.company_id);
                    }
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
            url: "{{ route('companies.update', '') }}" + '/' + id,
            type: 'PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: $('#form-edit-establishment').serialize(),
            success: function(response) {
                Swal.fire({
                    title: response?.title ?? 'Sucesso!',
                    text: response?.message ?? 'Sucesso na ação!',
                    icon: response?.type ?? 'success'
                }).then((result) => {
                    $('#form-edit-establishment')[0].reset();

                    //window.location.reload();
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

    $(document).ready(function () {
        let cnpjMask = new Inputmask('99.999.999/9999-99', { 
            placeholder: ' ', 
            clearIncomplete: true 
        });
        cnpjMask.mask('.cnpj');

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
    
    window.onload = function() {
        if (companySections.length > 0) {
            loadExistingRegisteredSections(companySections,sections);
        }
    };

</script>