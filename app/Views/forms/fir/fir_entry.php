<?php
$content = ob_start();
?>

<link rel="stylesheet" href="public/assets/css/fir_entry.css">

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
            <svg fill="white" viewBox="0 0 20 20" width="22" height="22">
                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 5a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1zm0 4a1 1 0 011-1h4a1 1 0 110 2H9a1 1 0 01-1-1zm0 4a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div>
            <h3>Family Information Record</h3>
            <p>Fill in the details below to generate the document</p>
        </div>
    </div>


    <div class="warning-banner">
        Warning: Some information may require manual input. Please check the information before submitting.
    </div>

    <form action="index.php?controller=forms&action=processFirEntry" method="POST" target="_blank">
        <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">

        <!-- Pregnant Women -->
        <div class="form-card">
            <div class="form-card-header">
                <svg width="14" height="14" fill="#4361ee" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
                <span class="form-card-header-label">Number of Pregnant Women</span>
            </div>
            <div class="form-card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="field-group">
                            <label for="pregnant_10_14">10–14 years old</label>
                            <input type="number" class="form-control" id="pregnant_10_14" name="pregnant_10_14" min="0" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="field-group">
                            <label for="pregnant_15_19">15–19 years old</label>
                            <input type="number" class="form-control" id="pregnant_15_19" name="pregnant_15_19" min="0" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="field-group">
                            <label for="pregnant_20_above">20 years old and above</label>
                            <input type="number" class="form-control" id="pregnant_20_above" name="pregnant_20_above" min="0" placeholder="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Family Planning Acceptors -->
        <div class="form-card">
            <div class="form-card-header">
                <svg width="14" height="14" fill="#4361ee" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h8a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
                <span class="form-card-header-label">Family Planning Acceptors</span>
            </div>
            <div class="form-card-body">

                <!-- Modern Methods -->
                <span class="section-label">Modern Methods</span>
                <div class="row">
                    <div class="col-6 col-md-2">
                        <div class="field-group">
                            <label for="fp_fs">FS</label>
                            <input type="number" class="form-control" id="fp_fs" name="fp_fs" min="0" placeholder="0">
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="field-group">
                            <label for="fp_ms">MS</label>
                            <input type="number" class="form-control" id="fp_ms" name="fp_ms" min="0" placeholder="0">
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="field-group">
                            <label for="fp_iud">IUD</label>
                            <input type="number" class="form-control" id="fp_iud" name="fp_iud" min="0" placeholder="0">
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="field-group">
                            <label for="fp_pill">Pill</label>
                            <input type="number" class="form-control" id="fp_pill" name="fp_pill" min="0" placeholder="0">
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="field-group">
                            <label for="fp_injectable">Injectable</label>
                            <input type="number" class="form-control" id="fp_injectable" name="fp_injectable" min="0" placeholder="0">
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="field-group">
                            <label for="fp_implant">Implant</label>
                            <input type="number" class="form-control" id="fp_implant" name="fp_implant" min="0" placeholder="0">
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="field-group">
                            <label for="fp_condom">Condom</label>
                            <input type="number" class="form-control" id="fp_condom" name="fp_condom" min="0" placeholder="0">
                        </div>
                    </div>
                </div>

                <hr class="section-divider">

                <!-- Natural Methods -->
                <span class="section-label">Natural Methods</span>
                <div class="row">
                    <div class="col-6 col-md-2">
                        <div class="field-group">
                            <label for="fp_cm">CM</label>
                            <input type="number" class="form-control" id="fp_cm" name="fp_cm" min="0" placeholder="0">
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="field-group">
                            <label for="fp_bbt">BBT</label>
                            <input type="number" class="form-control" id="fp_bbt" name="fp_bbt" min="0" placeholder="0">
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="field-group">
                            <label for="fp_st">ST</label>
                            <input type="number" class="form-control" id="fp_st" name="fp_st" min="0" placeholder="0">
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="field-group">
                            <label for="fp_sd">SD</label>
                            <input type="number" class="form-control" id="fp_sd" name="fp_sd" min="0" placeholder="0">
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="field-group">
                            <label for="fp_lam">LAM</label>
                            <input type="number" class="form-control" id="fp_lam" name="fp_lam" min="0" placeholder="0">
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="field-group">
                            <label for="fp_twoday">Two Day</label>
                            <input type="number" class="form-control" id="fp_twoday" name="fp_twoday" min="0" placeholder="0">
                        </div>
                    </div>
                </div>

                <hr class="section-divider">

                <!-- Totals -->
                <span class="section-label">Totals</span>
                <div class="row">
                    <div class="col-md-4">
                        <div class="field-group">
                            <label for="fp_totalcu">Total CU</label>
                            <input type="number" class="form-control" id="fp_totalcu" name="fp_totalcu" min="0" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="field-group">
                            <label for="fp_mcra">MCRA</label>
                            <input type="number" class="form-control" id="fp_mcra" name="fp_mcra" min="0" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="field-group">
                            <label for="fp_cpr">CPR</label>
                            <input type="number" class="form-control" id="fp_cpr" name="fp_cpr" min="0" placeholder="0">
                        </div>
                    </div>
                </div>

            </div>

            <div class="form-card-footer">
                <button type="submit" class="btn-generate">
                    <svg width="15" height="15" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 5a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1zm0 4a1 1 0 011-1h4a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Generate Document
                </button>
            </div>
        </div>

    </form>
</div>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>