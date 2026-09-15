<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('INSTRUCTOR');
$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(base_url('/instructor/dashboard.php'));
}

$assessmentId = (int) ($_POST['assessment_id'] ?? 0);
$questionText = trim((string) ($_POST['question_text'] ?? ''));
$questionType = trim((string) ($_POST['question_type'] ?? 'MCQ'));
$marks = (float) ($_POST['question_marks'] ?? 0);

if ($assessmentId <= 0 || $questionText === '' || $marks <= 0) {
    set_flash('error', 'Invalid question data.');
    redirect(base_url('/instructor/create_assessment.php'));
}

$questionStmt = $pdo->prepare('INSERT INTO Questions (Assessment_id, Question_text, Question_type, Question_marks) VALUES (?, ?, ?, ?)');
$questionStmt->execute([$assessmentId, $questionText, $questionType, $marks]);
$questionId = (int) $pdo->lastInsertId();

$options = [
    'A' => trim((string) ($_POST['option_a'] ?? '')),
    'B' => trim((string) ($_POST['option_b'] ?? '')),
    'C' => trim((string) ($_POST['option_c'] ?? '')),
    'D' => trim((string) ($_POST['option_d'] ?? '')),
];

$correct = strtoupper(trim((string) ($_POST['correct_option'] ?? 'A')));

foreach ($options as $label => $optionText) {
    if ($optionText === '') {
        continue;
    }
    $isCorrect = ($label === $correct) ? 1 : 0;
    $optionStmt = $pdo->prepare('INSERT INTO Options (Question_id, Option_text, Is_correct) VALUES (?, ?, ?)');
    $optionStmt->execute([$questionId, $optionText, $isCorrect]);
}

set_flash('success', 'Question saved successfully.');
redirect(base_url('/instructor/manage_questions.php?assessment_id=' . $assessmentId));
