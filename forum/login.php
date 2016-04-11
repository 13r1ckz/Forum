<?

//ophalen van data voor remember me
if(isset($_COOKIE['remember'])) {
	$username = $_COOKIE["remember"];
	if(isset($_COOKIE['token'])) {
		$tokenget = $_COOKIE["token"];
		$query = "SELECT * FROM users WHERE username='$username'";
		$result = mysqli_query($db,$query);
		$row = mysqli_fetch_array($result);
		$tokendata = $row['token'];
	}
}
	
//controle token
if(!isset($_COOKIE['remember'])) {
   //echo "first fisit"; // voor debuggen
}
if(isset($_COOKIE['remember'])) { 
	echo "remeber got	/" . $tokenget . "		/		" . $tokendata;  // debuggen
	if($tokenget == $tokendata){
		 echo "u are in"; 
	infoMessage("Token is working", $tokendata);
			$_SESSION['authorized'] = true;
			$_SESSION['UID'] = $row['ID'];
			$userID = $row['ID'];
			$userrank = $row['userrank'];
			$$useravatar = $row['#'];
	//randomizer voor niewe token voor de veilighijd
				function generate($length = 64) {
				$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
 				$charactersLength = strlen($characters);
 				$randomString = '';
					for ($i = 0; $i < $length; $i++) {
						$randomString += $characters[rand(0, $charactersLength - 1)];
					}
					return $randomString;
				}
				//maken van de token en het in de databace ztten
				echo "1";
				$random = generateRandomString();
				$fulltoken = hash('sha256' ,$random);
				$theshit = $fulltoken;
				echo $theshit . "		/		" . $theshit;
				//	errorMessage("101010101010" , $fulltoken);
				$query = "UPDATE `H5E`.`users` SET `token` = '$fulltoken' WHERE `users`.ID='$userID'";
				$result = mysqli_query($db,$query);
				if (!$result) {
					errorMessage("Something went wrong!", mysqli_error($db));
					} 
				else {
					infoMessage("Succesfully created token");
				}
		mysqli_close($db);
				setcookie("remember", $username, time()+60*60*24*7);
				setcookie("token", $fulltoken, time()+60*60*24*7); 
				header("Location: index.php");
}
	}
// the login
if(isset($_POST['login'])) {
	if(!empty($_POST['username']) && !empty($_POST['password'])) {
		include "db_inc.php";
		$user = $_POST['username'];
		$password = $_POST['password'];
		$password_salt = "арахис" . $password . "кирпич";
		$password_hash = md5($password_salt);
		
		$query = "SELECT ID, username, password, email, customavatarurl, userrank FROM users WHERE username='$user'";
		$result = mysqli_query($db,$query);
		if (!$result) {
			errorMessage("Something went wrong!", mysqli_error($db));
		} 
		$row = mysqli_fetch_array($result);
		if($row['password'] == $password_hash) {
			$_SESSION['authorized'] = true;
			$_SESSION['UID'] = $row['ID'];
			//$userID = $row['ID'];
			//remember me option	******************************************************************************************************************************************************************************************************************
			if(isset($_POST['remember'])){
				//randomizer
				function generate($length = 64) {
				$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
 				$charactersLength = strlen($characters);
 				$randomString = '';
					for ($i = 0; $i < $length; $i++) {
						$randomString += $characters[rand(0, $charactersLength - 1)];
					}
					return $randomString;
				}
				//maken van de token en het in de databace ztten
				$random = generate();
				$fulltoken = hash('sha256' ,$random);
				$thetoken = $fulltoken;
				$query = "UPDATE `H5E`.`users` SET `token` = '$thetoken' WHERE `users`.ID='$userID'";
				echo $thetoken . "		/		" . $thetoken;
				$result = mysqli_query($db,$query);
				if (!$result) {
					errorMessage("Something went wrong! with the remember me option", mysqli_error($db) ,"please contact us!");
					} 
				else {
					infoMessage("Succesfully token created");
				}
				mysqli_close($db);
				setcookie("remember", $user, time()+60*60*24*7);
				setcookie("token", $thetoken, time()+60*60*24*7);
			}
			header("Location: index.php");
		}
		else {
			errorMessage("Please correct the following errors before continuing:", "You have entered in incorrect email/password combination.");
		}
	}
	else {
		errorMessage("Please correct the following errors before continuing:", "Please fill in all required forms");
	}
}

?>
	<html>
		<head>
			<title>Login</title>
		</head>
	<body>
	<div class="container content">

		<div class="breadcrumb">
			<a class="section" href="index.php"><? echo $brand ?></a>
			<div class="divider">
				/
			</div>
			<div class="active section">
				Login
			</div>
		</div>	
		<? 
		if(isset($message)) {
		echo $message;
		}
		?>
		<div class="two column middle aligned relaxed grid basic segment">

			<div class="column">
				<form class="form segment owncolor" method="post">
					
					<div class="field">	
						<label>Username</label>
						<div class="labeled icon input login">
							<input type="text" size="25" name="username" placeholder="Username"></input>
							<i class="icon fa fa-user"></i>
							<div class="corner label">
								<i class="icon fa fa-asterisk"></i>
							</div>
							
						</div>		
					</div>
					
					<div class="field">	
						<label>Password</label>
						<div class="labeled icon input input login">
							<input type="password" name="password" placeholder="Password">
							<i class="icon fa fa-lock"></i>
							<div class="corner label">
								<i class="icon fa fa-asterisk"></i>
							</div>
							
						</div>
					</div>
					
					<div class="field" >
						<div class="checkbox">
							<input name="remember" type="checkbox">
							Remember Me
						</div>
					</div>
				
					<button class="blue submit button" type="submit" value="login" name="login">			
						<span>LOGIN </span>
					</button>
				</form>	
			</div>
				
			<div class="vertical divider">
				Or
			</div>
		
			<div class="center aligned column">
				<a class="huge green labeled icon button" href="member.php?action=register">
					<i class="icon fa fa-sign-in"></i>
					Sign Up
				</a>
			</div>
		
		</div>
		
		<?
		if($_COOKIE['style'] == "v1") { include "footerv1.php"; }
		?>
	</div>
	<?
	if($_COOKIE['style'] == "v2") { include "footerv2.php"; }
	?>