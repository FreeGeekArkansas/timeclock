<?php
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
    if (isset($_SESSION['authorized']) && $_SESSION['authorized'] === true) {
        header('Location: main.php');
        exit();
    }
    
    if (isset($_REQUEST['username'])) {
        $username = $_REQUEST['username'];
    } else {
        $username='';
    }
    
    $username_error='';
    $password_error='';
    
    // class is autoloaded from include/Auth.php
    $auth = new Auth();
    
    if (isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'login') {       
        try {
            $authorized = $auth->authenticate($_REQUEST['username'], $_REQUEST['password']);
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
                $password_error = 'Invalid password';
            }
        }
    } else {
        try {
            $list = $auth->list();
        } catch (Exception $e) {
            switch($e->getCode()) {
                case 1: // username contained invalid characters
                    $username_error = $e->getMessage();
                    break;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<link href="css/style.css" rel="stylesheet" type="text/css">
<title>Timesheet</title>
</head>
<body>
<div class="login">
	<h1>Timesheet</h1>
    <form method="post" enctype="application/x-www-form-urlencoded" action="login.php">
		<select name="name">
<?php 
if (!empty($list)) {
    echo '<option value="'.$list["first_name"].' '.$list["middle_name"].' '.$list["last_name"];
}

?>		
		</select>
<!--     	<input type="text" name="username" placeholder="Username" value="<?php echo $username; ?>" required="required" /><?php showError($username_error); ?> -->
        <input type="password" name="password" placeholder="Password" required="required" /><?php showError($password_error); ?>
        <button type="submit" name="submit" value="login" class="btn btn-primary btn-block btn-large">Login</button>
    </form>
</div>
</body>
</html>