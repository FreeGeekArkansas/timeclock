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
    
    // classes are autoloaded from php files in include/ 
    $auth = new Auth();
    $people = new People();

    if (getRequest('submit') === "apply") {        
        $person_id = $people->apply();
        
        $success = $auth->apply();        
        if ($success === true) {
            header('Location: questions.php');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html class="new">
<head>
<meta charset="utf-8">
<link href="css/style.css" rel="stylesheet" type="text/css">
<title>Time clock</title>
</head>
<body>
<div class="newbox">
	<h1>New application</h1>
    <form method="post" enctype="application/x-www-form-urlencoded" action="new.php">
<?php

    $people->showInput('first_name', 'First Name', 'text', true);
    $people->showInput('middle_name', 'Middle Name', 'text', false);
    $people->showInput('last_name', 'Last Name', 'text', false);
    echo '<div class="label">Date of Birth: ';
    $people->showInput('dob', 'DOB', 'date', true);
    echo "</div>\n";
    
    $people->showInput('email', 'Email address', 'text', false);
    $people->showInput('phone', 'Phone number', 'text', false);
    $people->showInput('address1', 'Address Line #1');
    $people->showInput('address2', 'Address Line #2');
    $people->showInput('city', 'City');
    $people->showInput('zipcode', 'Zipcode');
       
    $states = new States();
    $states->showList('class="label"');
    
    $countries = new Countries();
    $countries->showList('class="input"');
?>
<h3 class="label">Emergency Contact's Name and Phone number</h3>
<?php
    $people->showInput('emergency_first_name', 'First Name', 'text', true);
    $people->showInput('emergency_middle_name', 'Middle Name', 'text', false);
    $people->showInput('emergency_last_name', 'Last Name', 'text', false);
    $people->showInput('emergency_phone', 'Phone number', 'text', false);
    $people->showInput('emergency_relationship', 'Relationship', 'text', false);
    
    //            ,'guardian_first_name','guardian_middle_name', 'guardian_last_name','guardian_phone','guardian_relationship',
    //            'emergency_first_name','emergency_middle_name', 'emergency_last_name','emergency_phone','emergency_relationship',
    //            'type');
    
    $dob = $people->get('dob');
    if ($dob !== null) {
        $systemTimeZone = exec('date +%Z');
        $sysdtz = new DateTimeZone($systemTimeZone);
        
        $dt = new DateTime($dob,$sysdtz);        
        $now = new DateTime(null,$sysdtz);
        
        $di = date_diff($dt, $now);
        $age = $di->y;
        if ($age < 18) {
?>
            <h3 class="label">Guardian's Name and Phone number</h3>
<?php 
            $people->showInput('guardian_first_name', 'First Name', 'text', false);
            $people->showInput('guardian_middle_name', 'Middle Name', 'text', false);
            $people->showInput('guardian_last_name', 'Last Name', 'text', false);
            $people->showInput('guardian_phone', 'Phone number', 'text', false);
            $people->showInput('guardian_relationship', 'Relationship', 'text', false);
?>
<div class="label">Since you are not yet 18 we need to know who your parent or guardian is.</div>

<?php
        }
    }
?>

		<div><h3 class="label">Your username, PIN and Password</h3>
     	<input type="text" name="username" placeholder="Username" value="<?php echo $auth->get('username'); ?>" required="required" /><?php showError($auth->error('username')); ?>
     	<input type="password" name="pin" placeholder="PIN" size=4 maxlength=4 value="<?php echo $auth->get('pin'); ?>" required="required" /><?php showError($auth->error('pin')); ?><div class="label">The pin is a 4 digit number that can be used to quickly clock-in or out.</div>
     	</div>
     	<p>
     	<input type="password" name="password" placeholder="Password" value="<?php echo $auth->get('password'); ?>" required="required" /><?php showError($auth->error('password')); ?>
        <input type="password" name="password2" placeholder="Repeat password" value="<?php echo $auth->get('password2'); ?>" required="required" /><?php showError($auth->error('password2')); ?>
        <div class="label">Your password is used to authenticate you when you want to change your information.</div></p>
        <button type="submit" name="submit" value="apply" class="btn btn-primary btn-block btn-large">Apply</button>        
    </form>
</div>
</body>
</html>