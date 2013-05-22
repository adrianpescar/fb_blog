<?php
include_once 'db.inc.php';

class Comments
{
	public $db;
	public $comments;
	
	public function __construct()
	{
		$this->db= new PDO(DB_INFO, DB_USER, DB_PASS);
	}
	
	//display form for new comm
	public function showCommentForm($blog_id)
	{
		return <<<FORM
<form action="/fb_blog/inc/update.inc.php"
	method="post" id="comment-form">
	<fieldset>
		<legend>Post a Comment</legend>
		<label>Name
			<input type="text" name="name" maxlength="75" />
		</label>
		<label>Email
			<input type="text" name="email" maxlength="150" />
		</label>
		<label>Comment
			<textarea rows="10" cols="45" name="comment"></textarea>
		</label>
		<input type="hidden" name="blog_id" value="$blog_id" />
		<input type="submit" name="submit" value="Post Comment" />
		<input type="submit" name="submit" value="Cancel" />
	</fieldset>
</form>
FORM;
			}
	
	//save comments in db
	public function saveComment($p)
	{
		//sanitize data and store in variables
		$blog_id=htmlentities(strip_tags($p['blog_id']),ENT_QUOTES);
		$name = htmlentities(strip_tags($p['name']),ENT_QUOTES);
		$email = htmlentities(strip_tags($p['email']),ENT_QUOTES);
		$comment = htmlentities(strip_tags($p['comment']),ENT_QUOTES);
		//keep formatting of comments and remove extra whitespace
		$comment = nl2br(trim($comment));
		
		$sql = "INSERT INTO comments (blog_id, name, email, comment)
				VALUES (?, ?, ?, ?)";
		if($stmt = $this->db->prepare($sql))
		{
			$stmt->execute(array($blog_id,$name,$email,$comment));
			$stmt->closeCursor();
			return TRUE;
		}
		else 
		{
			return FALSE;	
		}
	}
	public function retrieveComments($blog_id)
	{
		$sql = "SELECT id,name,email,comment,date
				FROM comments
				where blog_id=?
				ORDER BY date DESC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array($blog_id));
		while($comment = $stmt->fetch())
		{
			$this->comments[] = $comment;
		}
		if(empty($this->comments))
		{
			$this->comments[]= array(
				'id' => NULL,
				'name'=>NULL,
				'email'=>NULL,
				'comment'=>"There are no comments on this entry",
				'date'=> NULL	
			
			);
		}
	}
	public function showComments($blog_id)
	{
		$display=NULL;
		$this->retrieveComments($blog_id);
		
		foreach($this->comments as $c)
		{
			if(!empty($c['date'])&& !empty($c['name']))
			{
				$format = "F j, Y \a\\t g:iA";
				$date = date($format, strtotime($c['date']));
				$byline = "<span><strong>$c[name]</strong>
							[Posted on $date]</span>";
				//delete link
				if(isset($_SESSION['loggedin'])&& $_SESSION['loggedin']==1)
				{
				$admin = "<a href =\"/fb_blog/inc/update.inc.php"
						."?action=comment_delete&id=$c[id]\""
						."class=\"admin\">delete</a>";
				}
				else 
				{
					$admin=NULL;
				}
			}
			else {
				//if we get here,no comments exists
				$byline = NULL;
				$admin=NULL;
			}
			//assemble the pieces into formatted comment
			$display .="<p class = \"comment\">$byline$c[comment]$admin</p>";
		}
		//return all formated comments as a string
		return $display;
	}
	public function confirmDelete($id)
	{
		//store the entry url if available
		if(isset($_SERVER['HTTP_REFERER']))
		{
			$url = $_SERVER['HTTP_REFERER'];
		}
		//otherwise use the default view
		else {
			$url="../";
		}
		return <<<FORM
<html>
<head>
<title>Please Confirm Your Decision</title>
<link rel="stylesheet" type="text/css"
	href="/fb_blog/css/default.css" />
</head>
<body>
<form action="/fb_blog/inc/update.inc.php" method="post">
	<fieldset>
		<legend>Are You Sure?</legend>
		<p>
			Are you sure you want to delete this comment?
		</p>
		<input type="hidden" name="id" value="$id" />
		<input type="hidden" name="action" value="comment_delete" />
		<input type="hidden" name="url" value="$url" />
		<input type="submit" name="confirm" value="Yes" />
		<input type="submit" name="confirm" value="No" />
	</fieldset>
</form>
</body>
</html>
FORM;
	}
	public function deleteComment($id)
	{
		$sql="DELETE FROM comments
			  WHERE id=?
				LIMIT 1";
		if($stmt = $this->db->prepare($sql))
		{
			$stmt->execute(array($id));
			$stmt->closeCursor();
			return TRUE;
		}
		else 
		{
			return FALSE;
		}
	}
}
