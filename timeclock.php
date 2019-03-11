<?php
/*
 Copyright (C) 2018  Jared H. Hudson
 
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 
 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
{
    include_once 'include/default.inc.php';
    session_start();

    // If user is not already logged in then take them to login page
    if (authorized() == false) {
        header('Location: index.php');
        exit();
    }

    $db = new DB();
    $purposes = new Purposes($db->authdb);
    $tc = new Timeclock($db->authdb, getSession('person_id'));
    $clocked_in = $tc->status(1);

    if (!$clocked_in && getRequest('submit') === "Clock-In") {
        $tc->clockin();
    } else if (getRequest('submit') === "Clock-Out") {
        $tc->clockout();
    }
    $clocked_in = $tc->status(20);

    if ($db->authdb->inTransaction()) {
        $db->authdb->commit();
    }
}
?>
<!DOCTYPE html>
<html class="new">
<head>
<meta charset="utf-8">
<link href="css/style.css" rel="stylesheet" type="text/css">
<title>Timeclock</title>
</head>
<script></script>
<body>
<a href="logout.php" class="btn btn-primary btn-block btn-large" style="width: 250px">Log out</a>
<div class="timeclockbox">
<h2><?php echo $_SESSION['username']; ?>'s Timeclock</h2>
    <form method="post" enctype="application/x-www-form-urlencoded" action="timeclock.php">
    <table>
    	<tr><td>Clock-In</td><td>Clock-Out</td><td>Length</td><td>Purpose</td><td>Action</td></tr>
<?php if (!$clocked_in) { ?>
    	<tr>
    	<td></td>
    	<td></td>
    	<td></td>
    	<td><?php $purposes->showList(); ?></td>
    	<td><button type="submit" name="submit" value="Clock-In" class="btn btn-primary btn-block btn-large">Clock-In</button></td>
		</tr>
<?php } ?>
<?php foreach ($tc->timeclock as $i => $entry) { ?> 
    	<tr>
    	<td><?php echo $entry['clock_in']; ?></td>
    	<td><?php echo $entry['clock_out']; ?></td>
    	<td><?php echo $entry['length']; ?></td>
    	
	<?php if ($i === 0 && $clocked_in) { ?>
	    <td><?php echo $entry['purpose']; ?></td>
    	<td><button type="submit" name="submit" value="Clock-Out" class="btn btn-primary btn-block btn-large">Clock-Out</button></td>
    
	<?php } else { ?>
	    <td><?php echo $entry['purpose']; ?></td>
    	<td></td>
	<?php } ?>
<?php } ?>
    </table>
    </form>
    <span class="error"><?php echo $tc->error('questions'); ?></span>
</div>
</body>
<script></script>
</html>