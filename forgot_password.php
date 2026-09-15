<?php
require_once __DIR__ . '/includes/auth.php';

$errors = [];
$success = false;
$username = '';
$newPassword = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $newPassword = (string) ($_POST['new_password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');

    if ($username === '' || $newPassword === '') {
        $errors[] = 'Username and new password are required.';
    } elseif ($newPassword !== $confirm) {
        $errors[] = 'Passwords do not match.';
    } elseif (!reset_password($username, $newPassword)) {
        $errors[] = 'Could not update password. Please check the username.';
    } else {
        $success = true;
        $username = '';
        $newPassword = '';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="secondary-auth">
<div class="main-container">
    <div class="leftside">
        <img src="icons/unicap1.png" alt="University Logo">
        <section>
            <h1>SCAPMS</h1>
            <p class="slogan">Learn.Grow.Succeed</p>
            <h2>Reset Access</h2>
            <span>Set a new password for your account.</span>
        </section>
    </div>

    <div class="main">
        <h1>Forgot Password</h1>
        <h3>Enter your username and choose a new password</h3>

        <?php foreach ($errors as $error): ?>
            <div class="form-error"><?= e($error) ?></div>
        <?php endforeach; ?>

        <?php if ($success): ?>
            <div class="form-success">Password updated successfully. You may now <a href="login.php">login</a>.</div>
        <?php endif; ?>

        <form method="post" action="forgot_password.php" class="form-grid">
            <div>
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="<?= e($username) ?>" required>
            </div>

            <div>
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" required>
            </div>

            <div>
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>

            <button type="submit">Update Password</button>
        </form>

        <br>
        <span class="accmsg"><a href="login.php">Back to login</a></span>
    </div>
</div>
</body>
</html>
