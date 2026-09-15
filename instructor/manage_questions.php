<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('INSTRUCTOR');
$pdo = get_pdo();
$assessmentId = (int)($_GET['assessment_id'] ?? 0);

$stmt = $pdo->prepare('SELECT q.Question_id, q.Question_text, q.Question_type, q.Question_marks FROM Questions q WHERE q.Assessment_id = ? ORDER BY q.Question_id');
$stmt->execute([$assessmentId]);
$questions = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="table-card">
    <h2>Questions</h2>
    <table>
        <thead>
            <tr>
                <th>Question</th>
                <th>Type</th>
                <th>Marks</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($questions as $question): ?>
                <tr>
                    <td><?= e($question['Question_text']) ?></td>
                    <td><?= e($question['Question_type']) ?></td>
                    <td><?= e($question['Question_marks']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
