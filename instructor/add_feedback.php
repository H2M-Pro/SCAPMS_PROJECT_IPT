<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('INSTRUCTOR');
$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(base_url('/instructor/submissions.php'));
}

$submissionId = (int) ($_POST['submission_id'] ?? 0);
$feedback = trim((string) ($_POST['feedback'] ?? ''));
$user = current_user();

$stmt = $pdo->prepare('SELECT Instructor_id FROM Instructors WHERE User_id = ?');
$stmt->execute([$user['user_id']]);
$instructor = $stmt->fetch();

if ($submissionId <= 0 || $feedback === '' || !$instructor) {
    set_flash('error', 'Feedback could not be saved.');
    redirect(base_url('/instructor/submissions.php'));
}

$insert = $pdo->prepare('INSERT INTO Feedback (Submission_id, Instructor_id, Feedback_content, Date_Provided) VALUES (?, ?, ?, NOW())');
$insert->execute([$submissionId, $instructor['Instructor_id'], $feedback]);

set_flash('success', 'Feedback saved successfully.');
redirect(base_url('/instructor/submissions.php'));
