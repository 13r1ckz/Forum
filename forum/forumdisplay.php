<?
include "default.php";
include "db_inc.php";
$FID = $_GET['fid'];
//order stuff
if(isset($_GET['order'])){
	if($_GET['order'] == "ascending") {
		$order = "ASC";
		$addition = "&order=descending";
		$orderby="desc";
	}
	elseif($_GET['order'] == "descending") {
		$order= "DESC";
		$addition = "&order=ascending";
		$orderby="asc";
	}
}
else {
	$order = "DESC";
	$addition = "&order=ascending";
	$orderby="asc";
}

function orderBy($ordertoggle) {
	if(isset($_GET['sort'])) {
		if($ordertoggle == "subject" && $_GET['sort'] == "subject") {
			echo "<span class='smalltext'>[<a href='forumdisplay.php?fid=".$GLOBALS['FID']."&sort=subject".$GLOBALS['addition']."'>".$GLOBALS['orderby']."</a>]";
		}
		elseif($ordertoggle == "starter" && $_GET['sort'] == "starter") {
			echo "<span class='smalltext'>[<a href='forumdisplay.php?fid=".$GLOBALS['FID']."&sort=starter".$GLOBALS['addition']."'>".$GLOBALS['orderby']."</a>]";
		}
		elseif($ordertoggle == "replies" && $_GET['sort'] == "replies") {
			echo "<span class='smalltext'>[<a href='forumdisplay.php?fid=".$GLOBALS['FID']."&sort=replies".$GLOBALS['addition']."'>".$GLOBALS['orderby']."</a>]";
		}
		elseif($ordertoggle == "views" && $_GET['sort'] == "views") {
			echo "<span class='smalltext'>[<a href='forumdisplay.php?fid=".$GLOBALS['FID']."&sort=views".$GLOBALS['addition']."'>".$GLOBALS['orderby']."</a>]";
		}
		elseif($ordertoggle == "lastpost" && $_GET['sort'] == "lastpost") {
			echo "<span class='smalltext'>[<a href='forumdisplay.php?fid=".$GLOBALS['FID']."&sort=lastpost".$GLOBALS['addition']."'>".$GLOBALS['orderby']."</a>]";
		}	
	}
	
}
		
//sort stuff
if(isset($_GET['sort'])) {
	if($_GET['sort'] == "subject") {
		$sort = "topic";
	}
	elseif($_GET['sort'] == "starter") {
		$sort = "threadauthor";
	}
	elseif($_GET['sort'] == "replies") {
		$sort = "postcount";
	}
	elseif($_GET['sort'] == "views") {
		$sort = "views";
	}
	elseif($_GET['sort'] == "lastpost") {
		$sort = "timestamp";
	}
}
else {
	$sort = "timestamp";
}

