<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('ACADEMIC_OFFICER');
$pdo = get_pdo();

$stmt = $pdo->query('SELECT u.User_id, u.Username, u.Role, s.Student_fname, s.Student_lname FROM Users u LEFT JOIN Students s ON s.User_id = u.User_id ORDER BY u.User_id');
$users = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="table-card">
    <h2>Manage Users</h2>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= e($user['Username']) ?></td>
                    <td><?= e($user['Role']) ?></td>
                    <td><?= e(($user['Student_fname'] ?? '') . ' ' . ($user['Student_lname'] ?? '')) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
