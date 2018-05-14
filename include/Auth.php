<?php

class Auth {        
    function __construct() {
        // Connect to POS and authorization databases      
        
        try {
            global $dbconfig;
            $this->authdb = new PDO($dbconfig['auth']['dsn'], $dbconfig['auth']['username'], $dbconfig['auth']['password'], $dbconfig['auth']['options']);
        } catch(PDOException $e) {
            echo 'Error connecting to auth DB. Caught exception: [',  $e->getMessage(), "]\n";
            die();
        }
        
        $this->variables = array();
    }
    
    function list() {
        $stmt = $this->authdb->query("SELECT first_name,middle_name,last_name from people order by first_name,last_name,middle_name");
        if ($stmt !== false) {
            $results = $stmt->fetchAll();
            $stmt->closeCursor();
        } else {
            $results = false;
        }
        
        return $results;        
    }
    
    function isValid($str) {
        return preg_match('/[A-Za-z0-9]/', $str);
    }
    
    function apply() {
        $keys = array('username','pin','password', 'password2');
        
        foreach ($keys as $i => $value) {
            $this->variables[$value] = getRequest($value);
        }
        
        if ($this->variables['password'] != $this->variables['password2']) {
            $this->var_errors['password2'] = 'passwords do not match';
        }
    }
    
    function get($var) {
        if (isset($this->variables[$var])) {
            return $this->variables[$var];
        }
        return '';
    }
    
    function error($var) {
        if (isset($this->var_errors[$var])) {
            return $this->var_errors[$var];
        }
        return '';
    }
    
    function authenticate($username, $password) {
        if (!$this->isValid($username)) {
            throw new Exception("invalid username", 1);
        }
        
        $stmt = $this->authdb->prepare("SELECT username,firstname,lastname,passwd from ost_staff where username = :username");
        $stmt->execute(array(':username' => $username));
        
        foreach($stmt as $row) {
            echo $row['username'] . " " . $row['firstname'] . " " . $row['lastname'] . " " . $row['passwd'] . " " . "<br>\n";
            if (crypt($password, $row['passwd']) == $row['passwd']) {
                $stmt->closeCursor();
                $stmt = null;
                return true;
            }
        }
        $stmt->closeCursor();
        $stmt = null;
        
        return false;
    }
}
