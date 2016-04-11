<div class="navbar noselect">
	<div class="container">
		<a class="item brand" href="index.php">
			<? echo $brand ?>
		</a>
		<div class="menu right">
			<a class="item" href="index.php">
				<i class="fa fa-home"></i>
				Home
			</a>
			<a class="item seperate" href="about.php">
				<i class="fa fa-question"></i>
				About Us
			</a>
			<a class="item seperate" href="memberlist.php">
				<i class="fa fa-users"></i>
				Member List
			</a>
			<? 
			//Als niet ingelogd
			if($_SESSION['authorized'] == false) {
			?>
			<div class='item seperate nohover'>
				<a href='member.php?action=login' class='small button green'>
					Login
				</a>
				<a href='member.php?action=register' class='small button red'>
					Register
				</a>	
			</div>
			<?
			}
			//Als ingelogd
			elseif($_SESSION['authorized'] == true) {
			?>
			<div tabindex="0" class="dropdown item seperate brand">
				<? echo $username ?>
				<i class='fa fa-caret-down'></i>	
				<div class="menu">
					<?
					if($userrank >= 6) {
					?>
					<a class="item" href="admincp.php">
						<i class='fa fa-cogs'></i>
						Admin CP
					</a>
					<hr>
					<?
					}
					?>
					<a class="item" href="usercp.php">
						<i class='fa fa-user'></i>
						User CP
					</a>
					<hr>
					<a class="item" href="member.php?action=logout">
						<i class='fa fa-sign-out'></i>
						Sign Out
					</a>		
				</div> 
			</div>
			<?
			}
			?>
		</div>
	</div>
</div>