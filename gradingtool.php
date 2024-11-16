<?php
include 'database.php';

function calculate_final_grade($homeworks, $quizzes, $midterm, $final_project) {
    $homework_avg = array_sum($homeworks) / count($homeworks);
    $homework_weighted = $homework_avg * 0.2;

    sort($quizzes);
    array_shift($quizzes); // Drop the lowest quiz grade
    $quiz_avg = array_sum($quizzes) / count($quizzes);
    $quiz_weighted = $quiz_avg * 0.1;

    $midterm_weighted = $midterm * 0.3;
    $final_project_weighted = $final_project * 0.4;

    $final_grade = round($homework_weighted + $quiz_weighted + $midterm_weighted + $final_project_weighted);
    return $final_grade;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $homeworks = [];
    $quizzes = [];

    for ($i = 1; $i <= 5; $i++) {
        $homeworks[] = filter_var($_POST["homework$i"], FILTER_VALIDATE_INT, ["options" => ["min_range" => 0, "max_range" => 100]]);
        $quizzes[] = filter_var($_POST["quiz$i"], FILTER_VALIDATE_INT, ["options" => ["min_range" => 0, "max_range" => 100]]);
    }

    $midterm = filter_var($_POST['midterm'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 0, "max_range" => 100]]);
    $final_project = filter_var($_POST['final_project'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 0, "max_range" => 100]]);

    if (in_array(false, array_merge($homeworks, $quizzes), true) || $midterm === false || $final_project === false) {
        die("Invalid input. Ensure all grades are between 0 and 100.");
    }

    $final_grade = calculate_final_grade($homeworks, $quizzes, $midterm, $final_project);

    try {
        $stmt = $pdo->prepare("INSERT INTO grades (student_id, homework1, homework2, homework3, homework4, homework5, 
                              quiz1, quiz2, quiz3, quiz4, quiz5, midterm, final_project, final_grade) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array_merge([$student_id], $homeworks, $quizzes, [$midterm, $final_project, $final_grade]));

        echo "Grades submitted successfully! Final Grade: $final_grade";
    } catch (PDOException $e) {
        echo "Error saving grades: " . $e->getMessage();
    }
}
?>
