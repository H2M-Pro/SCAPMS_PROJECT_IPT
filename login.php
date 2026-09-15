<?php
require_once __DIR__ . '/includes/auth.php';

if (current_user()) {
    redirect(base_url('/index.php'));
}

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $errors[] = 'Please enter both username and password.';
    } else {
        $user = attempt_login($username, $password);
        if ($user) {
            redirect(base_url('/index.php'));
        }
        $errors[] = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCAPMS Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="main-container">
    <div class="leftside">
        <img src="icons/unicap1.png" alt="University Logo">
        <section>
            <h1>SCAPMS</h1>
            <p class="slogan">Learn.Grow.Succeed</p>
            <h2>Your Future Starts Here</h2>
            <span>Access your courses, assessments and progress reports.</span>
        </section>
    </div>

    <div class="main">
        <h1>Welcome Back</h1>
        <h3>Sign in to your account</h3>

        <?php foreach ($errors as $error): ?>
            <div class="form-error"><?= e($error) ?></div>
        <?php endforeach; ?>

        <form method="post" action="login.php">
            <label for="username">Username</label>
            <div class="inputsdiv">
                <input type="text" name="username" id="username" placeholder="Enter your username" value="<?= e($username) ?>" required>
                <img src="icons/usericon.png" alt="user">
            </div>
            <br>

            <label for="password">Password</label>
            <div class="inputsdiv">
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
                <img src="icons/showicon.png" alt="Show" id="show">
            </div>
            <br>

            <section class="rem_me">
                <label for="checkbox" class="remember-box">
                    <input type="checkbox" name="remember" id="checkbox">Remember me
                </label>
                <a href="forgot_password.php">Forgot Password?</a>
            </section>

            <br>
            <button type="submit" id="btn">Login</button>
            <br><br>
            <span class="accmsg">Don't have an account? <a href="signup.php">Sign up</a></span>
        </form>
    </div>
</div>
<script src="assets/js/script.js"></script>
</body>
</html>