<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "srm_system";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Optional: Check if email already exists
$check = $conn->prepare("SELECT * FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$checkResult = $check->get_result();

if ($checkResult->num_rows > 0) {
  echo "<script>alert('Email already registered!'); window.location.href = '../frontend/register.html';</script>";
  exit();
}

// Optional: You can hash password like this:
// $password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $password);

if ($stmt->execute()) {
  echo "<script>alert('Registration successful! You can now login.'); window.location.href = '../frontend/login.html';</script>";
} else {
  echo "<script>alert('Error during registration.'); window.location.href = '../frontend/register.html';</script>";
}
?>
