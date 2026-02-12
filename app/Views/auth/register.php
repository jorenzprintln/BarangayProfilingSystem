<?php

$content = ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div>
            <div class="card-header">
                <h4>Register</h4>
            </div>
            <div class="card-body">
                <?php if (isset($errors['register'])): ?>
                    <div class="alert alert-danger">
                        <?= $errors['register'] ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?controller=auth&action=register" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" id="username" name="username" value="<?= isset($username) ? $username : '' ?>">
                        <?php if (isset($errors['username'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['username'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= isset($email) ? $email : '' ?>">
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['email'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password">
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['password'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" id="confirm_password" name="confirm_password">
                        <?php if (isset($errors['confirm_password'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['confirm_password'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p>Already have an account? <a href="index.php?controller=auth&action=login">Login</a></p>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>