<?
if($_GET['action'] == "register" || $_GET['action'] == "login" || $_GET['action'] == "logout" || $_GET['action'] == "profile") {
  include "default.php";
}
else {
  header("Location: index.php");
}

if($_GET['action'] == "register") {
	include "register.php";
}
elseif($_GET['action'] == "login") {
	include "login.php";
}
elseif($_GET['action'] == "logout") {
	if($_SESSION['authorized'] == true){
		$_SESSION = array();
		session_destroy;
		setcookie("remember", $user, time()+60*60*24*7);
		setcookie("token", $fulltoken, time()+60*60*24*7);
		header("Location: index.php");
	}
	else{
		header("Location: index.php");
	}
}
elseif($_GET['action'] == "profile") {
	include "viewprofile.php";
}
?>