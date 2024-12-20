<?php

try {
$dsn = "mysql:host=localhost; dbname=forumdb";
$dbusername = "root";
$dbpass = "";

$conn = new PDO($dsn, $dbusername, $dbpass);
$conn ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if ($conn == true) {
   // echo "Successfully connected to db";
} else {
    echo "Failed to connect to db";
}
} catch (PDOException $e) {
    echo $e->getMessage();
}