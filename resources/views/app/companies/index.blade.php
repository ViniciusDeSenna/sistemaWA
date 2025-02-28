<x-app-layout>
    
    <div class="mb-3">
        <a href="{{ route('companies.create') }}" class="btn btn-outline-primary w-100">Cadastrar Estabelecimento</a>
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
                    @foreach ($companies as $establishment)
                        <tr>
                            <td>
                                <i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{ $establishment->name }}</strong>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toogle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('companies.edit', [$establishment->id]) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit</a>
                                    
                                    <button class="dropdown-item" href="javascript:void(0);" type="button" onclick="remove({{ $establishment->id }})">
                                        <i class="bx bx-trash me-1"></i> Delete
                                    </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

<script>
    function remove(id){
        Swal.fire({
            title: 'Você tem certeza?',
            text: "Esta ação não pode ser desfeita!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, remover!',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => { 
            if (result.isConfirmed){
                $.ajax({
                    url: "{{ route("companies.destroy", '') }}" + '/' + id,
                    type: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response){
                        Swal.fire({
                            title: response?.title ?? 'Sucesso!',
                            text: response?.message ?? 'Sucesso na ação!',
                            icon: response?.type ?? 'success'
                        }).then((result) => {
                            window.location.reload();
                        });
                    },
                    error: function(response){
                        response = JSON.parse(response.responseText);
                        Swal.fire({
                            title: response?.title ?? 'Oops!',
                                                    html: response?.message?.replace(/\n/g, '<br>') ?? 'Erro na ação!', // Substitui as quebras de linha por <br>
                            icon: response?.type ?? 'error'    
                        });
                    }
                });
            }
        });


    }
</script>