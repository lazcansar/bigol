<?php
require_once 'dbCon.php';
session_start();
session_destroy();
header("Location: login.php?logout=true");
exit();
?>