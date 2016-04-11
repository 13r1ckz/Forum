<?
include "default.php";

if($_SESSION['authorized'] == false || $userrank < 6) {
	header("Location: index.php");
}

if(isset($_GET['action'])) {
	if($_GET['action'] == "editprofile"){ 
		$activesection = "Profile Editor";
		
		if(isset($_POST['regsubmit'])) {
			include "db_inc.php";
			if(!empty($_POST['usertitle']) && !isset($_POST['reverttitle'])) {
				$titlequery = "customusertitle='".$_POST['usertitle']."',";
			}
			if(isset($_POST['reverttitle'])) {
				$titlequery = "customusertitle='',";
			}
			if(isset($_POST['removeavatar'])) {
				$avatarquery = "customavatarurl='',";
			}
			$updatequery = "UPDATE users SET ".$titlequery." ".$avatarquery." sex='".$_POST['sex']."', location='".$_POST['location']."', userrank='".$_POST['rank']."'  WHERE ID='".$_GET['uid']."'";
			mysqli_query($db, $updatequery);
			infoMessage("Succes!", "User information has been updated.");
						}
	}
	elseif($_GET['action'] == "finduser") {
		$activesection = "Users";
		include "db_inc.php";
        $query = "SELECT ID, username, userrank FROM users";
        $result = mysqli_query($db,$query);
	}
	elseif($_GET['action'] == "newforum") {
		$activesection = "Add Forum";
		include "db_inc.php";
 //       $query = "SELECT ID, username, userrank FROM users";
 //       $result = mysqli_query($db,$query);
	}
	elseif($_GET['action'] == "newcat") {
		$activesection = "Add Category";
		include "db_inc.php";
//        $query = "SELECT ID, username, userrank FROM users";
//        $result = mysqli_query($db,$query);
	}

}
	
?>
<head>
	<title>
		Admin Control Panel
	</title>
