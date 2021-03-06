<?php
/*
Copyright (C) 2019  Jared H. Hudson, Zac Slade

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

    // class is autoloaded from include/Auth.php
    $db = new DB();

    $tc = new Timeclock($db->authdb);
    $tc_req = $tc->statusAll();
}
?>
<!DOCTYPE html>
<html class="home">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="css/style.css" rel="stylesheet" type="text/css">
<title>Time Clock Status</title>
</head>
<body>
<div id="main_container">
    <div id="header">
        <div id="login_btn">
            <a href="login.php" class="btn btn-primary"
                style="text-decoration=none;">Login</a>
        </div>
        <h1>Who is here?</h1>
    </div>

    <div id="currentusers">
        <table>
<?php
        // Generate the table of logged in folks
        if ($tc_req) {
?>
            <tr>
                <th>Who</th>
                <th>When</th>
                <th>Why</th>
                <th>How Long</th>
           <!-- <th>With What</th> !-->
<?php
            foreach ($tc->clockedin_people as $i => $entry) {
?>
                <tr>
                    <td><?php echo $entry['first_name']; echo ' '; echo $entry['last_name']; ?></td>
                    <td><?php echo $entry['clock_in']; ?></td>
                    <td><?php echo $entry['purpose']; ?></td>
                    <td><?php echo $entry['since_clockedin']; ?></td>
               <!-- <td>With What</td> !-->
<?php
            }
        } else {
?>
            <tr><th><h2>No one is currently clocked in!</h2></th>
<?php
        }
?>
        </table>
    </div>

    <div id="footer">Copyright Free Geek of Arkansas 2019</div>
</div>
</body>
</html>
