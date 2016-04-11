<?
include "default.php";

if($_SESSION['authorized'] == false) {
	header("Location: index.php");
}
if(isset($_GET['action'])){
	if($_GET['action'] == "profile") {
		$activesection = "Your Profile";
		if(isset($_POST['regsubmit'])) {
			include "db_inc.php";
			if(!empty($_POST['usertitle']) && !isset($_POST['reverttitle']) && strlen($_POST['usertitle']) <= 50) {
				$titlequery = "customusertitle='".$_POST['usertitle']."',";
			}
			if(isset($_POST['reverttitle'])) {
				$titlequery = "customusertitle='',";
			}
			if(!isset($_POST['reverttitle']) && empty($_POST['usertitle'])) {
				$titlequery = "customusertitle='$usertitle',";
			}
			$location = $_POST['location'];
			$sex = $_POST['sex'];
			$query = "UPDATE users SET $titlequery location='$location', sex='$sex' WHERE ID='$userID'";
			$result = mysqli_query($db, $query);
			if(!$result) {
				errorMessage("Something went wrong:", mysqli_error($db));
			}
			else {
				infoMessage("Succes!", "Your information has been changed.");
			}
			
			if(strlen($_POST['usertitle']) > 50) {
				errorMessage("Please correct the following errors before continuing:", "You exceeded the character limit for your usertitle.");
			}
			updateUserInfo();
		}
	}
	elseif($_GET['action'] == "changename") {
		$activesection = "Change Username";
		if(isset($_POST['regsubmit'])) {
			include "db_inc.php";
			$password = $_POST['password'];
			$password_salt = "арахис" . $password . "кирпич";
			$password_hash = md5($password_salt);
			$query = "SELECT password FROM users WHERE ID='$userID'";
			$result = mysqli_query($db, $query);
			$row = mysqli_fetch_array($result);
			if(isset($_POST['regsubmit'])) {
				if(empty($_POST['password'])) {
					errorMessage("Please correct the following errors before continuing:", "Please fill in your current password.");
				}
				elseif(empty($_POST['username'])) {
					errorMessage("Please correct the following errors before continuing:", "Please fill in your desired username.");
				}
				elseif($row['password'] == $password_hash) {
					$query = "UPDATE users SET username='".$_POST['username']."' WHERE ID='$userID'";
					mysqli_query($db, $query);
					infoMessage("Succes!", "You username has been updated.");
					include "default.php";
				}
			}
		}
		
	}
	elseif($_GET['action'] == "password") {
		$activesection = "Change Password";
		if(isset($_POST['regsubmit'])) {
			include "db_inc.php";
			$password = $_POST['oldpassword'];
			$password_salt = "арахис" . $password . "кирпич";
			$result = mysqli_query($db, "SELECT password FROM users WHERE ID='$userID'");
			$row = mysqli_fetch_array($result);
			$password_hash = md5($password_salt);
			if(empty($_POST['oldpassword'])) {
				errorMessage("Please correct the following errors before continuing:", "Please fill in your current password.");
			}
			elseif(empty($_POST['password']) || empty($_POST['password2'])) {
				errorMessage("Please correct the following errors before continuing:", "Please fill in your new password.");
			}
			elseif($row['password'] == $password_hash) {
				if($_POST['password'] == $_POST['password2']){
					$password_new_salt = "арахис" . $_POST['password'] . "кирпич";
					$password_new_hash = md5($password_new_salt);
					$query = "UPDATE users SET password='$password_new_hash' WHERE ID='$userID'";
					mysqli_query($db, $query);
					infoMessage("Succes!", "You password has been updated.");
				}
				else {
					errorMessage("Please correct the following errors before continuing:", "Your password confirmation did not match you new password.");
				}
			}
			else {
				errorMessage("Please correct the following errors before continuing:", "You have entered an invalid password.");
			}
		}
	}
	elseif($_GET['action'] == "email") {
		$activesection = "Change Email Address";
		if(isset($_POST['regsubmit'])) {
			$password = $_POST['password'];
			$password_salt = "арахис" . $password . "кирпич";
			$result = mysqli_query($db, "SELECT password FROM users WHERE ID='$userID'");
			$row = mysqli_fetch_array($result);
			$password_hash = md5($password_salt);
			if(empty($_POST['password'])) {
				errorMessage("Please correct the following errors before continuing:", "Please fill in your current password.");
			}
			if(empty($_POST['email']) || empty($_POST['email2'])) {
				$message = errorMessage("Please correct the following errors before continuing:", "Please fill in your desired email address.");
			}
			if($row['password'] == $password_hash) {
				if($_POST['email'] == $_POST['email2']) {
					$query = "UPDATE users SET email='".$_POST['email']."' WHERE ID='$userID'";
					mysqli_query($db, $query);
					infoMessage("Done!", "Your email address has been changed succesfully.");
					//update alles
					include "default.php";
				}
				else {
					errorMessage("Please correct the following errors before continuing:", "Please confirm your new email.");
				}
			}
			else {
			errorMessage("Please correct the following errors before continuing:", "You have entered an invalid password.");
			}
		}
	}
	elseif($_GET['action'] == "avatar") {
		$activesection = "Change Avatar";
		if(isset($_POST['regsubmit'])) {
			include "db_inc.php";
			$query = "UPDATE users SET customavatarurl='".$_POST['avatarurl']."' WHERE ID='$userID'";
			$result = mysqli_query($db, $query);
			infoMessage("Succes!", "Your avatar has been changed succesfully.");
			//update alles
			include "default.php";
		}
	}
	elseif($_GET['action'] == "options") {
		$activesection = "Edit Options";
		if(isset($_POST['regsubmit'])) {
			setcookie("style", $_POST['boardstyle'], time() + (86400 * 1337));
			header("Location: usercp.php");
		}
	}
	else {
		header("Location: usercp.php");
	}
}
?>
<head>
	<title>User Control Panel</title>
