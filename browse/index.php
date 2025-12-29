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
?><html><head><title>Virtual Bible Snapshot Project - Static Folder Listing</title>
<link rel="stylesheet" href="/browse/static.css" />
</head><body>
<h1><a href="https://snapshots.vrbm.org">Virtual Bible Snapshot Project</a></h1>
<h2>Static Folder Listing</h2>
<table class="folders"><tbody>
<?php
	$last = "";

	$sql = "select distinct collection, dir from snapshots order by collection desc, dir";
	$result = $conn->query($sql);
	while($row = $result->fetch_assoc()) {
		if($row["collection"] != $last) {
			$last = $row["collection"];
		        echo "<tr><th>".$row["collection"]."</th></tr>";
		}
		echo "<tr><td><a href=\"folder/".$row['collection']."/".urlencode($row["dir"])."\">".$row["dir"]."</a></td></tr>";
	}

	$conn->close();

?></tbody></table></body></html>
