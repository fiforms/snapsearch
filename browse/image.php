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

	// Prepare the SQL statement
	$stmt = $conn->prepare("SELECT collection, filename, dir, bnum, md5, `desc`, width, height, license, attribution, medurl, largeurl, meddirlink, bigdirlink, sourceurl, arttype, `date`, ftype FROM snapshots WHERE md5 = ?");
	$stmt->bind_param('s',$md5param);


// Set the parameters
$md5param = $_GET['md5'];

// Execute the statement
$stmt->execute();

// Bind the result variables
$stmt->bind_result($collection, $filename, $dir, $bnum, $md5, $desc, $width, $height, $license, $attribution, $medurl, $largeurl, $meddirlink, $bigdirlink, $sourceurl, $arttype, $date, $ftype); // Add all columns you need to fetch

// Fetch the results
$stmt->fetch();
    // Print the results
    $letter = substr($md5,0,1);
    $dirurl = urlencode($dir);

    ?><html><head><title><?php echo $filename; ?></title>
<link rel="stylesheet" href="/browse/static.css" />
</head><body>

<h1><a href="https://snapshots.vrbm.org">Virtual Bible Snapshot Project</a></h1>

<div class="imagepage">
<?php

    $interactive = "/#q=".urlencode($filename);
    echo "<a href=\"/browse/folder/$collection/$dirurl\">Return to folder listing for $dir</a><br /><a href=\"/browse/\">Return to All Folder Listing</a><br />";
    echo "<a href=\"$interactive\"><img src=\"/$collection/$letter/$md5.768.webp\" /></a>";
    echo "<table class=\"imageinfo\">";
    echo "<tr><th>Filename</th><td>$filename</td></tr>";
    echo "<tr><th>Desc</th><td>$desc</td></tr>";
    echo "<tr><th>Type </th><td>$arttype </td></tr>";
    echo "<tr><th>Resolution </th><td>$width x $height </td></tr>";
    echo "<tr><th>License </th><td>$license </td></tr>";
    echo "<tr><th>Attribution </th><td>$attribution </td></tr>";
    echo "<tr><th>Collection </th><td>$collection </td></tr>";
    echo "<tr><th>Folder </th><td><a href=\"/browse/folder/$collection/$dirurl\">$dir</a> </td></tr>";
    echo "<tr><td colspan=\"2\">
	    <a href=\"$medurl\">Download Medium</a><br />
	    <a href=\"$largeurl\">Download Large</a><br />
	    <a href=\"$sourceurl\">Image Source</a><br />
	    <a href=\"$meddirlink\">Browse Folder (Medium Resolution)</a><br />
	    <a href=\"$bigdirlink\">Browse Folder (High Resolution</a><br />
	    <a href=\"$interactive\">Show in Interactive Search</a>
	    </td></tr>";

$stmt->close();


	$conn->close();

?>
<tr><td class="siteinfo" colspan="2">
The Virtual Bible Snapshot Project provides free artwork to illustrate the Bible and Bible topics, including video, photographs, illustrations, and AI Generated artwork. Creative-Commons licensed and public domain sermon graphics for pastors, teachers and more! Learn more at <a href="https://snapshots.vrbm.org/">snapshots.vrbm.org</a>.
</td></tr>
</table>
</div></body></html>
