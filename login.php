<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = trim($_POST['password']);


    if (!empty($username) && !empty($password)) {

        $conn = get_db_connection();

        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {

            if (password_verify($password, $row['password'])) {
                // sikeres bejelenkezesnel session
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $username;

                header("Location: index.php");
                exit();
            } else {

                $error = "Incorrect password!";
            }
        } else {
            $error = "No user found with this username or email.";
        }

        $stmt->close();
        $conn->close();
    } else {
        $error = "Please fill out all fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - Weather App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Log In</h2>

    <?php if (isset($error) && !empty($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>
            Username or Email:
            <input type="text" name="username" required>
        </label><br>
        <label>
            Password:
            <input type="password" name="password" required>
        </label><br>
        <button type="submit" name="login">Log In</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
</div>
</body>
</html>
