<?
include "default.php";

//pid heeft voorang op tid
if(isset($_GET['pid'])) {
	$PID = $_GET['pid'];
	$query = "SELECT t.TID, t.topic, t.closed, t.FID, pa.FID AS parentID, pa.name AS title, f.name , f.displayminrank, f.postminrank
	FROM posts p
	LEFT JOIN threads t ON t.TID=p.TID
	LEFT JOIN forum f ON f.FID=t.FID AND f.type='forum'
	LEFT JOIN forum pa ON pa.PAID=f.PAID AND pa.type='parent'
	WHERE p.PID='$PID'";
}

if(isset($_GET['tid']) && !isset($_GET['pid'])) {
	$TID = $_GET['tid'];
	$query = "SELECT t.TID, t.topic, t.closed, t.FID, pa.FID AS parentID, pa.name AS title, f.name , f.displayminrank, f.postminrank
	FROM threads t 
	LEFT JOIN forum f ON f.FID=t.FID AND f.type='forum'
	LEFT JOIN forum pa ON pa.PAID=f.PAID AND pa.type='parent'
	WHERE t.TID='$TID'";
}
//query voor post id of thread id, zelfde resultaat 
$result = mysqli_query($db, $query);
if(mysqli_num_rows($result) > 0) {
	$mainrow = mysqli_fetch_array($result);
	$TID = $mainrow['TID'];
	
	
	//submit of preview quick reply
	if(isset($_POST['submit']) && !empty($_POST['message'])) {
		if(isset($_POST['disablesmilies'])) {
			$enablesmilies = 'false';
		}
		else {
			$enablesmilies = 'true';
		}
		$message = str_replace("'", "''", $_POST['message']);
		$query = "INSERT INTO posts (`PID`, `TID`, `content`, `timestamp`, `authorID`, `ip`, `smiliesenabled`) VALUES (NULL, $TID, '$message', ".time().", $userID, '".$_SERVER['REMOTE_ADDR']."', '$enablesmilies')";
		mysqli_query($db, $query);
		if(isset($_POST['closethread']) && $userrank >= 6) {
			$query = "UPDATE threads SET closed='true' WHERE TID=$TID";
			mysqli_query($db, $query);
		}
		header("Location: showthread.php?tid=$TID&action=lastpost");
	}
	if(isset($_POST['preview'])) {
		header("Location: newreply.php?tid=".$TID."");
	}
	
	
	//update view count
	$updatequery = "UPDATE threads SET views=views + 1 WHERE TID='$TID'";
	mysqli_query($db, $updatequery);
	
	//pages
	$perpage = 10;
	$pagecountquery = "SELECT COUNT(DISTINCT p.PID) AS postcount FROM posts p WHERE p.TID=$TID";
	$pagecountresult = mysqli_query($db, $pagecountquery);
	$pagecountrow = mysqli_fetch_array($pagecountresult);
	$postcount = $pagecountrow['postcount'];
	$pagecount = ceil($postcount / $perpage);
	if(!isset($_GET['page'])) {
		//naar laatste post
		if(isset($_GET['action']) && $_GET['action'] == 'lastpost') {
			$query = "SELECT MAX(p.PID) AS maxPID FROM posts p WHERE p.TID=$TID";
			$result = mysqli_query($db, $query);
			$row = mysqli_fetch_array($result);
			$maxPID = $row['maxPID'];
			header("Location: showthread.php?tid=$TID&pid=$maxPID#pid$maxPID");
		}
		elseif(!isset($_GET['pid'])) {
			$page = 1;
		}
	}
	else {
		$page = $_GET['page'];
	}
	
	//naar juiste post
	if(isset($_GET['pid'])) {
		mysqli_query($db, "SET @rank=0");
		$query = "SELECT @rank:=@rank+1 AS rank, PID FROM posts WHERE TID=$TID";
		$result = mysqli_query($db, $query);
		if(mysqli_num_rows($result) > 0) {
			
			while($row = mysqli_fetch_array($result)) {
				if($row['PID'] == $_GET['pid']) {
					$page = ceil($row['rank'] / $perpage); 
				}
			}
		}
	}
	
	$limitstart = $page * $perpage;
	$limitstart = $limitstart - $perpage;
	
	//check voor permissies
	if($mainrow['displayminrank'] > $userrank) {
		
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
		<tr><td>You do not have permission to access this page.</td></tr>
	</table> 
	<?
	}
	else {
	$threadtopic = $mainrow['topic'];
	$FID = $mainrow['FID'];
	$parenttitle = $mainrow['title'];
	$forumname = $mainrow['name'];  
	$closed = $mainrow['closed'];
	$parentID = $mainrow['parentID'];
	if($closed == true && $userrank < 6) {
		$buttonclosed = "disabled ";
	}
	
?>
<head>
	<title><? echo $threadtopic ?></title>
</head>
<div class="container content">
	<? if(isset($message)) { echo $message; } ?>
	<div class="breadcrumb">
		<a class="section" href="index.php"><? echo $brand ?></a>
    <div class="divider">
      /
    </div>
    <a class="section" href="forumdisplay.php?fid=<? echo $parentID ?>"><? echo $parenttitle ?></a>
    <div class="divider">
      /
    </div>
    <a class="section" href="forumdisplay.php?fid=<? echo $FID ?>"><? echo $forumname ?></a>
    <div class="divider">
      /
    </div>
    <div class="active section">
      <? echo $threadtopic ?>
    </div>
  </div>
	
	<div class="clear"></div>
	<?
	if($pagecount > 1) {
	?>
	<div class="left">
		<div class="pagination menu">
			<?
			if($page != 1) {
				echo "<a class='item' href='showthread.php?tid=$TID'><i class='icon fa fa-chevron-left'></i></a>";
			}
			//Waarom dit? Omdat $i <= $pagecount niet werkt, vraag me niet waarom, geen idee.
			$pagecountplusone = $pagecount +1;
			$i = 1;
			while($i < $pagecountplusone) {
				if($i == $page) {
					echo "<a class='item active' href='showthread.php?tid=$TID&page=$i'>$i</a>";
				}
				else {
					echo "<a class='item' href='showthread.php?tid=$TID&page=$i'>$i</a>";
				}
				$i++;
			}
		
			if($page < $pagecount) {
				echo "<a class='item' href='showthread.php?tid=$TID&page=$pagecount'><i class='icon fa fa-chevron-right'></i></a>";
			}
			?>
		</div>
	</div>
	<?
	}
	?>
	<div class="right">
		<?
		if($_SESSION['authorized'] == true) {
		?>
		<a href="newreply.php?tid=<? echo $TID ?>" class="button <? if(isset($buttonclosed)) { echo $buttonclosed; } ?>teal animated">
			<div class="visible">
				<? 
				if($closed == true && $userrank < 6) { 
					echo "Thread Closed";	
				} 
				else { 
					echo "New Reply"; 
				} 
				?>
			</div>
			<div class="hidden">
				<i class="icon fa fa-reply"></i>
			</div>
		</a>
		<?
		}
		?>
	</div>
	<div class="clear"></div>

  <table border="0" cellspacing="0" cellpadding="5" class="segment table owncolor">
    <tr>
      <td class="thead"><strong><? echo $threadtopic ?></strong></td>
    </tr>
    <tr>
		<td style="padding:0;">
			<div class="posts">
      <?
      $postquery = "SELECT p.PID, p.TID, p.authorID, p.content, p.timestamp, p.lastedited, p.editauthorID, p.editreason, p.smiliesenabled, e.username AS editauthor, u.username, u.customusertitle, u.email, u.customavatarurl, u.joined, u.userrank, u.customusertitle
			FROM posts p 
			LEFT JOIN users u ON u.ID=p.authorID
			LEFT JOIN users e ON e.ID=p.editauthorID
			WHERE p.TID=$TID LIMIT {$limitstart}, {$perpage}";
      $postresult = mysqli_query($db, $postquery);
			$i = 1;
      while($row = mysqli_fetch_array($postresult)) {

				$PID = $row['PID'];
				$authorID = $row['authorID'];
				$editauthor = $row['editauthor'];
				$editreason = $row['editreason'];
				$editID = $row['editauthorID'];
				$smilies = $row['smiliesenabled'];
				if($smilies = "") {
					$smilies = 'true';
				}
				//post en threadcount
				$postcountquery = "SELECT COUNT(DISTINCT pc.PID) AS postcount FROM posts pc WHERE pc.authorID=$authorID";
				$postcountresult = mysqli_query($db, $postcountquery);
				$postcountrow = mysqli_fetch_array($postcountresult);
				$profilepostcount = $postcountrow['postcount'];
				
				$threadcountquery = "SELECT COUNT(DISTINCT tc.TID) AS threadcount FROM threads tc WHERE tc.authorID=$authorID";
				$threadcountresult = mysqli_query($db, $threadcountquery);
				$threadcountrow = mysqli_fetch_array($threadcountresult);
				$profilethreadcount = $threadcountrow['threadcount'];
				
				$timestamp = $row['timestamp'];
				$postcontent = $row['content'];
				$profileusername = $row['username'];
				$profileemail = $row['email'];
				$profileemailhash = md5( strtolower( trim( $profileemail ) ) );
				$profilegravatar = "http://www.gravatar.com/avatar/" . $profileemailhash . "?s=400&d=mm&r=g";
				$regdate = $row['joined'];
				$customprofileavatar = $row['customavatarurl'];
				if($customprofileavatar == "") {
					$profileavatar = $profilegravatar;
				}
				else {
					$profileavatar = $customprofileavatar;
				}
				
				$customusertitle = $row['customusertitle'];
				if($customusertitle == "") {
					if($row['userrank'] == 10) {
						$profileusertitle = "Admin";
					}		
					elseif($row['userrank'] == 8) {
						$profileusertitle = "Developer";
					}
					elseif($row['userrank'] == 6) {
						$profileusertitle = "Moderator";
					}

					elseif($row['userrank'] == 4) {
						$profileusertitle = "VIP";
					}
					else {
						$profileusertitle = "Member";
					}
				}
			else {
				$profileusertitle = $customusertitle;
			}

				if($row['userrank'] == 10) {
					$profiledisplayedname = "<span style='color: #00c086;'><strong>".$profileusername."</strong></span>";	
				}	
				elseif ($row['userrank'] == 8) {
					$profiledisplayedname = "<span style='color: #ff3131;'><strong>".$profileusername."</strong></span>";
				}
				elseif ($row['userrank'] == 6) {
					$profiledisplayedname = "<span style='color: #9750dd;'><strong>".$profileusername."</strong></span>";
				}

				elseif ($row['userrank'] == 4) {
					$profiledisplayedname = "<span style='color: #64B5F6;'>".$profileusername."</span>";
				}
				else {
					$profiledisplayedname = $profileusername;
				}

				
				$profilejoined = $row['joined'];
				$timestamp = $row['timestamp'];
        ?>
       
          <a name="pid<? echo $PID ?>" id="pid<? echo $PID ?>"></a>
          <div class="post" id="<? echo $PID ?>">
            <div class="author">
              <div class="avatar">
                <a href="member.php?action=profile&uid=<? echo $authorID ?>">
                  <img src="<? echo $profileavatar ?>" width="100" height="100">
                </a>
              </div>
              <div class="information">
                <strong><span class="largetext"><a href="member.php?action=profile&uid=<? echo $authorID ?>"><? echo $profiledisplayedname ?></a></span></strong>
								<br>
                <span class="smalltext"><? echo $profileusertitle ?></span>
              </div>
              <div class="statistics">
                Posts: <? echo $profilepostcount ?>
                <br>
                Threads: <? echo $profilethreadcount ?>
                <br>
                Joined: <? echo date("M Y", $profilejoined) ?>
              </div>
            </div>
            <div class="content">
              <div class="head" title="<? echo $threadtopic ?>">
                <div class="right">
                  <strong><a href="showthread.php?tid=<? echo $_GET['tid'] ?>&pid=<? echo $PID?>#pid<? echo $PID ?>">#<? echo $i; $i++ ?></a></strong>
                </div>
                <span class="date">
									<? echo displayTime($timestamp) ?>
									<? 	if($row['lastedited'] != "") { 
									?>
									<span class='edited'>(This post was last modified: <? echo displayTime($row['lastedited']) ?> by 
									<a href='member.php?action=profile&uid=<? echo $editID?>'><? echo $editauthor ?></a>.
									<? 	
										if($editreason != "") { ?>
									Edit Reason: <em><? echo $editreason ?></em>
									<? 
										}	
									?>
									)</span> 
									<?
											} 
									?>
								</span>
              </div>
              <div class="body" id="pid_<? echo $PID ?>">
                <? 
										$parser = new \SBBCodeParser\Node_Container_Document();
										if($smilies == 'true') {
											echo $parser->parse($postcontent)
											->detect_links()
											->detect_emoticons()
											->get_text(); 
										}
										else {
											echo $parser->parse($postcontent)
											->detect_links()
											->get_html(); 
										}										
								?>
              </div>
              <div class="controls">
               
                <div class="right" style="display: inline-block;">
								<?
								if($_SESSION['authorized'] == true) {
									if($authorID == $userID || $userrank >= 6) {
								?>
									<a href="editpost.php?pid=<? echo $PID ?>" title="Edit this post" class="button tiny labeled icon left"><i class="icon fa fa-pencil-square-o"></i> Edit</a>
                  <a title="Delete this post" class="button tiny labeled icon left red"><i class="icon fa fa-times"></i> Delete</a>
								<?
									}
								}
								?>  
									<!--More buttons here-->
                  <a href="newreply.php?tid=<? echo $_GET['tid'] ?>&replyto=<? echo $PID ?>" title="Quote this message in a reply" class="button tiny labeled icon left"><i class="icon fa fa-reply"></i> Reply</a>
								</div>
              </div>
            </div>
          </div>
        
        <?
        }
        ?>
				</div>
      </td>
    </tr>
		<?
		if($_SESSION['authorized'] == true) {
			if($closed == "false" || $userrank >= 6) {
		?>
		<tr>
			<td style="padding: 0em;">
				<form method="post" class="reply form">
					<table width="100%">
						<tr>
							<td class="thead" colspan="2"><strong>Reply</strong></td>
						</tr>
						<tr>
							<td class="trow" valign="top" width="20%">
								<div class="field">
									<label><strong>Message</strong></label>
									Type your reply to this message here.
								</div>

								<div class="field">
									<div class="checkbox">
										<input class="hidden" name="disablesmilies" type="checkbox">
										<label>Disable Smilies</label>
									</div>
								</div>
								<?
								if($userrank >= 6) {
								?>
								<div class="field">
									<div class="checkbox">
										<input type="checkbox" name="closethread" <? if($closed == 'true') { echo "checked"; } ?>>
										<label for="closethread">Close thread</label>
									</div>
								</div>
								<?
								}
								?>
							</td>
							<td>
								<textarea class="textarea" name="message"></textarea>
							</td>
						</tr>
						<tr>
							<td colspan="2">
									<div class="center aligned">
										<button name="submit" class="blue fade animated button">
											<div class="visible">
												Post Reply
											</div>
											<div class="hidden">
												<i class="icon fa fa-reply"></i>
											</div>
										</button>
										<button action="newreply.php?tid=<? echo $TID ?>" name="preview" class="orange fade animated button">
											<div class="visible">
												Preview Post
											</div>
											<div class="hidden">
												<i class="icon fa fa-eye"></i>
											</div>
										</button>
									</div>
							</td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
		<?
			}
		}
		?>
  </table>
	
	<?
	if($pagecount > 1) {
	?>
	<div class="left">
		<div class="pagination menu">
			<?
			if($page != 1) {
				echo "<a class='item' href='showthread.php?tid=$TID'><i class='icon fa fa-chevron-left'></i></a>";
			}
			//Waarom dit? Omdat $i <= $pagecount niet werkt, vraag me niet waarom, geen idee.
			$pagecountplusone = $pagecount +1;
			$i = 1;
			while($i < $pagecountplusone) {
				if($i == $page) {
					echo "<a class='item active' href='showthread.php?tid=$TID&page=$i'>$i</a>";
				}
				else {
					echo "<a class='item' href='showthread.php?tid=$TID&page=$i'>$i</a>";
				}
				$i++;
			}
		
			if($page < $pagecount) {
				echo "<a class='item' href='showthread.php?tid=$TID&page=$pagecount'><i class='icon fa fa-chevron-right'></i></a>";
			}
			?>
		</div>
	</div>
	<?
	}
	?>
	
	<div class="right">
		<?
		if($_SESSION['authorized'] == true) {
		?>
		<a href="newreply.php?tid=<? echo $TID ?>" class="button <? if(isset($buttonclosed)) { echo $buttonclosed; } ?>teal animated">
			<div class="visible">
				<? 
				if($closed == true && $userrank < 6) { 
					echo "Thread Closed";	
				} 
				else { 
					echo "New Reply"; 
				} 
				?>
			</div>
			<div class="hidden">
				<i class="icon fa fa-reply"></i>
			</div>
		</a>
		<?
		}
		?>
	</div>
	<br>
  
<?
	}
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
		<tr><td>The topic you specified is either invalid or doesn't exist.</td></tr>
	</table>
</div>
<?
}
if($_COOKIE['style'] == "v1") { include "footerv1.php"; }
?>
</div>
<?
if($_COOKIE['style'] == "v2" || !isset($_COOKIE['style'])) { include "footerv2.php"; }
?>