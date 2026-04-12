(function () {

    const searchInput = document.getElementById('searchInput');
    const searchForm  = document.getElementById('searchForm');

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

    const modal     = document.getElementById('rbiConfirmModal');
    const modalName = document.getElementById('rbiModalName');
    const modalLink = document.getElementById('rbiModalConfirm');
    const btnCancel = document.getElementById('rbiModalCancel');

    // Open modal when any Generate button is clicked
    document.querySelectorAll('.rbi-generate-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            modalName.textContent = btn.dataset.name;
            modalLink.href        = btn.dataset.href;
            modal.classList.add('active');
            setTimeout(function () { modalLink.focus(); }, 50);
        });
    });

    // Close modal
    function closeModal() {
        modal.classList.remove('active');
    }

    btnCancel.addEventListener('click', closeModal);

    // Click on backdrop closes modal
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    // Escape key closes modal
    document.addEventListener('keydown', function (e) {
        if (modal.classList.contains('active') && e.key === 'Escape') closeModal();
    });

    // Close after confirm link clicked (opens in new tab)
    modalLink.addEventListener('click', function () {
        setTimeout(closeModal, 100);
    });

})();