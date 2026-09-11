document.addEventListener('DOMContentLoaded', function () {
    const filter = document.getElementById('delivery-zone-status-filter');
    const tableElement = document.getElementById('delivery-zones-table');
    if (!filter || !tableElement) return;

    filter.addEventListener('change', function () {
        const table = window.jQuery?.fn?.DataTable?.isDataTable(tableElement)
            ? window.jQuery(tableElement).DataTable()
            : null;
        if (!table) return;

        const baseUrl = tableElement.dataset.route;
        const url = new URL(baseUrl, window.location.origin);
        if (filter.value) url.searchParams.set('status', filter.value);
        table.ajax.url(url.toString()).load();
    });
});