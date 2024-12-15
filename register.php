<?php
// register.php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = htmlspecialchars(trim($_POST['email']));

    // confirm pw & pw egyezik-e
    if ($password !== $confirm_password) {
        echo "password does not match";
    } else {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // megnezi h a felhasználónév vagy email már létezik-e
        $conn = get_db_connection();

        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "The username already exists!";
        } else {
            // Új felhasználó hozzáadása az adatbázishoz
            $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $password_hash, $email);
            $stmt->execute();

            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['username'] = $username;


            header("Location: index.php");
            exit;
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

</head>
<body>
<div class="form-container">
    <h1>Register</h1>
    <form method="POST" action="">
        <label>
            <input type="text" name="username" placeholder="Username" required>
        </label>
        <label>
            <input type="email" name="email" placeholder="Email" required>
        </label>
        <label>
            <input type="password" name="password" placeholder="Password" required>
        </label>
        <label>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        </label>
        <button type="submit" name="register">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</div>
</body>
</html>
