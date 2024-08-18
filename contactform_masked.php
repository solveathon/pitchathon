<?php
header('Content-Type: application/json');

function isValidEmail($email) {
    $sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
    $isValid = filter_var($sanitizedEmail, FILTER_VALIDATE_EMAIL) && preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $sanitizedEmail);
    if (!$isValid) {
        error_log("Invalid email detected: " . $email);
    }
    return $isValid;
}

function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$servername = "localhost";
$username = "ASKME";
$password = "ASKME";
$dbname = "ASKME";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize_input($_POST["name"]);
    $email = sanitize_input($_POST["email"]);
    $subject = sanitize_input($_POST["subject"]);
    $message = sanitize_input($_POST["message"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    $stmt = $conn->prepare("INSERT INTO pthonqueries (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "We have received your query and we'll get back to you asap!"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Error receiving your query: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
