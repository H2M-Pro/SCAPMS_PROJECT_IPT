<?php
require_once __DIR__ . '/includes/auth.php';

if (current_user()) {
    redirect(base_url('/index.php'));
}

$errors = [];
$success = false;
$fields = [
    'username' => '',
    'role' => 'STUDENT',
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'registration_number' => '',
    'program' => '',
    'year_of_study' => '1',
    'gender' => 'F',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($fields as $name => $default) {
        $fields[$name] = trim((string) ($_POST[$name] ?? $default));
    }

    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');

    if ($fields['username'] === '' || $fields['first_name'] === '' || $fields['last_name'] === '' || $fields['email'] === '' || $password === '') {
        $errors[] = 'Please complete all required fields.';
    } elseif ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    } else {
        $user = register_user([
            'username' => $fields['username'],
            'password' => $password,
            'role' => $fields['role'],
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'registration_number' => $fields['registration_number'],
            'program' => $fields['program'],
            'year_of_study' => $fields['year_of_study'],
            'gender' => $fields['gender'],
        ]);

        if ($user) {
            $success = true;
            foreach ($fields as $key => $value) {
                $fields[$key] = '';
            }
            $fields['role'] = 'STUDENT';
            $fields['year_of_study'] = '1';
            $fields['gender'] = 'F';
        } else {
            $errors[] = 'Unable to create account. Username may already exist or details are incomplete.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCAPMS Sign Up</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="secondary-auth">
<div class="main-container">
    <div class="leftside">
        <img src="icons/unicap1.png" alt="University Logo">
        <section>
            <h1>SCAPMS</h1>
            <p class="slogan">Learn.Grow.Succeed</p>
            <h2>Welcome</h2>
            <span>Create your student or staff account.</span>
        </section>
    </div>

    <div class="main">
        <h1>Create Account</h1>
        <h3>Register to continue</h3>

        <?php foreach ($errors as $error): ?>
            <div class="form-error"><?= e($error) ?></div>
        <?php endforeach; ?>

        <?php if ($success): ?>
            <div class="form-success">Account created successfully. You can now <a href="login.php">log in</a>.</div>
        <?php endif; ?>

        <form method="post" action="signup.php" class="form-grid">
            <div>
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="<?= e($fields['username']) ?>" required>
            </div>

            <div>
                <label for="role">Role</label>
                <select name="role" id="role">
                    <option value="STUDENT" <?= $fields['role'] === 'STUDENT' ? 'selected' : '' ?>>Student</option>
                    <option value="INSTRUCTOR" <?= $fields['role'] === 'INSTRUCTOR' ? 'selected' : '' ?>>Instructor</option>
                    <option value="ACADEMIC_OFFICER" <?= $fields['role'] === 'ACADEMIC_OFFICER' ? 'selected' : '' ?>>Academic Officer</option>
                </select>
            </div>

            <div>
                <label for="first_name">First Name</label>
                <input type="text" name="first_name" id="first_name" value="<?= e($fields['first_name']) ?>" required>
            </div>

            <div>
                <label for="last_name">Last Name</label>
                <input type="text" name="last_name" id="last_name" value="<?= e($fields['last_name']) ?>" required>
            </div>

            <div>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= e($fields['email']) ?>" required>
            </div>

            <div>
                <label for="registration_number">Registration Number</label>
                <input type="text" name="registration_number" id="registration_number" value="<?= e($fields['registration_number']) ?>">
            </div>

            <div>
                <label for="program">Program</label>
                <input type="text" name="program" id="program" value="<?= e($fields['program']) ?>">
            </div>

            <div>
                <label for="year_of_study">Year of Study</label>
                <input type="number" name="year_of_study" id="year_of_study" min="1" max="8" value="<?= e($fields['year_of_study']) ?>">
            </div>

            <div>
                <label for="gender">Gender</label>
                <select name="gender" id="gender">
                    <option value="F" <?= $fields['gender'] === 'F' ? 'selected' : '' ?>>Female</option>
                    <option value="M" <?= $fields['gender'] === 'M' ? 'selected' : '' ?>>Male</option>
                </select>
            </div>

            <div>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div>
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>

            <button type="submit">Create Account</button>
        </form>

        <br>
        <span class="accmsg">Already have an account? <a href="login.php">Login</a></span>
    </div>
</div>
</body>
</html>
