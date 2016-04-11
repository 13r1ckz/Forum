<?php 
if (isset($_POST['submit'])) {
	if($_POST['password1'] != $_POST['password2']) {
		$error = true;
		errorMessage("Please correct the following errors before continuing:", "Your passwords did not match");
	}
	if($_POST['password1'] == $_POST['password2']) {
		if(!empty($_POST['email']) && !empty($_POST['username'])) {
			$error = false;
		}
		else {
			$error = true;
			errorMessage("Please correct the following errors before continuing:", "Please fill in all required forms");
		}
	}
	
	if($error == false) {
		include "db_inc.php";
		$username = $_POST['username'];
		$mail = $_POST['email'];
		$password = $_POST['password1'];
		$password_salt = "арахис" . $password . "кирпич";
		$password_hash = md5($password_salt);

		$query = "INSERT INTO users (`ID`, `username`, `email`, `password`, `joined`, `lastonline`) VALUES (NULL, '{$username}', '{$mail}', '{$password_hash}', '".time()."', '".time()."')";
		$result = mysqli_query($db,$query);
		if (!$result) {
			errorMessage("Something went wrong!", mysqli_error($db));
		} 
		else {
			infoMessage("Succesfully registered!", "You can now sign in using your username and password.");
		}
		mysqli_close($db);
	}
}
?>
<html>
  <head>
    <title>
      Register
    </title>
		<script src='https://www.google.com/recaptcha/api.js'></script>
  </head>
  <body>
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
			<div class="active section">
				Register
			</div>
      </div>
      <form class="piled segment" method="post">
        <h4 class="header">
          Registration
        </h4>
        <div class="two column grid">
		
			<div class="column">
				<div class="form segment owncolor">
					<h5 class="top attached label green">
						Account Details
					</h5>
					<div class="field">
						<label>Username</label>
						<div class="input register">
							<input type="text" size="100" name="username" placeholder="Username">
							<i class="icon fa fa-user"></i>
							<div class="corner label">
								<i class="icon fa fa-asterisk"></i>
							</div>
						</div>
					</div>
					<div class="field">
						<label>E-mail</label>
						<div class="input register">
							<input type="text" size="100" name="email" placeholder="E-mail">
							<i class="icon fa fa-envelope"></i>
							<div class="corner label">
								<i class="icon fa fa-asterisk"></i>
							</div>
						</div>
					</div>
					<div class="two fields">
						<div class="field">
							<label>Password</label>
							<div class="input register">
								<input type="password" name="password1" placeholder="Password">
								<i class="icon fa fa-lock"></i>
								<div class="corner label">
									<i class="icon fa fa-asterisk"></i>
								</div>
							</div>
						</div>
						<div class="field">
							<label>Repeat Password</label>
							<div class="input register">
								<input type="password" name="password2" placeholder="Password">
								<i class="icon fa fa-lock"></i>
								<div class="corner label">
									<i class="icon fa fa-asterisk"></i>
									</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="column">
				<div class="form segment owncolor">
					<h5 class="top attached label purple">
						Image Verification
					</h5>
					<p>Please enter the text contained within the image into the text box below it. This process is used to prevent automated spam bots.</p>
					<div class="g-recaptcha" data-sitekey="6LendRwTAAAAAHLJeiAu3CX6DjfJJx4jQLc39ohy"></div>
				</div>
			</div>
			
			<div class="center aligned">
				<button class="blue vertical animated sumbit button" type="submit" value="Submit" name="submit">			
					<div class="visible">Register</div>
					<div class="hidden"><i class="fa fa-sign-in"></i></div>
				</button>
			</div>
			
        </div>
      </form>
    
		<?
		if($_COOKIE['style'] == "v1") { include "footerv1.php"; }
		?>
	</div>
	<?
	if($_COOKIE['style'] == "v2") { include "footerv2.php"; }
	?>
	
  </body>
</html>
