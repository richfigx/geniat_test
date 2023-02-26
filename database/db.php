<?php

$host = "localhost";
$db_name = "testDB";
$username = "phpUser";
$password = "adminGeniat%2023";

try {
	$con = new PDO("mysql:host={$host};dbname={$db_name}", $username, $password);
}

catch (PDOException $exception) {
	echo "Connection error: " . $exception->getMessage();
}
