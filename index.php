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
    /* TODO
     * check for session
     * connect to db
     * get list of volunteers
     * show list of volunteers in drop down menu
     * if volunteer selected, see if volunteer is clocked in or out
     * if clocked in, allow clock out
     * if clocked out, allow clock in
     *
     * allow a selected volunteer to provide a pin to see hour totals, change pin or volunteer info
     * allow an admin to reset pin of volunteer or view volunteer info
     *
     *  new volunteer page asks for info, pin and answer questions
     */
    
    include_once 'include/default.inc.php';
    session_start();
    
    // If user is already logged in then take them to the main page
    if (getSession('authorized') === true) {
        header('Location: main.php');
        exit();
    }
    
    $username=getRequest('username');
    $password=getRequest('password');
    $submit=getRequest('submit');
    $username_error='';
    $password_error='';
    
    // class is autoloaded from include/Auth.php
    $db = new DB();
    $auth = new Auth($db->authdb);
    
    if ($submit === 'log in') {       
        try {
            $authorized = $auth->authenticate($username, $password);
        } catch (Exception $e) {
            switch($e->getCode()) {
                case 1: // username contained invalid characters
                    $username_error = $e->getMessage();
                    break;
            }
        }
        
        if (isset($authorized)) {
            if ($authorized === true) {
                $_SESSION['authorized'] = true;
                header('Location: main.php');
                exit();
            } else {
                $password_error = 'Invalid PIN or password';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html class="login">
<head>
<meta charset="utf-8">
<link href="css/style.css" rel="stylesheet" type="text/css">
<title>Time clock</title>
</head>
<body>
<div class="loginbox">
	<h1>Time clock</h1>
    <form method="post" enctype="application/x-www-form-urlencoded" action="index.php">
     	<input  type="text"     name="username" placeholder="Username" class="login"                               value="<?php echo $username; ?>" required="required" /><?php showError($username_error); ?>
        <input  type="password" name="password" placeholder="PIN or Password"      class="login"                                                                required="required" /><?php showError($password_error); ?>
        <button type="submit"   name="submit"                                      class="btn btn-primary btn-block btn-large" value="log in" >Log in</button>
        <br>
        <a href="new.php" class="btn btn-primary btn-block btn-large">New Volunteer Application</a>
    </form>
</div>
</body>
</html>