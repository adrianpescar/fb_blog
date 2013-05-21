<?php
include_once '../inc/functions.inc.php';
include_once '../inc/db.inc.php';

$db = new PDO(DB_INFO, DB_USER, DB_PASS);
$e = retrieveEntries($db,'blog');
array_pop($e);
$e = sanitizeData($e);

// Add a content type header to ensure proper execution
header('Content-Type: application/rss+xml');
// Output the XML declaration
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<rss version="2.0">
<channel>
	<title>My Simple Blog</title>
	<link>http://localhost/fb_blog</link>
	<description>This blog is awesome.</description>
	<language>en-us</language>

<?php 

foreach($e as $e):
	$entry= htmlentities($e['entry']);
	$url = 'http://localhost/fb_blog/blog/'.$e['url'];
	$date = date(DATE_RSS, strtotime($e['created']));
?>
<item>
	<title><?php echo $e['title'];?></title>
	<description><?php echo $entry;?></description>
	<link><?php echo $url;?></link>
	<guid><?php echo $url; ?></guid>
	<pubDate><?php echo $date;?></pubDate>
</item>
<?php endforeach;?>
</channel>
</rss>

	