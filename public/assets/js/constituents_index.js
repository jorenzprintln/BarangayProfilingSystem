(function () {

    // ── Table sort ──
    window.sortTable = function (columnIndex, element) {
        const table = document.querySelector('table');
        const tbody = table.querySelector('tbody');
        const rows  = Array.from(tbody.querySelectorAll('tr'));

        if (rows.length === 0 || rows[0].querySelector('td').getAttribute('colspan')) return;

        let sortOrder = element.getAttribute('data-sort-order') || 'asc';
        sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';

        document.querySelectorAll('th.sortable').forEach(function (th) {
            th.removeAttribute('data-sort-order');
        });
        element.setAttribute('data-sort-order', sortOrder);

        rows.sort(function (rowA, rowB) {
            const cellA = rowA.querySelectorAll('td')[columnIndex].textContent.trim();
            const cellB = rowB.querySelectorAll('td')[columnIndex].textContent.trim();

            if (columnIndex === 2) {
                const numA = parseInt(cellA) || 0;
                const numB = parseInt(cellB) || 0;
                return sortOrder === 'asc' ? numA - numB : numB - numA;
            }
            return sortOrder === 'asc'
                ? cellA.localeCompare(cellB, undefined, { sensitivity: 'base' })
                : cellB.localeCompare(cellA, undefined, { sensitivity: 'base' });
        });

        rows.forEach(function (row) { tbody.removeChild(row); });
        rows.forEach(function (row) { tbody.appendChild(row); });
    };

    // ── jQuery-dependent logic ──
    $(document).ready(function () {

        // Auto-submit on filter change
        $('#age-filter').on('change', function () {
            $('#filter-form').submit();
        });

        // Debounced search submit
        let searchTimer;
        $('#search-input').on('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () {
                $('#filter-form').submit();
            }, 400);
        });

        // Archive modal — set confirm link dynamically
        $('#archiveModal').on('show.bs.modal', function (event) {
            const button        = $(event.relatedTarget);
            const constituentId = button.data('constituent-id');
            $(this).find('#archiveConfirmBtn').attr(
                'href',
                'index.php?controller=constituents&action=removeConstituent&id=' + constituentId
            );
        });

        // Auto-dismiss success alert after 5 seconds
        setTimeout(function () {
            const alert = document.getElementById('success-alert');
            if (alert) $('#success-alert').alert('close');
        }, 5000);

    });

})();