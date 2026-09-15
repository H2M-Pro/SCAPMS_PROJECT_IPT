<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('STUDENT');
$pdo = get_pdo();
$user = current_user();

$stmt = $pdo->prepare('SELECT s.Student_id FROM Students s WHERE s.User_id = ?');
$stmt->execute([$user['user_id']]);
$student = $stmt->fetch();

$stmt = $pdo->prepare('SELECT c.Course_id, c.Course_name, c.Course_code, i.Instructor_name FROM Enrollments e JOIN Courses c ON c.Course_id = e.Course_id JOIN Instructors i ON i.Instructor_id = c.Instructor_id WHERE e.Student_id = ? ORDER BY c.Course_name');
$stmt->execute([$student['Student_id']]);
$courses = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<section class="page-intro compact-intro"><h1>My Courses</h1></section>
<div class="course-list">
    <?php foreach ($courses as $index => $course): ?>
        <article class="course-row course-row-action">
            <div><strong><?= e($course['Course_name']) ?></strong><span>Instructor: <?= e($course['Instructor_name']) ?></span></div>
            <div class="course-progress"><strong><?= $index % 3 === 0 ? '60' : ($index % 3 === 1 ? '40' : '20') ?>%</strong><div class="progress-track"><span class="progress-fill progress-<?= $index % 3 ?>" style="width: <?= $index % 3 === 0 ? '60' : ($index % 3 === 1 ? '40' : '20') ?>%"></span></div></div>
            <a class="button-link" href="#">View Details</a>
        </article>
    <?php endforeach; ?>
    <?php if (!$courses): ?><div class="empty-state">No enrolled courses yet.</div><?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
<?php /* legacy table intentionally replaced with designer course cards */ ?>
<?php /*
<div class="table-card">
    <h2>My Courses</h2>
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
*/ ?>
