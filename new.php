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
    $db = new DB();    
    $auth = new Auth($db->authdb);
    $people = new People($db->authdb);            
    
    if (getRequest('submit') === "apply") {
        $p_success = $people->apply();
        if ($p_success === false) {
            global $coppa_age;
            if ($people->get('age') < $coppa_age) {
                header('Location: coppa.php');
                exit;
            }
        }
        
        $a_success = $auth->apply();
        
        if ($p_success && $a_success) {
            $success = $db->commit();
            if ($success === true) {
                header('Location: questions.php');
                exit();
            } else {
                if ($db->authdb->inTransaction()) {
                    $db->authdb->rollBack();
                }
            }
        } else {
            if ($db->authdb->inTransaction()) {
                $db->authdb->rollBack();
            }
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
<script>
function showStateOther() {
	document.getElementById("state_other").style.display="initial";
}
function hideStateOther() {
	document.getElementById("state_other").style.display="none";
}
function showCountryOther() {
	document.getElementById("country_other").style.display="initial";
}
function hideCountryOther() {
	document.getElementById("country_other").style.display="none";
}
function handleClick(cb) {
	if (cb.checked) {
		document.getElementById("guardian_first_name").value=document.getElementById("emergency_first_name").value;
		document.getElementById("guardian_middle_name").value=document.getElementById("emergency_middle_name").value;
		document.getElementById("guardian_last_name").value=document.getElementById("emergency_last_name").value;
		document.getElementById("guardian_phone").value=document.getElementById("emergency_phone").value;
		document.getElementById("guardian_relationship").value=document.getElementById("emergency_relationship").value;
	}
}
</script>
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
       
    $states = new States($db->authdb);
    $states->showList('class="label" onchange="if (this.selectedIndex==5) showStateOther(); else hideStateOther();"');
    if ($people->get('state') == 'Other') {
        $people->showInput('state_other', 'State (other)', 'text', false, 'class="label" id=state_other');
    } else {
        $people->showInput('state_other', 'State (other)', 'text', false, 'class="label" id=state_other style="display:none"');
    }
    
    $countries = new Countries($db->authdb);
    $countries->showList('class="label" onchange="if (this.selectedIndex==5) showCountryOther(); else hideCountryOther();"');
    if ($people->get('country') === 'Other') {
        $people->showInput('country_other', 'Country (other)', 'text', false, 'class="label" id=country_other');
    } else {
        $people->showInput('country_other', 'Country (other)', 'text', false, 'class="label" id=country_other style="display:none"');
    }
    
?>
<h3 class="label">Emergency Contact's Name and Phone number</h3>
<?php
    $people->showInput('emergency_first_name', 'First Name', 'text', true);
    $people->showInput('emergency_middle_name', 'Middle Name', 'text', false);
    $people->showInput('emergency_last_name', 'Last Name', 'text', false);
    $people->showInput('emergency_phone', 'Phone number', 'text', true);
    $people->showInput('emergency_relationship', 'Relationship', 'text', false);
?>
    <span class="error"><?php echo $people->error('emergency'); ?></span>
<?php
    $dob = $people->get('dob');
    if ($dob !== '') {
        $systemTimeZone = exec('date +%Z');
        $sysdtz = new DateTimeZone($systemTimeZone);
        
        $dt = new DateTime($dob,$sysdtz);        
        $now = new DateTime(null,$sysdtz);
        
        $di = date_diff($dt, $now);
        $age = $di->y;
        if ($age < $age_of_majority) {
?>
            <h3 class="label">Guardian's Name and Phone number</h3>
            <div class="label"><input type="checkbox" name="copy_emergency_guardian" value="checked" <?php echo getRequest('copy_emergency_guardian'); ?> onclick="handleClick(this);">Click to copy emergency contact information to guardian contact information.</div>
<?php
            $people->showInput('guardian_first_name', 'First Name', 'text', true);
            $people->showInput('guardian_middle_name', 'Middle Name', 'text', false);
            $people->showInput('guardian_last_name', 'Last Name', 'text', false);
            $people->showInput('guardian_phone', 'Phone number', 'text', true);
            $people->showInput('guardian_relationship', 'Relationship', 'text', false);            
?>
<div class="label">Since you are not yet <?php echo $age_of_majority; ?>, Free Geek Arkansas needs to know who your parent or guardian is.</div>
<span class="error"><?php echo $people->error('guardian'); ?></span>

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
	    <span class="error"><?php echo $auth->error('auth'); ?></span>        
        <div class="label">Your password is used to authenticate you when you want to change your information.</div>
        <button type="submit" name="submit" value="apply" class="btn btn-primary btn-block btn-large">Apply</button>        
    </form>
    <span class="error"><?php echo $people->error('people'); ?></span>
</div>
</body>
</html>