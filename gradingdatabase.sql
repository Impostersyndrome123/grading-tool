-- Create database
CREATE DATABASE grading_system;
USE grading_system;

-- Students table (list of students already available)
CREATE TABLE students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL
);

-- Grades table to store each student's homework, quizzes, midterm, and final project grades
CREATE TABLE grades (
    grade_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    homework1 INT,
    homework2 INT,
    homework3 INT,
    homework4 INT,
    homework5 INT,
    quiz1 INT,
    quiz2 INT,
    quiz3 INT,
    quiz4 INT,
    quiz5 INT,
    midterm INT,
    final_project INT,
    final_grade INT,
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);


