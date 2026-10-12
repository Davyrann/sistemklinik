@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
    <style>
        div.dt-container { font-size: 14px; color: #0F172A; }
        div.dt-container .dt-layout-row { padding: 14px 24px; margin: 0; }
        div.dt-container .dt-search input,
        div.dt-container .dt-length select { border-color: #E2E8F0; border-radius: 6px; padding: 6px 12px; font-size: 14px; }
        div.dt-container .dt-paging .dt-paging-button { border-radius: 6px; padding: 6px 12px; }
        table.dataTable > thead > tr > th { background-color: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #64748B; padding: 12px 24px; }
        table.dataTable > thead > tr > th span.dt-column-order { opacity: 1; color: #0D9488; }
        table.dataTable > thead > tr > th.dt-ordering-asc span.dt-column-order:before,
        table.dataTable > thead > tr > th.dt-ordering-desc span.dt-column-order:after { opacity: 1; }
        table.dataTable > thead > tr > th:hover span.dt-column-order { color: #0F766E; }
        table.dataTable > tbody > tr > td,
        table.dataTable > tbody > tr > th { border-top: 1px solid #E2E8F0; padding: 12px 24px; }
        table.dataTable th.dt-type-date, table.dataTable td.dt-type-date,
        table.dataTable th.dt-type-numeric, table.dataTable td.dt-type-numeric { text-align: left; }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('table.datatable').forEach(function (table) {
                new DataTable(table, {
                    pageLength: 25,
                    lengthMenu: [10, 25, 50, -1],
                    order: (function () {
                        var o = table.getAttribute('data-default-order');
                        return o ? [[parseInt(o.split(',')[0]), o.split(',')[1]]] : [];
                    })(),
                    language: {
                        search: 'Cari:',
                        lengthMenu: 'Tampilkan _MENU_',
                        info: 'Menampilkan _START_–_END_ dari _TOTAL_',
                        infoEmpty: 'Menampilkan 0–0 dari 0',
                        infoFiltered: '(disaring dari _MAX_)',
                        zeroRecords: 'Tidak ada data yang cocok',
                        emptyTable: 'Belum ada data.',
                        paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                    },
                });
            });
        });
    </script>
@endpush
