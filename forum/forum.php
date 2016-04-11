<?
include "default.php";
infoMessage("Important Notes", "Remove werkt nog niet, het is de bedoeling dat je met de rode knop forums kunt verwijderen (JS).");
?>
<!DOCTYPE html>
<html>
	<head>
		<title>
			<?
			if(isset($brand)) {
				echo $brand;
			}
			?>
		</title>
	</head>
	<div class="container content">
		<? 
		if(isset($message)) {
			echo $message;
		}
		?>
		<div class="breadcrumb">
			<div class="active section">
			<? echo $brand; ?>
			</div>
		</div>
		<?
		include "db_inc.php";
		$parentquery = "SELECT pa.FID, pa.name AS title, pa.PAID FROM forum pa WHERE pa.type='parent' AND pa.displayminrank<=$userrank";
		$parents = mysqli_query($db, $parentquery);
		while($parent = mysqli_fetch_array($parents)) {
			$PAID = $parent['PAID'];
		?>
		<table border="0" cellspacing="0" cellpadding="5" class="table forum segment">
			<tbody>
				<tr>
					<td class="thead" colspan="7">
						<div class="expander right">
							<i class="icon fa fa-minus"></i>
						</div>
						<div>
							<strong><a href="forumdisplay.php?fid=<? echo $parent['FID'] ?>"><? echo $parent['title'] ?></a></strong>
						</div>
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<td class="tcat" colspan="2"><span class="smalltext"><strong>Forum</strong></span></td>
					<td class="tcat centered" width="85"><strong>Threads</strong></td>
					<td class="tcat centered" width="85"><strong>Posts</strong></td>
					<td class="tcat centered" width="200"><strong>Last Post</strong></td>
					<?
					if(isset($userrank)) {
						if($userrank >= 8) {
							#echo "<td class='tcat centered' width='30'><strong>Remove</strong></td>";
						}
					}
					?>
				</tr>
				<?
				//Wat problemen met aantal posts en last posts in de query, moet een andere manier zien te vinden
				$forumquery = "SELECT f.FID, f.name, f.description
				FROM forum f 
				WHERE f.type='forum' AND f.PAID=$PAID AND f.displayminrank<=$userrank";
				$forumresult = mysqli_query($db, $forumquery);
				while($row = mysqli_fetch_array($forumresult)) {
					$FID = $row['FID'];
					//Thread & Post Count
					$countquery = "SELECT COUNT(DISTINCT t.TID) AS threadcount, COUNT(DISTINCT p.PID) AS postcount 
					FROM threads t 
					LEFT JOIN posts p ON p.TID=t.TID
					WHERE t.FID=$FID";
					$countresult = mysqli_query($db, $countquery);
					$countrow = mysqli_fetch_array($countresult);
					$threadcount = $countrow['threadcount'];
					$postcount = $countrow['postcount'];
					
					//Last Post
					$lpquery = "SELECT lp.PID, lp.authorID, lp.timestamp, u.username, lpt.topic, lpt.TID
					FROM threads t
					LEFT JOIN posts p ON p.TID=t.TID
					INNER JOIN (
						SELECT PID, authorID, TID, MAX(timestamp) AS timestamp
    				FROM posts
    				GROUP BY TID
					) lp ON lp.TID = t.TID AND lp.timestamp = p.timestamp 
					LEFT JOIN users u ON u.ID=p.authorID
					LEFT JOIN threads lpt ON lpt.TID=lp.TID
					WHERE t.FID=$FID
					ORDER BY p.timestamp DESC";
					$lpresult = mysqli_query($db, $lpquery);
					$lp = mysqli_fetch_array($lpresult);
					$TID = $lp['TID'];
					$topic = $lp['topic'];
					$timestamp = $lp['timestamp'];
					$author = $lp['username'];
					$UID = $lp['authorID'];
				?>
				<tr>
					<td class="trow centered" width="1"><img src="" title="" class="" id="" style="cursor: pointer;"></td>
					<td class="trow"><strong><a href="forumdisplay.php?fid=<? echo $FID ?>"><? echo $row['name'] ?></a></strong><div class="smalltext"><? echo $row['description'] ?></div></td>
					<td class="trow center aligned"><? echo $threadcount ?></td>
					<td class="trow center aligned"><? echo $postcount ?></td>
					<td class="trow" style="text-align: right;">
						<span class="smalltext">
							<?
							if($threadcount > 0) {
							?>
							<a href="showthread.php?tid=<? echo $TID ?>&action=lastpost" title="Title">
								<strong>
								<? 
								if(strlen($topic) > 25) {
									echo substr_replace($topic, "...", 25);
								}
								else {
									echo $topic;
								}
								
								?>
								</strong>
							</a>
							<br>
							<? echo displayTime($timestamp) ?>
							<br>
							By <a href="member.php?action=profile&uid=<? echo $UID ?>"><? echo $author ?></a>
							<?
							}
							else {
							?>
							<div style="text-align: center;">Never</div>
							<?
							}
							?>
						</span>
					</td>	
					<?
					if(isset($userrank)) {
						if($userrank >= 8) {
						//	echo "<td class='trow center aligned'>
						//					<forum method='post'>
							//					<button class='fade orange button' name='remove' value='$FID'><div class='closebox'>X</div>
								//			</forum>
							//			</td>";
								#echo"<td class='trow center aligned'>
								#			<forum method='post'>
								#				<button class='orange button' name='remove' value='$FID'><div class='closebox'>X</div>
								#			</forum>
								#			</td>";
							if(isset($_Post['remove'])){
								echo "derp";
								header("Location: https://www.google.nl");//om te teste
								//$query8 = "DELETE FROM `H5E`.`forum` WHERE `forum`.`FID` = '$FID'";
								//$result = mysqli_query($db,$query8);
								//if (!$result) {
								//	errorMessage("Something went wrong! With removing", mysqli_error($db));
								//} 
							}
						}
					}
					?>
				</tr>
				<?
				}
				?>
			<?
			if(isset($userrank)) {
				if($userrank == 10) {
				?>
				<!--<tr>
					<td class="trow" colspan="7">
						<strong>
							<a href="admincp.php?action=newforum&cat=<? echo $PAID ?>"><i class="icon fa fa-plus" style="text-decoration: none; color: #555;"></i>Add Forum</a>
						</strong>
					</td>
				</tr>-->
				<?
				}
			}
		?>
			</tbody>
		</table>
		<?
		}
		if(isset($userrank)) {
			if($userrank == 10) {
			?>
				<!--<table border="0" cellspacing="0" cellpadding="5" class="table segment">
					<tr>
						<td class="thead">
							<strong><a href="admincp.php?action=newcat" style="color: inherit !important;"><i class="icon fa fa-plus" style="text-decoration: none;"></i>Add Category</a></strong>
						</td>
					</tr>
				</table>-->
			<?
			}
		}
		?>
		
		<?
		if($_COOKIE['style'] == "v1") { include "footerv1.php"; }
		?>
	</div>
	<?
	if($_COOKIE['style'] == "v2" || !isset($_COOKIE['style'])) { include "footerv2.php"; }
	?>
	
<body>

</body>
</html>