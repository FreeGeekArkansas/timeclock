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
    $q = new Questions($db->authdb, getSession('person_id'));
    if ($q->newQuestions() == false) {
        header('Location: timeclock.php');
        exit();
    }
    //$a = new Answers($db->authdb, getSession('person_id'));
    
    if (getRequest('submit') === "Apply") {
        $q_success = $q->apply();
        
        if ($q_success) {
            $success = $db->commit();
            if ($success === true) {                
                header('Location: timeclock.php');
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
<title>Questions</title>
</head>
<script></script>
<body>
<a href="logout.php" class="btn btn-primary btn-block btn-large" style="width: 250px">Log out</a>
<div class="newbox">
<h2>Questions</h2>
    <form method="post" enctype="application/x-www-form-urlencoded" action="questions.php">
<?php 
{
    //print_r($q->questions);
    foreach ($q->questions as $value) {
        echo '<h3>'.$value[0].'. '.$value['question']."</h3>\n";

        switch ($value['answer_type']) {
            case 'boolean':
                echo '<div class="nonerror" style="text-align: center">';
                echo $value['bool_answer'];
                echo '<b>Yes</b><input type="radio" name='.$value[0].' value=Yes';
                if ($value['bool_answer'] == true) {
                    echo 'checked';
                }
                echo ">\n";
                echo '<b>No</b><input type="radio" name='.$value[0].' value=No';
                if ($value['bool_answer'] == false) {
                    echo 'checked';
                }
                echo ">\n";
                echo '<span class="error">'.$q->error($value[0])."</span>\n";
                echo '</div>';
                break;
            case 'text':
                echo '<div style="text-align: center">';
                echo '<textarea name='.$value[0].' cols=80 rows=5>'.$value['text_answer']."</textarea>\n";
                echo '</div>';
                break;
        }
        
    }
    
}    
?>
     <button type="submit" name="submit" value="Apply" class="btn btn-primary btn-block btn-large">Apply</button>        
    </form>
    <span class="error"><?php echo $q->error('questions'); ?></span>
	</form>
</div>
</body>
<script></script>
</html>