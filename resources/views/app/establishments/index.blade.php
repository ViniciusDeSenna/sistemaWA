<x-app-layout>
    
    <div class="mb-3">
        <a href="{{ route('establishments.create') }}" class="btn btn-outline-primary w-100">Cadastrar Estabelecimento</a>
    </div>

    <div class="card">
        <h5 class="card-header">Estabelecimentos</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Estabelecimentos</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($establishments as $establishment)
                        <tr>
                            <td>
                                <i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{ $establishment->name }}</strong>
                            </td>
                        </tr>
                    
                    
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>