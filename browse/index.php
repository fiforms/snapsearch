<?php 
        require_once('config.php');
        require_once('functions.php');

	// Create connection
	$conn = new mysqli($CONFIG['host'], $CONFIG['user'], $CONFIG['password'], $CONFIG['db']);

	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	checkReload($conn);

	$conn->close();

?>
