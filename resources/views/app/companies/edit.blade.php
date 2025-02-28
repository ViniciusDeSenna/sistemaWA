<x-app-layout>
    <div class="container">
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
                    <input type="text" class="form-control" id="basic-default-fullname" name="document" placeholder="XX.XXX.XXX/0001-XX" value="{{ $establishment?->document ?? ''}}" />
                </div>

                <div class="mb-3">
                    <label class="form-label" for="basic-default-fullname">Valor da Diária/Hora</label>
                    <input type="number" class="form-control" id="basic-default-fullname" name="value" step="0.01" placeholder="R$00,00" value="{{ $establishment?->time_value ?? ''}}" />
                </div>
                <div class="mb-3">
                        <label class="form-label" for="basic-default-text">Rede Pertencente</label>
                        <textarea id="basic-default-message" class="form-control" placeholder="Nome da Rede" name="category">{!! $establishment?->chain_of_stores ?? '' !!}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="basic-default-message">Observação</label>
                    <textarea id="basic-default-message" class="form-control" placeholder="Alguma observação?" name="observation">{!! $establishment?->observation ?? '' !!}</textarea>
                </div>
            </form>
         </div>
    
        <div class="card-footer">
                <div class="d-flex justify-content-start">
                    <button type="button" class="btn btn-primary right" onclick="window.history.back();">Voltar</button>
                </div>
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

    <script>
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
                }).then((result) => {
                    $('#form-edit-establishment')[0].reset();

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
</script>

</x-app-layout>