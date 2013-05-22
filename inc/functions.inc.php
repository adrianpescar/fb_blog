<?php

function retrieveEntries($db,$page,$url=NULL)
{
	if(isset($url))
	{
		$sql= "select id,title,image,entry,created
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
		$sql= "select id,page,title,image,entry,url,created
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
<?php 
function adminLinks($page,$url)
{
	$editURL = "/fb_blog/admin/$page/$url";
	$deleteURL = "/fb_blog/admin/delete/$url";
	
	$admin['edit'] = "<a href=\"$editURL\">edit</a>";
	$admin['delete'] = "<a href=\"$deleteURL\">delete</a>";
	return $admin;
}
function confirmDelete($db, $url)
{
	$e = retrieveEntries($db, '', $url);
	return <<<FORM
<form action="/fb_blog/admin.php" method="post">
	<fieldset>
		<legend>Are You Sure?</legend>
			<p>Are you sure you want to delete the entry "$e[title]"?</p>
		<input type="submit" name="submit" value="Yes" />
		<input type="submit" name="submit" value="No" />
		<input type="hidden" name="action" value="delete" />
		<input type="hidden" name="url" value="$url" />
	</fieldset>
</form>
FORM;
}
function deleteEntry($db, $url)
{
	$sql = "DELETE FROM entries
			WHERE url=?
			LIMIT 1";
	$stmt = $db->prepare($sql);
	return $stmt->execute(array($url));
}
function formatImage($img=NULL,$alt=NULL)
{
	if(isset($img))
	{
		return'<img src="' .$img.'"$alt=."'.$alt.'" />';
	}
	else 
	{
		return NULL;
	}
}
function createUserForm()
{
	return <<<FORM
<form action="/fb_blog/inc/update.inc.php" method="post">
	<fieldset>
		<legend>Create a New Administrator</legend>
		<label>Username
			<input type="text" name="username" maxlength="75" />
		</label>
		<label>Password
			<input type="password" name="password" />
		</label>
		<input type="submit" name="submit" value="Create" />
		<input type="submit" name="submit" value="Cancel" />
		<input type="hidden" name="action" value="createuser" />
	</fieldset>
</form>
FORM;
}
	

?>