$query = "SELECT f.type, f.name, f.displayminrank, f.PAID
FROM forum f
WHERE f.FID='$FID'";
$result = mysqli_query($db, $query);
if(mysqli_num_rows($result) > 0) {
  $row = mysqli_fetch_array($result);
  if($row['displayminrank'] > $userrank) {
	header("Location: index.php"); 
  }
	if($row['type'] == "forum") {
		$query = "SELECT f.name, f.type, f.displayminrank, f.postminrank, pa.name AS title, t.authorID, pa.FID AS parentFID
		FROM forum f
		LEFT JOIN forum pa ON pa.PAID=f.PAID AND pa.type='parent'
		LEFT JOIN threads t ON t.FID=f.FID
		WHERE f.FID='$FID'";
		$result = mysqli_query($db, $query);
		$row = mysqli_fetch_array($result);
  	$forumname = $row['name'];
  	$parent = $row['title'];
		$postminrank = $row['postminrank'];
		
		//pages
		$limit = 20;
		$pagecountquery = "SELECT COUNT(DISTINCT t.TID) AS threadcount FROM threads t WHERE t.FID=$FID";
		$pagecountresult = mysqli_query($db, $pagecountquery);
		$pagecountrow = mysqli_fetch_array($pagecountresult);
		$threadcount = $pagecountrow['threadcount'];
		$pagecount = ceil($threadcount / $limit);
		
		if(!isset($_GET['page'])) {
			$page = 1;
		}
		else {
			$page = $_GET['page'];
		}
		$limitstart = $page * $limit;
		$limitstart = $limitstart - $limit;
		
		
?>
<head>
  <title><? echo $forumname ?></title>
</head>
<div class="container content">
  <div class="breadcrumb">
    <a class="section" href="index.php"><? echo $brand ?></a>
    <div class="divider">
      /
    </div>
    <a class="section" href="forumdisplay.php?fid=<? echo $row['parentFID'] ?>"><? echo $parent ?></a>
    <div class="divider">
      /
    </div>
    <div class="active section">
      <? echo $forumname ?>
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
				echo "<a class='item' href='forumdisplay.php?fid=$FID'><i class='icon fa fa-chevron-left'></i></a>";
			}
			//Waarom dit? Omdat $i <= $pagecount niet werkt, vraag me niet waarom, geen idee.
			$pagecountplusone = $pagecount +1;
			$i = 1;
			while($i < $pagecountplusone) {
				if($i == $page) {
					echo "<a class='item active' href='forumdisplay.php?fid=$FID&page=$i'>$i</a>";
				}
				else {
					echo "<a class='item' href='forumdisplay.php?fid=$FID&page=$i'>$i</a>";
				}
				$i++;
			}
		
			if($page < $pagecount) {
				echo "<a class='item' href='forumdisplay.php?fid=$FID&page=$pagecount'><i class='icon fa fa-chevron-right'></i></a>";
			}
			?>
		</div>
	</div>
	<?
	}
	?>
		
	<div class="right">
		<?
		if($_SESSION['authorized'] == true && $userrank >= $postminrank) {
		?>
		<a href="newthread.php?fid=<? echo $FID ?>" class="button <? if(isset($buttonclosed)) { echo $buttonclosed; } ?> teal animated vertical">
			<div class="visible">
				New Thread
			</div>
			<div class="hidden">
				<i class="icon fa fa-plus"></i>
			</div>
		</a>
		<?
		}
		?>
	</div>
	<div class="clear"></div>
	
  <table border="0" cellspacing="0" cellpadding="5" class="table segment">
    <tr>
      <td class="thead" colspan="6">
        <div>
          <strong><? echo $forumname ?></strong>
        </div>
      </td>
    </tr>
    <tr>
      <td class="tcat" colspan="2" width="66%">
				<strong>
					<span class="smalltext">
			 		 	<a href="forumdisplay.php?fid=<? echo $FID ?>&sort=subject">Thread</a><? orderBy("subject") ?>
			  			/
			  		<a href="forumdisplay.php?fid=<? echo $FID ?>&sort=starter">Author</a><? orderBy("starter") ?>
					</span>
				</strong>
      </td>
			<td class="tcat centered" width="7%">
			<span class="smalltext">
				<a href="forumdisplay.php?fid=<? echo $FID ?>&sort=replies"><strong>Replies</strong></a><? orderBy("replies") ?>
			</span>
			</td>
			<td class="tcat centered" width="7%">
				<span class="smalltext">
					<a href="forumdisplay.php?fid=<? echo $FID ?>&sort=views"><strong>Views</strong></a><? orderBy("views") ?>
				</span>
			</td>
			<td class="tcat centered" width="22%">
				<span class="smalltext">
					<a href="forumdisplay.php?fid=<? echo $FID ?>&sort=lastpost"><strong>Last Post</strong></a><? orderBy("lastpost") ?>
				</span>
			</td>
    </tr>
	<?
				
	$threadsquery = "SELECT t.TID, t.topic, t.authorID AS threadauthorID, t.closed, t.views, u.ID, u.username AS threadauthor, p.timestamp, p.authorID AS postauthorID, pu.username AS postauthor, COUNT(DISTINCT pc.PID) - 1 AS postcount
	FROM threads t
	LEFT JOIN users u ON u.ID=t.authorID
	LEFT JOIN posts p ON p.TID=t.TID
	INNER JOIN (
    SELECT TID, MAX(timestamp) AS timestamp
    FROM posts
    GROUP BY TID
	) lp ON lp.TID = t.TID AND lp.timestamp = p.timestamp
	LEFT JOIN users pu ON pu.ID=p.authorID
	LEFT JOIN posts pc ON pc.TID=t.TID
	WHERE t.FID='$FID' 
	GROUP BY t.TID
	ORDER BY {$sort} {$order} 
	LIMIT {$limitstart}, {$limit}";
	$threadsresult = mysqli_query($db, $threadsquery);
	if(mysqli_num_rows($threadsresult) > 0) {
		while($row = mysqli_fetch_array($threadsresult)) {
			$TID = $row['TID'];
			$topic = $row['topic'];
			$threadauthorID = $row['threadauthorID'];
			$postauthorID = $row['postauthorID'];
			$threadauthorname = $row['threadauthor'];
			$postauthorname = $row['postauthor'];
			$timestamp = $row['timestamp'];
			$postcount = $row['postcount'];
	?>
		<tr>
			<td class="trow centered" width="2%"></td>
			<td class="trow">
				<div>
					<span><a href="showthread.php?tid=<? echo $TID ?>"><? echo $topic ?></a></span>
					<div class="smalltext"><a href="member.php?action=profile&uid=<? echo $threadauthorID ?>"><? echo $threadauthorname ?></a></div>
				</div>
			</td>
			<td class="trow centered">
				<? echo $postcount ?>
			</td>
			<td class="trow centered">
				<? echo $row['views']; ?>
			</td>
			<td class="trow" style="text-align: right;">
				<span class="smalltext">
					<? echo displayTime($timestamp) ?>
					<br>
					Last Post: <a href="member.php?action=profile&uid=<? echo $postauthorID ?>"><? echo $postauthorname ?></a>
				</span>
			</td>
		</tr>
	<?
		}
	}
	else {
	?>
	<tr>
		<td colspan="6" class="trow">There are currently no threads in this forum.</td>
	</tr>	
	<?
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
				echo "<a class='item' href='forumdisplay.php?fid=$FID'><i class='icon fa fa-chevron-left'></i></a>";
			}
			//Waarom dit? Omdat $i <= $pagecount niet werkt, vraag me niet waarom, geen idee.
			$pagecountplusone = $pagecount +1;
			$i = 1;
			while($i < $pagecountplusone) {
				if($i == $page) {
					echo "<a class='item active' href='forumdisplay.php?fid=$FID&page=$i'>$i</a>";
				}
				else {
					echo "<a class='item' href='forumdisplay.php?fid=$FID&page=$i'>$i</a>";
				}
				$i++;
			}
		
			if($page < $pagecount) {
				echo "<a class='item' href='forumdisplay.php?fid=$FID&page=$pagecount'><i class='icon fa fa-chevron-right'></i></a>";
			}
			?>
		</div>
	</div>
	<?
	}
	?>
	
	<div class="right">
		<?
		if($_SESSION['authorized'] == true && $userrank >= $postminrank) {
		?>
		<a href="newthread.php?fid=<? echo $FID ?>" class="button <? if(isset($buttonclosed)) { echo $buttonclosed; } ?> teal animated vertical">
			<div class="visible">
				New Thread
			</div>
			<div class="hidden">
				<i class="icon fa fa-plus"></i>
			</div>
		</a>
		<?
		}
		?>
	</div>
