<?php
/*
 * Paste <https://github.com/jordansamuel/PASTE>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License in GPL.txt for more details.
 */
define('IN_ADMIN', 1);
require_once('common.php');

updateAdminHistory($conn);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paste - Pastes</title>
	<link rel="shortcut icon" href="favicon.ico">
    <link href="css/paste.css" rel="stylesheet" type="text/css" />
	<link href="css/datatables.min.css" rel="stylesheet" type="text/css" />
  </head>
  <body>
  
	<div id="top" class="clearfix">
		<!-- Start App Logo -->
		<div class="applogo">
		  <a href="../" class="logo">Paste</a>
		</div>
		<!-- End App Logo -->

		<!-- Start Top Right -->
		<ul class="top-right">
			<li class="dropdown link">
				<a href="#" data-toggle="dropdown" class="dropdown-toggle profilebox"><b>Admin</b><span class="caret"></span></a>
				<ul class="dropdown-menu dropdown-menu-list dropdown-menu-right">
				  <li><a href="admin.php">Settings</a></li>
				  <li><a href="?logout">Logout</a></li>
				</ul>
			</li>
		</ul>
		<!-- End Top Right -->
	</div>
	<!-- END TOP -->	

	<div class="content">
		  <!-- START CONTAINER -->
		<div class="container-widget">
			<!-- Start Menu -->
        <?php include 'menu.php';?>
			<!-- End Menu -->
                                 
			<?php
			if (isset($_GET['delete'])) {
				$delid  = htmlentities(Trim($_GET['delete']));
				$query  = "DELETE FROM pastes WHERE id=$delid";
				$result = mysqli_query($con, $query);
				if (mysqli_errno($con)) {
					$msg = '<div class="paste-alert alert6" style="text-align: center;">
				 ' . mysqli_error($con) . '
				 </div>';
				} else {
					$msg = '<div class="paste-alert alert3" style="text-align: center;">
					 Paste deleted
					 </div>';
				}
				
			}
			?>    

			<!-- Start Pastes -->
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-widget">
						<?php
						if (isset($_GET['details'])) {
							$detail_id = htmlentities(Trim($_GET['details']));
							$query     = "SELECT * FROM pastes WHERE id='$detail_id'";
							$result    = mysqli_query($con, $query);
							while ($row = mysqli_fetch_array($result)) {
								$p_title    = $row['title'];
								$p_content  = $row['content'];
								$p_visible  = $row['visible'];
								$p_code     = $row['code'];
								$p_expiry   = $row['expiry'];
								$p_password = $row['password'];
								$p_member   = $row['member'];
								$p_date     = $row['date'];
								$p_encrypt  = $row['encrypt'];
								$p_views    = $row['views'];
								$p_ip       = $row['ip'];
							}
							if ($p_encrypt == "" || $p_encrypt == null || $p_encrypt == '0') {
								$encrypt = "Not Encrypted";
							} else {
								$encrypt = "Encrypted";
							}
							if ($p_expiry == "NULL") {
								$expiry = "Never";
							} else {
								$input_time   = $p_expiry;
								$current_time = mktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y"));
								if ($input_time < $current_time) {
									$expiry = "Paste is expired";
								} else {
									$expiry = "Paste is not expired";
								}
							}
							
							if ($p_password == 'NONE') {
								$pass = "Not protected";
							} else {
								$pass = "Password protected paste";
							}
							if ($p_visible == '0') {
								$visible = "Public";
							} elseif ($p_visible == '1') {
								$visible = "Unlisted";
							} elseif ($p_visible == '2') {
								$visible = "Private";
							} else {
								$visible = "Something went wrong";
							}
							
						?>
							<div class="panel-title">
							Details of Paste ID <?php echo $detail_id; ?>
							</div>
							
							<div class="panel-body table-responsive">
							  <table class="table display dataTable">
								<tbody>
								  <tr>
									<td>  Username </td>
									<td> <?php echo $p_member; ?> </td>   
								  </tr>
								  
								  <tr>
									<td> Paste Title </td>
									<td> <?php echo $p_title; ?> </td>
								  </tr>
								  
								  <tr>
									<td> Visibility </td>
									<td> <?php echo $visible; ?> </td>
								  </tr>
								  
								 <tr>
									<td> Password </td>
									<td> <?php echo $pass; ?> </td>
								 </tr>
								  
								 <tr>
									<td> Views </td>
									<td> <?php echo $p_views; ?> </td>
								 </tr>
								  
								 <tr>
									<td> IP </td>
									<td> <?php echo $p_ip; ?> </td>
								 </tr>
								  
								 <tr>
									<td>  Syntax Highlighting </td>
									<td> <?php echo $p_code; ?> </td>
								 </tr>
								  
								 <tr>
									<td> Expiration </td>
									<td> <?php echo $expiry; ?> </td>
								 </tr>
								 
								 <tr>
									<td> Encrypted Paste </td>
									<td> <?php echo $encrypt; ?></td>
								 </tr>
								</tbody>
							  </table>
							</div>
 
						<?php } else { ?>
					  
					  	<div class="panel-body">
							<div class="panel-title">
							Manage Pastes
							</div>

							<?php if (isset($msg)) echo $msg; ?>
                                       
							<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="pastesTable">
								<thead>
									<tr>
										  <th>ID</th>
										  <th>Username</th>
										  <th>IP</th>
										  <th>Visibility</th>
										  <th>More Details</th>
										  <th>View Paste</th>
										  <th>Delete</th>
									</tr>
								</thead>         
								<tbody>
																
								</tbody>
							</table>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<!-- End Admin Settings -->
		</div>
		<!-- END CONTAINER -->

		<!-- Start Footer -->
		<div class="row footer">
		  <div class="col-md-6 text-left">
		   <a href="https://github.com/jordansamuel/PASTE" target="_blank">Updates</a> &mdash; <a href="https://github.com/jordansamuel/PASTE/issues" target="_blank">Bugs</a>
		  </div>
		  <div class="col-md-6 text-right">
			Powered by <a href="https://phpaste.sourceforge.io" target="_blank">Paste</a>
		  </div> 
		</div>
		<!-- End Footer -->

	</div>
	<!-- End content -->

	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
	<script type="text/javascript" language="javascript" class="init">
		$(document).ready(function() {
			$('#pastesTable').dataTable( {
				"processing": true,
				"serverSide": true,
				"ajax": "ajax_pastes.php"
			} );
		} );
		</script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
  </body>
</html>