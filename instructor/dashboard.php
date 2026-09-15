<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('INSTRUCTOR');

$pdo = get_pdo();
$user = current_user();

$stmt = $pdo->prepare('SELECT Instructor_id, Instructor_name FROM Instructors WHERE User_id = ?');
$stmt->execute([$user['user_id']]);
$instructor = $stmt->fetch();

$courseStmt = $pdo->prepare('SELECT Course_id, Course_name, Course_code FROM Courses WHERE Instructor_id = ? ORDER BY Course_name');
$courseStmt->execute([$instructor['Instructor_id']]);
$courses = $courseStmt->fetchAll();

$assessmentStmt = $pdo->prepare('SELECT a.Assessment_id, a.Title, a.Status, c.Course_name FROM Assessments a JOIN Courses c ON c.Course_id = a.Course_id WHERE c.Instructor_id = ? ORDER BY a.Assessment_id DESC');
$assessmentStmt->execute([$instructor['Instructor_id']]);
$assessments = $assessmentStmt->fetchAll();

$studentStmt = $pdo->prepare('SELECT COUNT(DISTINCT e.Student_id) AS total_students FROM Enrollments e JOIN Courses c ON c.Course_id = e.Course_id WHERE c.Instructor_id = ?');
$studentStmt->execute([$instructor['Instructor_id']]);
$totalStudents = (int) $studentStmt->fetch()['total_students'];

include __DIR__ . '/../includes/header.php';
?>
<section class="page-intro">
    <h1>Welcome, <?= e($instructor['Instructor_name']) ?></h1>
    <p>Manage your courses and students</p>
</section>
<div class="dashboard-card-grid dashboard-stats">
    <div class="stat-card stat-green"><strong><?= count($courses) ?></strong><span>My courses</span></div>
    <div class="stat-card stat-neutral"><strong><?= $totalStudents ?></strong><span>Total students</span></div>
    <div class="stat-card stat-rose"><strong><?= count($assessments) ?></strong><span>Upcoming assessments</span></div>
</div>
<section class="section-heading"><h2>Course Overview</h2><a href="<?= e(base_url('/instructor/course.php')) ?>">View all</a></section>
<div class="overview-list">
    <?php foreach ($courses as $course): ?>
        <a class="overview-row" href="<?= e(base_url('/instructor/course_assessments.php?course_id=' . $course['Course_id'])) ?>">
            <strong><?= e($course['Course_name']) ?></strong>
            <span><?= e($course['Course_code']) ?></span>
            <span><?= $totalStudents ?> students</span>
            <span class="row-arrow">›</span>
        </a>
    <?php endforeach; ?>
    <?php if (!$courses): ?><div class="empty-state">No courses assigned yet.</div><?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
    </section>

    <section class="panel">
        <h2>Assessment Overview</h2>
        <?php if (empty($assessments)): ?>
            <p>No assessments created yet.</p>
        <?php else: ?>
            <ul class="list-stack">
                <?php foreach (array_slice($assessments, 0, 5) as $assessment): ?>
                    <li>
                        <strong><?= e($assessment['Title']) ?></strong>
                        <span><?= e($assessment['Status']) ?></span><br>
                        <small><?= e($assessment['Course_name']) ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>