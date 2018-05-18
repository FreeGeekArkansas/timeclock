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
}
?>
<!DOCTYPE html>
<html class="coppa">
<head>
<meta charset="utf-8">
<link href="css/style.css" rel="stylesheet" type="text/css">
<title>COPPA</title>
</head>
<body>
<h3 class="nonerror">In order to comply with <a class="nonerror" href="http://www.coppa.org">COPPA</a> (Children's Online Privacy Protection Act) we cannot collect personal information from an individual under the age of <?php echo $coppa_age; ?>.
A parent or guardian must provide consent for us to store the name, date of birth, address, email address of anyone under the age of <?php echo $coppa_age; ?>. Please email us at <a class="nonerror" href="mailto:<?php echo $contact_email; ?>"><?php echo $contact_email; ?></a> or call us at <?php echo $contact_phone; ?> for more information.</h3>
<a class="nonerror" href="index.php"><span class="noerror">Return to login page</span></a>
</body>
</html>