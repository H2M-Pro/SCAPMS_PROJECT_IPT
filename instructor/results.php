<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('INSTRUCTOR');
$pdo = get_pdo();
$user = current_user();

$stmt = $pdo->prepare('SELECT Instructor_id FROM Instructors WHERE User_id = ?');
$stmt->execute([$user['user_id']]);
$instructor = $stmt->fetch();

$stmt = $pdo->prepare('SELECT r.Result_id, a.Title, s.Student_fname, s.Student_lname, r.Score, r.Results_status FROM Results r JOIN Submissions sub ON sub.Submission_id = r.Submission_id JOIN Students s ON s.Student_id = sub.Student_id JOIN Assessments a ON a.Assessment_id = sub.Assessment_id JOIN Courses c ON c.Course_id = a.Course_id WHERE c.Instructor_id = ? ORDER BY r.Result_id DESC');
$stmt->execute([$instructor['Instructor_id']]);
$results = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="table-card">
    <h2>Results</h2>
    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Assessment</th>
                <th>Score</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $result): ?>
                <tr>
                    <td><?= e($result['Student_fname'] . ' ' . $result['Student_lname']) ?></td>
                    <td><?= e($result['Title']) ?></td>
                    <td><?= e($result['Score']) ?></td>
                    <td><?= e($result['Results_status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
