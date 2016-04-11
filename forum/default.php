<?
ob_start();
include "db_inc.php";
include_once "functions.php";
date_default_timezone_set("Europe/Amsterdam");

//BBCode stuff
include "sceditor/parser/SBBCodeParser.php";

//Naam van de website
$brand = "LederHosen";

//authorized mag niet undefined zijn
if(!isset($_SESSION['authorized'])) {
	$_SESSION['authorized'] = false;
}

//stuff als ingelogd
if($_SESSION['authorized'] == true) {
	updateUserInfo();
}
else {
	$userrank = 0;
}

?>
<!DOCTYPE html>
<head>
	<link rel="stylesheet" href="style/screen.css?<? echo time() ?>">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<link rel="stylesheet" href="sceditor/themes/square.min.css?<? echo time() ?>" type="text/css" media="all">
	<script type="text/javascript" src="sceditor/jquery.sceditor.bbcode.min.js"></script>
	<script>
	$(function() {
    $("#message").sceditor({
      plugins: "bbcode",
			toolbar: "bold,italic,underline,strike|left,center,right,justify|font,size,color,removeformat|horizontalrule,image,email,link,unlick|youtube,emoticon|bulletlist,orderedlist||maximize,source", 
			width: "100%", 
			resizeWidth: "false", 
			enablePasteFiltering: "true", 
			emoticonsCompat: "true", 
			emoticonsRoot: "sceditor/emoticons/", 
			style: "sceditor/jquery.sceditor.square.css"
    });		
	});
			
</script>
</head>	
<?
//Welke style gebruikt wordt
if(isset($_COOKIE['style'])){
	if($_COOKIE['style'] == "v1") {
		include "headerv1.php";
		//temp solution, javascript, ajax, jquery, idk soon™
		?>
		<style>
			.container.content {
				margin-top: 60px !important;
			}
		</style>
		<?
	}
	elseif($_COOKIE['style'] == "v2" || !isset($_COOKIE['style'])) {
		include "headerv2.php";
	}
}
else{
	setcookie("style", "v2", time() + (86400 * 1337));
	include "headerv2.php";
}
?>