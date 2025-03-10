<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "school_report";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;

    if ($student_id <= 0) {
        echo json_encode(['error' => 'Invalid Student ID']);
        exit;
    }

    // Fetch student details
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo json_encode(['error' => 'Student not found']);
        exit;
    }

    // Fetch marks
    $stmt = $conn->prepare("SELECT subject, q1_mark, q2_mark, q3_mark FROM marks WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $marks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = [
        'student_name' => $student['student_name'],
        'school_year' => $student['school_year'],
        'grade' => $student['grade'],
        'term' => $student['term'],
        'teacher' => $student['teacher'],
        'date' => $student['date'],
        'absences' => $student['absences'],
        'tardies' => $student['tardies'],
        'early_dismissals' => $student['early_dismissals'],
        'penalties' => $student['penalties'],
        'marks' => $marks
    ];

    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
}
?>
