<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$role = current_user()['role'] ?? null;
if ($role !== 'STUDENT' && strpos(realpath(__FILE__), '/student/') !== false) {
    require_role('STUDENT');
}
if ($role !== 'INSTRUCTOR' && strpos(realpath(__FILE__), '/instructor/') !== false) {
    require_role('INSTRUCTOR');
}
if ($role !== 'ACADEMIC_OFFICER' && strpos(realpath(__FILE__), '/officer/') !== false) {
    require_role('ACADEMIC_OFFICER');
}

$pdo = get_pdo();
$user = current_user();
$studentStmt = $pdo->prepare('SELECT Student_id, Student_fname, Student_lname FROM Students WHERE User_id = ?');
$studentStmt->execute([$user['user_id']]);
$student = $studentStmt->fetch();

$courseStmt = $pdo->prepare('SELECT c.Course_id, c.Course_name, i.Instructor_name FROM Enrollments e JOIN Courses c ON c.Course_id = e.Course_id JOIN Instructors i ON i.Instructor_id = c.Instructor_id WHERE e.Student_id = ? ORDER BY c.Course_name');
$courseStmt->execute([$student['Student_id']]);
$courses = $courseStmt->fetchAll();

$assessmentStmt = $pdo->prepare('SELECT COUNT(*) FROM Assessments a JOIN Enrollments e ON e.Course_id = a.Course_id WHERE e.Student_id = ? AND a.Status = "PUBLISHED" AND a.End_date >= NOW()');
$assessmentStmt->execute([$student['Student_id']]);
$upcomingAssessments = (int) $assessmentStmt->fetchColumn();

$resultStmt = $pdo->prepare('SELECT COUNT(*) FROM Results r JOIN Submissions s ON s.Submission_id = r.Submission_id WHERE s.Student_id = ? AND r.Results_status = "PUBLISHED"');
$resultStmt->execute([$student['Student_id']]);
$recentResults = (int) $resultStmt->fetchColumn();
include __DIR__ . '/../includes/header.php';
?>
<section class="page-intro">
    <h1>Welcome, <?= e(trim($student['Student_fname'] . ' ' . $student['Student_lname'])) ?></h1>
    <p>Keep learning, keep growing!</p>
</section>
<div class="dashboard-card-grid dashboard-stats">
    <div class="stat-card stat-neutral"><strong><?= count($courses) ?></strong><span>Enrolled courses</span></div>
    <div class="stat-card stat-green"><strong><?= $upcomingAssessments ?></strong><span>Upcoming assessments</span></div>
    <div class="stat-card stat-rose"><strong><?= $recentResults ?></strong><span>Recent results</span></div>
</div>
<section class="section-heading">
    <h2>Enrolled Courses</h2>
    <a href="<?= e(base_url('/student/courses.php')) ?>">View all</a>
</section>
<div class="course-list">
    <?php foreach ($courses as $index => $course): ?>
        <article class="course-row">
            <div>
                <strong><?= e($course['Course_name']) ?></strong>
                <span>Instructor: <?= e($course['Instructor_name']) ?></span>
            </div>
            <div class="course-progress">
                <strong><?= $index % 3 === 0 ? '60' : ($index % 3 === 1 ? '40' : '20') ?>%</strong>
                <div class="progress-track"><span class="progress-fill progress-<?= $index % 3 ?>" style="width: <?= $index % 3 === 0 ? '60' : ($index % 3 === 1 ? '40' : '20') ?>%"></span></div>
            </div>
        </article>
    <?php endforeach; ?>
    <?php if (!$courses): ?><div class="empty-state">No enrolled courses yet.</div><?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
