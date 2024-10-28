<?php
$host = 'localhost';
$db = 'grading_system';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>


<?php include 'database.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Grades</title>
</head>
<body>
    <h1>Enter Student Grades</h1>
    <form action="process_grades.php" method="POST">
        <label for="student">Select Student:</label>
        <select name="student_id" required>
            <?php
            $stmt = $pdo->query("SELECT * FROM students");
            while ($row = $stmt->fetch()) {
                echo "<option value='{$row['student_id']}'>{$row['name']}</option>";
            }
            ?>
        </select>

        <h2>Homework Grades</h2>
        <?php for ($i = 1; $i <= 5; $i++) { ?>
            <label>Homework <?= $i ?>: <input type="number" name="homework<?= $i ?>" required></label><br>
        <?php } ?>

        <h2>Quiz Grades</h2>
        <?php for ($i = 1; $i <= 5; $i++) { ?>
            <label>Quiz <?= $i ?>: <input type="number" name="quiz<?= $i ?>" required></label><br>
        <?php } ?>

        <label>Midterm: <input type="number" name="midterm" required></label><br>
        <label>Final Project: <input type="number" name="final_project" required></label><br>

        <button type="submit">Submit Grades</button>
    </form>
</body>
</html>

<?php
include 'database.php';

function calculate_final_grade($homeworks, $quizzes, $midterm, $final_project) {

    $homework_avg = array_sum($homeworks) / count($homeworks);
    $homework_weighted = $homework_avg * 0.2;

  
    sort($quizzes);
    array_shift($quizzes);
    $quiz_avg = array_sum($quizzes) / count($quizzes);
    $quiz_weighted = $quiz_avg * 0.1;

   
    $midterm_weighted = $midterm * 0.3;
    $final_project_weighted = $final_project * 0.4;

   
    $final_grade = round($homework_weighted + $quiz_weighted + $midterm_weighted + $final_project_weighted);
    return $final_grade;
}


$student_id = $_POST['student_id'];
$homeworks = [];
$quizzes = [];

for ($i = 1; $i <= 5; $i++) {
    $homeworks[] = (int) $_POST["homework$i"];
    $quizzes[] = (int) $_POST["quiz$i"];
}

$midterm = (int) $_POST['midterm'];
$final_project = (int) $_POST['final_project'];


$final_grade = calculate_final_grade($homeworks, $quizzes, $midterm, $final_project);


$stmt = $pdo->prepare("INSERT INTO grades (student_id, homework1, homework2, homework3, homework4, homework5, 
                      quiz1, quiz2, quiz3, quiz4, quiz5, midterm, final_project, final_grade) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$student_id, ...$homeworks, ...$quizzes, $midterm, $final_project, $final_grade]);

echo "Grades submitted successfully! Final Grade: $final_grade";
?>
