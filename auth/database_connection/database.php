<?php
	$host = "localhost";
	$username = "root";
	$password = "";
	$database = "db_mywp";

	try {
		$pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch(PDOException $e) {
		echo "Connection failed: " . $e->getMessage();
	}
?>
