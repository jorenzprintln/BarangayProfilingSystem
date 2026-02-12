<?php
$content = ob_start();
?>

<body>
    <div class="container">
        <div class="row">
            <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
                <div class="border-0 shadow rounded-lg my-5 login-card">
                    <div class="card-body p-4 p-sm-5">
                        <div class="d-flex justify-content-between text-center mb-3">
                            <img src="public/assets/imgs/brgy.logo.png" alt="Logo" class="img-fluid"
                                style="width: 96px;">
                            <div class="d-flex flex-column">
                                <small class="text-white">Republic of the Philippines</small>
                                <small class="text-white">City of Tacloban</small>
                                <small class="text-white">Barangay 36-A</small>
                            </div>
                            <img src="public/assets/imgs/city_logo.png" alt="Logo" class="img-fluid"
                                style="width: 96px;">
                        </div>
                        <hr class="mb-4">
                        <h5 class="card-title text-center mb-5 fw-bold fs-5 text-white">Barangay Profiling System</h5>
                        <?php if (isset($errors['login'])): ?>
                            <div class="alert alert-danger">
                                <?= $errors['login'] ?>
                            </div>
                        <?php endif; ?>
                        <form action="index.php?controller=auth&action=login" method="post">

                            <div class="form-floating mb-3">
                                <input type="text"
                                    class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                                    id="floatingInput" name="username" value="<?= $username ?? '' ?>"
                                    placeholder="Username">
                                <?php if (isset($errors['username'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $errors['username'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="password"
                                    class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                    id="password" name="password" placeholder="Password">
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $errors['password'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid">
                                <button type="submit"
                                    class="btn btn-primary btn-login text-uppercase fw-bold w-100">Sign In</button>
                            </div>
                            <div class="d-flex align-items-center justify-content-center mt-2">
                                <a class="text-white text-center" href="index.php?controller=auth&action=register">Register</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>


<!-- <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Login</h4>
            </div>
            <div class="card-body">
                <?php if (Session::hasFlash('success')): ?>
                    <div class="alert alert-success">
                        <?= Session::getFlash('success') ?>
                    </div>
                <?php endif; ?>

                

                <form action="index.php?controller=auth&action=login" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                       
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                       
                    </div>
                    <div class="d-grid">
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p>Don't have an account? <a href="index.php?controller=auth&action=register">Register</a></p>
            </div>
        </div>
    </div> -->
<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>