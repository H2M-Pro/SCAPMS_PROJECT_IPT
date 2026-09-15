<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('STUDENT');
$pdo = get_pdo();
$user = current_user();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assessmentId = (int) ($_POST['assessment_id'] ?? 0);
    $stmt = $pdo->prepare('SELECT Student_id FROM Students WHERE User_id = ?');
    $stmt->execute([$user['user_id']]);
    $student = $stmt->fetch();

    if (!$student || $assessmentId <= 0) {
        redirect(base_url('/student/assessments.php'));
    }

    $qStmt = $pdo->prepare('SELECT q.Question_id, q.Question_text, q.Question_marks FROM Questions q WHERE q.Assessment_id = ? ORDER BY q.Question_id');
    $qStmt->execute([$assessmentId]);
    $questions = $qStmt->fetchAll();

    if (!$questions) {
        $errors[] = 'This assessment has no questions yet.';
    } else {
        $submissionStmt = $pdo->prepare('INSERT INTO Submissions (Student_id, Assessment_id, Submission_date, Submission_status) VALUES (?, ?, NOW(), "SUBMITTED")');
        $submissionStmt->execute([$student['Student_id'], $assessmentId]);
        $submissionId = (int) $pdo->lastInsertId();

        $score = 0.0;

        foreach ($questions as $question) {
            $selectedOption = (int) ($_POST['question_' . $question['Question_id']] ?? 0);
            if ($selectedOption <= 0) {
                continue;
            }

            $correctStmt = $pdo->prepare('SELECT Option_id FROM Options WHERE Question_id = ? AND Is_correct = 1 LIMIT 1');
            $correctStmt->execute([$question['Question_id']]);
            $correctOptionId = (int) $correctStmt->fetchColumn();

            $mark = ($selectedOption === $correctOptionId) ? (float) $question['Question_marks'] : 0.0;
            $score += $mark;

            $answerStmt = $pdo->prepare('INSERT INTO Answers (Submission_id, Question_id, Option_id, Student_answer, Awarded_marks) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE Option_id = VALUES(Option_id), Student_answer = VALUES(Student_answer), Awarded_marks = VALUES(Awarded_marks)');
            $answerStmt->execute([$submissionId, $question['Question_id'], $selectedOption, (string) $selectedOption, $mark]);
        }

        $resultStmt = $pdo->prepare('INSERT INTO Results (Submission_id, Score, Results_status) VALUES (?, ?, "PENDING") ON DUPLICATE KEY UPDATE Score = VALUES(Score), Results_status = "PENDING"');
        $resultStmt->execute([$submissionId, $score]);

        set_flash('success', 'Assessment submitted successfully.');
        redirect(base_url('/student/results.php'));
    }
}

$assessmentId = (int)($_GET['assessment_id'] ?? 0);
if ($assessmentId <= 0) {
    redirect(base_url('/student/assessments.php'));
}

$stmt = $pdo->prepare('SELECT s.Student_id FROM Students s WHERE s.User_id = ?');
$stmt->execute([$user['user_id']]);
$student = $stmt->fetch();

$stmt = $pdo->prepare('SELECT a.Assessment_id, a.Title, a.Instructions, a.Total_marks FROM Assessments a WHERE a.Assessment_id = ?');
$stmt->execute([$assessmentId]);
$assessment = $stmt->fetch();

$stmt = $pdo->prepare('SELECT q.Question_id, q.Question_text, q.Question_type, q.Question_marks FROM Questions q WHERE q.Assessment_id = ? ORDER BY q.Question_id');
$stmt->execute([$assessmentId]);
$questions = $stmt->fetchAll();

foreach ($questions as &$question) {
    $optStmt = $pdo->prepare('SELECT Option_id, Option_text FROM Options WHERE Question_id = ? ORDER BY Option_id');
    $optStmt->execute([$question['Question_id']]);
    $question['options'] = $optStmt->fetchAll();
}
unset($question);

include __DIR__ . '/../includes/header.php';
?>
<div class="panel assessment-player">
    <h2><?= e($assessment['Title']) ?></h2>
    <p><?= e($assessment['Instructions']) ?></p>
    <p><strong>Total marks:</strong> <?= e($assessment['Total_marks']) ?></p>

    <?php foreach ($errors as $error): ?>
        <div class="form-error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <form method="post" action="<?= e(base_url('/student/take_assessment.php')) ?>">
        <input type="hidden" name="assessment_id" value="<?= (int) $assessmentId ?>">

        <?php foreach ($questions as $question): ?>
            <div class="question-box">
                <p><strong><?= e($question['Question_text']) ?></strong> (<?= e($question['Question_type']) ?> - <?= e($question['Question_marks']) ?> marks)</p>
                <?php foreach ($question['options'] as $option): ?>
                    <label class="option-row">
                        <input type="radio" name="question_<?= e($question['Question_id']) ?>" value="<?= e($option['Option_id']) ?>" required>
                        <span><?= e($option['Option_text']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <button class="button-link" type="submit">Submit Assessment</button>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
