<?php
/*
 * Copyright (C) 2019 Jared H. Hudson
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

    // classes are autoloaded from php files in include/
    $db = new DB();
    // $auth = new Auth($db->authdb);
    $people = new People($db->authdb);

    if (getRequest('submit') === "sendemail") {
        $results = $people->find($_REQUEST ['email'], $_REQUEST ['username']);
    }

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
<title>Time clock :: Reset PIN/Password</title>
</head>
<body>
<div class="newbox">
<h1>Reset PIN/Password</h1>
<h3 class="label">Type your previously registered username or email address. You will be emailed a link to click that will allow you to change your password and PIN.</h3>
    <form method="post" enctype="application/x-www-form-urlencoded" action="reset.php">
            <div style="text-align: center"><input type="text" name="username" placeholder="Username"/> <span style="color: white">or</span> <input type="text" name="email" placeholder="Email address"/></div>
            <button type="submit" name="submit" value="sendemail" class="btn btn-primary btn-block btn-large">Send e-mail</button>
    </form>
    <span class="error"><?=$people->error('people');?></span>
</div>
</body>
</html>