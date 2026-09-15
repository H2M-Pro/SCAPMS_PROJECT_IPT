<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('INSTRUCTOR');
$pdo = get_pdo();
$user = current_user();

$stmt = $pdo->prepare('SELECT Instructor_id FROM Instructors WHERE User_id = ?');
$stmt->execute([$user['user_id']]);
$instructor = $stmt->fetch();

$stmt = $pdo->prepare('SELECT sub.Submission_id, a.Title, s.Student_fname, s.Student_lname, sub.Submission_date, sub.Submission_status FROM Submissions sub JOIN Students s ON s.Student_id = sub.Student_id JOIN Assessments a ON a.Assessment_id = sub.Assessment_id JOIN Courses c ON c.Course_id = a.Course_id WHERE c.Instructor_id = ? ORDER BY sub.Submission_date DESC');
$stmt->execute([$instructor['Instructor_id']]);
$submissions = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="table-card">
    <h2>Student Submissions</h2>
    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Assessment</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($submissions as $submission): ?>
                <tr>
                    <td><?= e($submission['Student_fname'] . ' ' . $submission['Student_lname']) ?></td>
                    <td><?= e($submission['Title']) ?></td>
                    <td><?= e(date('d M Y H:i', strtotime($submission['Submission_date']))) ?></td>
                    <td><?= e($submission['Submission_status']) ?></td>
                    <td><a class="button-link" href="<?= e(base_url('/instructor/view_submission.php?submission_id=' . $submission['Submission_id'])) ?>">Review</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
