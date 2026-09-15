<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('ACADEMIC_OFFICER');
$pdo = get_pdo();

$stmt = $pdo->query('SELECT c.Course_id, c.Course_name, c.Course_code, i.Instructor_name FROM Courses c JOIN Instructors i ON i.Instructor_id = c.Instructor_id ORDER BY c.Course_name');
$courses = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="table-card">
    <h2>Manage Courses</h2>
    <table>
        <thead>
            <tr>
                <th>Course</th>
                <th>Code</th>
                <th>Instructor</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?= e($course['Course_name']) ?></td>
                    <td><?= e($course['Course_code']) ?></td>
                    <td><?= e($course['Instructor_name']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
