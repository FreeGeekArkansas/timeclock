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

    $tc = new TimeClock($db->authdb);
    $tc_req = $tc->statusAll();
}
?>
<!DOCTYPE html>
<html class="home">
<head>
<meta charset="utf-8">
<link href="css/style.css" rel="stylesheet" type="text/css">
<title>Time Clock Status</title>
</head>
<body>
<div class="main-container">
    <div class="btn btn-primary"><a href="login.php">Login</a></div>
    <h1>Who is here?</h1>
    <div class="status-list">
        <table id="clockedin_people">
<?php
        // Generate the table of logged in folks
        if ($tc_req) {
?>
            <tr>
                <th>Who</th>
                <th>What</th>
                <th>When</th>
<?php
            foreach ($tc->clockedin_people as $i => $entry) {
?>
                <tr>
                    <td><?php echo $entry['first_name']; echo ' '; echo $entry['last_name']; ?></td>
                    <td><?php echo $entry['purpose']; ?></td>
                    <td><?php echo $entry['clock_in']; ?></td>
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
</div>
</body>
</html>
