<?php
session_start();

$host = "localhost";
$user = "root";
$password = "";
$database = "srm_system";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $user = $result->fetch_assoc();

  // You can use password_verify() if passwords are hashed
  if ($password === $user['password']) {
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['email'] = $user['email'];
    header("Location: ../frontend/dashboard.html");
    exit();
  }
}

echo "<script>alert('Invalid credentials'); window.location.href = '../frontend/login.html';</script>";
