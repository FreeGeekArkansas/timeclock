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
    function __construct(PDO &$authdb) {
        $this->authdb =& $authdb;
        $this->variables = array();
    }
    
    function list() {
        $stmt = $this->authdb->query("SELECT name FROM countries order by name;");
        if ($stmt !== false) {
            $results = $stmt->fetchAll();
            $stmt->closeCursor();
        } else {
            $results = false;
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
    
    function lookup($name) {
        static $country_id = 0;
        
        if ($country_id === 0) {
            $stmt = $this->authdb->prepare('SELECT country_id FROM countries where name = ?;');
            $vars = array();
            array_push($vars, $name);
            $success = $stmt->execute($vars);
            if ($success === true) {
                $result = $stmt->fetch(PDO::FETCH_OBJ);
                $country_id = $result->country_id;
            }
        }
        
        return $country_id;
    }
}
