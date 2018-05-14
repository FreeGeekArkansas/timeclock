<?php

class People {        
    function __construct() {
        try {
            global $dbconfig;
            $this->db = new PDO($dbconfig['auth']['dsn'], $dbconfig['auth']['username'], $dbconfig['auth']['password'], $dbconfig['auth']['options']);
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
        $keys = array('first_name','middle_name','last_name','address1','address2','city','state','state_other','zipcode','country','country_other','phone',
            'email','dob','guardian_first_name','guardian_middle_name', 'guardian_last_name','guardian_phone','guardian_relationship',
            'emergency_first_name','emergency_middle_name', 'emergency_last_name','emergency_phone','emergency_relationship',
            'type');
        
        foreach ($keys as $i => $value) {
            $this->variables[$value] = getRequest($value);
        }        
    }
    
    function get($var) {
        if (isset($this->variables[$var])) {
            return $this->variables[$var];
        }
        return '';
    }
    
    function error($var) {
        return '';
    }
    
    function showInput($name, $placeholder, $type = 'text', $required = false)
    {
        echo '<input type="'.$type.'" name="'.$name.'" placeholder="'.$placeholder.'" value="'.$this->get($name).'" ';
        if ($required === true) {
            echo 'required="required"'; 
        }
        echo '/>';
        showError($this->error($name));
        echo "\n";
    }
}
