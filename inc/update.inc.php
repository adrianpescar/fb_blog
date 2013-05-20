<?php
include_once 'functions.inc.php';
include_once 'images.inc.php';

if($_SERVER['REQUEST_METHOD']=='POST'
&& $_POST['submit']=='Save Entry'
&& !empty($_POST['page'])
&& !empty($_POST['title'])
&& !empty($_POST['entry']))
{

	$url = makeUrl($_POST['title']);
	if(isset($_FILES['image']['tmp_name']))
	{
		try 
		{
		$img = new ImageHandler("/fb_blog/images/");
		$img_path = $img->processUploadedImage($_FILES['image']);	
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	else 
	{
		//no img uploaded
		$img_path = NULL;	
	}

	include_once 'db.inc.php';
	$db = new PDO(DB_INFO, DB_USER, DB_PASS);
	if(!empty($_POST['id']))
	{
		$sql = "update entries
				set 
				title=?,
				image=?,
				entry=?,
				url=?
				where id=?
				limit 1";
		$stmt = $db->prepare($sql);
		$stmt->execute(
				array(
						$_POST['title'],
						$img_path,
						$_POST['entry'],
						$url,
						$_POST['id']
				)
		);
		$stmt->closeCursor();
		}
	else 
	{
	$sql = "INSERT INTO entries (page, title,image, entry, url)
		VALUES (?, ?, ?, ?, ?)";
	$stmt = $db->prepare($sql);
	$stmt->execute(
					array(
							$_POST['page'], 
							$_POST['title'], 
							$img_path,
							$_POST['entry'], 
							$url
							)
					);
	$stmt->closeCursor();
	}
	$page = htmlentities(strip_tags($_POST['page']));
	$id_obj = $db->query("SELECT LAST_INSERT_ID()");
	$id = $id_obj->fetch();
	$id_obj->closeCursor();
	// Send the user to the new entry
	header('Location: /fb_blog/'.$page.'/'.$url);
	exit;

}

else
{
header('Location: ../');
}
?>
