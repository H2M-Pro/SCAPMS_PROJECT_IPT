<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('INSTRUCTOR');
$pdo = get_pdo();
$submissionId = (int)($_GET['submission_id'] ?? 0);

$stmt = $pdo->prepare('SELECT sub.Submission_id, a.Title, s.Student_fname, s.Student_lname, sub.Submission_status FROM Submissions sub JOIN Students s ON s.Student_id = sub.Student_id JOIN Assessments a ON a.Assessment_id = sub.Assessment_id WHERE sub.Submission_id = ?');
$stmt->execute([$submissionId]);
$submission = $stmt->fetch();

include __DIR__ . '/../includes/header.php';
?>
<div class="panel">
    <h2>Submission Review</h2>
    <p><strong>Student:</strong> <?= e($submission['Student_fname'] . ' ' . $submission['Student_lname']) ?></p>
    <p><strong>Assessment:</strong> <?= e($submission['Title']) ?></p>
    <p><strong>Status:</strong> <?= e($submission['Submission_status']) ?></p>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
