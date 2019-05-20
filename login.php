<?php
/*
 * Copyright (C) 2018 Jared H. Hudson
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
{
    include_once 'include/default.inc.php';
    session_start();

    // If user is already logged in then take them to the main page
    if (authorized() === true) {
        header('Location: questions.php');
        exit();
    }

    $username = getRequest('username');
    $password = getRequest('password');
    $submit = getRequest('submit');
    $username_error = '';
    $password_error = '';

    // class is autoloaded from include/Auth.php
    $db = new DB();
    $auth = new Auth($db->authdb);

    if ($submit === 'log in') {
        try {
            $authorized = $auth->authenticate($username, $password);
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 1: // username contained invalid characters
                    $username_error = $e->getMessage();
                    break;
            }
        }

        if (isset($authorized)) {
            if ($authorized === true) {
                header('Location: questions.php');
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
    <a href="new.php" class="btn btn-primary btn-block btn-large">New Volunteer Application</a>
    <form method="post" enctype="application/x-www-form-urlencoded" action="login.php">
        <input  type="text"     name="username" placeholder="Username" class="login"
            value="<?=$username;?>" required="required" /><?=getError($username_error);?>
        <input  type="password" name="password" placeholder="PIN or Password"
            class="login" required="required" /><?=getError($password_error);?>
        <button type="submit"   name="submit" class="btn btn-primary btn-block btn-large" value="log in" >Log in</button>
        <a href="reset.php" class="btn btn-primary btn-block btn-large">Reset PIN/Password</a>
    </form>
</div>
</body>
</html>
