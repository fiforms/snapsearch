<?php 

require_once('config.php');

function checkReload($conn) {
	global $CONFIG;

	$sql = "SELECT last_reload_time FROM reload_log ORDER BY id DESC LIMIT 1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
	    $row = $result->fetch_assoc();
	    $lastReloadTime = strtotime($row['last_reload_time']);
	} else {
	    $lastReloadTime = 0; // Default to 0 if no record found
	}

	$needsReload = false;

	foreach($CONFIG['load'] as $collection) {
	// Get the modification time of the JSON file
	$fileModTime = filemtime($collection['file']);

	// Check if the JSON file is newer than the last reload time
	if ($fileModTime > $lastReloadTime) {
		$needsReload = true;
	}
	}
	if($needsReload) {
	    reloadData($conn);
	}

} // function checkReload

function reloadData($conn) {
	global $CONFIG;

	// Empty the table

	$conn->query("TRUNCATE TABLE snapshots");

	// Prepare and bind
	$stmt = $conn->prepare("INSERT INTO snapshots (collection, filename, dir, bnum, md5, `desc`, width, height, license, attribution, medurl, largeurl, meddirlink, bigdirlink, sourceurl, arttype, `date`, ftype) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("ssssssiissssssssss", $collection, $filename, $dir, $bnum, $md5, $desc, $width, $height, $license, $attribution, $medurl, $largeurl, $meddirlink, $bigdirlink, $sourceurl, $arttype, $date, $ftype);


	// Load JSON file
	$collections = $CONFIG['load'];
	foreach($collections as $collectioninfo) {

	$jsonData = file_get_contents($collectioninfo['file']);
	$snapshots = json_decode($jsonData, true);

	foreach ($snapshots as $snapshot) {

	    $collection = $collectioninfo['collection'];
	    $filename = $snapshot['filename'];
	    $dir = $snapshot['dir'];
	    $bnum = $snapshot['bnum'];
	    $md5 = $snapshot['md5'];
	    $desc = $snapshot['desc'];
	    $width = $snapshot['width'];
	    $height = $snapshot['height'];
	    $license = $snapshot['license'];
	    $attribution = $snapshot['attribution'];
	    $medurl = $snapshot['medurl'];
	    $largeurl = $snapshot['largeurl'];
	    $meddirlink = $snapshot['meddirlink'];
	    $bigdirlink = $snapshot['bigdirlink'];
	    $sourceurl = $snapshot['sourceurl'];
	    $arttype = $snapshot['arttype'];

	    $dateTime = new DateTime($snapshot['date']);
	    $date = $dateTime->format('Y-m-d H:i:s');
	    $ftype = $snapshot['ftype'];
	    if($filename) {
		$stmt->execute();
	    }
	}

	} // foreach collections

	$stmt->close();
	$conn->query("INSERT INTO reload_log () VALUES ()");

} // funciton reloadData

