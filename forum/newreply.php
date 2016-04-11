<?
include "default.php";
if($_SESSION['authorized'] == false) {
  header("Location: member.php?action=login");
}
if(isset($_GET['tid'])) {
  include "db_inc.php";
  $TID = $_GET['tid'];
  $query = "SELECT t.TID, t.topic, t.closed, f.FID, f.name, cat.FID AS CATID, cat.name AS catname 
	FROM threads t 
	LEFT JOIN forum f ON f.FID=t.FID AND f.type='forum' 
	LEFT JOIN forum cat ON cat.PAID=f.PAID AND cat.type='parent' 
	WHERE t.TID='$TID'";
  $result = mysqli_query($db, $query);
	
  $row = mysqli_fetch_array($result);
	if($row['closed'] == true && $userrank < 6) {
		header("Location: showthread.php?tid=$TID");
	}
	
	if(isset($_GET['replyto']) && !isset($_POST['preview'])) {
		$quotePID = $_GET['replyto'];
		$quotequery = "SELECT * FROM posts WHERE PID=$quotePID";
		$quoteresult = mysqli_query($db, $quotequery);
		
		if(mysqli_num_rows($quoteresult) > 0) {
			$quote = mysqli_fetch_array($quoteresult);
			$quoteauthorID = $quote['authorID'];
			$namequery = "SELECT username FROM users WHERE ID=$quoteauthorID";
			$authorresult = mysqli_query($db, $namequery);
			$quoteauthor = mysqli_fetch_array($authorresult);
			$quotename = $quoteauthor['username'];
			$quotetimestamp = $quote['timestamp'];
			$quotemessage = $quote['content'];
			$_POST['message'] = "[quote='$quotename' pid='$quotePID' dateline='$quotetimestamp']".$quotemessage."[/quote]";
		}
		
	}
	
  if(isset($_POST['submit'])) {
		if(!empty($_POST['message'])) {
			if(isset($_POST['disablesmilies'])) {
				$enablesmilies = 'false';
			}
			else {
				$enablesmilies = 'true';
			}
			if(isset($_POST['closethread']) && $userrank >= 6) {
				$query = "UPDATE threads SET closed='true' WHERE TID=$TID";
				mysqli_query($db, $query);
			}
			$message = $_POST['message'];
			$query = "INSERT INTO posts (`PID`, `TID`, `content`, `timestamp`, `authorID`, `ip`, `smiliesenabled`) VALUES (NULL, $TID, '{$message}', ".time().", $userID, '".$_SERVER['REMOTE_ADDR']."', '$enablesmilies')";
			mysqli_query($db, $query);
			if(isset($_POST['closethread']) && $userrank >= 6) {
				$query = "UPDATE threads SET closed='true' WHERE TID=$TID";
				mysqli_query($db, $query);
			}
			header("Location: showthread.php?tid=$TID&action=lastpost");
		}
		else {
			errorMessage("Please correct the following errors before continuing:", "The message is missing. Please enter a message.");
		}
  }
}
else {
  header("Location: index.php");
}
if(mysqli_num_rows($result) > 0) {
?>
<head>
  <title>New Reply - <? echo $row['topic'] ?></title>
</head>
<div class="container content">
	<? if(isset($message)) { echo $message; } ?>
  <div class="breadcrumb">
    <a class="section" href="index.php">
      <? echo $brand ?>
    </a>
    <div class="divider">
      /
    </div>
		<a class="section" href="forumdisplay.php?fid=<? echo $row['CATID'] ?>">
      <? echo $row['catname'] ?>
    </a>
    <div class="divider">
      /
    </div>
		<a class="section" href="forumdisplay.php?fid=<? echo $row['FID']?>">
      <? echo $row['name'] ?>
    </a>
    <div class="divider">
      /
    </div>
    <a class="section" href="showthread.php?tid=<? echo $TID ?>">
      <? echo $row['topic'] ?>
    </a>
    <div class="divider">
      /
    </div>
    <div class="active section">
      New Reply
    </div>
  </div>
	
	<?
	if(!empty($_POST['message'])) {
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
									if(isset($_POST['message'])) {
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
	
  <form method="post" class="reply">
    <table class="segment table">
      <tr><td class="thead"><strong>New Reply</strong></td></tr>
      <tr>
        <td width="20%" class="trow" valign="top">
					<strong>Your Message:</strong>
        </td>
        <td>
					<textarea class="textarea" id="message" name="message" style="height: 20em; resize: none;"><? if(!empty($_POST['message'])) { echo $_POST['message']; } ?></textarea>
				</td>
      </tr>
      <tr>
        <td width="20%" class="trow" valign="top">
          <strong>Post Options:</strong>
        </td>
				<td class="trow">
					<div class="field">
							<div class="checkbox">
								<input name="disablesmiles" type="checkbox">
								<label><strong>Disable Smilies:</strong> disable smilies from showing in this post.</label>
							</div>
						</div>
					<?
					if($userrank >= 6) {
					?>
						<div class="field">
							<div class="checkbox">
								<input name="closethread" type="checkbox">
								<label><strong>Close Thread:</strong> close the thread.</label>
							</div>
						</div>
					<?
					}
					?>
				</td>
      </tr>
    </table>
		<div class="center aligned">
			<button name="submit" class="blue fade animated button">
        <div class="visible">Post Reply</div>
        <div class="hidden"><i class="icon fa fa-reply"></i></div>
      </button>
      <button action="newreply.php?tid=<? echo $TID ?>" name="preview" class="orange fade animated button">
        <div class="visible">Preview Post</div>
        <div class="hidden"><i class="icon fa fa-eye"></i></div>
      </button>
			<button type="submit" class="animated fade red button" name="savedraft" value="Save as Draft">
				<div class="visible content">Save as Draft</div>
				<div class="hidden content"><i class="icon fa fa-floppy-o"></i></div>
			</button>
    </div>
  </form>
<?
}	
else {
?>
<head>
	<title><? echo $brand ?></title>	
</head>
<div class="container content">
	<? if(isset($message)) { echo $message; } ?>
	<div class="breadcrumb">
		<div class="active section">
			<? echo $brand ?>
		</div>
	</div>
	<table class="table segment menu">
		<tr><td class="thead"><strong><? echo $brand ?></strong></td></tr>
		<tr><td>The thread you specified is either invalid or doesn't exist.</td></tr>
	</table>
<?
}	

if($_COOKIE['style'] == "v1") { include "footerv1.php"; }
?>
</div>
<?
if($_COOKIE['style'] == "v2" || !isset($_COOKIE['style'])) { include "footerv2.php"; }
?>