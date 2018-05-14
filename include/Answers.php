<?php

class Answers {        
    function __construct() {
        // Connect to POS and authorization databases      
        
        try {
            global $dbconfig;
            $this->authdb = new PDO($dbconfig['auth']['dsn'], $dbconfig['auth']['username'], $dbconfig['auth']['password'], $dbconfig['auth']['options']);
        } catch(PDOException $e) {
            echo 'Error connecting to auth DB. Caught exception: [',  $e->getMessage(), "]\n";
            die();
        }
    }   
}
