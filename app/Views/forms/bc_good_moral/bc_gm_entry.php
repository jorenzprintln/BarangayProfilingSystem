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
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div>
            <h3>Certificate of Good Moral Character</h3>
            <p>Fill in the details below to generate the certificate</p>
        </div>
    </div>

    <form action="index.php?controller=forms&action=processBcGoodMoralEntry" target="_blank" method="POST">
        <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">

        <div class="form-card">
            <div class="form-card-header">
                <svg width="14" height="14" fill="#4361ee" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span class="form-card-header-label">Certificate Details</span>
            </div>

            <div class="form-card-body">

                <!-- Requesting Person -->
                <div class="field-group">
                    <label for="constituent_id">
                        Requesting Person
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
                        Purpose
                        <span class="req">*</span>
                    </label>
                    <textarea class="form-control" id="purpose" name="purpose" rows="5"
                        placeholder="e.g. For employment purposes, school requirements..." required></textarea>
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