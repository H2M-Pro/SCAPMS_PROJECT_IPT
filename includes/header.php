<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/auth.php';

$user = current_user();
$role = $user['role'] ?? null;
$flash = get_flash();
$currentPath = str_replace('\\', '/', $_SERVER['PHP_SELF'] ?? '');

$navItems = [];
if ($role === 'STUDENT') {
    $navItems = [
        ['label' => 'Dashboard', 'href' => '/student/dashboard.php', 'icon' => 'navbar.png'],
        ['label' => 'Courses', 'href' => '/student/courses.php', 'icon' => 'courseicon.png'],
        ['label' => 'Assessments', 'href' => '/student/assessments.php', 'icon' => 'courseicon.png'],
        ['label' => 'Results & feedback', 'href' => '/student/results.php', 'icon' => 'results and feedback.png'],
        ['label' => 'View History', 'href' => '/student/progress.php', 'icon' => 'viewhistory.png'],
    ];
} elseif ($role === 'INSTRUCTOR') {
    $navItems = [
        ['label' => 'Dashboard', 'href' => '/instructor/dashboard.php', 'icon' => 'navbar.png'],
        ['label' => 'Courses', 'href' => '/instructor/course.php', 'icon' => 'courseicon.png'],
        ['label' => 'Assessments', 'href' => '/instructor/course_assessments.php', 'icon' => 'courseicon.png'],
        ['label' => 'View submissions', 'href' => '/instructor/submissions.php', 'icon' => 'results and feedback.png'],
        ['label' => 'Feedback', 'href' => '/instructor/results.php', 'icon' => 'results and feedback.png'],
        ['label' => 'Performance', 'href' => '/instructor/reports.php', 'icon' => 'viewhistory.png'],
    ];
} elseif ($role === 'ACADEMIC_OFFICER') {
    $navItems = [
        ['label' => 'Dashboard', 'href' => '/officer/dashboard.php', 'icon' => 'navbar.png'],
        ['label' => 'Courses', 'href' => '/officer/manage_courses.php', 'icon' => 'courseicon.png'],
        ['label' => 'Users', 'href' => '/officer/manage_users.php', 'icon' => 'usericon.png'],
        ['label' => 'Enrollments', 'href' => '/officer/enrollments.php', 'icon' => 'results and feedback.png'],
        ['label' => 'Reports', 'href' => '/officer/reports.php', 'icon' => 'viewhistory.png'],
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCAPMS</title>
    <link rel="stylesheet" href="<?= e(base_url('/assets/css/style.css')) ?>">
</head>
<body class="app-body">
<?php if ($user): ?>
    <header class="app-topbar">
        <button class="menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false">
            <img src="<?= e(base_url('/icons/navbar.png')) ?>" alt="">
        </button>
        <div class="topbar-user">
            <button class="topbar-user-button" type="button" aria-label="Show user menu" aria-expanded="false">
                <img class="topbar-user-icon" src="<?= e(base_url('/icons/usericon.png')) ?>" alt="">
            </button>
            <div class="user-menu">
                <div class="account-details">
                    <strong>Account details</strong>
                    <span class="account-username"><?= e($user['username']) ?></span>
                    <span class="account-role"><?= e(str_replace('_', ' ', $role)) ?></span>
                </div>
                <a href="<?= e(base_url('/logout.php')) ?>" class="logout-link">Logout</a>
            </div>
        </div>
    </header>
    <aside class="app-sidebar">
        <a class="brand-block" href="<?= e(base_url('/index.php')) ?>">
            <img class="brand-mark" src="<?= e(base_url('/icons/universitycap.png')) ?>" alt="">
            <span>SCAPMS</span>
        </a>
        <nav class="side-nav">
            <?php foreach ($navItems as $item): ?>
                <?php $isActive = strpos($currentPath, $item['href']) !== false || ($item['label'] === 'Assessments' && strpos($currentPath, 'take_assessment.php') !== false); ?>
                <a class="<?= $isActive ? 'active' : '' ?>" href="<?= e(base_url($item['href'])) ?>">
                    <span class="nav-icon" aria-hidden="true"><img src="<?= e(base_url('/icons/' . $item['icon'])) ?>" alt=""></span>
                    <span><?= e($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>
<?php endif; ?>

<?php if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>

<main class="page-shell">