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
        $selected=false;
        $country=getRequest('country', getSession('country'));
        echo "<select name=country ".$misc.">\n";
        echo '<option value="" disabled="disabled" selected="selected">Please select a Country</option>'."\n";
        
        $keys = Array('United States', 'Mexico', 'Canada');
        foreach ($keys as $i => $value) {
            if ($country === $value) {
                echo '<option value="'.$value.'" selected>'.$value."</option>\n";
                $selected=true;
            } else {
                echo '<option value="'.$value.'">'.$value."</option>\n";
            }
        }
        
        echo '<option value="" disabled="disabled">-----</option>'."\n";
        
        if ($country === 'Other') {
            echo '<option value="Other" onselect="showCountryOther();" selected>A country not listed here (Other)</option>'."\n";
        } else {
            echo '<option value="Other" onselect="showCountryOther();">A country not listed here (Other)</option>'."\n";
        }
        echo '<option value="" disabled="disabled">-----</option>'."\n";
        
        $keys = $this->list();        
        foreach ($keys as $i => $value) {
            if ($country === $value[0] && $selected === false) {
                echo '<option value="'.$value[0].'" selected>'.$value[0]."</option>\n";
            } else {
                echo '<option value="'.$value[0].'">'.$value[0]."</option>\n";
            }
        }
        echo "</select>\n";        
    }
}
