<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('STUDENT');
$pdo = get_pdo();
$user = current_user();

$stmt = $pdo->prepare('SELECT s.Student_id FROM Students s WHERE s.User_id = ?');
$stmt->execute([$user['user_id']]);
$student = $stmt->fetch();

$stmt = $pdo->prepare('SELECT c.Course_name, AVG(r.Score) AS average_score FROM Results r JOIN Submissions sub ON sub.Submission_id = r.Submission_id JOIN Assessments a ON a.Assessment_id = sub.Assessment_id JOIN Courses c ON c.Course_id = a.Course_id WHERE sub.Student_id = ? GROUP BY c.Course_id, c.Course_name');
$stmt->execute([$student['Student_id']]);
$progress = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="table-card">
    <h2>Academic Progress</h2>
    <table>
        <thead>
            <tr>
                <th>Course</th>
                <th>Average Score</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($progress as $item): ?>
                <tr>
                    <td><?= e($item['Course_name']) ?></td>
                    <td><?= number_format((float)$item['average_score'], 1) ?> %</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
