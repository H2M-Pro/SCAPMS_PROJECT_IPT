<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('INSTRUCTOR');
$pdo = get_pdo();

$submissionId = (int) ($_GET['submission_id'] ?? 0);
if ($submissionId <= 0) {
    set_flash('error', 'Submission not found.');
    redirect(base_url('/instructor/submissions.php'));
}

$stmt = $pdo->prepare('SELECT * FROM Submissions WHERE Submission_id = ?');
$stmt->execute([$submissionId]);
$submission = $stmt->fetch();
if (!$submission) {
    set_flash('error', 'Submission not found.');
    redirect(base_url('/instructor/submissions.php'));
}

$scoreStmt = $pdo->prepare('SELECT SUM(Awarded_marks) AS score FROM Answers WHERE Submission_id = ?');
$scoreStmt->execute([$submissionId]);
$score = (float) ($scoreStmt->fetch()['score'] ?? 0);

$update = $pdo->prepare('INSERT INTO Results (Submission_id, Score, Results_status) VALUES (?, ?, "PUBLISHED") ON DUPLICATE KEY UPDATE Score = VALUES(Score), Results_status = "PUBLISHED"');
$update->execute([$submissionId, $score]);

set_flash('success', 'Result published successfully.');
redirect(base_url('/instructor/submissions.php'));
