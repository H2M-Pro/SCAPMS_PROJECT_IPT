<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('INSTRUCTOR');
$pdo = get_pdo();
$user = current_user();

$assessmentId = (int) ($_GET['assessment_id'] ?? 0);

$stmt = $pdo->prepare('SELECT Instructor_id FROM Instructors WHERE User_id = ?');
$stmt->execute([$user['user_id']]);
$instructor = $stmt->fetch();

$stmt = $pdo->prepare('SELECT Course_id, Course_name, Course_code FROM Courses WHERE Instructor_id = ? ORDER BY Course_name');
$stmt->execute([$instructor['Instructor_id']]);
$courses = $stmt->fetchAll();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseId = (int) ($_POST['course_id'] ?? 0);
    $title = trim((string) ($_POST['title'] ?? ''));
    $instructions = trim((string) ($_POST['instructions'] ?? ''));
    $type = trim((string) ($_POST['assessment_type'] ?? 'MCQ'));
    $duration = (int) ($_POST['duration'] ?? 60);
    $totalMarks = (float) ($_POST['total_marks'] ?? 0);
    $startDate = trim((string) ($_POST['start_date'] ?? ''));
    $endDate = trim((string) ($_POST['end_date'] ?? ''));

    if ($courseId <= 0 || $title === '' || $instructions === '' || $startDate === '' || $endDate === '') {
        $errors[] = 'Please complete all required assessment fields.';
    } else {
        $insert = $pdo->prepare('INSERT INTO Assessments (Course_id, Title, Assessment_type, Instructions, Start_date, End_date, Duration, Total_marks, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, "DRAFT")');
        $ok = $insert->execute([$courseId, $title, $type, $instructions, $startDate, $endDate, $duration, $totalMarks]);

        if ($ok) {
            $success = true;
            $assessmentId = (int) $pdo->lastInsertId();
            $_SESSION['last_assessment_id'] = $assessmentId;
        } else {
            $errors[] = 'Unable to create assessment.';
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="panel">
    <h2>Create Assessment</h2>

    <?php if ($errors): foreach ($errors as $error): ?>
        <div class="form-error"><?= e($error) ?></div>
    <?php endforeach; endif; ?>

    <?php if ($success): ?>
        <div class="form-success">Assessment created successfully. You can now add questions below.</div>
    <?php endif; ?>

    <form method="post" action="create_assessment.php" class="form-grid">
        <div>
            <label for="course_id">Course</label>
            <select name="course_id" id="course_id" required>
                <option value="">Select Course</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= e($course['Course_id']) ?>"><?= e($course['Course_name']) ?> (<?= e($course['Course_code']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="title">Assessment Title</label>
            <input type="text" name="title" id="title" required>
        </div>

        <div>
            <label for="assessment_type">Assessment Type</label>
            <select name="assessment_type" id="assessment_type">
                <option value="MCQ">MCQ</option>
                <option value="TRUE_FALSE">True/False</option>
            </select>
        </div>

        <div>
            <label for="duration">Duration (minutes)</label>
            <input type="number" name="duration" id="duration" min="1" value="60" required>
        </div>

        <div>
            <label for="total_marks">Total Marks</label>
            <input type="number" step="0.01" name="total_marks" id="total_marks" min="1" value="20" required>
        </div>

        <div>
            <label for="start_date">Start Date & Time</label>
            <input type="datetime-local" name="start_date" id="start_date" required>
        </div>

        <div>
            <label for="end_date">End Date & Time</label>
            <input type="datetime-local" name="end_date" id="end_date" required>
        </div>

        <div>
            <label for="instructions">Instructions</label>
            <textarea name="instructions" id="instructions" rows="4" required></textarea>
        </div>

        <button type="submit">Create Assessment</button>
    </form>
</div>

<?php if (!empty($_SESSION['last_assessment_id'])): ?>
    <div class="panel">
        <h2>Add Questions</h2>
        <form method="post" action="save_questions.php" class="form-grid">
            <input type="hidden" name="assessment_id" value="<?= (int) $_SESSION['last_assessment_id'] ?>">
            <div>
                <label for="question_text">Question</label>
                <textarea name="question_text" id="question_text" rows="3" required></textarea>
            </div>
            <div>
                <label for="question_type">Question Type</label>
                <select name="question_type" id="question_type">
                    <option value="MCQ">MCQ</option>
                    <option value="TRUE_FALSE">True / False</option>
                </select>
            </div>
            <div>
                <label for="question_marks">Marks</label>
                <input type="number" step="0.01" name="question_marks" id="question_marks" min="1" value="5" required>
            </div>
            <div>
                <label for="option_a">Option A</label>
                <input type="text" name="option_a" id="option_a" required>
            </div>
            <div>
                <label for="option_b">Option B</label>
                <input type="text" name="option_b" id="option_b" required>
            </div>
            <div>
                <label for="option_c">Option C</label>
                <input type="text" name="option_c" id="option_c">
            </div>
            <div>
                <label for="option_d">Option D</label>
                <input type="text" name="option_d" id="option_d">
            </div>
            <div>
                <label for="correct_option">Correct Option</label>
                <select name="correct_option" id="correct_option">
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>
            <button type="submit">Save Question</button>
        </form>
    </div>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
