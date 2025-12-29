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
?><html><head><title>Browse Folder</title>
<link rel="stylesheet" href="/browse/static.css" />
</head><body>

<h1><a href="https://snapshots.vrbm.org">Virtual Bible Snapshot Project</a></h1>
<h2>Folder: <?php echo htmlspecialchars($_GET['dir']); ?></h2>

<a href="/browse/">Return to All Folder Listing</a><br />
<br />
<table class="folderlisting"><tbody>
<?php

	// Prepare the SQL statement
	$stmt = $conn->prepare("SELECT collection, filename, md5, arttype, ftype FROM snapshots WHERE dir = ? AND collection = ? ORDER BY filename");
	$stmt->bind_param('ss',$dir,$col);


// Set the parameters
$dir = $_GET['dir'];
$col = $_GET['col'];

// Execute the statement
$stmt->execute();

// Bind the result variables
$stmt->bind_result($collection, $filename, $md5, $arttype, $ftype); // Add all columns you need to fetch

// Fetch the results
while ($stmt->fetch()) {
    // Print the results
    $letter = substr($md5,0,1);
    $link = "/browse/image/$md5/".htmlspecialchars(urlencode($filename));
    echo "<tr><td><a href=\"$link\"><img src=\"/$collection/$letter/$md5.webp\" /></a></td><td><a href=\"$link\">$filename</a></td></tr>\r\n";
}

$stmt->close();


	$conn->close();

?></tbody></table></body></html>
