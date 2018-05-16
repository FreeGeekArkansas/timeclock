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


class People {        
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
        $keys = array('first_name','middle_name','last_name','address1','address2','city','state','state_other','zipcode','country','country_other','phone',
            'email','dob','guardian_first_name','guardian_middle_name', 'guardian_last_name','guardian_phone','guardian_relationship',
            'emergency_first_name','emergency_middle_name', 'emergency_last_name','emergency_phone','emergency_relationship',
            'type');
        
        foreach ($keys as $i => $value) {
            $this->variables[$value] = getRequest($value);
        }
        if (getRequest('copy_emergency_guardian') === 'checked') {
            $this->copy('emergency_first_name', 'guardian_first_name');
            $this->copy('emergency_middle_name', 'guardian_middle_name');
            $this->copy('emergency_last_name', 'guardian_last_name');
            $this->copy('emergency_phone', 'guardian_phone');
            $this->copy('emergency_relationship', 'guardian_relationship');
        }
        
        $form_completed_status = true;
        $required_keys = array('first_name','dob','email','emergency_first_name','emergency_phone');
        foreach ($required_keys as $i => $value) {
            if (empty($this->variables[$value])) {
                $this->var_errors[$value] = 'required';
                $form_completed_status = false;
            }
        }
        
        if ($form_completed_status === true) {
            $stmt = $this->authdb->prepare('INSERT INTO people (first_name,middle_name,last_name,address1,address2,city,state,state_other,zipcode,country,country_other,phone,email,dob,guardian_first_name,guardian_middle_name, guardian_last_name,guardian_phone,guardian_relationship,emergency_first_name,emergency_middle_name, emergency_last_name,emergency_phone,emergency_relationship) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');

            $vars = array();
            foreach ($keys as $i => $value) {
                if (isset($this->variables[$value])) {
                    array_push($vars, $this->variables[$value]);
                } else {
                    array_push($vars, null);
                }
            }
            
            $success = $stmt->execute($vars);
            if ($success === true) {
                $_SESSION['person_id'] = lastInsertId();
            } else {
                return false;
            }
        }
        
        return $form_completed_status;
    }
    
    function get($var) {
        if (isset($this->variables[$var])) {
            return $this->variables[$var];
        }
        return '';
    }
    
    function copy($src, $dst) {
        if (isset($this->variables[$src])) {
            $this->variables[$dst] = $this->variables[$src];
        }        
    }
    
    function error($var) {
        if (isset($this->var_errors[$var])) {
            return $this->var_errors[$var];
        }
        return '';
    }
    
    function showInput($name, $placeholder, $type = 'text', $required = false, $misc = '')
    {
        echo '<input '.$misc.' type="'.$type.'" name="'.$name.'" id="'.$name.'" placeholder="'.$placeholder.'" value="'.$this->get($name).'" ';
        if ($required === true) {
            echo 'required="required"'; 
        }
        echo '/>';
        showError($this->error($name));
    }
}
