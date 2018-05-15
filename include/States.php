<?php

class States {        
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
        $stmt = $this->authdb->query("SELECT name FROM states order by name;");
        if ($stmt !== false) {
            $results = $stmt->fetchAll();
            $stmt->closeCursor();
        } else {
            $results = false;
            print_r($this->authdb->errorInfo());
        }
        
        return $results;        
    }
    
    function showList($misc = '') {
        echo "<select name=state ".$misc.">\n";
        echo '<option value="" disabled="disabled" selected="selected">Please select a State</option>'."\n";
        echo '<option value="Arkansas">Arkansas</option>'."\n";
        echo '<option value="Oklohoma">Oklohoma</option>'."\n";
        echo '<option value="Kansas">Kansas</option>'."\n";
        echo '<option value="" disabled="disabled">-----</option>'."\n";
        
        $state=getRequest('state', getSession('state'));
        $keys = $this->list();
        foreach ($keys as $i => $value) {
            if ($state === $value[0]) {
                echo '<option value="'.$value[0].'" selected>'.$value[0]."</option>\n";
            } else {
                echo '<option value="'.$value[0].'">'.$value[0]."</option>\n";
            }
        }
        echo "</select>\n";        
    }
}
