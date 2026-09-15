<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('ACADEMIC_OFFICER');
$pdo = get_pdo();

$stmt = $pdo->query('SELECT e.Enrollment_id, s.Student_fname, s.Student_lname, c.Course_name, e.Academic_year, e.Semester FROM Enrollments e JOIN Students s ON s.Student_id = e.Student_id JOIN Courses c ON c.Course_id = e.Course_id ORDER BY e.Enrollment_id DESC');
$enrollments = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="table-card">
    <h2>Enrollments</h2>
    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Course</th>
                <th>Year</th>
                <th>Semester</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($enrollments as $enrollment): ?>
                <tr>
                    <td><?= e($enrollment['Student_fname'] . ' ' . $enrollment['Student_lname']) ?></td>
                    <td><?= e($enrollment['Course_name']) ?></td>
                    <td><?= e($enrollment['Academic_year']) ?></td>
                    <td><?= e($enrollment['Semester']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
