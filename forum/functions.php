<?

session_start();
//functie voor een infe or error message bovenaan de pagina
function infoMessage($infotitle, $infomessage) {
	$GLOBALS['message'] = "<div class='info message'><h4 class='header'>".$infotitle."</h4>".$infomessage."</div>";
}
function errorMessage($errortitle, $errormessage) {
	$GLOBALS['message'] = "<div class='error message'><div class='header'>".$errortitle."</div><ul><li>".$errormessage."</li></ul></div>";
}

//functie om time() om the zetten in x hour(s)/minute(s)/second(s) ago, yesterday of naar datum #proud
function displayTime($time) {
	$yesterday = date("d") - 1;
	if(date("d", $time) == $yesterday) {
		return "Yesterday, " . date("h:i A", $time);
	}
	else {
		$difference = time() - $time;
		if($difference < 1) {
			return 'Just now';
		}
		elseif($difference < 60) {
			if($difference == 1) {
				return "1 second ago";
			}
			else {
				return round($difference) . " seconds ago";
			}
		}
		elseif($difference >= 60 && $difference < 3600) {
			$minutes = $difference / 60;
			if($minutes < 2) {
				return "1 minute ago";
			}
			else {
				return round($minutes) . " minutes ago";
			}
		}
		elseif($difference >= 3600 && $difference < 86400) {
			$hours = $difference / 3600;
			if($hours < 2) {
				return "1 hour ago";
			}
			else {
				return round($hours) . " hours ago";
			}
		}
		else{
			return date("Y-m-d, h:i A", $time);
		}
	}
	
}

function updateUserInfo(){
		include "db_inc.php";
		$query = "SELECT * FROM users WHERE ID='".$_SESSION['UID']."'";
		$result = mysqli_query($db, $query);
		$row = mysqli_fetch_array($result);
		//dingen opslaan om later terug te halen
		$GLOBALS['username'] = $row['username'];
		$GLOBALS['userID'] = $row['ID'];
		$GLOBALS['userrank'] = $row['userrank'];
		$GLOBALS['customusertitle'] = $row['customusertitle'];
		$GLOBALS['customavatar'] = $row['customavatarurl'];
		$GLOBALS['userlocation'] = $row['location'];
		$GLOBALS['usersex'] = $row['sex'];
		$GLOBALS['useremail'] = $row['email'];
		$GLOBALS['userjoined'] = $row['joined'];
		$useremail_hash = md5( strtolower( trim( $GLOBALS['useremail'] ) ) );
		$gravatar = "http://www.gravatar.com/avatar/" . $useremail_hash . "?s=400&d=mm&r=g";

		if ($GLOBALS['customavatar'] == "") {
			$GLOBALS['useravatar'] = $gravatar;
		}
		else {
			$GLOBALS['useravatar'] = $GLOBALS['customavatar'];
		}

		if ($GLOBALS['userrank'] == 10) {
			$GLOBALS['usergroup'] = "Admin";
			$GLOBALS['usernamecolor'] = "style='color: #00c086;'";
		}
		elseif ($GLOBALS['userrank'] == 8) {
			$GLOBALS['usergroup'] = "Developer";
			$GLOBALS['usernamecolor'] = "style='color: #ff3131;'";
		}
		elseif ($GLOBALS['userrank'] == 6) {
			$GLOBALS['usergroup'] = "Moderator";
			$GLOBALS['usernamecolor'] = "style='color: #9750dd;'";
		}

		elseif ($GLOBALS['userrank'] == 4) {
			$GLOBALS['usergroup'] = "VIP";
			$GLOBALS['usernamecolor'] = "style='color:#64B5F6;'";
		}
		else {
			$GLOBALS['usergroup'] = "Member";
		}

		if ($GLOBALS['customusertitle'] != "") {
			$GLOBALS['usertitle'] = $GLOBALS['customusertitle'];
		}
		else {
			$GLOBALS['usertitle'] = $GLOBALS['usergroup'];
		}
		
	//Get post count
	$subquery1 = "SELECT COUNT(DISTINCT PID) AS postcount FROM posts WHERE authorID='".$GLOBALS['userID']."'";
	$postresult = mysqli_query($db, $subquery1);
	$postrow = mysqli_fetch_array($postresult);
	$GLOBALS['userpostcount'] = $postrow['postcount'];
	//Get thread count
	$subquery2 = "SELECT COUNT(DISTINCT TID) AS threadcount FROM threads WHERE authorID='".$GLOBALS['userID']."'";
	$threadresult = mysqli_query($db, $subquery2);
	$threadrow = mysqli_fetch_array($threadresult);
	$GLOBALS['userthreadcount'] = $threadrow['threadcount'];
	//last visit update
	$updatequery = "UPDATE users SET lastonline='".time()."' WHERE ID='".$GLOBALS['userID']."'";
	mysqli_query($db, $updatequery);
	
}

