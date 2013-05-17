
<?php 
include_once '/inc/functions.inc.php';
include_once '/inc/db.inc.php';
$db = new PDO(DB_INFO, DB_USER, DB_PASS);
$id = (isset($_GET['id'])) ? (int) $_GET['id'] : NULL;

if(isset($_GET['page']))
{
	$page = htmlentities(strip_tags($_GET['page']));
}
else
{
	$page = 'blog';
}
$url = (isset($_GET['url'])) ? $_GET['url'] : NULL;

$e = retrieveEntries($db, $page, $url);

$fulldisp = array_pop($e);

$e=sanitizeData($e);

?>
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type"
content="text/html;charset=utf-8" />
<link rel="stylesheet" href="/fb_blog/css/default.css" type="text/css" />
<title> Simple Blog </title>
</head>
<body>
<h1> Simple Blog Application </h1>
<ul id="menu">
<li><a href="/fb_blog/blog/">Blog</a></li>
<li><a href="/fb_blog/about/">About the Author</a></li>
</ul>
<div id="entries">
<?php

if($fulldisp==1)
{
	$url= (isset($url)) ? $url : $e['url'];
	?>
	<h2><?php echo $e['title']?></h2>
	<p> <?php  echo $e['entry']?></p>
	<?php if($page=='blog'): ?>
	<p class="backlink">
	<a href="./">Back to Latest Entries</a>
	</p>
	<?php endif?>
	<?php 
}
else 
{
foreach($e as $entry){
?>
<p>
	<a href="/fb_blog/<?php echo $entry['page'] ?>/<?php echo $entry['url']?>">
		<?php echo $entry['title']?>
	</a>
</p>
<?php 

}
}
?>

<p class="backlink">
<a href="/fb_blog/admin/<?php echo $page ?>">Post a New Entry</a>

</p>
</a>
</p>
</p>
</div>
</body>
</html>