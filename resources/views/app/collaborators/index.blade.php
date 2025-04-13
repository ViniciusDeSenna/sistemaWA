<x-app-layout>
    <div class="container">

        <div class="mb-3">
            <a href="{{ route('collaborators.create') }}" class="btn btn-outline-primary w-100">Cadastrar Colaborador</a>
        </div>

        <!-- Basic Bootstrap Table -->
        <div class="card pb-3">
            <h5 class="card-header">Colaboradores</h5>
            <div class="table-responsive text-nowrap">
                <table id="table-collaborators" class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <!--/ Basic Bootstrap Table -->
        
    </div>
</x-app-layout>

<script>
    $(document).ready(function() {
        $('#table-collaborators').DataTable({
            processing: true,
            serverSide: false,
            pagingType: 'simple',
            responsive: true,
            ajax: '{{ route('collaborators.table') }}',
            columns: [
                { data: 'name', name: 'name' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.2.2/i18n/pt-BR.json',
            },
        });
    });

    function remove(id) {
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
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('collaborators.destroy', '') }}" + '/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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