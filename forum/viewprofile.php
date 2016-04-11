<?
include "db_inc.php";
if (isset ($_GET['uid'])) {
	$ID = $_GET['uid'];		
}
else {
	if ($_SESSION['authorized'] == true) {
	$ID = $userID;
	}
	else {
		header("Location: memberlist.php");
	}
}
$query = "SELECT * FROM users WHERE ID='".$ID."'";	
$result = mysqli_query($db, $query);
if(mysqli_num_rows($result) > 0) {
	$row = mysqli_fetch_array($result);

	$profileusername = $row['username'];
	$profileemail = $row['email'];
	$ID = $row['ID'];

	$lastonline= $row['lastonline'];
	$joined = $row['joined'];

	$customavatarurl = $row['customavatarurl'];
	$email_hash = md5( strtolower( trim( $profileemail ) ) );
	$profileusergravatar = "http://www.gravatar.com/avatar/" . $email_hash . "?s=400&d=mm";
	if($customavatarurl == "") {
		$profileuseravatar = $profileusergravatar;
	}
	else {
		$profileuseravatar = $customavatarurl;
	}

	$rank = $row['userrank'];
	if ($rank == 10) {
		$profileusergroup = "Admin";
		$profilenamecolor = "style='color: #00c086;'";
	}		
	elseif ($rank == 8) {
		$profileusergroup = "Developer";
		$profilenamecolor = "style='color: #ff3131;'";
	}
	elseif ($rank == 6) {
		$profileusergroup = "Moderator";
		$profilenamecolor = "style='color: #9750dd;'";
	}
	elseif ($rank == 4) {
		$profileusergroup = "VIP";
		$profilenamecolor = "style='color:#64B5F6;'";
	}
	else {
		$profileusergroup = "Member";
	}

	$customusertitle = $row['customusertitle'];
	if ($customusertitle == "") {
		$profileusertitle = $profileusergroup;
	}
	else {
		$profileusertitle = $customusertitle;
	}

	

	//Get post count
	$subquery1 = "SELECT PID FROM posts WHERE authorID='$ID'";
	$postresult = mysqli_query($db, $subquery1);
	$postcount = mysqli_num_rows($postresult);
	//Get thread count
	$subquery2 = "SELECT TID FROM threads WHERE authorID='$ID'";
	$threadresult = mysqli_query($db, $subquery2);
	$threadcount = mysqli_num_rows($threadresult);

?>
<head>
	<title>Profile of <? echo $profileusername ?></title>
</head>

<body>
	<div class="container content">
		<div class="breadcrumb">
			<a class="section" href="index.php">
				<? echo $brand; ?>
			</a>
			<div class="divider">
				/
			</div>
			<div class="active section">
				Profile of  <? echo $profileusername ?>
			</div>
		</div>
		
		<div class="relaxed grid">
			<div class="five wide column">
					<div class="card green">
						<div class="image">
							<img src="<? echo $profileuseravatar ?>">
						</div>
						<div class="content">
							<a class="header"><span <? if(isset($profilenamecolor)) { echo $profilenamecolor; } ?> ><strong><? echo $profileusername ?></strong></span></a>
							<div class="meta">
								<? echo $profileusertitle ?>
							</div>
							<div class="description">
							</div>
						</div>
					</div>
					<?
					if(isset($userrank)) {
						if($userrank >= 6) {
					?>
					<table border="0" cellspacing="0" cellpadding="5" width="100%" class="segment table">
						<tr><td colspan="2" class="thead"><strong>Administator Options</strong></td></tr>
						<tr>
							<td class="trow">
								<ul>
									<li><a href="admincp.php?action=editprofile&uid=<? echo $ID ?>">Edit this user in Admin CP</a></li>
								</ul>
							</td>
						</tr>
					</table>
					<?
						}
					}
					?>	
				</div>
				
				<div class="eleven wide column">
					<table border="0" cellspacing="0" cellpadding="5" class="segment table">
						<colgroup>
							<col style="width: 30%;"> 
						</colgroup>
						<tr>
							<td colspan="2" class="thead"><strong><? echo $profileusername ?>'s Info</strong></td>
						</tr>
						<tr>
							<td class="trow"><strong>Joined:</strong></td>
							<td class="trow"><? echo displayTime($joined) ?></td>
						</tr>
						<tr>
							<td class="trow"><strong>Last Visit:</strong></td>
							<td class="trow"><? echo displayTime($lastonline) ?></td>
						</tr>
						<tr>
							<td class="trow"><strong>Total Posts:</strong></td>
							<td class="trow"><? echo $postcount ?></td>
						</tr>
						<tr>
							<td class="trow"><strong>Total Threads:</strong></td>
							<td class="trow"><? echo $threadcount ?></td>
						</tr>
					</table>
					
					<table border="0" cellspacing="0" cellpadding="5" class="segment table menu">
						<colgroup>
							<col style="width: 30%;"> 
						</colgroup>
						<tr>
							<td colspan="2" class="thead"><strong>Additional Info About <? echo $profileusername ?></strong></td>
						</tr>
						<tr>
							<td class="trow"><strong>Location:</strong></td>
							<td class="trow"><? echo $row['location'] ?></td>
						</tr>
						<tr>
							<td class="trow"><strong>Sex:</strong></td>
							<td class="trow"><? echo $row['sex'] ?></td>
						</tr>
					</table>
				</div>
				
		</div>
	<?
}		
else {
?>
		<div class="container content">
			<div class="breadcrumb">
				<div class="active section">
					<? echo $brand ?>
				</div>
			</div>
			<table class="table segment menu">
				<tr><td class="thead"><strong><? echo $brand ?></strong></td></tr>
				<tr><td>The member you specified is either invalid or doesn't exist.</td></tr>
			</table>
		</div>
<?
}
?>
		<?
		if($_COOKIE['style'] == "v1") { include "footerv1.php"; }
		?>
	</div>
	<?
	if($_COOKIE['style'] == "v2") { include "footerv2.php"; }
	?>