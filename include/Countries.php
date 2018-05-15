<?php

class Countries {        
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
        $stmt = $this->authdb->query("SELECT name FROM countries order by name;");
        if ($stmt !== false) {
            $results = $stmt->fetchAll();
            $stmt->closeCursor();
        } else {
            $results = false;
            print_r($this->authdb->errorInfo());
        }
        
        return $results;        
    }
    
    function showList($misc='') {
        echo "<select name=country ".$misc.">\n";
        echo '<option value="" disabled="disabled" selected="selected">Please select a Country</option>'."\n";
        echo '<option value="United States">United States</option>'."\n";
        echo '<option value="Mexico">Mexico</option>'."\n";
        echo '<option value="Canada">Canada</option>'."\n";
        echo '<option value="" disabled="disabled">-----</option>'."\n";
        
        $country=getRequest('country', getSession('country'));
        $keys = $this->list();        
        foreach ($keys as $i => $value) {
            if ($country === $value[0]) {
                echo '<option value="'.$value[0].'" selected>'.$value[0]."</option>\n";
            } else {
                echo '<option value="'.$value[0].'">'.$value[0]."</option>\n";
            }
        }
        echo "</select>\n";        
    }
}
