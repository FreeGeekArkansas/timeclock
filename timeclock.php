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
    $tc = new Timeclock($db->authdb, getSession('person_id'));

    if (getRequest('submit') === "Clock-In") {
        $tc->clockin();
    } else if (getRequest('submit') === "Clock-In") {
        $tc->clockout();
    }
    $status = $tc->status();
    
    if ($db->authdb->inTransaction()) {
        $db->authdb->rollBack();
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
<div class="newbox">
<h2>Timeclock</h2>
	
    <form method="post" enctype="application/x-www-form-urlencoded" action="timeclock.php">
    <?php if ($status === 'clocked in') { ?>
    	<button type="submit" name="submit" value="Clock-Out" class="btn btn-primary btn-block btn-large">Clock-Out</button>
    	<?php } else { ?>
    	<button type="submit" name="submit" value="Clock-In" class="btn btn-primary btn-block btn-large">Clock-In</button>
    	<?php } ?>        
    </form>
    <span class="error"><?php echo $tc->error('questions'); ?></span>
</div>
</body>
<script></script>
</html>