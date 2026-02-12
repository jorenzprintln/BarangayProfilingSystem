<?php
$content = ob_start();
?>
<div class="container-fluid px-4 mt-4 ">
    <div class="d-flex align-items-center mb-3">
        <!-- Header Container -->
        <a href="index.php?controller=home&action=forms" class="mr-2">
            <img src="public/assets/icons/back.icon.png" alt="Back" style="width: 32px; height: 32px;">
        </a>
        <h3 class="font-weight-bold mb-0">Family Information Record</h3>
    </div>
    <small class="text-danger font-weight-bold">Warning: Some information may require manual input. Please check the
        information before submitting.</small>

    <div class="mt-4">
        <form action="index.php?controller=forms&action=processFirEntry" method="POST" target="_blank">

            <!-- Pregnant Women Section -->
            <div class="mb-4">
                <div class="card-header">
                    <h4>Number of Pregnant Women</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pregnant_10_14">10-14 years old</label>
                                <input type="number" class="form-control" id="pregnant_10_14" name="pregnant_10_14"
                                    min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pregnant_15_19">15-19 years old</label>
                                <input type="number" class="form-control" id="pregnant_15_19" name="pregnant_15_19"
                                    min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pregnant_20_above">20 years old above</label>
                                <input type="number" class="form-control" id="pregnant_20_above"
                                    name="pregnant_20_above" min="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Family Planning Acceptors Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Family Planning ACCEPTORS</h4>
                </div>
                <div class="card-body">
                    <!-- Modern Methods -->
                    <h5>MODERN</h5>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fp_fs">FS</label>
                                <input type="number" class="form-control" id="fp_fs" name="fp_fs" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fp_ms">MS</label>
                                <input type="number" class="form-control" id="fp_ms" name="fp_ms" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fp_iud">IUD</label>
                                <input type="number" class="form-control" id="fp_iud" name="fp_iud" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fp_pill">PILL</label>
                                <input type="number" class="form-control" id="fp_pill" name="fp_pill" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fp_injectable">INJECTABLE</label>
                                <input type="number" class="form-control" id="fp_injectable" name="fp_injectable"
                                    min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fp_implant">IMPLANT</label>
                                <input type="number" class="form-control" id="fp_implant" name="fp_implant" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fp_condom">CONDOM</label>
                                <input type="number" class="form-control" id="fp_condom" name="fp_condom" min="0">
                            </div>
                        </div>
                    </div>

                    <!-- Natural Methods -->
                    <h5>NATURAL</h5>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fp_cm">CM</label>
                                <input type="number" class="form-control" id="fp_cm" name="fp_cm" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fp_bbt">BBT</label>
                                <input type="number" class="form-control" id="fp_bbt" name="fp_bbt" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fp_st">ST</label>
                                <input type="number" class="form-control" id="fp_st" name="fp_st" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fp_sd">SD</label>
                                <input type="number" class="form-control" id="fp_sd" name="fp_sd" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fp_lam">LAM</label>
                                <input type="number" class="form-control" id="fp_lam" name="fp_lam" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fp_twoday">Two Day</label>
                                <input type="number" class="form-control" id="fp_twoday" name="fp_twoday" min="0">
                            </div>
                        </div>
                    </div>

                    <!-- Totals -->
                    <h5 class="mt-4">Totals</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fp_totalcu">Total CU</label>
                                <input type="number" class="form-control" id="fp_totalcu" name="fp_totalcu" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fp_mcra">MCRA</label>
                                <input type="number" class="form-control" id="fp_mcra" name="fp_mcra" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fp_cpr">CPR</label>
                                <input type="number" class="form-control" id="fp_cpr" name="fp_cpr" min="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">Generate Document</button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>