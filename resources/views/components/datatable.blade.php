@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
    <style>
        /* DataTable Container: overflow-x: auto, width: 100%, -webkit-overflow-scrolling: touch */
        div.dt-container {
            font-size: 14px;
            color: var(--color-text, #0F172A);
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            position: relative;
            box-sizing: border-box;
        }

        div.dt-container .dt-layout-row {
            width: 100% !important;
            box-sizing: border-box;
        }

        div.dt-container .dt-layout-cell {
            box-sizing: border-box;
        }

        div.dt-container .dt-layout-cell.dt-layout-full {
            width: 100% !important;
            display: block !important;
        }

        div.dt-container .dt-layout-table,
        div.dt-container .dt-scroll,
        div.dt-container .dt-scroll-body {
            width: 100% !important;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table.dataTable,
        table.datatable,
        div.dt-container table {
            width: 100% !important;
            min-width: 100% !important;
            margin: 0 !important;
            border-collapse: collapse;
            table-layout: auto;
        }

        /* Toolbar: px-4 py-3 gap-3 border-b border-border */
        div.dt-container .dt-layout-row:first-child,
        div.dt-container div.dt-layout-row:first-of-type,
        div.dt-container .dt-toolbar {
            padding: 12px 16px; /* px-4 py-3 */
            gap: 12px; /* gap-3 */
            border-bottom: 1px solid var(--color-border, #CBD5E1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            margin: 0;
            box-sizing: border-box;
        }

        div.dt-container .dt-layout-row:first-child .dt-layout-cell,
        div.dt-container div.dt-layout-row:first-of-type .dt-layout-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        div.dt-container .dt-search,
        div.dt-container .dt-length {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--color-text-muted, #475569);
        }

        div.dt-container .dt-search input,
        div.dt-container .dt-length select {
            border: 1px solid var(--color-border, #CBD5E1);
            border-radius: var(--radius-md, 8px);
            padding: 6px 12px;
            font-size: 14px;
            height: 36px;
            color: var(--color-text, #0F172A);
            background-color: var(--color-surface, #FFFFFF);
            transition: border-color 150ms ease-in-out, box-shadow 150ms ease-in-out;
            box-sizing: border-box;
        }

        div.dt-container .dt-search input:focus,
        div.dt-container .dt-length select:focus {
            outline: 2px solid var(--color-primary-600, #0D9488);
            outline-offset: 2px;
            border-color: var(--color-primary-600, #0D9488);
        }

        @media (pointer: coarse), (max-width: 767px) {
            div.dt-container .dt-search input,
            div.dt-container .dt-length select {
                height: 44px;
            }
        }

        /* Header rhythm: px-3 py-2 text-xs font-medium text-text-muted bg-bg border-b border-border-subtle */
        table.dataTable > thead > tr > th {
            background-color: var(--color-bg, #F1F5F9);
            border-bottom: 1px solid var(--color-border-subtle, #E2E8F0);
            color: var(--color-text-muted, #475569);
            font-size: 12px;
            font-weight: 500;
            line-height: 1rem;
            padding: 8px 12px; /* px-3 py-2 */
            text-align: left;
            white-space: nowrap;
            box-sizing: border-box;
        }

        table.dataTable > thead > tr > th:first-child,
        table.datatable > thead > tr > th:first-child,
        .table-container table > thead > tr > th:first-child {
            padding-left: 1.5rem !important; /* 24px desktop */
        }

        @media (max-width: 640px) {
            table.dataTable > thead > tr > th:first-child,
            table.datatable > thead > tr > th:first-child,
            .table-container table > thead > tr > th:first-child {
                padding-left: 1rem !important; /* 16px mobile */
            }
        }

        table.dataTable > thead > tr > th:last-child,
        table.datatable > thead > tr > th:last-child,
        .table-container table > thead > tr > th:last-child {
            padding-right: 1.5rem !important; /* 24px desktop */
        }

        @media (max-width: 640px) {
            table.dataTable > thead > tr > th:last-child,
            table.datatable > thead > tr > th:last-child,
            .table-container table > thead > tr > th:last-child {
                padding-right: 1rem !important; /* 16px mobile */
            }
        }

        table.dataTable > thead > tr > th span.dt-column-order {
            opacity: 1;
            color: var(--color-primary-600, #0D9488);
        }

        table.dataTable > thead > tr > th.dt-ordering-asc span.dt-column-order:before,
        table.dataTable > thead > tr > th.dt-ordering-desc span.dt-column-order:after {
            opacity: 1;
            color: var(--color-primary-700, #0F766E);
        }

        table.dataTable > thead > tr > th:hover span.dt-column-order {
            color: var(--color-primary-700, #0F766E);
        }

        /* Cell rhythm: px-3 py-3 text-sm border-t border-border-subtle hover:bg-primary-50 */
        table.dataTable > tbody > tr > td,
        table.dataTable > tbody > tr > th {
            border-top: 1px solid var(--color-border-subtle, #E2E8F0);
            padding: 12px 12px; /* px-3 py-3 */
            font-size: 14px; /* text-sm */
            line-height: 1.25rem;
            color: var(--color-text, #0F172A);
            vertical-align: middle;
            box-sizing: border-box;
        }

        table.dataTable > tbody > tr > td:first-child,
        table.datatable > tbody > tr > td:first-child,
        .table-container table > tbody > tr > td:first-child {
            padding-left: 1.5rem !important; /* 24px desktop */
        }

        @media (max-width: 640px) {
            table.dataTable > tbody > tr > td:first-child,
            table.datatable > tbody > tr > td:first-child,
            .table-container table > tbody > tr > td:first-child {
                padding-left: 1rem !important; /* 16px mobile */
            }
        }

        table.dataTable > tbody > tr > td:last-child,
        table.datatable > tbody > tr > td:last-child,
        .table-container table > tbody > tr > td:last-child {
            padding-right: 1.5rem !important; /* 24px desktop */
        }

        @media (max-width: 640px) {
            table.dataTable > tbody > tr > td:last-child,
            table.datatable > tbody > tr > td:last-child,
            .table-container table > tbody > tr > td:last-child {
                padding-right: 1rem !important; /* 16px mobile */
            }
        }

        table.dataTable > tbody > tr > td[colspan],
        table.datatable > tbody > tr > td[colspan],
        .table-container table > tbody > tr > td[colspan] {
            padding: 3rem 1.5rem !important;
            text-align: center !important;
            width: 100% !important;
        }

        table.dataTable > tbody > tr:hover,
        table.dataTable > tbody > tr:hover > td,
        table.dataTable > tbody > tr:hover > th {
            background-color: var(--color-primary-50, #F0FDFA) !important;
        }

        /* Pagination: px-4 py-3 border-t border-border. Active page button bg-primary-700 text-white rounded-md h-9 min-w-9 (pointer-coarse: h-11 min-w-11) */
        div.dt-container .dt-layout-row:last-child,
        div.dt-container div.dt-layout-row:last-of-type,
        div.dt-container .dt-pagination-row {
            padding: 12px 16px; /* px-4 py-3 */
            border-top: 1px solid var(--color-border, #CBD5E1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin: 0;
            box-sizing: border-box;
        }

        div.dt-container .dt-info {
            font-size: 12px;
            color: var(--color-text-muted, #475569);
            padding: 0;
            margin: 0;
        }

        div.dt-container .dt-paging {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        div.dt-container .dt-paging .dt-paging-button {
            border-radius: var(--radius-md, 8px);
            height: 36px;
            min-width: 36px;
            padding: 0 12px;
            font-size: 14px;
            font-weight: 500;
            color: var(--color-text, #0F172A);
            background-color: transparent;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            cursor: pointer;
            user-select: none;
            transition: background-color 150ms ease-in-out, border-color 150ms ease-in-out, color 150ms ease-in-out;
        }

        div.dt-container .dt-paging .dt-paging-button:hover:not(.disabled):not(.current):not(.active) {
            background-color: var(--color-bg, #F1F5F9);
            border-color: var(--color-border-subtle, #E2E8F0);
            color: var(--color-text, #0F172A);
        }

        div.dt-container .dt-paging .dt-paging-button.disabled,
        div.dt-container .dt-paging .dt-paging-button:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            color: var(--color-text-subtle, #64748B);
            background-color: transparent;
        }

        div.dt-container .dt-paging .dt-paging-button.current,
        div.dt-container .dt-paging .dt-paging-button.active,
        div.dt-container .dt-paging .dt-paging-button.current:hover,
        div.dt-container .dt-paging .dt-paging-button.active:hover {
            background-color: var(--color-primary-700, #0F766E) !important;
            color: #FFFFFF !important;
            border-color: var(--color-primary-700, #0F766E) !important;
            border-radius: var(--radius-md, 8px);
            height: 36px;
            min-width: 36px;
            font-weight: 600;
        }

        @media (pointer: coarse), (max-width: 767px) {
            div.dt-container .dt-paging .dt-paging-button {
                height: 44px;
                min-width: 44px;
            }
            div.dt-container .dt-paging .dt-paging-button.current,
            div.dt-container .dt-paging .dt-paging-button.active,
            div.dt-container .dt-paging .dt-paging-button.current:hover,
            div.dt-container .dt-paging .dt-paging-button.active:hover {
                height: 44px;
                min-width: 44px;
            }
        }

        /* Right-aligned tabular numbers for currency and counts */
        table.dataTable th.dt-type-numeric,
        table.dataTable td.dt-type-numeric,
        table.dataTable th.text-right,
        table.dataTable td.text-right,
        table.dataTable th.dt-right,
        table.dataTable td.dt-right,
        table.dataTable th[data-type="currency"],
        table.dataTable td[data-type="currency"],
        table.dataTable th[data-type="num"],
        table.dataTable td[data-type="num"],
        table.dataTable th[data-type="numeric"],
        table.dataTable td[data-type="numeric"],
        table.dataTable .dt-type-numeric,
        table.dataTable td.font-mono.text-right {
            text-align: right;
            font-variant-numeric: tabular-nums;
            font-family: var(--font-mono, 'JetBrains Mono', ui-monospace, monospace);
        }

        table.dataTable th.dt-type-date,
        table.dataTable td.dt-type-date {
            text-align: left;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script>
            document.querySelectorAll('table.datatable').forEach(function (table) {
                const tbody = table.querySelector('tbody');
                const rows = tbody ? tbody.querySelectorAll('tr') : [];
                const hasColspanRow = tbody && tbody.querySelector('tr td[colspan]');

                // If table is empty (e.g. only has 1 empty state row with colspan), don't initialize DataTables
                if (rows.length === 1 && hasColspanRow) {
                    table.style.width = '100%';
                    table.classList.add('w-full');
                    return;
                }

                new DataTable(table, {
                    autoWidth: false,
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
    </script>
@endpush

@if (isset($slot) && !$slot->isEmpty())
    <div {{ $attributes->merge(['class' => 'dt-container-wrapper w-full overflow-x-auto']) }} style="-webkit-overflow-scrolling: touch;">
        {{ $slot }}
    </div>
@endif
