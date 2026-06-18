document.addEventListener('DOMContentLoaded', () => {
    if (typeof DataTable === 'undefined') {
        return;
    }

    document.querySelectorAll('table.admin-datatable').forEach((table) => {
        if (table.dataset.dtInit === '1') {
            return;
        }

        new DataTable(table, {
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            order: [],
            autoWidth: false,
            columnDefs: [
                { orderable: false, targets: 'no-sort' },
            ],
            language: {
                search: 'Search:',
                searchPlaceholder: 'Type to filter…',
                lengthMenu: 'Show _MENU_',
                info: '_START_–_END_ of _TOTAL_',
                infoEmpty: 'No entries',
                paginate: { next: '›', previous: '‹' },
            },
        });

        table.dataset.dtInit = '1';
    });
});
