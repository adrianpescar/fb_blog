<?php

function retrieveEntries($db,$page,$url=NULL)
{
	if(isset($url))
	{
		$sql= "select title,entry
				from entries
				where id=?
				limit 1";
		$stmt =$db->prepare($sql);
		$stmt->execute(array($_GET['id']));
		$e= $stmt->fetch();
		$fulldisp= 1;
	}
	else 
		{
		$sql= "select id,page,title,entry
			from entries
			where page=?
			order by created desc";	
		$stmt = $db->prepare($sql);
		$stmt->execute(array($page));
		$e=NULL;
		while($row=$stmt->fetch()){
			$e[]=$row;
		}
		$fulldisp=0;		
		if(!is_array($e))
			{
			$fulldisp = 1;
			$e = array(
				'title' => 'No Entries Yet',
				'entry' => '<a href="/fb_blog/admin.php?page=<?php echo $page ?>">Post a New Entry</a>'
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