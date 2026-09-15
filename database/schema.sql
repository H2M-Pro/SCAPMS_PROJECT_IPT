CREATE DATABASE IF NOT EXISTS SCAPMS;
USE SCAPMS;

CREATE TABLE IF NOT EXISTS Users (
    User_id INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(100) NOT NULL UNIQUE,
    Password_hash VARCHAR(255) NOT NULL,
    Role VARCHAR(30) NOT NULL,
    Account_status ENUM('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE'
);

CREATE TABLE IF NOT EXISTS Students (
    Student_id INT AUTO_INCREMENT PRIMARY KEY,
    User_id INT NOT NULL,
    Registration_number VARCHAR(50) NOT NULL UNIQUE,
    Student_fname VARCHAR(100) NOT NULL,
    Student_mname VARCHAR(100) DEFAULT NULL,
    Student_lname VARCHAR(100) NOT NULL,
    Student_email VARCHAR(100) DEFAULT NULL,
    Program VARCHAR(100) NOT NULL,
    Student_gender ENUM('F','M') DEFAULT NULL,
    Year_of_study INT NOT NULL,
    FOREIGN KEY (User_id) REFERENCES Users(User_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS Instructors (
    Instructor_id INT AUTO_INCREMENT PRIMARY KEY,
    User_id INT NOT NULL,
    Instructor_name VARCHAR(100) NOT NULL,
    instructor_email VARCHAR(100) NOT NULL,
    FOREIGN KEY (User_id) REFERENCES Users(User_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS Academic_Officer (
    Academic_officer_id INT AUTO_INCREMENT PRIMARY KEY,
    User_id INT NOT NULL,
    Name VARCHAR(100) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    FOREIGN KEY (User_id) REFERENCES Users(User_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS Courses (
    Course_id INT AUTO_INCREMENT PRIMARY KEY,
    Instructor_id INT NOT NULL,
    Course_name VARCHAR(100) NOT NULL,
    Course_code VARCHAR(50) NOT NULL UNIQUE,
    FOREIGN KEY (Instructor_id) REFERENCES Instructors(Instructor_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS Enrollments (
    Enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
    Student_id INT NOT NULL,
    Course_id INT NOT NULL,
    Academic_year INT NOT NULL,
    Semester VARCHAR(20) NOT NULL,
    UNIQUE(Student_id, Course_id, Academic_year, Semester),
    FOREIGN KEY (Student_id) REFERENCES Students(Student_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Course_id) REFERENCES Courses(Course_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS Assessments (
    Assessment_id INT AUTO_INCREMENT PRIMARY KEY,
    Course_id INT NOT NULL,
    Title VARCHAR(150) NOT NULL,
    Assessment_type VARCHAR(100) NOT NULL,
    Instructions TEXT,
    Start_date DATETIME NOT NULL,
    End_date DATETIME NOT NULL,
    Duration INT NOT NULL,
    Total_marks DECIMAL(5,2) NOT NULL,
    Status ENUM('DRAFT','PUBLISHED','CLOSED') NOT NULL DEFAULT 'DRAFT',
    FOREIGN KEY (Course_id) REFERENCES Courses(Course_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS Questions (
    Question_id INT AUTO_INCREMENT PRIMARY KEY,
    Assessment_id INT NOT NULL,
    Question_text TEXT NOT NULL,
    Question_type ENUM('MCQ','TRUE_FALSE') NOT NULL,
    Question_marks DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (Assessment_id) REFERENCES Assessments(Assessment_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS Options (
    Option_id INT AUTO_INCREMENT PRIMARY KEY,
    Question_id INT NOT NULL,
    Option_text TEXT NOT NULL,
    Is_correct TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (Question_id) REFERENCES Questions(Question_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS Submissions (
    Submission_id INT AUTO_INCREMENT PRIMARY KEY,
    Student_id INT NOT NULL,
    Assessment_id INT NOT NULL,
    Submission_date DATETIME NOT NULL,
    Submission_status VARCHAR(50) NOT NULL DEFAULT 'SUBMITTED',
    FOREIGN KEY (Student_id) REFERENCES Students(Student_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Assessment_id) REFERENCES Assessments(Assessment_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS Answers (
    Answer_id INT AUTO_INCREMENT PRIMARY KEY,
    Submission_id INT NOT NULL,
    Question_id INT NOT NULL,
    Option_id INT NOT NULL,
    Student_answer VARCHAR(255) NOT NULL,
    Awarded_marks DECIMAL(5,2) NOT NULL DEFAULT 0,
    UNIQUE(Submission_id, Question_id),
    FOREIGN KEY (Submission_id) REFERENCES Submissions(Submission_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Question_id) REFERENCES Questions(Question_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Option_id) REFERENCES Options(Option_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS Results (
    Result_id INT AUTO_INCREMENT PRIMARY KEY,
    Submission_id INT NOT NULL,
    Score DECIMAL(5,2) NOT NULL,
    Results_status ENUM('PUBLISHED','PENDING','NOT_PUBLISHED') NOT NULL DEFAULT 'PENDING',
    FOREIGN KEY (Submission_id) REFERENCES Submissions(Submission_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS Feedback (
    Feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    Submission_id INT NOT NULL,
    Instructor_id INT NOT NULL,
    Feedback_content TEXT NOT NULL,
    Date_Provided DATETIME NOT NULL,
    FOREIGN KEY (Submission_id) REFERENCES Submissions(Submission_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Instructor_id) REFERENCES Instructors(Instructor_id) ON DELETE CASCADE ON UPDATE CASCADE
);
