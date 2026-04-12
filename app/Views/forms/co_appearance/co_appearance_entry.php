<?php
$content = ob_start();
?>
<link rel="stylesheet" href="public/assets/css/co_appearance_entry.css">

<div class="container-fluid px-4 mt-3">

    <!-- Back Button -->
    <a href="index.php?controller=home&action=forms" class="back-link">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        Back to Forms
    </a>

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title-icon">
            <svg width="22" height="22" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div>
            <h3>Certificate of Appearance</h3>
            <p>Fill in the details below to generate the certificate</p>
        </div>
    </div>

    <form id="coaForm" method="POST" action="index.php?controller=forms&action=processCoaEntry" novalidate>
        <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">

        <div class="form-card">
            <div class="form-card-header">
                <svg width="14" height="14" fill="#4361ee" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span class="form-card-header-label">Appearance Details</span>
            </div>

            <div class="form-card-body">

                <!-- Name -->
                <div class="field-group">
                    <label for="name">
                        Name
                        <span class="req">*</span>
                    </label>
                    <input type="text" class="form-control" id="name" name="name"
                        placeholder="e.g. MR. JUAN DELA CRUZ" required>
                    <div class="invalid-feedback">Please enter a name.</div>
                    <div class="field-hint">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink:0;margin-top:1px">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Full name of the person appearing
                    </div>
                </div>

                <!-- From -->
                <div class="field-group">
                    <label for="location">
                        From
                        <span class="req">*</span>
                    </label>
                    <input type="text" class="form-control" id="location" name="location"
                        placeholder="e.g. City Mayor's Office, Barangay Hall..." required>
                    <div class="invalid-feedback">Please enter a location.</div>
                    <div class="field-hint">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink:0;margin-top:1px">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Office or establishment where the person is appearing from
                    </div>
                </div>

                <!-- Reason -->
                <div class="field-group">
                    <label for="reason">
                        Reason
                        <span class="req">*</span>
                    </label>
                    <textarea class="form-control" id="reason" name="reason" rows="4"
                        placeholder="e.g. Inspection, Official Business..." required></textarea>
                    <div class="invalid-feedback">Please enter a reason.</div>
                </div>

            </div>

            <div class="form-card-footer">
                <button type="submit" class="btn-generate">
                    <svg width="15" height="15" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd"/>
                    </svg>
                    Generate Certificate
                </button>
            </div>
        </div>

    </form>
</div>

<script src="public/assets/js/co_appearance_entry.js?v=<?= filemtime('public/assets/js/co_appearance_entry.js') ?>"></script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>