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
session_start();

if (isset($_SESSION['login'])) {
// Do nothing	
} else {
    header("Location: .");
    exit();
}

if (isset($_GET['logout'])) {
    if (isset($_SESSION['login']))
        unset($_SESSION['login']);
    
    session_destroy();
    header("Location: .");
    exit();
}

$date = date('jS F Y');
$ip   = $_SERVER['REMOTE_ADDR'];
require_once('../config.php');
$con = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);

if (mysqli_connect_errno()) {
    $sql_error = mysqli_connect_error();
    die("Unable connect to database");
}

$query = "SELECT @last_id := MAX(id) FROM admin_history";

$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result)) {
    $last_id = $row['@last_id := MAX(id)'];
}

$query  = "SELECT * FROM admin_history WHERE id=" . Trim($last_id);
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result)) {
    $last_date = $row['last_date'];
    $last_ip   = $row['ip'];
}

if ($last_ip == $ip) {
    if ($last_date == $date) {
        
    } else {
        $query = "INSERT INTO admin_history (last_date,ip) VALUES ('$date','$ip')";
        mysqli_query($con, $query);
    }
} else {
    $query = "INSERT INTO admin_history (last_date,ip) VALUES ('$date','$ip')";
    mysqli_query($con, $query);
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paste - Tasks</title>
	<link rel="shortcut icon" href="favicon.ico">
    <link href="css/paste.css" rel="stylesheet" type="text/css" />
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
			<div class="row">
				<div class="col-md-12">
				  <ul class="panel quick-menu clearfix">
					<li class="col-xs-3 col-sm-2 col-md-1">
					  <a href="dashboard.php"><i class="fa fa-home"></i>Dashboard</a>
					</li>
					<li class="col-xs-3 col-sm-2 col-md-1">
					  <a href="configuration.php"><i class="fa fa-cogs"></i>Configuration</a>
					</li>
					<li class="col-xs-3 col-sm-2 col-md-1">
					  <a href="interface.php"><i class="fa fa-eye"></i>Interface</a>
					</li>
					<li class="col-xs-3 col-sm-2 col-md-1">
					  <a href="admin.php"><i class="fa fa-user"></i>Admin Account</a>
					</li>
					<li class="col-xs-3 col-sm-2 col-md-1">
					  <a href="pastes.php"><i class="fa fa-clipboard"></i>Pastes</a>
					</li>
					<li class="col-xs-3 col-sm-2 col-md-1">
					  <a href="users.php"><i class="fa fa-users"></i>Users</a>
					</li>
					<li class="col-xs-3 col-sm-2 col-md-1">
					  <a href="ipbans.php"><i class="fa fa-ban"></i>IP Bans</a>
					</li>
					<li class="col-xs-3 col-sm-2 col-md-1">
					  <a href="stats.php"><i class="fa fa-line-chart"></i>Statistics</a>
					</li>
					<li class="col-xs-3 col-sm-2 col-md-1">
					  <a href="ads.php"><i class="fa fa-gbp"></i>Ads</a>
					</li>
					<li class="col-xs-3 col-sm-2 col-md-1">
					  <a href="pages.php"><i class="fa fa-file"></i>Pages</a>
					</li>
					<li class="col-xs-3 col-sm-2 col-md-1">
					  <a href="sitemap.php"><i class="fa fa-map-signs"></i>Sitemap</a>
					</li>
					<li class="col-xs-3 col-sm-2 col-md-1 menu-active">
					  <a href="tasks.php"><i class="fa fa-tasks"></i>Tasks</a>
					</li>
				  </ul>
				</div>
			</div>
			<!-- End Menu -->
    
			<?php
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				//POST HANDLER
				
			}
			if (isset($_GET['expired'])) {
				$query  = "SELECT * FROM pastes";
				$result = mysqli_query($con, $query);
				while ($row = mysqli_fetch_array($result)) {
					$p_expiry = Trim($row['expiry']);
					$p_id     = Trim($row['id']);
					if ($p_expiry == "NULL" || $p_expiry == "SELF") {
					} else {
						$input_time   = $p_expiry;
						$current_time = mktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y"));
						if ($input_time < $current_time) {
							$query = "DELETE FROM pastes where id='$p_id'";
							mysqli_query($con, $query);
						}
					}
				}
				if (mysqli_errno($con)) {
					$msg = '<div class="paste-alert alert6">
				 ' . mysqli_error($con) . '
				 </div>';
					
				} else {
					$msg = '<div class="paste-alert alert3">
					 All expired pastes have been deleted
					 </div>';
				}
			}
			if (isset($_GET['all_pastes'])) {
				$query = "DELETE FROM pastes";
				mysqli_query($con, $query);
				
				if (mysqli_errno($con)) {
					$msg = '<div class="paste-alert alert6">
				 ' . mysqli_error($con) . '
				 </div>';
					
				} else {
					$msg = '<div class="paste-alert alert3">
					 All pastes have been deleted
					 </div>';
				}
				
			}
			if (isset($_GET['not_verfied'])) {
				$query = "DELETE FROM users where verified='0'";
				mysqli_query($con, $query);
				
				if (mysqli_errno($con)) {
					$msg = '<div class="paste-alert alert6">
				 ' . mysqli_error($con) . '
				 </div>';
					
				} else {
					$msg = '<div class="paste-alert alert3">
					 All unverified accounts have been deleted
					 </div>';
				}
			}
			if (isset($_GET['admin_history'])) {
				$query = "DELETE FROM admin_history";
				mysqli_query($con, $query);
				
				if (mysqli_errno($con)) {
					$msg = '<div class="paste-alert alert6">
				 ' . mysqli_error($con) . '
				 </div>';
					
				} else {
					$msg = '<div class="paste-alert alert3">
					 Admin history has been cleared
					 </div>';
				}
			}
			?>
			
			<!-- Start Tasks -->
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-widget">
						<div class="panel-body">
						 <div class="panel-title">Maintenance tasks</a></div>
						<?php if (isset($msg)) echo $msg; ?>
                             <a href="?expired" class="btn btn-default btn-block">Delete all expired pastes</a>
								<br />
                             <a href="?admin_history" class="btn btn-info btn-block">Clear admin history</a>
								<br />
                             <a href="?not_verfied" class="btn btn-warning btn-block">Delete unverified accounts</a>
								<br />
                             <a href="?all_pastes" class="btn btn-danger btn-block">Delete all pastes</a>
						</div>
					</div>
				</div>
			</div>
			<!-- End Tasks -->
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
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
  </body>
</html>