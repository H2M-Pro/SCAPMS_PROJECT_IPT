<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$role = current_user()['role'] ?? null;
if ($role !== 'STUDENT' && strpos(realpath(__FILE__), '/student/') !== false) {
    require_role('STUDENT');
}
if ($role !== 'INSTRUCTOR' && strpos(realpath(__FILE__), '/instructor/') !== false) {
    require_role('INSTRUCTOR');
}
if ($role !== 'ACADEMIC_OFFICER' && strpos(realpath(__FILE__), '/officer/') !== false) {
    require_role('ACADEMIC_OFFICER');
}

$pdo = get_pdo();
include __DIR__ . '/../includes/header.php';
?>
<div class="panel">
    <h2>Section</h2>
    <p>This section is ready for the project’s specific business logic. The app structure, login flow, and database access are now connected and can be extended from here.</p>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
