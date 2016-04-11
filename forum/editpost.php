<?
include "default.php";

if(isset($_GET['pid'])) {
  $PID = $_GET['pid'];
  $query = "SELECT p.*, t.topic, t.TID, f.FID, f.name, cat.FID AS CATID, cat.name AS catname
  FROM posts p 
  LEFT JOIN threads t ON t.TID=p.TID
  LEFT JOIN forum f ON f.FID=t.FID AND f.type='forum'
  LEFT JOIN forum cat ON cat.PAID=f.PAID AND cat.type='parent'
  WHERE PID=$PID";
  $result = mysqli_query($db, $query);
  if(mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_array($result);
		if($userID != $row['authorID'] && $userrank < 6) {
			header("Location: index.php");
		}
    $TID= $row['TID'];
    if(isset($_POST['submit'])) {
      $message = str_replace("'", "''", $_POST['message']);
      $editreason = str_replace("'", "''", $_POST['editreason']);
      $query = "UPDATE posts SET content='$message', editreason='$editreason', lastedited='".time()."', editauthorID='$userID' WHERE PID=$PID";
      mysqli_query($db, $query);
      header("Location: showthread.php?tid=$TID&pid=$PID#pid$PID");
    }
    
  ?>
<head>
  <title>Edit This Post</title>    
</head>
<div class="container content">
  <div class="breadcrumb">
    <a class="section" href="index.php"><? echo $brand ?></a>
    <div class="divider">
      /
    </div>
    <a class="section" href="forumdisplay.php?fid=<? echo $row['CATID'] ?>"><? echo $row['catname'] ?></a>
    <div class="divider">
      /
    </div>
    <a class="section" href="forumdisplay.php?fid=<? echo $row['FID'] ?>"><? echo $row['name'] ?></a>
    <div class="divider">
      /
    </div>
    <a class="section" href="showthread.php?tid=<? echo $row['TID'] ?>"><? echo $row['topic'] ?></a>
    <div class="divider">
      /
    </div>
    <div class="active section">
      Edit Post
    </div>
  </div>
  
  <?
	if(isset($_POST['previewpost'])) {
		if($userrank == 10) {
					$userdisplayedname = "<span style='color: #00c086;'><strong>".$username."</strong></span>";	
				}	
				elseif ($userrank == 8) {
					$userdisplayedname = "<span style='color: #ff3131;'><strong>".$username."</strong></span>";
				}
				elseif ($userrank == 6) {
					$userdisplayedname = "<span style='color: #9750dd;'><strong>".$username."</strong></span>";
				}

				elseif ($userrank == 4) {
					$userdisplayedname = "<span style='color: #64B5F6;'>".$username."</span>";
				}
				else {
					$userdisplayedname = $username;
				}
	?>

	<table border="0" cellspacing="0" cellpadding="5" class="table">
		<tr>
			<td class="thead"><strong>Preview</strong></td>
		</tr>
		<tr>
			<td style="padding:0;">
				<div class="posts">
          <div class="post">
            <div class="author">
              <div class="avatar">
                <a href="member.php?action=profile&uid=<? echo $authorID ?>">
                  <img src="<? echo $useravatar ?>" width="100" height="100">
                </a>
              </div>
              <div class="information">
                <strong><span class="largetext"><a href="member.php?action=profile&uid=<? echo $userID ?>"><? echo $userdisplayedname ?></a></span></strong>
								<br>
                <span class="smalltext"><? echo $usertitle ?></span>
              </div>
              <div class="statistics">
                Posts: <? echo $userpostcount ?>
                <br>
                Threads: <? echo $userthreadcount ?>
                <br>
                Joined: <? echo date("M Y", $userjoined) ?>
              </div>
            </div>
            <div class="content">
              <div class="head" title="<? echo $_POST['subject'] ?>">
                <span class="date"><? echo displayTime(time()) ?></span>
              </div>
              <div class="body">
                <? 
										$parser = new \SBBCodeParser\Node_Container_Document();
										if(isset($_POST['disablesmiles'])) {
											echo $parser->parse($_POST['message'])
											->detect_links()
											->get_html(); 
										}
										else {
											echo $parser->parse($_POST['message'])
											->detect_links()
											->detect_emoticons()
											->get_html(); 
										}
										
								?>
              </div>
              <div class="controls">
               
              </div>
            </div>
          </div>
				</div>
			</td>
		</tr>
	</table>
	<?	
	}
	?>
  
  
  <form method="post">
    <table class="segment table">
      <tr><td class="thead">Edit This Post</td></tr>
      <tr>
        <td class="trow" width="20%" valign="top"><strong>Your Message:</strong></td>
        <td class="trow"><textarea class="textarea" name="message" id="message"><? echo $row['content'] ?></textarea></td>
      </tr>
      <tr>
        <td class="trow" width="20%"><strong>Edit Reason:</strong></td>
        <td class="trow"><input type="text" name="editreason" size="40" maxlength="85" tabindex="1" class="textbox" style="padding: .68em 1em; width: 100%; box-sizing: border-box;"></td>
      </tr>
    </table>
    <br>
		<div class="center aligned">
			<button type="submit" class="animated fade teal button" name="submit" value="Post Thread" tabindex="4" accesskey="s">
					<div class="visible content">Update Post</div>
					<div class="hidden content"><i class="icon fa fa-plus"></i></div>
			</button>
			<button type="submit" class="animated fade orange button" name="previewpost" value="Preview Post" tabindex="5">
					<div class="visible content">Preview Post</div>
					<div class="hidden content"><i class="icon fa fa-eye"></i></div>
			</button>
		</div>
    
  </form>
</div>
  <?
		
  }
  else {
  ?>
<head>
  <title><? echo $brand ?></title>
</head>
<div class="container content">
	<div class="breadcrumb">
		<div class="active section">
			<? echo $brand ?>
		</div>
	</div>
	<table class="table segment menu">
		<tr><td class="thead"><strong><? echo $brand ?></strong></td></tr>
		<tr><td>The post you specified is invalid or does not exist.</td></tr>
	</table>
  <?
  }
}
else {
  header("Location: index.php");
}

if($_COOKIE['style'] == "v1") { include "footerv1.php"; }
?>
</div>
<?
if($_COOKIE['style'] == "v2" || !isset($_COOKIE['style'])) { include "footerv2.php"; }
?>