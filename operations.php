<?php
require_once 'dbCon.php';

// User Register Post Method
if (isset($_POST['register'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // DB Request
    $stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashedPassword);
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
        $_SESSION['role'] = $row['role'];
        $_SESSION['ibanConfirm'] = $row['ibanConfirm'];
        if ($row['role'] == '1') {
            header("Location: adminProfile.php");
            exit();
        } else {
            header("Location: profile.php");
            exit();
        }
    } else {
        header("Location: login.php?login=fail");
        exit();
    }

}


?>