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
        $people->apply();
        $auth->apply();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<link href="css/style.css" rel="stylesheet" type="text/css">
<title>Time clock</title>
</head>
<body>
<div class="login">
	<h1>New application</h1>
    <form method="post" enctype="application/x-www-form-urlencoded" action="new.php">
  <?php
  $people->showInput('first_name', 'First Name', 'text', true);
  $keys = array('middle_name','last_name','address1','address2','city','state','state_other','zipcode','country','phone','email');
  $placeholders = array('Middle Name','Last Name','Address Line #1','Address Line #2','City','State','State','Zipcode','Country','Phone','Email');
  foreach ($keys as $i => $value) {
      if ($value === 'country') {
          $countries = new Countries();
          $countries->showList();
      } else {
        $people->showInput($value, $placeholders[$i]);
      }
  }
  $people->showInput('dob', 'DOB', 'date', true);
            
//            ,'guardian_first_name','guardian_middle_name', 'guardian_last_name','guardian_phone','guardian_relationship',
//            'emergency_first_name','emergency_middle_name', 'emergency_last_name','emergency_phone','emergency_relationship',
//            'type');     	
?>
     	<input type="text" name="username" placeholder="Username" value="<?php echo $auth->get('username'); ?>" /><?php showError($auth->error('username')); ?>     	
     	<input type="password" name="pin" placeholder="PIN" size=4 value="<?php echo $auth->get('pin'); ?>" required="required" /><?php showError($auth->error('pin')); ?>
     	<p>
     	<input type="password" name="password" placeholder="Password" value="<?php echo $auth->get('password'); ?>" required="required" /><?php showError($auth->error('password')); ?>
        <input type="password" name="password2" placeholder="Repeat password" value="<?php echo $auth->get('password2'); ?>" required="required" /><?php showError($auth->error('password2')); ?></p>
        <button type="submit" name="submit" value="apply" class="btn btn-primary btn-block btn-large">Apply</button>        
    </form>
</div>
</body>
</html>