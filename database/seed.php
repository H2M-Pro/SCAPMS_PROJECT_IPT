<?php
require_once __DIR__ . '/../config/db.php';

$pdo = get_pdo();

$users = [
    ['student1', 'student123', 'STUDENT'],
    ['instructor1', 'instructor123', 'INSTRUCTOR'],
    ['officer1', 'officer123', 'ACADEMIC_OFFICER'],
];

foreach ($users as [$username, $plainPassword, $role]) {
    $hash = password_hash($plainPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO Users (Username, Password_hash, Role, Account_status) VALUES (?, ?, ?, "ACTIVE") ON DUPLICATE KEY UPDATE Password_hash = VALUES(Password_hash), Role = VALUES(Role), Account_status = "ACTIVE"');
    $stmt->execute([$username, $hash, $role]);
}

$studentUser = $pdo->prepare('SELECT User_id FROM Users WHERE Username = ?');
$studentUser->execute(['student1']);
$studentUserId = $studentUser->fetchColumn();

$instructorUser = $pdo->prepare('SELECT User_id FROM Users WHERE Username = ?');
$instructorUser->execute(['instructor1']);
$instructorUserId = $instructorUser->fetchColumn();

$officerUser = $pdo->prepare('SELECT User_id FROM Users WHERE Username = ?');
$officerUser->execute(['officer1']);
$officerUserId = $officerUser->fetchColumn();

$pdo->prepare('INSERT INTO Students (User_id, Registration_number, Student_fname, Student_mname, Student_lname, Student_email, Program, Student_gender, Year_of_study) VALUES (?, "T25-03-1001", "Amina", "H", "Juma", "amina@student.cive.ac.tz", "BIT", "F", 2) ON DUPLICATE KEY UPDATE Registration_number = VALUES(Registration_number)')->execute([$studentUserId]);

$pdo->prepare('INSERT INTO Instructors (User_id, Instructor_name, instructor_email) VALUES (?, "Dr. Salim Mkwawa", "salim.instructor@cive.ac.tz") ON DUPLICATE KEY UPDATE Instructor_name = VALUES(Instructor_name)')->execute([$instructorUserId]);

$pdo->prepare('INSERT INTO Academic_Officer (User_id, Name, Email) VALUES (?, "Ms. Ruth Kivuyo", "ruth.officer@cive.ac.tz") ON DUPLICATE KEY UPDATE Name = VALUES(Name)')->execute([$officerUserId]);

$instructorId = $pdo->query('SELECT Instructor_id FROM Instructors WHERE User_id = ' . (int)$instructorUserId)->fetchColumn();
$studentId = $pdo->query('SELECT Student_id FROM Students WHERE User_id = ' . (int)$studentUserId)->fetchColumn();

$course1 = $pdo->prepare('INSERT INTO Courses (Instructor_id, Course_name, Course_code) VALUES (?, "Web Technology", "WEB-101") ON DUPLICATE KEY UPDATE Course_name = VALUES(Course_name)');
$course1->execute([$instructorId]);
$courseId = $pdo->query('SELECT Course_id FROM Courses WHERE Course_code = "WEB-101"')->fetchColumn();

$pdo->prepare('INSERT INTO Enrollments (Student_id, Course_id, Academic_year, Semester) VALUES (?, ?, 2026, "SEMESTER 1") ON DUPLICATE KEY UPDATE Academic_year = VALUES(Academic_year)')->execute([$studentId, $courseId]);

$assessment = $pdo->prepare('INSERT INTO Assessments (Course_id, Title, Assessment_type, Instructions, Start_date, End_date, Duration, Total_marks, Status) VALUES (?, "Web Basics Quiz", "MCQ", "Answer all questions.", NOW(), DATE_ADD(NOW(), INTERVAL 2 HOUR), 60, 20, "PUBLISHED") ON DUPLICATE KEY UPDATE Title = VALUES(Title)');
$assessment->execute([$courseId]);
$assessmentId = $pdo->query('SELECT Assessment_id FROM Assessments WHERE Title = "Web Basics Quiz" ORDER BY Assessment_id DESC LIMIT 1')->fetchColumn();

$question1 = $pdo->prepare('INSERT INTO Questions (Assessment_id, Question_text, Question_type, Question_marks) VALUES (?, "Which tag is used to create a hyperlink in HTML?", "MCQ", 5)');
$question1->execute([$assessmentId]);
$q1Id = $pdo->lastInsertId();
$pdo->prepare('INSERT INTO Options (Question_id, Option_text, Is_correct) VALUES (?, "<img>", 0), (?, "<a>", 1), (?, "<p>", 0), (?, "<div>", 0)')->execute([$q1Id, $q1Id, $q1Id, $q1Id]);

$question2 = $pdo->prepare('INSERT INTO Questions (Assessment_id, Question_text, Question_type, Question_marks) VALUES (?, "CSS is used to style the structure of a webpage.", "TRUE_FALSE", 5)');
$question2->execute([$assessmentId]);
$q2Id = $pdo->lastInsertId();
$pdo->prepare('INSERT INTO Options (Question_id, Option_text, Is_correct) VALUES (?, "True", 1), (?, "False", 0)')->execute([$q2Id, $q2Id]);

$submission = $pdo->prepare('INSERT INTO Submissions (Student_id, Assessment_id, Submission_date, Submission_status) VALUES (?, ?, NOW(), "SUBMITTED") ON DUPLICATE KEY UPDATE Submission_status = VALUES(Submission_status)');
$submission->execute([$studentId, $assessmentId]);
$submissionId = $pdo->query('SELECT Submission_id FROM Submissions WHERE Student_id = ' . (int)$studentId . ' AND Assessment_id = ' . (int)$assessmentId . ' ORDER BY Submission_id DESC LIMIT 1')->fetchColumn();

$optionQ1 = $pdo->query('SELECT Option_id FROM Options WHERE Question_id = ' . (int)$q1Id . ' AND Is_correct = 1')->fetchColumn();
$optionQ2 = $pdo->query('SELECT Option_id FROM Options WHERE Question_id = ' . (int)$q2Id . ' AND Is_correct = 1')->fetchColumn();

$pdo->prepare('INSERT INTO Answers (Submission_id, Question_id, Option_id, Student_answer, Awarded_marks) VALUES (?, ?, ?, "A", 0), (?, ?, ?, "True", 5) ON DUPLICATE KEY UPDATE Awarded_marks = VALUES(Awarded_marks)')->execute([$submissionId, $q1Id, $optionQ1, $submissionId, $q2Id, $optionQ2]);

$pdo->prepare('INSERT INTO Results (Submission_id, Score, Results_status) VALUES (?, 5, "PUBLISHED") ON DUPLICATE KEY UPDATE Score = VALUES(Score)')->execute([$submissionId]);

$instructorIdForFeedback = $pdo->query('SELECT Instructor_id FROM Instructors WHERE User_id = ' . (int)$instructorUserId)->fetchColumn();
$pdo->prepare('INSERT INTO Feedback (Submission_id, Instructor_id, Feedback_content, Date_Provided) VALUES (?, ?, "Good attempt. Review HTML tags and CSS basics.", NOW())')->execute([$submissionId, $instructorIdForFeedback]);

echo "Seed data loaded successfully.\n";
