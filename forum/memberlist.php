<?
include "default.php";

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
	$order = "ASC";
	$addition = "&order=descending";
	$orderby="desc";
}

function orderBy($ordertoggle) {
	if(isset($_GET['sort'])) {
		if($ordertoggle == "username" && $_GET['sort'] == "username") {
			echo "<span class='smalltext'>[<a href='memberlist.php?sort=username".$GLOBALS['addition']."'>".$GLOBALS['orderby']."</a>]";
		}
		elseif($ordertoggle == "regdate" && $_GET['sort'] == "regdate") {
			echo "<span class='smalltext'>[<a href='memberlist.php?sort=regdate".$GLOBALS['addition']."'>".$GLOBALS['orderby']."</a>]";
		}
		elseif($ordertoggle == "lastvisit" && $_GET['sort'] == "lastvisit") {
			echo "<span class='smalltext'>[<a href='memberlist.php?sort=lastvisit".$GLOBALS['addition']."'>".$GLOBALS['orderby']."</a>]";
		}
		elseif($ordertoggle == "postnum" && $_GET['sort'] == "postnum") {
			echo "<span class='smalltext'>[<a href='memberlist.php?sort=postnum".$GLOBALS['addition']."'>".$GLOBALS['orderby']."</a>]";
		}
		elseif($ordertoggle == "threadnum" && $_GET['sort'] == "threadnum") {
			echo "<span class='smalltext'>[<a href='memberlist.php?sort=threadnum".$GLOBALS['addition']."'>".$GLOBALS['orderby']."</a>]";
		}	
	}
	
}
		
//sort stuff
if(isset($_GET['sort'])) {
	if($_GET['sort'] == "username") {
		$sort = "username";
	}
	elseif($_GET['sort'] == "regdate") {
		$sort = "joined";
	}
	elseif($_GET['sort'] == "lastvisit") {
		$sort = "lastonline";
	}
	elseif($_GET['sort'] == "postnum") {
		$sort = "postcount";
	}
	elseif($_GET['sort'] == "threadnum") {
		$sort = "threadcount";
	}
}
else {
	$sort = "joined";
}

?>
<head>
	<title>Memberlist</title>
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
			Memberlist
		</div>
	</div>
	<?
	if(isset($message)) {
		echo $message;
	}
	?>
	<table border="0" cellspacing="0" cellpadding="5" class="table segment">
		<tr>
			<td class="thead" colspan="7">
				<div>
					<strong>Member List</strong>
				</div>
				<div class="right">
			
				</div>
			</td>
		</tr>

		<tr>
			<td class="tcat" width="1%">
				<span class="smalltext"><strong>Avatar</strong></span>
			</td>
			<td class="tcat">
				<span class="smalltext"><a href="memberlist.php?sort=username"><strong>Username</strong></a><? orderBy("username") ?></span>
			</td>

			<td class="tcat center aligned" width="15%">
				<span class="smalltext centered"><a href="memberlist.php?sort=regdate"><strong>Joined</strong></a><? orderBy("regdate") ?></span>
			</td>

			<td class="tcat center aligned" width="15%">
				<span class="smalltext"><a href="memberlist.php?sort=lastvisit"><strong>Last Visit</strong></a><? orderBy("lastvisit") ?></span>
			</td>

			<td class="tcat center aligned" width="15%">
				<span class="smalltext"><a href="memberlist.php?sort=postnum"><strong>Post Count</strong></a><? orderBy("postnum") ?></span>
			</td>

			<td class="tcat center aligned" width="15%">
				<span class="smalltext"><a href="memberlist.php?sort=threadnum"><strong>Thread Count</strong></a><? orderBy("threadnum") ?></span>
			</td>
		</tr>
	
		<?		
		include "db_inc.php";
		//Group by zorgt ervoor dat alle resultaten weergegeven worden, zonder zou er maar een resultaat zijn
		$query = "SELECT u.ID, u.username, u.email, u.userrank, u.customavatarurl, u.customusertitle, u.joined, u.lastonline, COUNT(DISTINCT t.TID) AS threadcount, COUNT(DISTINCT p.PID) AS postcount
		FROM users u
		LEFT JOIN threads t ON t.authorID=u.ID
		LEFT JOIN posts p ON p.authorID=u.ID
		GROUP BY u.ID
		ORDER BY {$sort} {$order}";
		$result = mysqli_query($db, $query);
		while($row = mysqli_fetch_array($result)) {
			$ID = $row['ID'];
			$threadcount = $row['threadcount'];
			$postcount = $row['postcount'];
			$profileusername = $row['username'];
			$joined = $row['joined'];
			$lastonline = $row['lastonline'];
			$customprofileavatar = $row['customavatarurl'];
			$profileemail = $row['email'];
			$profileemailhash = md5( strtolower( trim( $profileemail ) ) );
			$profilegravatar = "http://www.gravatar.com/avatar/" . $profileemailhash . "?s=400&d=mm&r=g";
			$customusertitle = $row['customusertitle'];
			$regdate = $row['joined'];
			if($customprofileavatar == "") {
				$profileavatar = $profilegravatar;
			}
			else {
				$profileavatar = $customprofileavatar;
			}
			
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
		
			
		?>
		<tr>
			<td class="trow center aligned"><img class="tiny rounded image" src="<? echo $profileavatar ?>"></td>
			<td class="trow">
				<a href="member.php?action=profile&uid=<? echo $ID ?>"><? echo $profiledisplayedname ?></a>
				<br>
				<span class="smalltext"><? echo $profileusertitle ?></span>
			</td>
			<td class="trow center aligned"><? echo displayTime($joined) ?></td>
			<td class="trow center aligned"><? echo displayTime($lastonline) ?></td>
			<td class="trow center aligned"><? echo $postcount ?></td>
			<td class="trow center aligned"><? echo $threadcount ?></td>
		</tr>
		<?
		}
		?>
	
	</table>
<?
	if($_COOKIE['style'] == "v1") { include "footerv1.php"; }
	?>
</div>
<?
if($_COOKIE['style'] == "v2" || !isset($_COOKIE['style'])) { include "footerv2.php"; }
?>