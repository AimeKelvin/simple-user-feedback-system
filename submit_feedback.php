<?php
require 'config/db.php';

$name = htmlspecialchars($_POST['name']);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$rating = (int)$_POST['rating'];
$comment = htmlspecialchars($_POST['comment']);

if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid input.");
}

$sql = "INSERT INTO feedback (name, email, rating, comment) VALUES ('$name', '$email', $rating, '$comment')";

if ($conn->query($sql) === TRUE) {
    echo "Feedback submitted successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
