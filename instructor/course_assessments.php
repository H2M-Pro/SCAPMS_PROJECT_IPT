<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('INSTRUCTOR');
$pdo = get_pdo();
$user = current_user();

$courseId = (int)($_GET['course_id'] ?? 0);
$stmt = $pdo->prepare('SELECT Instructor_id FROM Instructors WHERE User_id = ?');
$stmt->execute([$user['user_id']]);
$instructor = $stmt->fetch();

$stmt = $pdo->prepare('SELECT Assessment_id, Title, Status, Total_marks FROM Assessments WHERE Course_id = ? ORDER BY Assessment_id DESC');
$stmt->execute([$courseId]);
$assessments = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="table-card">
    <h2>Assessment List</h2>
    <div class="inline-actions" style="margin-bottom: 1rem;">
        <a href="<?= e(base_url('/instructor/create_assessment.php')) ?>">New Assessment</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Total Marks</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($assessments as $assessment): ?>
                <tr>
                    <td><?= e($assessment['Title']) ?></td>
                    <td><?= e($assessment['Status']) ?></td>
                    <td><?= e($assessment['Total_marks']) ?></td>
                    <td><a class="button-link" href="<?= e(base_url('/instructor/manage_questions.php?assessment_id=' . $assessment['Assessment_id'])) ?>">Manage</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
