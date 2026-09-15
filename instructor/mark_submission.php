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

$stmt = $pdo->prepare('SELECT q.Question_id, q.Question_marks, o.Option_id, o.Is_correct FROM Questions q LEFT JOIN Options o ON o.Question_id = q.Question_id WHERE q.Assessment_id = (SELECT Assessment_id FROM Submissions WHERE Submission_id = ?) ORDER BY q.Question_id, o.Option_id');
$stmt->execute([$submissionId]);
$rows = $stmt->fetchAll();

$questionMap = [];
foreach ($rows as $row) {
    $questionId = (int) $row['Question_id'];
    if (!isset($questionMap[$questionId])) {
        $questionMap[$questionId] = ['marks' => (float) $row['Question_marks'], 'correct' => []];
    }

    if ((int) $row['Is_correct'] === 1) {
        $questionMap[$questionId]['correct'][] = (int) $row['Option_id'];
    }
}

$answerStmt = $pdo->prepare('SELECT Question_id, Option_id FROM Answers WHERE Submission_id = ?');
$answerStmt->execute([$submissionId]);
$answerRows = $answerStmt->fetchAll();
$studentAnswers = [];
foreach ($answerRows as $answer) {
    $studentAnswers[(int) $answer['Question_id']] = (int) $answer['Option_id'];
}

foreach ($questionMap as $questionId => $details) {
    $selected = $studentAnswers[$questionId] ?? null;
    if ($selected === null) {
        continue;
    }

    $mark = in_array($selected, $details['correct'], true) ? $details['marks'] : 0.0;
    $upsert = $pdo->prepare('INSERT INTO Answers (Submission_id, Question_id, Option_id, Student_answer, Awarded_marks) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE Option_id = VALUES(Option_id), Student_answer = VALUES(Student_answer), Awarded_marks = VALUES(Awarded_marks)');
    $upsert->execute([$submissionId, $questionId, $selected, (string) $selected, $mark]);
}

$sumStmt = $pdo->prepare('SELECT COALESCE(SUM(Awarded_marks), 0) AS total FROM Answers WHERE Submission_id = ?');
$sumStmt->execute([$submissionId]);
$total = (float) $sumStmt->fetch()['total'];

$insertResult = $pdo->prepare('INSERT INTO Results (Submission_id, Score, Results_status) VALUES (?, ?, "PENDING") ON DUPLICATE KEY UPDATE Score = VALUES(Score), Results_status = "PENDING"');
$insertResult->execute([$submissionId, $total]);

set_flash('success', 'Submission scored and ready for review.');
redirect(base_url('/instructor/submissions.php'));