</head>
<div class="container content">
	<div class="breadcrumb">
		<a class="passive section" href="index.php">
			<? echo $brand ?>
		</a>
		<div class="divider">
			/
		</div>	
		<?
		if(isset($_GET['action'])) {
		?>
		<a class="passive section" href="admincp.php">
			Admin CP
		</a>
		<div class="divider">
			/
		</div>
		<div class="active section">
			<? echo $activesection; ?>
		</div>
		<?
		}
		else {
		?>
		<div class="active section">
			Admin CP
		</div>
		<?
		}
		?>
	</div>
		<? 
		if(isset($message)){
			echo $message;
		}
		?>
    <form method="post" name="input">
		<table width="100%" border="0" align="center">
			<tr>
				<td width="180" valign="top">
					<table border="0" cellspacing="0" cellpadding="5" class="segment table menu">
						<tbody>
							<tr><td class="thead"><strong>Menu</strong></td></tr>
							<tr><td class="trow smalltext"><i class="icon fa fa-home"></i><a href="admincp.php">Admin CP Home</a></td></tr>
						</tbody>
						<tbody>
							<tr><td class="tcat">
								<div class="expander right">
									<i class="icon fa fa-minus"></i>
								</div>
								<div>
									<strong>Users</strong>
								</div>
							</td></tr>
						</tbody>
						<tbody>
							<tr><td class="trow smalltext"><i class="icon fa fa-folder"></i><a href="admincp.php?action=finduser">Profile Editor</a></td></tr>
						</tbody>
						<tbody>
							<tr><td class="tcat">
								<div class="expander right">
									<i class="icon fa fa-minus"></i>
								</div>
								<div>
									<strong>Forum</strong>
								</div>
							</td></tr>
						</tbody>
						<tbody>
							<tr><td class="trow smalltext"><i class="icon fa fa-plus"></i><a href="admincp.php?action=newforum">Add Forum</a></td></tr>
							<tr><td class="trow smalltext"><i class="icon fa fa-plus"></i><a href="admincp.php?action=newcat">Add Category</a></td></tr>
						</tbody>
					</table>
				</td>
				<?
				if(!isset($_GET['action'])) {  
				?>
				<td valign="top" style="padding-left: 10px;">
					<table class="table segment menu">
						<tr><td class="thead"><strong>Links and Info</strong></td></tr>
						<tr>
							<td class="tcat"><strong>Name</strong></td>
							<td class="tcat"><strong>Login</strong></td>
							<td class="tcat"><strong>Password</strong></td>
						</tr>
						<tr>
							<td class="trow"><a href="http://no.pe/" target="_blank">hard code your link like for database</a></td>
							<td class="trow">usename</td>
							<td class="trow">password</td>
						</tr>
					
					</table>
				</td>
				<?
				}
				else {
					if($_GET['action'] == "finduser")  {
				?>
				<td valign="top" style="padding-left: 10px;">
					<table class="table segment menu">
						<tr><td class="thead"><strong>Users</strong></td></tr>
						<tr>
							<td class="tcat"><strong>Username</strong></td>
							<td class="tcat"><strong>Usergroup</strong></td>
						</tr>
						<?
						while($row = mysqli_fetch_array($result)) {
							$ID = $row['ID'];
							$profileusername = $row['username'];
							$rank = $row['userrank'];
							if ($rank == 10) {
								$profileusergroup = "Admin";
								$profilename = "<span style='color: #00c086;'><strong>".$profileusername."</strong></span>";
							}		
							elseif ($rank == 8) {
								$profileusergroup = "Developer";
								$profilename = "<span style='color: #ff3131;'><strong>".$profileusername."</strong></span>";
							}
							elseif ($rank == 6) {
								$profileusergroup = "Moderator";
								$profilename = "<span style='color: #9750dd;'><strong>".$profileusername."</strong></span>";
							}
							elseif ($rank == 4) {
								$profileusergroup = "VIP";
								$profilename = "<span style='color: #64B5F6;'>".$profileusername."</span>";
							}
							else {
								$profileusergroup = "Member";
								$profilename = $profileusername;
							}
						?>
						<tr>
							<td class="trow"><a href="admincp.php?action=editprofile&uid=<? echo $ID ?>"><? echo $profilename ?></a></td>
							<td class="trow"><? echo $profileusergroup ?></td>
						</tr>
						<?
						}
						?>
					</table>
				</td>
					<?
					}
					elseif($_GET['action'] == "editprofile") {
						if(!isset($_GET['uid'])) { 
						$get_uid = 0; 
						}
						else {
							$get_uid = $_GET['uid'];
						}		
						include "db_inc.php";
						$query = "SELECT * FROM users WHERE ID=".$get_uid."";
						$result = mysqli_query($db, $query);
						$row = mysqli_fetch_array($result);
						
						if(isset($result) && mysqli_num_rows($result) >0) {
							$profileusername = $row['username'];
							$customavatarurl = $row['customavatarurl'];
							$sex = $row['sex'];
							if($sex == "Male") {
								$malepreselect = "selected";
							}
							elseif($sex == "Female") {
								$femalepreselect = "selected";
							}
							elseif($sex == "Undisclosed") {
								$undispreselect = "selected";
							}
							
							$profileemail = $row['email'];
							$email_hash = md5( strtolower( trim( $profileemail ) ) );
							$profileusergravatar = "http://www.gravatar.com/avatar/" . $email_hash . "?s=400&d=mm&r=g";
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
								$adminpreselect = "selected";
							}		
							elseif ($rank == 8) {
								$profileusergroup = "Developer";
								$profilenamecolor = "style='color: #ff3131;'";
								$devpreselect = "selected";
							}
							elseif ($rank == 6) {
								$profileusergroup = "Moderator";
								$profilenamecolor = "style='color: #9750dd;'";
								$modpreselect = "selected";
							}
							elseif ($rank == 4) {
								$profileusergroup = "VIP";
								$profilenamecolor = "style='color:#64B5F6;'";
								$vippreselect = "selected";
							}
							else {
								$profileusergroup = "Member";
							}
							
							$sex = $row['sex'];
							$location = $row['location'];
							$customusertitle = $row['customusertitle'];
							if ($customusertitle !== "") {
								$profileusertitle = $customusertitle;
							}
							else {
								$profileusertitle = $profileusergroup;
							}
							
						?>
				<td valign="top" style="padding-left: 10px;">
					<table class="table segment menu">
						<tr><td class="thead"><strong>Edit Profile of <? echo $profileusername ?></strong></td></tr>
						<tr>
							<td width="50%" class="trow" valign="top">
								<fieldset class="trow">
									<legend><strong>Username</strong></legend>
									<table cellspacing="0" cellpadding="5">
										<tr><td><span class="smalltext">Username:</span></td></tr>
										<tr><td><strong><a href="member.php?action=profile&uid=<? echo $_GET['uid'] ?>"><span <? if(isset($profilenamecolor)) { echo $profilenamecolor; } ?>> <? echo $profileusername ?></span></a></strong></td></tr>
										<tr><td><span class="smalltext">Usergroup:</span></td></tr>
										<tr><td>
											<select name="rank">
												<option value="1">Member</option>
												<? if ($userrank >= 4) { ?>
												<option value="4" <? if(isset($vippreselect)) { echo $vippreselect; } ?>>VIP</option>
												<? }
												if ($userrank >= 6) { ?>
												<option value="6" <? if(isset($modpreselect)) { echo $modpreselect; } ?>>Moderator</option>
												<? } 
												if ($userrank >= 8) { ?>
												<option value="8" <? if(isset($devpreselect)) { echo $devpreselect; } ?>>Developer</option>
												<? }
												if ($userrank == 10) { ?>	
												<option value="10" <? if(isset($adminpreselect)) { echo $adminpreselect; } ?>>Admin</option>
												<?}?>
											</select>
										</td></tr>
									</table>
								</fieldset>
								<br>
								<fieldset class="trow">
									<legend><strong>Additional Information</strong></legend>
									<table cellspacing="0" cellpadding="5">
										<tr><td><span class="smalltext"><input type="checkbox" name="removeavatar">Remove user's avatar? (Gravatar excluded)</span></td></tr>
										<tr><td><span class="smalltext">Sex:</span></td></tr>
										<tr><td>
											<select name="sex">
												<option value=""></option>
												<option value="Male" <? if(isset($malepreselect)) { echo $malepreselect; } ?>>Male</option>
												<option value="Female" <? if(isset($femalepreselect)) { echo $femalepreselect; } ?>>Female</option>
												<option value="Undisclosed" <? if(isset($undispreselect)) { echo $undispreselect; } ?>>Undisclosed</option>
											</select>
										</td></tr>
										<tr><td><span class="smalltext">Location:</span></td></tr>
										<tr><td><span class="smalltext"><input type="text" name="location" class="textbox" value="<? echo $location ?>"></span></td></tr>
									</table>
								</fieldset>
							</td>
							<td width="50%" class="trow" valign="top">
								<fieldset class="trow">
									<legend><strong>Custom Usertitle</strong></legend>
									<table cellspacing="0" cellpadding="5">
									<tr><td><span class="smalltext">Here you can assign a custom user title which will overwrite the one based on users display group.</span></td></tr>
										<tr><td><span class="smalltext">Default Usertitle:</span></td></tr>
										<tr><td><span class="smalltext"><strong><? echo $profileusergroup ?></strong></span></td></tr>
										<tr><td><span class="smalltext">Current Usertitle:</span></td></tr>
										<tr><td><span class="smalltext"><strong><? echo $profileusertitle?></strong></span></td></tr>
										<tr><td><span class="smalltext">New Custom Usertitle (leave blank to use existing):</span></td></tr>
										<tr><td><span class="smalltext"><input type="text" class="textbox" name="usertitle"></span></td></tr>
										<tr><td><span class="smalltext"><input type="checkbox" name="reverttitle">Revert to group default</span></td></tr>
									</table>
								</fieldset>
							</td>
						</tr>
					</table>
					<div align="center">
						<button class="blue submit button" type="submit" name="regsubmit"><span>Update Options</span></button>
					</div>
				</td>
						<?
						}
						else {
						?>
				<td valign="top" style="padding-left: 10px;">
					<table class="table segment menu">
						<tr><td class="thead"><strong><? echo $brand ?></strong></td></tr>
						<tr><td>The member you specified is either invalid or doesn't exist.</td></tr>
					</table>
				</td>
						<?
						}
					}
					elseif($_GET['action'] == "newforum") {
							?>
	<td valign="top" style="padding-left: 10px;">
	<form method="post">
		<table class="table segment menu">
			<tbody><tr>
				<td class="thead"><strong>New Forum</strong></td>
			</tr>
				<?
				if(!isset($_GET['cat'])){
				?>
				<tr>
				<td class="trow"><strong>Category:</strong></td>
				<td class="trow">
					<select name="wher">
 						<?php
						include "db_inc.php";
							$query = "SELECT * FROM `forum` WHERE type='parent'";
							$result1 = mysqli_query($db, $query)
							?>
						<?
							while($row1 = mysqli_fetch_array($result1)):;?>
 							<option value="<?php echo $row1[3];?>"><?php echo $row1[1];?></option>
							<?php endwhile;?>
				</select>
			</td></tr>
				<?
				}
				?>
			<tr>
				<td width="20%" class="trow"><strong>Forum Title:</strong></td>
				<td class="trow"><input type="text" name="subject" size="40" maxlength="85" tabindex="1" class="textbox" autocomplete="off" style="padding: .68em 1em; width: 100%; box-sizing: border-box;" value="" <="" td="">
			</td></tr>
			<tr>
				<td width="20%" class="trow" valign="top"><strong>Description:</strong></td>
				<td class="trow"><textarea class="textarea" name="message" autocomplete="off" style="height: 20em; resize: none;"></textarea></td>
			</tr>
				<tr>
				<td width="20%" class="trow"><strong>Forum Display Rank:</strong></td>
				<td class="trow">
					<select name="display">
 						<option value="0" selected>Member</option>
 						<option value="4">VIP</option>
						<option value="6">Moderator</option>
						<option value="8">Developer</option>
 						<option value="10">Admin</option>
				</select>
			</td></tr>
				<tr>
				<td width="20%" class="trow"><strong>Forum Post Rank:</strong></td>
				<td class="trow">
					<select name="post">
 						<option value="0" selected>Member</option>
 						<option value="4">VIP</option>
						<option value="6">Moderator</option>
						<option value="8">Developer</option>
 						<option value="10">Admin</option>
				</select>
			</td></tr>
		</tbody></table>
		<br>
		<div class="center aligned">
			<button type="submit" class="animated fade teal button" name="submit" value="Post Thread" tabindex="4" accesskey="s">
					<div class="visible content">Add Forum</div>
					<div class="hidden content"><i class="icon fa fa-plus"></i></div>
			</button>
			<button type="submit" class="animated fade orange button" name="previewpost" value="Preview Post" tabindex="5">
					<div class="visible content">Preview Forum (N/A)</div>
					<div class="hidden content"><i class="icon fa fa-eye"></i></div>
			</button>
		</div>
	</form>	
			</td>
				<?
					if(isset($_POST['submit'])){
						include "db_inc.php";
						$title = $_POST['subject'];
						$masage = $_POST['message'];
						$display = $_POST['display'];
						$post = $_POST['post'];
						$cat = $_GET['cat'];
						if(!$_GET['cat']){
						$cat = $_POST['wher'];}
						$query2 = "INSERT INTO `H5E`.`forum` (`FID`, `name`, `displayminrank`, `PAID`, `description`, `postminrank`, `type`) VALUES (NULL, '{$title}', '{$display}', '{$cat}', '{$masage}' , '{$post}', 'forum');";
						$result4 = mysqli_query($db,$query2);
				if (!$result4) {
					errorMessage("Something went wrong! with the add form option", mysqli_error($db));
					} 
				else {
					infoMessage("Forum succesfully created");
				}
		mysqli_close($db);
					}
					}
					elseif($_GET['action'] == "newcat") {
							?>
	<td valign="top" style="padding-left: 10px;">
	<form method="post">
		<table class="table segment menu">
			<tbody><tr>
				<td class="thead"><strong>New Category</strong></td>
			</tr>
			<tr>
				<td width="20%" class="trow"><strong>Category Name:</strong></td>
				<td class="trow"><input type="text" name="subject" size="40" maxlength="85" tabindex="1" class="textbox" autocomplete="off" style="padding: .68em 1em; width: 100%; box-sizing: border-box;" value="" <="" td="">
			</td></tr>
				<tr>
				<td width="20%" class="trow"><strong>Category Display Rank:</strong></td>
				<td class="trow">
					<select name="display">
 						<option value="0" selected>Member</option>
 						<option value="4">VIP</option>
						<option value="6">Moderator</option>
						<option value="8">Developer</option>
 						<option value="10">Admin</option>
				</select>
			</td></tr>
				<tr>
				<td width="20%" class="trow"><strong>Category Post Rank:</strong></td>
				<td class="trow">
					<select name="post">
 						<option value="0" selected>Member</option>
 						<option value="4">VIP</option>
						<option value="6">Moderator</option>
						<option value="8">Developer</option>
 						<option value="10">Admin</option>
				</select>
			</td></tr>
		</tbody></table>
		<br>
		<div class="center aligned">
			<button type="submit" class="animated fade teal button" name="submit" value="Post Thread" tabindex="4" accesskey="s">
					<div class="visible content">Add Category</div>
					<div class="hidden content"><i class="icon fa fa-plus"></i></div>
			</button>
			<button type="submit" class="animated fade orange button" name="previewpost" value="Preview Post" tabindex="5">
					<div class="visible content">Preview Category (N/A)</div>
					<div class="hidden content"><i class="icon fa fa-eye"></i></div>
			</button>
		</div>
	</form>	
			</td>
				<?
						if(isset($_POST['submit'])) {
						$query4 = "SELECT MAX(PAID) FROM forum";
						$result = mysqli_query($db, $query4);
						$row2 = mysqli_fetch_array($result);
						if (!$row2) {
						errorMessage("Something went wrong!", mysqli_error($db));
						} 
						
						$title = $_POST['subject'];
						$masage = $_POST['message'];
						$display = $_POST['display'];
						$post = $_POST['post'];
						$paid1 = $row2['0'];
						$paid = $paid1 + 1;
						$query2 = "INSERT INTO forum (`name`, `displayminrank`, `PAID`, `postminrank`, `type`) VALUES ('{$title}', '{$display}', '{$paid}', '{$post}', 'parent')";
						$result4 = mysqli_query($db,$query2);
						if (!$result4) {
						errorMessage("Something went wrong! with the add form option", mysqli_error($db));
						} 
				else {
					infoMessage("Forum succesfully created");
				}
					}
						}
					
				}
					?>
			</tr>
		</table>
	</form>
<?
if($_COOKIE['style'] == "v1") { include "footerv1.php"; }
?>
</div>
<?
if($_COOKIE['style'] == "v2" || !isset($_COOKIE['style'])) { include "footerv2.php"; }
?>