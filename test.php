<?php
//print_r($_SERVER);

$foo = 7;
echo "<h1> Welcome to $_SERVER[HTTP_HOST]! </h1>";
?>
<h2>nimic interesant</h2>

<ul id="menu">
<li> <a href="test.php">Home</a> </li>
<li> <a href="test.php?page=about">About Us</a> </li>
<li> <a href="test.php?page=contact">Contact Us</a> </li>
</ul>

<?php

if(isset($_GET['page'])) {
$page = htmlentities($_GET['page']);
} else {
$page = NULL;
}
switch($page) {
case 'about':
echo "
<h1> About Us </h1>
<p> We are rockin' web developers! </p>
	We are rock";
break;
case 'contact':
echo "
<h1> Contact Us </h1>
	<p> Email us at
	<a href=\"mailto:info@example.com\">
info@example.com
</a>
</p>";
break;
/*
* Create a default page in case no variable is passed
*/
default:
echo "
<h1> Select a Page! </h1>
<p>
Choose a page from above
to learn more about us!
</p>";
break;
}
?>

<?php

/* Checks if the form was submitted
*/
if($_SERVER['REQUEST_METHOD'] == 'POST') {
  if(htmlentities($_POST['username'])=="" or htmlentities($_POST['email'])=="")
  {echo "Empty fields <br />";
 	goto nou;
  }
  else
echo "Thanks for registering! <br />",
"Username: ", htmlentities($_POST['username']), "<br />",
"Email: ", htmlentities($_POST['email']), "<br />";
} else {
// If the form was not submitted, displays the form
nou:
?>
<form action="test.php" method="post">
<label for="username">Username:</label>
<input type="text" name="username" />
<label for="email">Email:</label>
<input type="text" name="email" />
<input type="submit" value="Register!" />
</form>
<?php } // End else statement ?>

<?php
// Checks if the form was submitted
if($_SERVER['REQUEST_METHOD'] == 'POST') {
// Checks if a file was uploaded without errors
if(isset($_FILES['photo'])
&& is_uploaded_file($_FILES['photo']['tmp_name'])
&& $_FILES['photo']['error']==UPLOAD_ERR_OK) {
// Checks if the file is a JPG image
if($_FILES['photo']['type']=='image/jpeg') {
$tmp_img = $_FILES['photo']['tmp_name'];
// Creates an image resource
$image = imagecreatefromjpeg($tmp_img);
// Tells the browser what type of file
header('Content-Type: image/jpeg');
// Outputs the file to the browser
imagejpeg($image, '', 90);
// Frees the memory used for the file
imagedestroy($image);
} else {
echo "Uploaded file was not a JPG image.";
}
} else {
echo "No photo uploaded!";
}
} else {
// If the form was not submitted, displays the form HTML
?>
<form action="test.php" method="post"
enctype="multipart/form-data">
<label for="photo">User Photo:</label>
<input type="file" name="photo" />
<input type="submit" value="Upload a Photo" />
</form>
<?php } // End else statement ?>


<?php
// Open a MySQL connection
$link = new mysqli('localhost', 'root', '', 'teste');
if(!$link) {
die('Connection failed: ' . $link->error());
}
// Create and execute a MySQL query
$sql = "SELECT artist_name FROM artists";
$result = $link->query($sql);
// Loop through the returned data and output it
while($row = $result->fetch_assoc()) {
printf("Artist: %s<br />", $row['artist_name']);
}
// Free the memory associated with the query
$result->close();
// Close the connection
$link->close();
?>
<form method="post">
<label for="artist">Select an Artist:</label>
<select name="artist">
<option value="1">Smiley</option>
<option value="2">Puya</option>
</select>
<input type="submit" />
</form>
<?php
if($_SERVER['REQUEST_METHOD']=='POST')
{
// Open a MySQL connection
$link = new mysqli('localhost', 'root', '', 'teste');
if(!$link) {
	die('Connection failed: ' . $mysqli->error());
}
// Create and execute a MySQL query
$sql = "SELECT album_name FROM albums WHERE artist_id=?";
if($stmt = $link->prepare($sql))
{
	$stmt->bind_param('i', $_POST['artist']);
	$stmt->execute();
	$stmt->bind_result($album);
	while($stmt->fetch()) {
		printf("Album: %s<br />", $album);
	}
	$stmt->close();
}
// Close the connection
$link->close();
}
else {
	?>
<form method="post">
<label for="artist">Select an Artist:</label>
<select name="artist">
<option value="1">Smiley</option>
<option value="2">Puya</option>
</select>
<input type="submit" />
</form>
<?php } // End else ?>