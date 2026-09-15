<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('ACADEMIC_OFFICER');
$pdo = get_pdo();

$stmt = $pdo->query('SELECT c.Course_name, COUNT(DISTINCT e.Student_id) AS total_students, AVG(r.Score) AS avg_score FROM Courses c LEFT JOIN Enrollments e ON e.Course_id = c.Course_id LEFT JOIN Assessments a ON a.Course_id = c.Course_id LEFT JOIN Submissions s ON s.Assessment_id = a.Assessment_id LEFT JOIN Results r ON r.Submission_id = s.Submission_id GROUP BY c.Course_id, c.Course_name');
$reports = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="table-card">
    <h2>Academic Reports</h2>
    <table>
        <thead>
            <tr>
                <th>Course</th>
                <th>Students</th>
                <th>Average Score</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reports as $report): ?>
                <tr>
                    <td><?= e($report['Course_name']) ?></td>
                    <td><?= e($report['total_students']) ?></td>
                    <td><?= number_format((float)($report['avg_score'] ?? 0), 1) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
