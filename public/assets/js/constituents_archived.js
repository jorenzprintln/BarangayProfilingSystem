(function () {

    $(document).ready(function () {

        // ── Restore modal — set name and confirm link dynamically ──
        $('#restoreModal').on('show.bs.modal', function (event) {
            const button        = $(event.relatedTarget);
            const constituentId = button.data('constituent-id');
            const constituentName = button.data('constituent-name');
            $(this).find('#constituentName').text(constituentName);
            $(this).find('#restoreConfirmBtn').attr(
                'href',
                'index.php?controller=constituents&action=restoreConstituent&id=' + constituentId
            );
        });

        let searchTimer;
        $('#search-input').on('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () {
                $('#search-form').submit();
            }, 400);
        });

    });

})();