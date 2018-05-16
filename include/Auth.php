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


class Auth {        
    function __construct(DB $db = null) {
        if ($db === null || $db->authdb->getAttribute(PDO::ATTR_CONNECTION_STATUS) !== 'Connection OK; waiting to send.') {
            try {
                global $dbconfig;
                $this->authdb = new PDO($dbconfig['auth']['dsn'], $dbconfig['auth']['username'], $dbconfig['auth']['password'], $dbconfig['auth']['options']);
                $this->authdb->beginTransaction();
            } catch(PDOException $e) {
                echo 'Error connecting to auth DB. Caught exception: [',  $e->getMessage(), "]\n";
                die();
            }
        } else {
            $this->authdb = $db->authdb;
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
        
        $form_completed_status = true;
        if (strlen($this->variables['username']) < 1 || strlen($this->variables['username']) > 8) {
            $this->var_errors['username'] = 'username must be between 1 and 8 characters long.';
            $form_completed_status = false;
        } else {        
            $username_ok = $this->isValid($this->variables['username']);        
            if ($username_ok === false) {
                $this->var_errors['username'] = 'invalid username. Only A-Z, a-z or 0-9 allowed.';
                $form_completed_status = false;
            }
        }
         
        if ($this->variables['password'] != $this->variables['password2']) {
            $this->var_errors['password2'] = 'passwords do not match';
            $form_completed_status = false;
        }
        
        if (strlen($this->variables['password']) < 8) {
            $this->var_errors['password'] = 'password must be at least 8 characters';
            $form_completed_status = false;
        }
        
        if (strlen($this->variables['pin']) !== 4) {
            $this->var_errors['pin'] = 'PIN must be 4 characters long.';
            $form_completed_status = false;
        }
        
        $person_id = getSession('person_id');
        if ($form_completed_status === true && is_numeric($person_id)) {
            $stmt = $this->authdb->prepare('INSERT INTO authentication (person_id,username,password,pin) VALUES (?,?,?)');
            $stmt->execute(array($person_id, $this->variables['username'], $this->variables['password'], $this->variables['pin']));
        }
        
        return $form_completed_status;
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
