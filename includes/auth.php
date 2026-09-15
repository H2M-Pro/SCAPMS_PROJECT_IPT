<?php

// If you deploy this app inside a subfolder (e.g. http://localhost/scapms),
// set BASE_PATH to that subfolder so links/redirects resolve correctly.
define('BASE_PATH', '');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

/**
 * FR-01: authenticate a user by username + password.
 * Returns the user row (with role-specific profile joined in) on success, or null.
 */
function attempt_login(string $username, string $password): ?array
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT * FROM Users WHERE Username = ? AND Account_status = "ACTIVE"');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['Password_hash'])) {
        return null;
    }

    // FR-02: identify the user's role and load their profile record.
    $profile = null;
    switch ($user['Role']) {
        case 'STUDENT':
            $s = $pdo->prepare('SELECT * FROM Students WHERE User_id = ?');
            $s->execute([$user['User_id']]);
            $profile = $s->fetch();
            break;
        case 'INSTRUCTOR':
            $s = $pdo->prepare('SELECT * FROM Instructors WHERE User_id = ?');
            $s->execute([$user['User_id']]);
            $profile = $s->fetch();
            break;
        case 'ACADEMIC_OFFICER':
            $s = $pdo->prepare('SELECT * FROM Academic_Officer WHERE User_id = ?');
            $s->execute([$user['User_id']]);
            $profile = $s->fetch();
            break;
    }

    $_SESSION['user_id']  = $user['User_id'];
    $_SESSION['username'] = $user['Username'];
    $_SESSION['role']     = $user['Role'];
    $_SESSION['profile']  = $profile;

    return $user;
}

function current_user(): ?array
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    return [
        'user_id'  => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role'     => $_SESSION['role'],
        'profile'  => $_SESSION['profile'],
    ];
}

/** NFR-01: require authentication before accessing protected resources. */
function require_login(): array
{
    $user = current_user();
    if (!$user) {
        set_flash('error', 'Please log in to continue.');
        redirect(base_url('/login.php'));
    }
    return $user;
}

/** NFR-02 / NFR-03: enforce role-based access control. */
function require_role(string $role): array
{
    $user = require_login();
    if ($user['role'] !== $role) {
        http_response_code(403);
        die('403 - You do not have permission to access this page.');
    }
    return $user;
}

function do_logout(): void
{
    $_SESSION = [];
    session_destroy();
}

function register_user(array $data): ?array
{
    $pdo = get_pdo();
    $username = trim($data['username'] ?? '');
    $role = strtoupper(trim($data['role'] ?? ''));

    if ($username === '' || $role === '') {
        return null;
    }

    $stmt = $pdo->prepare('SELECT User_id FROM Users WHERE Username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        return null;
    }

    $password = $data['password'] ?? '';
    if ($password === '') {
        return null;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $pdo->beginTransaction();
    try {
        $userStmt = $pdo->prepare('INSERT INTO Users (Username, Password_hash, Role, Account_status) VALUES (?, ?, ?, "ACTIVE")');
        $userStmt->execute([$username, $hash, $role]);
        $userId = (int) $pdo->lastInsertId();

        if ($role === 'STUDENT') {
            $studentStmt = $pdo->prepare('INSERT INTO Students (User_id, Registration_number, Student_fname, Student_mname, Student_lname, Student_email, Program, Student_gender, Year_of_study) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $studentStmt->execute([
                $userId,
                $data['registration_number'] ?? '',
                $data['first_name'] ?? '',
                $data['middle_name'] ?? '',
                $data['last_name'] ?? '',
                $data['email'] ?? '',
                $data['program'] ?? '',
                $data['gender'] ?? null,
                (int)($data['year_of_study'] ?? 1),
            ]);
        } elseif ($role === 'INSTRUCTOR') {
            $insStmt = $pdo->prepare('INSERT INTO Instructors (User_id, Instructor_name, instructor_email) VALUES (?, ?, ?)');
            $insStmt->execute([
                $userId,
                trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
                $data['email'] ?? '',
            ]);
        } elseif ($role === 'ACADEMIC_OFFICER') {
            $officerStmt = $pdo->prepare('INSERT INTO Academic_Officer (User_id, Name, Email) VALUES (?, ?, ?)');
            $officerStmt->execute([
                $userId,
                trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
                $data['email'] ?? '',
            ]);
        }

        $pdo->commit();
        return ['User_id' => $userId, 'Username' => $username, 'Role' => $role];
    } catch (Throwable $e) {
        $pdo->rollBack();
        return null;
    }
}

function reset_password(string $username, string $newPassword): bool
{
    $pdo = get_pdo();
    $username = trim($username);
    if ($username === '' || $newPassword === '') {
        return false;
    }

    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('UPDATE Users SET Password_hash = ? WHERE Username = ?');
    return $stmt->execute([$hash, $username]);
}