<?
	}
	elseif($row['type'] == "parent") {
		$forumname = $row['name'];
		$PAID = $row['PAID'];
	?>
<head>
  <title><? echo $forumname ?></title>
</head>
<div class="container content">
	<div class="breadcrumb">
		<a class="section" href="index.php">
			<? echo $brand ?>
		</a>
		<div class="divider">
			/
		</div>
		<div class="active section">
			<? echo $forumname ?>
		</div>
	</div>	
	
	<table border="0" cellspacing="0" cellpadding="5" class="segment table">
		<tr>
			<td class="thead centered" colspan="5"><strong>Forums in '<? echo $forumname ?>'</strong></td>
		</tr>
		<tr>
			<td class="tcat" colspan="2"><span class="smalltext"><strong>Forum</strong></span></td>
			<td class="tcat centered" width="85"><strong>Threads</strong></td>
			<td class="tcat centered" width="85"><strong>Posts</strong></td>
			<td class="tcat centered" width="200"><strong>Last Post</strong></td>
		</tr>
		<?
		$query = "SELECT f.FID, f.name, f.description
		FROM forum f 
		WHERE f.type='forum' AND f.PAID=$PAID AND f.displayminrank<=$userrank";
		$result = mysqli_query($db, $query);
		while($row = mysqli_fetch_array($result)) {
			$forumID = $row['FID'];
			//Thread & Post Count
			$tcountquery = "SELECT COUNT(DISTINCT t.TID) AS threadcount, COUNT(DISTINCT p.PID) AS postcount 
			FROM threads t 
			LEFT JOIN posts p ON p.TID=t.TID
			WHERE t.FID=$forumID";
			$tcountresult = mysqli_query($db, $tcountquery);
			$countrow = mysqli_fetch_array($tcountresult);
			$threadcount = $countrow['threadcount'];
			$postcount = $countrow['postcount'];
			
			//Last Post
			$lpquery = "SELECT lp.PID, lp.authorID, lp.timestamp, u.username, lpt.topic, lpt.TID
			FROM threads t
			LEFT JOIN posts p ON p.TID=t.TID
			INNER JOIN (
				SELECT PID, authorID, TID, MAX(timestamp) AS timestamp
    		FROM posts
    		GROUP BY TID
			) lp ON lp.TID = t.TID AND lp.timestamp = p.timestamp 
			LEFT JOIN users u ON u.ID=p.authorID
			LEFT JOIN threads lpt ON lpt.TID=lp.TID
			WHERE t.FID=$forumID
			ORDER BY p.timestamp DESC";
			$lpresult = mysqli_query($db, $lpquery);
			$lp = mysqli_fetch_array($lpresult);
			$TID = $lp['TID'];
			$topic = $lp['topic'];
			$timestamp = $lp['timestamp'];
			$author = $lp['username'];
			$UID = $lp['authorID'];
			
		?>
		<tr>
			<td class="trow centered" width="1"><img src="" title="" class="" id="" style="cursor: pointer;"></td>
			<td class="trow"><strong><a href="forumdisplay.php?fid=<? echo $forumID ?>"><? echo $row['name'] ?></a></strong><div class="smalltext"><? echo $row['description'] ?> </div></td>
			<td class="trow center aligned"><? echo $threadcount ?></td>		
			<td class="trow center aligned"><? echo $postcount ?></td>		
			<td class="trow" style="text-align: right;">
				<span class="smalltext">
					<? 
					if($threadcount > 0) {
					?>
					<a href="showthread.php?tid=<? echo $TID ?>&action=lastpost" title="Title">
						<strong>
						<? 
								//niet meer dan 25 chars van topic weergeven
								if(strlen($topic) > 25) {
									echo substr_replace($topic, "...", 25);
								}
								else {
									echo $topic;
								}
						?>
						</strong>
					</a>
					<br>
					<? echo displayTime($timestamp) ?>
					<br>
					By <a href="member.php?action=profile&uid=<? echo $UID ?>"><? echo $author ?></a>
					<?
					}
					else {
					?>
					<div style="text-align: center;">Never</div>
					<?
					}
					?>
				</span>
			</td>					
		</tr>
		<?
		}
			if(isset($userrank)) {
				if($userrank == 10) {
				?>
		<!--<tr>
			<td class="trow" colspan="7">
				<strong>
					<a href="admincp.php?action=newforum&paid=<? echo $PAID ?>"><i class="icon fa fa-plus" style="text-decoration: none; color: #555;"></i>Add Forum</a>
				</strong>
			</td>
		</tr>-->
				<?
				}
			}
		?>
	</table>

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
		<tr><td>The forum you specified is either invalid or doesn't exist.</td></tr>
	</table> 
<?
}
if($_COOKIE['style'] == "v1") { include "footerv1.php"; }
?>
</div>
<?
if($_COOKIE['style'] == "v2" || !isset($_COOKIE['style'])) { include "footerv2.php"; }
?>