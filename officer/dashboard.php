<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('ACADEMIC_OFFICER');

$pdo = get_pdo();
$stmt = $pdo->query('SELECT COUNT(*) AS total_users FROM Users');
$totalUsers = (int) $stmt->fetch()['total_users'];

$stmt = $pdo->query('SELECT COUNT(*) AS total_courses FROM Courses');
$totalCourses = (int) $stmt->fetch()['total_courses'];

$stmt = $pdo->query('SELECT COUNT(*) AS total_assessments FROM Assessments');
$totalAssessments = (int) $stmt->fetch()['total_assessments'];

$stmt = $pdo->query('SELECT COALESCE(AVG(r.Score), 0) AS avg_score FROM Results r');
$avgScore = (float) $stmt->fetch()['avg_score'];

include __DIR__ . '/../includes/header.php';
?>
<section class="page-intro">
    <h1>Academic overview</h1>
    <p>Monitor courses, users, assessments, and performance</p>
</section>
<div class="dashboard-card-grid dashboard-stats">
    <div class="stat-card stat-green"><strong><?= $totalCourses ?></strong><span>Total courses</span></div>
    <div class="stat-card stat-neutral"><strong><?= $totalUsers ?></strong><span>Total users</span></div>
    <div class="stat-card stat-rose"><strong><?= $totalAssessments ?></strong><span>Assessments</span></div>
</div>
<section class="section-heading"><h2>Administration shortcuts</h2></section>
<div class="overview-list">
    <a class="overview-row" href="<?= e(base_url('/officer/manage_courses.php')) ?>"><strong>Course management</strong><span>Manage active courses</span><span class="row-arrow">›</span></a>
    <a class="overview-row" href="<?= e(base_url('/officer/manage_users.php')) ?>"><strong>User management</strong><span>Review platform accounts</span><span class="row-arrow">›</span></a>
    <a class="overview-row" href="<?= e(base_url('/officer/reports.php')) ?>"><strong>Performance reports</strong><span>Average score: <?= number_format($avgScore, 1) ?></span><span class="row-arrow">›</span></a>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>