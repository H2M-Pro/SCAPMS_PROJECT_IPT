<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('INSTRUCTOR');
$pdo = get_pdo();
$user = current_user();

$stmt = $pdo->prepare('SELECT Instructor_id FROM Instructors WHERE User_id = ?');
$stmt->execute([$user['user_id']]);
$instructor = $stmt->fetch();

$stmt = $pdo->prepare('SELECT c.Course_name, COUNT(DISTINCT e.Student_id) AS enrolled, COUNT(DISTINCT a.Assessment_id) AS assessments FROM Courses c LEFT JOIN Enrollments e ON e.Course_id = c.Course_id LEFT JOIN Assessments a ON a.Course_id = c.Course_id WHERE c.Instructor_id = ? GROUP BY c.Course_id, c.Course_name');
$stmt->execute([$instructor['Instructor_id']]);
$reports = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="table-card">
    <h2>Course Reports</h2>
    <table>
        <thead>
            <tr>
                <th>Course</th>
                <th>Students</th>
                <th>Assessments</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reports as $report): ?>
                <tr>
                    <td><?= e($report['Course_name']) ?></td>
                    <td><?= e($report['enrolled']) ?></td>
                    <td><?= e($report['assessments']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
