<?php
require_once 'dbCon.php';

// User Register Post Method
if (isset($_POST['register'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_NUMBER_INT);
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // DB Request Post
    $stmt = $db->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $hashedPassword);
    if ($stmt->execute()) {
        header("Location: register.php?register=success");
        exit();
    } else {
        header("Location: register.php?register=fail");
    }
    $stmt->close();
}


?>