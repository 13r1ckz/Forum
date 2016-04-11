<?
include "default.php";
if(isset($_GET['fid'])) {
  $FID = $_GET['fid'];
  $query = "SELECT f.FID, f.name, f.postminrank, f.type, cat.name AS catname, cat.FID AS CATID
	FROM forum f 
	LEFT JOIN forum cat ON cat.PAID=f.PAID AND cat.type='parent'
	WHERE f.FID=$FID";
  $result = mysqli_query($db, $query);
  if(mysqli_num_rows($result) < 1) {
    header("Location: index.php");
  }
  else {
    $row = mysqli_fetch_array($result);
    $forumname = $row['name'];
    if($row['postminrank'] > $userrank || $row['type'] == "parent") {
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
		<tr><td>You may not post in this forum because either the forum is closed, it is a redirect to another webpage, or it is a category.</td></tr>
	</table>
<?
    }
    else {
			
			if(isset($_POST['submit'])) {
				if(isset($_POST['closethread'])) {
					$closed = 'true';
				}
				else {
					$closed = 'false';
				}
				if(!empty($_POST['subject'])) {
					if(!empty($_POST['message'])) {
						$message = str_replace("'", "''", $_POST['message']);
						$subject = str_replace("'", "''", $_POST['subject']);
						$query = "INSERT INTO threads (`TID`, `FID`, `topic`, `authorID`, `closed`) 
						VALUES (NULL, $FID, '{$subject}', $userID, '$closed')";
						mysqli_query($db, $query);						
						$TID = mysqli_insert_id($db);
						$query2 = "INSERT INTO posts (`TID`, `PID`, `authorID`, `content`, `timestamp`, `ip`) 
						VALUES ($TID, NULL, $userID, '{$message}', ".time().", '".$_SERVER['REMOTE_ADDR']."')";
						mysqli_query($db, $query2);
						header("Location: showthread.php?tid=$TID&action=lastpost");
					}
					else {
						errorMessage("Please correct the following errors before continuing:", "The message is missing. Please enter a message.");
					}
				}
				else {
					errorMessage("Please correct the following errors before continuing:", "The subject is missing. Please enter a subject.");
				}
			}
			if(isset($_POST['savedraft'])) {
				//(nog) niks hier
			}
?>
<head>
	<title><? echo $brand ?> - New Thread</title>
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
    <a class="section" href="forumdisplay.php?fid=<? echo $FID ?>">
      <? echo $forumname ?>
    </a>
    <div class="divider">
      /
    </div>
    <div class="active section">
      New Thread
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
			<tr>
				<td class="thead"><strong>New Thread</strong></td>
			</tr>
			<tr>
				<td width="20%" class="trow"><strong>Thread Subject:</strong></td>
				<td class="trow"><input type="text" name="subject" size="40" maxlength="85" tabindex="1" class="textbox" style="padding: .68em 1em; width: 100%; box-sizing: border-box;" value="<? if(!empty($_POST['subject'])) { echo $_POST['subject']; } ?>"></td>
			</tr>
			<tr>
				<td width="20%" class="trow" valign="top"><strong>Your Message:</strong></td>
				<td class="trow"><textarea class="textarea" name="message" id="message" style="height: 20em; resize: none;"><? if(!empty($_POST['message'])) {echo $_POST['message']; } ?></textarea></td>
			</tr>
		</table>
		<br>
		<div class="center aligned">
			<button type="submit" class="animated fade teal button" name="submit" value="Post Thread" tabindex="4" accesskey="s">
					<div class="visible content">Post Thread</div>
					<div class="hidden content"><i class="icon fa fa-plus"></i></div>
			</button>
			<button type="submit" class="animated fade orange button" name="previewpost" value="Preview Post" tabindex="5">
					<div class="visible content">Preview Post</div>
					<div class="hidden content"><i class="icon fa fa-eye"></i></div>
			</button>
			<button type="submit" class="animated fade red button" name="savedraft" value="Save as Draft">
				<div class="visible content">Save as Draft</div>
				<div class="hidden content"><i class="icon fa fa-floppy-o"></i></div>
			</button>
		</div>
	</form>	
<? 
    }
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