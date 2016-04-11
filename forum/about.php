<?
include "default.php";
?>
<head>
	<title>About Us</title>
</head>

<div class="container content">
	<div class="breadcrumb">
		<a class="section" href="index.php"><? echo $brand ?></a>
		<div class="divider">
			/
		</div>
		<div class="active section">
			About Us
		</div>
	</div>
	
	<div class="about segment">
		<h1 class="brand">
			<? echo $brand ?>
		</h1>
		<p>
			<strong>
				text just random text
			</strong>
		</p>
	</div>
		
	<table class="about table segment menu">
		<tr><td class="thead" colspan="2"><strong>Het team en hun rol in <? echo $brand ?></strong></td></tr>
		<tr><td class="tcat"><strong>Naam</strong></td><td class="tcat right aligned"><strong>Rol</strong></td></tr>
		<tr><td><strong>dev name</strong></td><td class="right aligned">thing he did</td></tr>
	</table>
	
	<table class="about table segment menu">
		<tr><td class="thead" colspan="2"><strong>Credits</strong></td></tr>
		<tr><td class="tcat"><strong>Naam</strong></td><td class="tcat right aligned"><strong>Reden</strong></td></tr>
		<tr><td><strong>Sam Clarke<br><a href="https://github.com/samclarke">Github</a></strong></td><td class="right aligned">BBCode</td></tr>
	</table>
	
<?
if($_COOKIE['style'] == "v1") {
	include "footerv1.php";
}
?>
</div>
<?
if($_COOKIE['style'] == "v2" || !isset($_COOKIE['footer'])) {
	include "footerv2.php";
}
?>
