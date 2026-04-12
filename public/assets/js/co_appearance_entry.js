(function () {
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('coaForm');

        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');
                return;
            }

            event.preventDefault();

            form.setAttribute('target', '_blank');
            form.submit();
        });
    });
})();