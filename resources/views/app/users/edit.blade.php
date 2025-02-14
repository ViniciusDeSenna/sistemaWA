<x-app-layout>
    <div class="container">
        <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Cadastrando usuário</h5>
            {{-- <small class="text-muted float-end">Default label</small> --}}
        </div>
        <div class="card-body">
            <form>
            <div class="mb-3">
                <label class="form-label" for="basic-default-fullname">Nome</label>
                <input type="text" class="form-control" id="basic-default-fullname" name="name" placeholder="João Doe" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="basic-default-company">Email</label>
                <input type="email" class="form-control" id="basic-default-company"name="email" placeholder="exemplo@exemplo.com" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="basic-default-company">Senha</label>
                <input type="password" class="form-control" id="basic-default-company"name="password" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="basic-default-company">Confirmar Senha</label>
                <input type="password" class="form-control" id="basic-default-company"name="confirm_password" />
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            </form>
        </div>
        </div>
    </div>
</x-app-layout>