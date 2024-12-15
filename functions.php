<?php

require 'db.php';

global $conn;
function getWeather($city) {
    $apiKey = "f48d2c8261ae4431df7d73ce9f8c6927";  // API kulcs
    $apiUrl = "https://api.openweathermap.org/data/2.5/weather?q={$city}&units=metric&appid={$apiKey}";

    // CURL hívás az API lekéréséhez
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {

        echo 'Curl error: ' . curl_error($ch);
        curl_close($ch);
        return false;
    }

    curl_close($ch);

    $data = json_decode($response, true);  // JSON válasz dekódolása

    if ($data && $data['cod'] == 200) {  // Sikeres válasz (200 - sikeres)
        return $data;
    } else {
        return false;
    }
}

function saveCity($userId, $city): void
{
    global $conn;

    // ellenőrzés, hogy a város már mentve van-e a felhasználóhoz
    $stmt = $conn->prepare("SELECT id FROM saved_cities WHERE user_id = ? AND city = ?");
    if (!$stmt) {
        die('MySQL hiba: ' . $conn->error);
    }

    $stmt->bind_param('is', $userId, $city); // i=int, s=string
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        // Ha a város nem létezik, mentjük el
        $stmt = $conn->prepare("INSERT INTO saved_cities (user_id, city) VALUES (?, ?)");
        if (!$stmt) {
            die('MySQL hiba: ' . $conn->error);
        }

        $stmt->bind_param('is', $userId, $city);
        $stmt->execute();
    }

    $stmt->close();

}



// A felhasználó mentett városainak lekérése
function getSavedCities($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT city FROM saved_cities WHERE user_id = ?");
    if ($stmt === false) {
        die('MySQL error: ' . $conn->error);
    }

    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $cities = $result->fetch_all(MYSQLI_ASSOC);  // Az összes város adatának lekérése
    $stmt->close();
    return $cities;
}

