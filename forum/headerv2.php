<div class="large menu fixed noselect">
  <div class="container">
    <a class="item header" href="index.php">
      <div class="mini-brand">
        LH
      </div>
    </a>
    <div class="middle">
      <a class="item" href="index.php">Home</a>
      <a class="item" href="about.php">About</a>
      <a class="item" href="memberlist.php">Memberlist</a>
    </div>
    <div class="right menu">
<? 
//Als niet ingelogd
if ($_SESSION['authorized'] == false) {
?>
      <div class="item">
				<a class="button basic" href="member.php?action=login">Login</a>
			</div>
			<div class="item">
				<a class="button basic blue" href="member.php?action=register">Register</a>
			</div>
<?
}
//Als ingelogd
elseif ($_SESSION['authorized'] == true) {
?>	
			<div class="pointing dropdown item" tabindex="0">
				<img class="avatar image" src="<? echo $useravatar ?>"></img>
				<i class="icon fa fa-caret-down"></i>
				<div class="menu">
					<?
					if($userrank >= 6) {
					?>
					<a class="item" href="admincp.php">Admin CP</a>
					<div class="divider"></div>
					<?
					}
					?>
					<a class="item" href="member.php?action=profile">View Profile</a>
					<a class="item" href="usercp.php">User CP</a>
					<div class="divider"></div>
					<a class="item" href="member.php?action=logout">Log Out</a>
				</div>
			</div>
<?
}
?>
    </div>
  </div>
</div>