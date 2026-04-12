(function () {

    /* ─────────────────────────────────────────
       Search — debounced auto-submit
    ───────────────────────────────────────── */
    const searchInput = document.getElementById('searchInput');
    const searchForm  = document.getElementById('search-form');

    if (searchInput && searchForm) {
        let searchTimer;

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () {
                searchForm.submit();
            }, 400);
        });

        // Reset to page 1 on new search
        searchForm.addEventListener('submit', function () {
            const pageInputs = searchForm.querySelectorAll('input[name="page"]');
            pageInputs.forEach(function (el) { el.remove(); });
        });
    }

    /* ─────────────────────────────────────────
       Confirmation Modal
    ───────────────────────────────────────── */
    const modal      = document.getElementById('ofwConfirmModal');
    const modalName  = document.getElementById('ofwModalName');
    const btnCancel  = document.getElementById('ofwModalCancel');
    const btnConfirm = document.getElementById('ofwModalConfirm');
    let pendingForm  = null;

    document.querySelectorAll('.ofw-generate-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            pendingForm = btn.closest('form');
            modalName.textContent = btn.dataset.name;
            modal.classList.add('active');
            setTimeout(function () { btnConfirm.focus(); }, 50);
        });
    });

    function closeModal() {
        modal.classList.remove('active');
        setTimeout(function () { pendingForm = null; }, 200);
    }

    btnCancel.addEventListener('click', closeModal);

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    document.addEventListener('keydown', function (e) {
        if (modal.classList.contains('active') && e.key === 'Escape') closeModal();
    });

    btnConfirm.addEventListener('click', function () {
        if (pendingForm) {
            pendingForm.submit();
            closeModal();
        }
    });

})();