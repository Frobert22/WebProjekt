<?php
const DB_USER = "root";
const DB_PASSWORD = "";
const DB_DATABASE = "weather_app";
const DB_HOST = "localhost";


function get_db_connection(): mysqli
{
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);


    if ($mysqli->connect_error) {

        error_log("Database connection failed: " . $mysqli->connect_error);
        die("Database connection failed, please try again later.");
    }

    return $mysqli;
}

