<?php

function retrieveEntries($db,$page,$url=NULL)
{
	if(isset($url))
	{
		$sql= "select title,entry
				from entries
				where url=?
				limit 1";
		$stmt =$db->prepare($sql);
		$stmt->execute(array($url));
		$e= $stmt->fetch();
		$fulldisp= 1;
	}
	else 
		{
		$sql= "select id,page,title,entry,url
			from entries
			where page=?
			order by created desc";	
		$stmt = $db->prepare($sql);
		$stmt->execute(array($page));
		$e=NULL;
		while($row=$stmt->fetch()){
			if ($page=='blog')
			{
				$e[]=$row;
				$fulldisp=0;
			}
			else
			{
				$e=$row;
				$fulldisp=1;
			}
		}		
		if(!is_array($e))
			{
			$fulldisp = 1;
			$e = array(
				'title' => 'No Entries Yet',
				'entry' => '<a href="/fb_blog/admin/"<?php echo $page?>">Post a New Entry</a>'
				);
			}		
		}
	array_push($e,$fulldisp);
	return $e;
}

function sanitizeData($data)
{
	if(!is_array($data))
	{
		return strip_tags($data,"<a>");
	}
	else 
	{
		return array_map('sanitizedata',$data);
	}
}
?>
<?php 
function makeUrl($title)
{
	
$patterns = array(
'/\s+/',
'/(?!-)\W+/'
);
$replacements = array('-', '');
return preg_replace($patterns, $replacements, strtolower($title));
}
?>