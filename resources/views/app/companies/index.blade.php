<x-app-layout>
    <div class="container">

        <div class="mb-3">
            <a href="{{ route('companies.create') }}" class="btn btn-outline-primary w-100">Cadastrar Estabelecimento</a>
        </div>

        <div class="card pb-3">
            <h5 class="card-header">Estabelecimentos</h5>
            <div class="table-responsive text-nowrap">
                <table id="table-companies" class="table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Documento</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    $(document).ready(function() {
        $('#table-companies').DataTable({
            processing: true,
            serverSide: false,
            pagingType: 'simple',
            responsive: true,
             
            ajax: '{{ route('companies.table') }}',
            columns: [
                { data: 'name', name: 'name' },
                { data: 'document', name: 'document' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.2.2/i18n/pt-BR.json',
            },
        });
    });

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