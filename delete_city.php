<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_city'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $city_name = htmlspecialchars(trim($_POST['city_name']));

    $conn = get_db_connection();
    $stmt = $conn->prepare("DELETE FROM saved_cities WHERE user_id = ? AND city = ?");
    $stmt->bind_param("is", $user_id, $city_name);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error in deleting city";
    }

    $stmt->close();
    $conn->close();
}

