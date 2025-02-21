<x-app-layout>
    <div class="container">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Cadastrando usuário</h5>
            </div>
            <div class="card-body">
                <form id="form-edit-collaborator">
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-fullname">Nome</label>
                        <input type="text" class="form-control" id="basic-default-fullname" name="name" placeholder="João Doe" value="{{ $collaborator?->name ?? ''}}" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-fullname">Documento</label>
                        <input type="text" class="form-control" id="basic-default-fullname" name="document" placeholder="000.000.000-00" value="{{ $collaborator?->document ?? ''}}" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-message">Observação</label>
                        <textarea id="basic-default-message" class="form-control" placeholder="Alguma observação?" name="observation">{!! $collaborator?->observation ?? '' !!}</textarea>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-start">
                    <button type="button" class="btn btn-primary right" onclick="window.history.back();">Voltar</button>
                </div>
                @if ($collaborator?->id ?? false)
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary right" onclick="update({{ $collaborator?->id ?? null }})">Salvar</button>
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
    function post() {
        $.ajax({
            url: '{{ route('collaborators.store') }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: $('#form-edit-collaborator').serialize(),
            success: function(response) {
                Swal.fire({
                    title: response?.title ?? 'Sucesso!',
                    text: response?.message ?? 'Sucesso na ação!',
                    icon: response?.type ?? 'success'
                }).then((result) => {
                    $('#form-edit-collaborator')[0].reset();

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
            url: "{{ route('collaborators.update', '') }}" + '/' + id,
            type: 'PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: $('#form-edit-collaborator').serialize(),
            success: function(response) {
                Swal.fire({
                    title: response?.title ?? 'Sucesso!',
                    text: response?.message ?? 'Sucesso na ação!',
                    icon: response?.type ?? 'success'
                }).then((result) => {
                    $('#form-edit-collaborator')[0].reset();

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