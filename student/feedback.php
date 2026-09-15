<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('STUDENT');
$pdo = get_pdo();
$user = current_user();

$stmt = $pdo->prepare('SELECT s.Student_id FROM Students s WHERE s.User_id = ?');
$stmt->execute([$user['user_id']]);
$student = $stmt->fetch();

$stmt = $pdo->prepare('SELECT f.Feedback_content, f.Date_Provided, a.Title, c.Course_name FROM Feedback f JOIN Submissions sub ON sub.Submission_id = f.Submission_id JOIN Assessments a ON a.Assessment_id = sub.Assessment_id JOIN Courses c ON c.Course_id = a.Course_id WHERE sub.Student_id = ? ORDER BY f.Date_Provided DESC');
$stmt->execute([$student['Student_id']]);
$feedback = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="table-card">
    <h2>Feedback</h2>
    <table>
        <thead>
            <tr>
                <th>Course</th>
                <th>Assessment</th>
                <th>Feedback</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($feedback as $item): ?>
                <tr>
                    <td><?= e($item['Course_name']) ?></td>
                    <td><?= e($item['Title']) ?></td>
                    <td><?= e($item['Feedback_content']) ?></td>
                    <td><?= e(date('d M Y H:i', strtotime($item['Date_Provided']))) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
