<?php
	include('time_checker.php');
	session_start();
	if ($_SESSION['login']==0) header('Location: login_page.php');
?>

<html>
	<head>
		<title>Healthcare System</title>
		<link rel="stylesheet" type="text/css" href="CSS/dboardCSS.css">
	</head>
	<body>
		<div id = "profilePic">
			<img src = "sample.png" height = "200" width = "200"/>
		</div>
		
		<div id = "topControls">
			<ul class = "controlsA">
				<form name='searchdoctor' action='search_patient.php' method='post'>
					<li> 
						Search by: 
						<select name="searchtype" id = "option">
							<option name="specialty">Specialty</option>
							<option name="name">Name</option>
							<option name="hospital">Hospital</option>
						</select>
						<input type='text' name='searchinput'> </a>
					</li>
				</form>
				<li> <a href = "notifications.php"> Notifications </a> </li>
				<li> <a href = "logout.php"> Log Out </a> </li>
			</ul>
		</div>
		
		<div class="headbanner"></div>
		
		<div id = "pageControls">
			<ul class = "controlsB">
				<li><a href = "dboardPatient.php">Dashboard</a></li>
				<li><a href = "profile_patient.php">Profile</a></li>
				<li><a href = "appointments_patient.php">Appointment</a></li>
				<li><a href = "dboardPatient.php">Search</a> </li>
				<li><a href = "dboardPatient.php">Settings</a> </li>
			</ul>
		</div>
		
		<div id = "main">
			<?php
				$conn = pg_connect('host=localhost dbname=healthcare user=postgres password=user');

				$username = $_SESSION['username'];
				$query = "SELECT app_patientname, app_number, app_date, app_time, app_doctorname, app_hospital, app_status FROM appointment WHERE app_patientusername='$username' ORDER BY app_date";

				$result = pg_query($query);

				echo '<table><tr>
						<tr>
							<th>App #</th>
							<th>Date</th>
							<th>Time</th>
							<th>Doctor</th>
							<th>Hospital</th>
							<th>Status</th>
							<th>Manage</th>
						</tr>';
						
				$x = 1;
				while ($row = pg_fetch_row($result)) {
					echo '<tr>';
					
					$count = count($row);
					for ($datacounter=0; $datacounter<$count; $datacounter++) {
						$c_row = current($row);
						if($datacounter > 0) {
							echo '<td>' . $c_row . '</td>';
						}
						if($datacounter == 1) {
							$tableID = $c_row;
						}
						next($row);
					}
						
					/*Buttons*/
					echo '<td>
							<form action="cancel_apprequest.php" method="post">';
					
					/*Get time difference in minutes*/
					$timestamp_query = pg_query("SELECT app_date, app_time FROM appointment WHERE app_number='$tableID'");
					$timestamp_array = pg_fetch_array($timestamp_query);
					$time_difference = check_time($timestamp_array[0], $timestamp_array[1]);
					
					/*If the time_difference is more than 24 hours*/
					if($time_difference <= 1440) {
						echo '<button type="button" onclick="alert(\'Cannot cancel appointment!\');">Cancel</button>';
					}
					else {
						echo '<input type="hidden" name="cancelid" value="' . $tableID . '">
							<button type="submit" onclick="return confirm(\'Cancel appointment?\');">Cancel</button>';
					}
					
					echo '</form></td></tr>';
				}
				
				echo '</table>';
				
				pg_close($conn);
			?>
			<a href="request_patient.php">Request Appointment</a>
		</div>
	</body>
</html>