//randomizer voor niewe token voor de veilighijd
function generateRandomString($length = 64) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

//Soort van BBCode replica, vervangen door sceditor
function parseBB($string) {
	
	$find = array(
		"@\n@", 
		"/\[img\](.+?)\[\/img\]/is", 
		#"@[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]@is", 
    "/\[url\=(.+?)\](.+?)\[\/url\]/is", 
    "/\[b\](.+?)\[\/b\]/is",  
    "/\[i\](.+?)\[\/i\]/is",  
    "/\[u\](.+?)\[\/u\]/is",  
    "/\[color\=(.+?)\](.+?)\[\/color\]/is", 
    "/\[size\=(.+?)\](.+?)\[\/size\]/is",  
    "/\[font\=(.+?)\](.+?)\[\/font\]/is", 
    "/\[center\](.+?)\[\/center\]/is", 
    "/\[right\](.+?)\[\/right\]/is", 
    "/\[left\](.+?)\[\/left\]/is", 
    "/\[email\](.+?)\[\/email\]/is",
		"/\[quote='(.+?)' pid='(.+?)' dateline='(.+?)'\](.+?)\[\/quote\]/is", 
		"/\[quote='(.+?)' pid='(.+?)'\](.+?)\[\/quote\]/is", 
		"/\[quote='(.+?)'\](.+?)\[\/quote\]/is",
		"/\[quote\](.+?)\[\/quote\]/is", 
		"/\[code\](.+?)\[\/code\]/is", 
		"/\[video=youtube\](.+?)\[\/video\]/is", 
		"#(.*?)(?:href='https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch?.*?v=))([\w\-]{10,12}).*#x"
	);
	$replace = array( 
    "<br />", 
		"<img src=\"$1\" alt=\"Image\" />", 
    #"<a href=\"\\0\">\\0</a>", 
    "<a href=\"$1\" target=\"_blank\">$2</a>", 
    "<strong>$1</strong>", 
    "<em>$1</em>", 
    "<span style=\"text-decoration:underline;\">$1</span>", 
    "<font color=\"$1\">$2</font>", 
    "<font size=\"$1\">$2</font>", 
    "<span style=\"font-family: $1\">$2</span>", 
    "<div style=\"text-align:center;\">$1</div>", 
    "<div style=\"text-align:right;\">$1</div>", 
    "<div style=\"text-align:left;\">$1</div>", 
    "<a href=\"mailto:$1\" target=\"_blank\">$1</a>", 
		"<blockquote><cite><span>($3)</span>$1 Wrote: <a href='showthread.php?pid=$2#pid$2'><i class='icon fa fa-arrow-right'></i></a></cite>$4</blockquote>", 
		"<blockquote><cite>$1 Wrote: <a href='showthread.php?pid=$2#pid$2'><i class='icon fa fa-arrow-right'></i></a></cite>$3</blockquote>", 
		"<blockquote><cite>$1 Wrote:</cite>$2</blockquote>", 
		"<blockquote><cite>Quote:</cite>$1</blockquote>", 
		"<code>$1</code>", 
		"<iframe width='560' height='315' src='$1' allowfullscreen></iframe>", 
		"http://www.youtube.com/embed/$2"
    );
	$string = htmlspecialchars($string);
	$string = preg_replace($find, $replace, $string);
	return $string;
}

?>