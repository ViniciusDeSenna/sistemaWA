<x-app-layout>
    <div class="container">
        <div class="md-3 mb-3">
            <a href="{{ route('daily-rate.create') }}" class="btn btn-outline-primary w-100">Registrar Diária</a>
        </div>

        <div class="card pb-3">
            <h5 class="card-header">Diárias</h5>
            <div class="table-responsive text-nowrap">
                <table id="table-daily-rate" class="table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Colaborador</th>
                            <th>Estabelecimento</th>
                            <th>Hora Início</th>
                            <th>Hora Fim</th>
                            <th>Horas Totais</th>
                            <th>Valor Hora</th>
                            <th>Valor Acréscimos</th>
                            <th>Valor Custos</th>
                            <th>Valor Total</th>
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
        $('#table-daily-rate').DataTable({
            processing: true,
            serverSide: false,
            pagingType: 'simple',
            responsive: true,
            rowReorder: {
                selector: 'td:nth-child(2)',
                update: true
            },
            ajax: '{{ route('daily-rate.table') }}',
            columns: [
                { data: 'collaborators_name', name: 'collaborators_name' },
                { data: 'companies_name', name: 'companies_name' },
                { data: 'daily_rate_start', name: 'daily_rate_start' },
                { data: 'daily_rate_end', name: 'daily_rate_end' },
                { data: 'daily_rate_daily_total_time', name: 'daily_rate_daily_total_time' },
                { data: 'daily_rate_hourly_rate', name: 'daily_rate_hourly_rate' },
                { data: 'daily_rate_addition', name: 'daily_rate_addition' },
                { data: 'daily_rate_costs', name: 'daily_rate_costs' },
                { data: 'daily_rate_total', name: 'daily_rate_total' },
                { data: 'actions', name: 'actions' },
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.2.2/i18n/pt-BR.json',
            },
        });
    });
</script>