<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('INSTRUCTOR');
$pdo = get_pdo();
$user = current_user();

$stmt = $pdo->prepare('SELECT Instructor_id FROM Instructors WHERE User_id = ?');
$stmt->execute([$user['user_id']]);
$instructor = $stmt->fetch();

$stmt = $pdo->prepare('SELECT Course_id, Course_name, Course_code FROM Courses WHERE Instructor_id = ? ORDER BY Course_name');
$stmt->execute([$instructor['Instructor_id']]);
$courses = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="table-card">
    <h2>Courses</h2>
    <div class="inline-actions" style="margin-bottom: 1rem;">
        <a href="<?= e(base_url('/instructor/create_assessment.php')) ?>">Create Assessment</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Course</th>
                <th>Code</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?= e($course['Course_name']) ?></td>
                    <td><?= e($course['Course_code']) ?></td>
                    <td><a class="button-link" href="<?= e(base_url('/instructor/course_assessments.php?course_id=' . $course['Course_id'])) ?>">Open</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
