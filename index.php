<?php
require_once __DIR__ . '/includes/auth.php';

$user = current_user();
if (!$user) {
    redirect(base_url('/login.php'));
}

switch ($user['role']) {
    case 'STUDENT':
        redirect(base_url('/student/dashboard.php'));
    case 'INSTRUCTOR':
        redirect(base_url('/instructor/dashboard.php'));
    case 'ACADEMIC_OFFICER':
        redirect(base_url('/officer/dashboard.php'));
    default:
        redirect(base_url('/login.php'));
}
