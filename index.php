<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$host = 'localhost';
$db = 'bvc_students';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// GET all students
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM students";
    $result = $conn->query($sql);
    $students = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }
    echo json_encode($students);
}

// POST a new student

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Check if the necessary fields are set
    if (isset($data['name']) && isset($data['age'])) {
        $name = $data['name'];
        $age = $data['age'];

        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO students (name, age) VALUES (?, ?)");
        $stmt->bind_param("si", $name, $age); // "si" means string for name and integer for age

        if ($stmt->execute()) {
            echo json_encode(["message" => "Student added successfully"]);
        } else {
            echo json_encode(["error" => "Error adding student"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["error" => "Invalid input data"]);
    }
}



// DELETE a student
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    $sql = "DELETE FROM students WHERE id = $id";
    if ($conn->query($sql)) {
        echo json_encode(["message" => "Student deleted successfully"]);
    } else {
        echo json_encode(["error" => "Error deleting student"]);
    }
}

$conn->close();
?>