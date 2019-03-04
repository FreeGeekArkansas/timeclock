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

class Auth extends Form {        
    function __construct(PDO &$authdb) {
        $this->authdb = &$authdb;       
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
        
    function apply() {
        $keys = array('username','pin','password', 'password2');
        
        foreach ($keys as $value) {
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
        if (!is_numeric($person_id)) {
            $form_completed_status = false;            
        }
        
        if ($form_completed_status === true) {
            $stmt = $this->authdb->prepare('INSERT INTO authentication (person_id,username,password,pin) VALUES (?,?,?,?)');
            $hashed_password = password_hash($this->variables['password'], PASSWORD_DEFAULT, array('cost' => 12));
            $hashed_pin = password_hash($this->variables['pin'], PASSWORD_DEFAULT, array('cost' => 12));
            $success = $stmt->execute(array($person_id, $this->variables['username'], $hashed_password, $hashed_pin));
            if ($success === true) {
                return true;
            } else {
                $error = $stmt->errorInfo();
                $this->var_errors['auth'] = $error[0].' '.$error[1].' '.$error[2];
                return false;
            }            
        }
        
        return $form_completed_status;
    }
        
    function authenticate($username, $password) {
        if (!$this->isValid($username)) {
            throw new Exception("invalid username", 1);
        }
        
        $stmt = $this->authdb->prepare("SELECT * FROM authentication where username = :username LIMIT 1");
        $stmt->execute(array(':username' => $username));

        foreach($stmt as $row) {
            if (password_verify($password, $row['password'])) {
                $stmt->closeCursor();
                $_SESSION['password_used'] = true;
                $_SESSION['authorized'] = true;
                $_SESSION['person_id'] = $row['person_id'];
                return true;
            }
            if (password_verify($password, $row['pin'])) {
                $stmt->closeCursor();
                $_SESSION['pin_used'] = true;
                $_SESSION['authorized'] = true;
                $_SESSION['person_id'] = $row['person_id'];
                return true;
            }            
        }
        
        $stmt->closeCursor();       
        return false;
    }
}
