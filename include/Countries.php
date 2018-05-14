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
    
    function showList() {
        echo "<select name=country>\n";
        echo '<option value="" disabled="disabled" selected="selected">Please select a Country</option>'."\n";
        $keys = $this->list();
        foreach ($keys as $i => $value) {   
            echo '<option value="'.$value[0].'1">'.$value[0]."</option>\n";
        }
        echo "</select>\n";        
    }
}
