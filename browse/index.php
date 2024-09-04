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
?><html><head><title>Browse Collection</title></head><body><table>
<?php

	$sql = "select distinct collection, dir from snapshots order by collection, dir";
	$result = $conn->query($sql);
	while($row = $result->fetch_assoc()) {
		echo "<tr><td>".$row["collection"]."</td><td><a href=\"folder/".$row['collection']."/".urlencode($row["dir"])."\">".$row["dir"]."</a></td></tr>";
	}

	$conn->close();

?></body></html>