</head>

<div class="container content">
<? 
if(isset($message)) {
	echo $message;
}
?>
<div class="breadcrumb">
	<a class="section" href="index.php">
		<? echo $brand ?>
	</a>
	<div class="divider">
		/
	</div>
	<?
	if(!isset($_GET['action'])) {
	?>	
	<div class="active section">
		User Control Panel
	</div>
	<?
	}
	else {
	?>
	<a class="section" href="usercp.php">
		User Control Panel
	</a>
	<div class="divider">
		/
	</div>
	<div class="active section">
		<? echo $activesection ?>
	</div>
	<?
	}
	?>
</div>
	
	<form method="post" name="input">
		<table width="100%" border="0" align="center">
			<td width="180" valign="top">
				<table border="0" cellspacing="0" cellpadding="5" class="segment table menu">							
					<tbody>
						<tr><td class="thead"><strong>Menu</strong></td></tr>
						<tr><td class="trow smalltext"><i class="icon fa fa-home"></i><a href="usercp.php">User CP Home</a></td></tr>
					</tbody>
					<tbody>
						<tr><td class="tcat"><span class="smalltext"><div class="expander right"><i class="icon fa fa-minus"></i></div><div><strong>Your Profile</strong></span></div></td></tr>							
					</tbody>						
					<tbody>							
						<tr><td class="trow smalltext">
							<div><i class="icon fa fa-folder"></i><a href="usercp.php?action=profile">Edit Profile</a></div>
							<div><i class="icon fa fa-user"></i><a href="usercp.php?action=changename">Change Username</a></div>
							<div><i class="icon fa fa-lock"></i><a href="usercp.php?action=password">Change Password</a></div>
							<div><i class="icon fa fa-envelope"></i><a href="usercp.php?action=email">Change Email</a></div>
							<div><i class="icon fa fa-image"></i><a href="usercp.php?action=avatar">Change Avatar</a></div>
						</td></tr>
						<tr><td class="trow smalltext">
							<i class="icon fa fa-cog"></i>
							<a href="usercp.php?action=options">Edit Options</a>
						</td></tr>
					</tbody>
				</table>
			</td>
			<td valign="top" style="padding-left: 10px;">
				<?
				if (!isset($_GET['action'])){
				?>
				<table class="segment table menu">
					<tr>
						<td class="thead" colspan="2">
							<strong>Your Account Summary</strong>
						</td>
					</tr>
					<tr>
						<td class="trow" width="1" valign="top" align="center">
							<div class="userimage">
								<img width="100" height="100" title="" alt="" src="<? echo $useravatar ?>"></img>
							</div>
						</td>
						<td class="trow">
							<a href="member.php?action=profile&uid=<? echo $userID ?>"><span class="largetext"><strong <? if(isset($usernamecolor)) { echo $usernamecolor; } ?>><? echo $username ?></strong></span></a>
							<br>
							<strong>Posts:</strong>
							<? echo $userpostcount ?>
							<br>
							<strong>Email:</strong>
							<? echo $useremail ?>
							<br>
							<strong>Registration Date:</strong>
							<? echo displayTime($userjoined) ?>
							<br>
							<strong>User Group:</strong>
							<? echo $usergroup ?>
						</td>
					</tr>
				</table>
				<?
				}
				else{				
					if($_GET['action'] == "profile") {
				?>
				<table class="segment table">
					<tr>
						<td class="thead"><strong>Edit Profile</strong></td>
					</tr>
					<tr>
						<td class="trow" width="50%" valign="top">
							<fieldset class="trow">
								<legend><strong>Additional Information</strong></legend>
								<table cellspacing="0" cellpadding="2">
									<tr>
										<td colspan="2">
											<span class="smalltext">Location:</span>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<input type="text" class="textbox" name="location" value="<? echo $userlocation ?>" size="25" maxlength="50">
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<span class="smalltext">Sex:</span>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<select name="sex">
												<option value="" <? if($usersex == "") { echo "selected='selected'"; } ?>></option>
												<option value="Male" <? if($usersex == "Male") { echo "selected='selected'"; } ?>>Male</option>
												<option value="Female" <? if($usersex == "Female") { echo "selected='selected'"; } ?>>Female</option>
												<option value="Unspecified" <? if($usersex == "Undisclosed") { echo "selected='selected'"; } ?>>Undisclosed</option>
											</select>
										</td>
									</tr>
								</table>
							</fieldset>
						</td>
						<td class="trow" width="50%" valign="top">
							<fieldset class="trow">
								<legend><strong>Custom Usertitle</strong></legend>
								<table cellspacing="0" cellpadding="2">
									<tr><td><span class="smalltext">Here you can assign a custom user title which will overwrite the one based on users display group.</span></td></tr>
									<tr>
										<td colspan="2">
											<span class="smalltext">Default Usertitle:</span>
										</td>
									</tr>
									<tr>
										<td>
											<span class="smalltext">
												<strong><?echo $usergroup?></strong>
											</span>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<span class="smalltext">Current Custom Usertitle:</span>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<span class="smalltext"><strong><?echo $usertitle?></strong></span>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<span class="smalltext">New Custom Usertitle (Leave blank to use existing):</span>
											</td>
									</tr>
									<tr>
										<td colspan="2">
											<input type="text" class="textbox" name="usertitle" size="25" maxlength="50">
										</td>
									</tr>
									<tr>
										<td><span class="smalltext"><input type="checkbox" name="reverttitle" id="reverttitle" class="checkbox">Revert to group default</span></td>
									</tr>
								</table>
							</fieldset>
						</td>
					</tr>
				</table>
				<?
					}
					elseif($_GET['action'] == "changename") {
					?>
				<table border="0" cellspacing="0" cellpadding="5" class="segment table menu">
					<tbody><tr><td class="thead"><strong>Change Username</strong></td></tr>
						<tr><td class="tcat" colspan="2"><strong>Change Username</strong></td></tr>
						<tr>
							<td class="trow" width="40%"><strong>New Username:</strong></td>
							<td class="trow" width="60%"><input type="text" class="textbox" name="username" size="25" maxlenght="30"></td>
						</tr>
						<tr><td class="tcat" colspan="2"><strong>Password Confirmation</strong></td></tr>
						<tr>
							<td class="trow" width="40%"><strong>Current Password:</strong></td>
							<td class="trow" width="60%"><input type="password" class="textbox" name="password" size="25"></td>
						</tr>
					</tbody>
				</table>
					<?
					}
					elseif($_GET['action'] == "password") {
					?>
				<table border="0" cellspacing="0" cellpadding="5" class="segment table menu">
					<tr><td class="thead"><strong>Change Password</strong></td></tr>
					<tr><td class="tcat" colspan="2"><strong>Change Password</strong></td></tr>
					<tr>
						<td class="trow" width="40%"><strong>New Password:</strong></td>
						<td class="trow" width="60%"><input type="password" class="textbox" name="password" size="25" maxlenght="30" autocomplete="off"></td>
					</tr>
					<tr>
						<td class="trow" width="40%"><strong>Confirm Password:</strong></td>
						<td class="trow" width="60%"><input type="password" class="textbox" name="password2" size="25" maxlenght="30" autocomplete="off"></td>
					</tr>
					<tr><td class="tcat" colspan="2"><strong>Password Confirmation</strong></td></tr>
					<tr>
						<td class="trow" width="40%"><strong>Current Password:</strong></td>
						<td class="trow" width="60%"><input type="password" class="textbox" name="oldpassword" size="25"></td>
					</tr>
				</table>
				<?
					}
					elseif($_GET['action'] == "email") {
				?>
				<table border="0" cellspacing="0" cellpadding="5" class="segment table menu">
					<tr><td class="thead"><strong>Change Email</strong></td></tr>
					<tr><td class="tcat" colspan="2"><strong>Change Email</strong></td></tr>
					<tr>
						<td class="trow" width="40%"><strong>New Email Address:</strong></td>
						<td class="trow" width="60%"><input type="text" class="textbox" name="email" size="25" maxlenght="30" autocomplete="off"></td>
					</tr>
					<tr>
						<td class="trow" width="40%"><strong>Confirm Email Address:</strong></td>
						<td class="trow" width="60%"><input type="text" class="textbox" name="email2" size="25" maxlenght="30" autocomplete="<off></off>"></td>
					</tr>
					<tr><td class="tcat" colspan="2"><strong>Password Confirmation</strong></td></tr>
					<tr>
						<td class="trow" width="40%"><strong>Current Password:</strong></td>
						<td class="trow" width="60%"><input type="password" class="textbox" name="password" size="25"></td>
					</tr>
				</table>
				<?
					}
					elseif($_GET['action'] == "avatar") {
				?>
				<table border="0" cellspacing="0" cellpadding="5" class="segment table menu">
					<tr><td class="thead"><strong>Change Avatar</strong></td></tr>
					<tr><td class="trow" colspan="2">
						<table cellspacing="0" cellpadding="0" width="100%">
							<tr>
								<td>
								An avatar is a small identifying image for your profile<br>
								</td>
								<td width="150" align="right">
									<img width="100" height="100" src="<? echo $useravatar ?>">
								</td>
							</tr>
						</table>
					</td></tr>
					</tr><td class="tcat" colspan="2"><strong>Custom Avatar</strong></td></tr>
					<tr>
						<td class="trow" width="40%">
							<strong>Avatar URL:</strong><br><span class="smalltext">Enter the URL of an avatar on the internet.</span>
						</td>
						<td class="trow" width="60%">
							<input type="text" class="textbox" name="avatarurl" value="<? echo $customavatar ?>" size="45"><br>
							<span class="smalltext">To use <a href="http://gravatar.com" target="_blank">Gravatar</a>, leave this form blank.</span>
						</td>
					</tr>
				</table>
				<?
					}
					elseif($_GET['action'] == "options") {
				?>
				<table class="segment table">
					<tr>
						<td class="thead"><strong>Edit Profile</strong></td>
					</tr>
					<tr>
						<td class="trow" width="50%" valign="top">
							<fieldset class="trow">
								<legend><strong>Other Options</strong></legend>
								<table cellspacing="0" cellpadding="2">
									<tr>
										<td>
											<span class="smalltext">Board Style:</span>
										</td>
									</tr>
									<tr>
										<td>
											<select name="boardstyle">
												<option value="v2">V2</option>
												<option value="v1">V1</option>
											</select>
										</td>
									</tr>
								</table>
							</fieldset>
						</td>
				</table>
				<?
					}
				?>
				<div align="center">
					<button class="blue submit button" type="submit" name="regsubmit"><span>Update Options</span></button>
				</div>
				<?
				}
				?>
				
		</table>
	</form>
<?
if($_COOKIE['style'] == "v1") { include "footerv1.php"; }
?>
</div>
<?
if($_COOKIE['style'] == "v2") { include "footerv2.php"; }
?>