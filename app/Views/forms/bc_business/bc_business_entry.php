<?php
$content = ob_start();
?>

<link rel="stylesheet" href="public/assets/css/bcb_entry.css?v=<?= filemtime('public/assets/css/bcb_entry.css') ?>">

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
                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div>
            <h3>Barangay Certificate for Business</h3>
            <p>Fill in the business details below to generate the certificate</p>
        </div>
    </div>

    <form id="bcbForm" method="POST" action="index.php?controller=forms&action=processBcbEntry" target="_blank" novalidate>
        <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">

        <div class="form-card">
            <div class="form-card-header">
                <svg width="14" height="14" fill="#4361ee" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span class="form-card-header-label">Business Details</span>
            </div>

            <div class="form-card-body">

                <!-- Business Name -->
                <div class="field-group">
                    <label for="bname">
                        Name of Business
                        <span class="req">*</span>
                    </label>
                    <input type="text" class="form-control" id="bname" name="bname"
                        placeholder="e.g. Aling Puring Store" required>
                    <div class="invalid-feedback">Please enter the name of the business.</div>
                    <div class="field-hint">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Official registered name of the business
                    </div>
                </div>

                <!-- Type of Business -->
                <div class="field-group">
                    <label for="tob">
                        Type of Business
                        <span class="req">*</span>
                    </label>
                    <input type="text" class="form-control" id="tob" name="tob"
                        placeholder="e.g. Sari-sari Store, Restaurant, Pharmacy..." required>
                    <div class="invalid-feedback">Please enter the type of business.</div>
                    <div class="field-hint">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Nature or classification of the business
                    </div>
                </div>

                <!-- Owner -->
                <div class="field-group">
                    <label for="constituent_id">
                        Owner of Business
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
                        Search and select the registered constituent who owns the business
                    </div>
                </div>

                <!-- Location -->
                <div class="field-group">
                    <label for="location">
                        Business Location (Block &amp; Lot)
                        <span class="req">*</span>
                    </label>
                    <input type="text" class="form-control" id="location" name="location"
                        placeholder="e.g. Block 4, Lot 1" required>
                    <div class="invalid-feedback">Please enter the business location.</div>
                    <div class="field-hint">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Exact block and lot number of the business premises
                    </div>
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

<script src="public/assets/js/bcb_entry.js?v=<?= filemtime('public/assets/js/bcb_entry.js') ?>"></script>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>