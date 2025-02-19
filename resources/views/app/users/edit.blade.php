<x-app-layout>
    <div class="container">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Cadastrando usuário</h5>
                {{-- <small class="text-muted float-end">Default label</small> --}}
            </div>
            <div class="card-body">
                <form id="form-edit-user">
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-fullname">Nome</label>
                        <input type="text" class="form-control" id="basic-default-fullname" name="name" placeholder="João Doe" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-company">Email</label>
                        <input type="email" class="form-control" id="basic-default-company" name="email" placeholder="exemplo@exemplo.com" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-company">Senha</label>
                        <input type="password" class="form-control" id="basic-default-company" name="password" />
                    </div>
                    <div class="">
                        <label class="form-label" for="basic-default-company">Confirmar Senha</label>
                        <input type="password" class="form-control" id="basic-default-company" name="password_confirmation" />
                        <div class="form-text">A confirmação de senha deve ser igual a senha!</div>
                    </div>
                </form>
            </div>
            <hr class="m-0">
            <div class="card-body">
                <form id="form-permissions-user">
                    @foreach ($permissions as $permission)
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="permission-{{ $permission->id }}" name="permissions[{{ $permission->id }}]">
                            <label class="form-check-label" for="flexSwitchCheckDefault">{{ $permission->name }}</label>
                        </div>
                    @endforeach
                </form>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-primary right" onclick="post()">Salvar</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function post() {
        $.ajax({
            url: '{{ route('users.store') }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: $('#form-edit-user').serialize() + '&' + $('#form-permissions-user').serialize(),
            success: function(response) {
                Swal.fire({
                    title: response?.title ?? 'Sucesso!',
                    text: response?.message ?? 'Sucesso na ação!',
                    icon: response?.type ?? 'success'
                });

                console.log(response.data);

                $('#form-edit-user')[0].reset();
                $('#form-permissions-user')[0].reset();
            },
            error: function(response) {
                response = JSON.parse(response.responseText);
                Swal.fire({
                    title: response?.title ?? 'Oops!',
                    html: response?.message?.replace(/\n/g, '<br>') ?? 'Erro na ação!', // Substitui as quebras de linha por <br>
                    icon: response?.type ?? 'error'
                });
            }
        });
    }
</script>