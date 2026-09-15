<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('STUDENT');
$pdo = get_pdo();
$user = current_user();

$stmt = $pdo->prepare('SELECT s.Student_id FROM Students s WHERE s.User_id = ?');
$stmt->execute([$user['user_id']]);
$student = $stmt->fetch();

$stmt = $pdo->prepare('SELECT a.Assessment_id, a.Title, c.Course_name, a.Start_date, a.End_date, a.Status FROM Assessments a JOIN Courses c ON c.Course_id = a.Course_id JOIN Enrollments e ON e.Course_id = c.Course_id WHERE e.Student_id = ? ORDER BY a.Start_date DESC');
$stmt->execute([$student['Student_id']]);
$assessments = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<section class="page-intro compact-intro"><h1>Available Assessments</h1></section>
<div class="assessment-list">
    <?php foreach ($assessments as $assessment): ?>
        <article class="assessment-row">
            <div>
                <strong><?= e($assessment['Title']) ?></strong>
                <span><?= e($assessment['Course_name']) ?></span>
                <span>Deadline: <?= e(date('d M Y', strtotime($assessment['End_date']))) ?></span>
                <span>Status: <?= e(strtolower($assessment['Status'] === 'PUBLISHED' ? 'Not started' : $assessment['Status'])) ?></span>
            </div>
            <a class="button-link" href="<?= e(base_url('/student/take_assessment.php?assessment_id=' . $assessment['Assessment_id'])) ?>">Start Assessment</a>
        </article>
    <?php endforeach; ?>
    <?php if (!$assessments): ?><div class="empty-state">No assessments are available right now.</div><?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
