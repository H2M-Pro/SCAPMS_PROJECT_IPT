<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('STUDENT');
$pdo = get_pdo();
$user = current_user();

$stmt = $pdo->prepare('SELECT s.Student_id FROM Students s WHERE s.User_id = ?');
$stmt->execute([$user['user_id']]);
$student = $stmt->fetch();

$stmt = $pdo->prepare('SELECT a.Title, c.Course_name, r.Score, r.Results_status, sub.Submission_date FROM Results r JOIN Submissions sub ON sub.Submission_id = r.Submission_id JOIN Assessments a ON a.Assessment_id = sub.Assessment_id JOIN Courses c ON c.Course_id = a.Course_id WHERE sub.Student_id = ? ORDER BY sub.Submission_id DESC');
$stmt->execute([$student['Student_id']]);
$results = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<section class="page-intro compact-intro"><h1>My Results &amp; Feedback</h1></section>
<div class="designer-table-wrap">
    <table>
        <thead>
            <tr>
                <th>Course</th>
                <th>Score</th>
                <th>Feedback</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $result): ?>
                <tr>
                    <td><?= e($result['Course_name']) ?></td>
                    <td><?= e($result['Score']) ?></td>
                    <td>See feedback</td>
                    <td><?= e(date('d M Y', strtotime($result['Submission_date']))) ?></td>
                    <td><?= e($result['Results_status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
