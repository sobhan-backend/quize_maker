<?php
$servername = "localhost";
$password   = "123456";
$dbname     = "quize";
$username   = "root";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $conn;
} catch(PDOException $e) {
    die("خطا در اتصال به دیتابیس: " . $e->getMessage());
}