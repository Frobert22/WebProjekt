<?php
session_start();
require 'functions.php';

$weather = null;
$error = null;
$conn = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['city'])) {
        $city = htmlspecialchars(trim($_GET['city']));
        $weather = getWeather($city);

        if ($weather) {
            // ha a felhasznalo belepet es akarja menteni
            if (isset($_GET['action']) && $_GET['action'] === 'saveCity' && isset($_SESSION['user_id'])) {
                saveCity($_SESSION['user_id'], $city);
            }
        } else {
            $error = "City not found. Please try again.";
        }
    }
}
$saved_cities = [];
if (isset($_SESSION['user_id'])) {
    $saved_cities = getSavedCities($_SESSION['user_id']);
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App</title>
    <link rel="stylesheet" href="style.css">
</head>

</html>

<div class="container">
    <h1>Weather Application</h1>

    <?php if (isset($_SESSION['username'])): ?>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! <a href="logout.php" style="color: #fff; text-decoration: underline;">Logout</a></p>
    <?php else: ?>
        <p><a href="login.php" style="color: #fff; text-decoration: underline;">Login</a> or <a href="register.php" style="color: #fff; text-decoration: underline;">Register</a> to save your favorite cities.</p>
    <?php endif; ?>

    <form method="GET" action="">
        <label>
            <input type="text" name="city" placeholder="Enter a city" required value="<?php echo htmlspecialchars($_GET['city'] ?? '') ?>">
        </label>
        <button type="submit">Get Weather</button>
        <?php if (isset($_SESSION['user_id']) && isset($_GET['city']) && $weather): ?>
            <button type="submit" name="action" value="saveCity">Save</button>
        <?php endif; ?>
    </form>

    <?php
    if ($weather) {
        echo "<div class='weather-info'>";
        echo "<h2>Weather in " . htmlspecialchars($weather['name']) . "</h2>";
        echo "<p>Temperature: " . htmlspecialchars($weather['main']['temp']) . "°C</p>";
        echo "<p>Humidity: " . htmlspecialchars($weather['main']['humidity']) . "%</p>";
        echo "<p>Condition: " . htmlspecialchars($weather['weather'][0]['description']) . "</p>";
        echo "</div>";
    } elseif ($error) {
        echo "<p>$error</p>";
    }
    ?>

    <?php if (!empty($saved_cities)): ?>
        <ul>
            <?php foreach ($saved_cities as $city): ?>
                <ul>
                    <a href="?city=<?php echo urlencode($city['city']); ?>">
                        <?php echo htmlspecialchars($city['city']); ?>
                    </a>
                    <form method="POST" action="delete_city.php" style="display:inline;">
                        <input type="hidden" name="city_name" value="<?php echo htmlspecialchars($city['city']); ?>">
                        <button type="submit" name="delete_city">Delete</button>
                    </form>
                </ul>

            <?php endforeach; ?>
        </ul>
    <?php endif; ?>




</div>
