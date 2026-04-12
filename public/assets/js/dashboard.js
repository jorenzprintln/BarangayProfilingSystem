(function () {

    // ── Animate counter ──
    function animateCounter(id, target) {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = '0';
        if (target === 0) return;
        let count = 0;
        const step = Math.max(1, Math.ceil(target / 80));
        const interval = setInterval(() => {
            count = Math.min(count + step, target);
            el.textContent = count.toLocaleString();
            if (count >= target) clearInterval(interval);
        }, 16);
    }

    // ── Sort table ──
    window.sortTable = function (columnIndex, header) {
        const table = document.querySelector('.dash-table');
        const rows  = Array.from(table.tBodies[0].rows);
        const isAsc = header?.getAttribute('data-sort-order') === 'asc';
        const order = isAsc ? 'desc' : 'asc';

        rows.sort((a, b) => {
            let A = a.cells[columnIndex].textContent.trim();
            let B = b.cells[columnIndex].textContent.trim();
            if (columnIndex === 3) {
                A = new Date(A).getTime() || 0;
                B = new Date(B).getTime() || 0;
            } else {
                A = A.toLowerCase();
                B = B.toLowerCase();
            }
            if (A < B) return isAsc ? -1 : 1;
            if (A > B) return isAsc ?  1 : -1;
            return 0;
        });

        rows.forEach(row => table.tBodies[0].appendChild(row));

        document.querySelectorAll('th.sortable').forEach(th => {
            th.removeAttribute('data-sort-order');
            const ic = th.querySelector('.sort-icon');
            if (ic) ic.textContent = '\u21C5';
        });

        if (header) {
            header.setAttribute('data-sort-order', order);
            const ic = header.querySelector('.sort-icon');
            if (ic) ic.textContent = order === 'asc' ? '\u2191' : '\u2193';
        }
    };

    // ── Load counts via fetch, fall back to PHP-injected values ──
    function loadCounts() {
        fetch('index.php?controller=dashboard&action=getCounts&_=' + Date.now())
            .then(r => r.json())
            .then(data => {
                animateCounter('constituentsCount',   data.constituents);
                animateCounter('householdCount',      data.households);
                animateCounter('familiesCount',       data.families);
                animateCounter('recentFamiliesCount', data.seniorCitizens);
            })
            .catch(() => {
                const c = window.DASHBOARD_COUNTS || {};
                animateCounter('constituentsCount',   c.constituents   || 0);
                animateCounter('householdCount',      c.households     || 0);
                animateCounter('familiesCount',       c.families       || 0);
                animateCounter('recentFamiliesCount', c.seniorCitizens || 0);
            });
    }

    loadCounts();

    // ── Default sort by date descending on load ──
    document.addEventListener('DOMContentLoaded', function () {
        const dateHeader = document.querySelector('.dash-table th:nth-child(4)');
        if (dateHeader) {
            dateHeader.setAttribute('data-sort-order', 'desc');
            const ic = dateHeader.querySelector('.sort-icon');
            if (ic) ic.textContent = '\u2193';
            sortTable(3, dateHeader);
        }
    });

})();