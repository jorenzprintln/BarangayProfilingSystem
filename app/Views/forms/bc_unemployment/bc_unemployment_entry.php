<?php
$content = ob_start();
?>

<link rel="stylesheet" href="public/assets/css/good_moral_form.css">

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
            <svg fill="white" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"/>
            </svg>
        </div>
        <div>
            <h3>Certificate of Unemployment</h3>
            <p>Fill in the details below to generate the certificate</p>
        </div>
    </div>

    <form action="index.php?controller=forms&action=processBcUnemploymentEntry" target="_blank" method="POST">
        <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">

        <div class="form-card">
            <div class="form-card-header">
                <svg width="14" height="14" fill="#4361ee" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span class="form-card-header-label">Certificate Details</span>
            </div>

            <div class="form-card-body">

                <!-- Person Requesting -->
                <div class="field-group">
                    <label for="constituent_id">
                        Person Requesting
                        <span class="req">*</span>
                    </label>
                    <select class="form-control selectpicker"
                        id="constituent_id"
                        name="constituent_id"
                        data-live-search="true"
                        data-size="10"
                        title="Select a constituent..."
                        required>
                        <?php foreach ($constituents as $constituent): ?>
                            <option value="<?= htmlspecialchars($constituent['id']) ?>">
                                <?= htmlspecialchars(
                                    $constituent['last_name'] . ', ' .
                                    $constituent['first_name'] . ' ' .
                                    ($constituent['middle_name'] ?? '') . ' ' .
                                    ($constituent['suffix'] ?? '')
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="field-hint">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink:0;margin-top:1px">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Search and select the registered constituent requesting the certificate
                    </div>
                </div>

                <!-- Purpose -->
                <div class="field-group">
                    <label for="purpose">
                        Purpose of Requesting
                        <span class="req">*</span>
                    </label>
                    <textarea class="form-control" id="purpose" name="purpose" rows="5"
                        placeholder="e.g. For SSS benefit application, financial assistance..." required></textarea>
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

<script src="public/assets/js/bc_general_entry.js"></script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>