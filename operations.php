<?php
require_once 'dbCon.php';

// User Register Post Method
if (isset($_POST['register'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_NUMBER_INT);
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // DB Request
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

// User Login Post Method
if (isset($_POST['login'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // DB Request
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        session_start();
        $_SESSION['id'] = $row['id'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['name'] = $row['name'];
        $_SESSION['phone'] = $row['phone'];
        header("Location: index.php");
        exit();
    } else {
        header("Location: register.php?login=fail");
        exit();
    }
    $stmt->close();
}


?>