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


class States extends Form {        
    function __construct(PDO &$authdb) {
        $this->authdb =& $authdb;
        $this->variables = array();
    }
    
    function list() {
        $stmt = $this->authdb->query("SELECT name FROM states order by name;");
        if ($stmt !== false) {
            $results = $stmt->fetchAll();
            $stmt->closeCursor();
        } else {
            $results = false;
        }
        
        return $results;        
    }
    
    function showList($misc = '') {
        $selected=false;
        $state=getRequest('state', getSession('state'));
        echo "<select name=state ".$misc.">\n";
        echo '<option value="" disabled="disabled" selected="selected">Please select a State</option>'."\n";
        
        $keys = Array('Arkansas', 'Oklahoma', 'Missouri');
        foreach ($keys as $i => $value) {
            if ($state === $value) {
                echo '<option value="'.$value.'" selected>'.$value."</option>\n";
                $selected=true;
            } else {
                echo '<option value="'.$value.'">'.$value."</option>\n";
            }
        }
        
        echo '<option value="" disabled="disabled">-----</option>'."\n";
                
        if ($state === 'Other') {
            echo '<option value="Other" onselect="showStateOther();" selected>A state or province not listed here (Other)</option>'."\n";            
        } else {
            echo '<option value="Other" onselect="showStateOther();">A state or province not listed here (Other)</option>'."\n";
        }
        echo '<option value="" disabled="disabled">-----</option>'."\n";
        
        
        $keys = $this->list();
        foreach ($keys as $i => $value) {
            if ($state === $value[0] && $selected === false) {
                echo '<option value="'.$value[0].'" selected>'.$value[0]."</option>\n";
            } else {
                echo '<option value="'.$value[0].'">'.$value[0]."</option>\n";
            }
        }
        echo "</select>\n";        
    }
    
    function lookup($name) {
        static $state_id = 0;
        
        if ($state_id === 0) {
            $stmt = $this->authdb->prepare('SELECT state_id FROM states where name = ?;');
            $vars = array();
            array_push($vars, $name);
            $success = $stmt->execute($vars);
            if ($success === true) {
                $result = $stmt->fetch(PDO::FETCH_OBJ);
                $state_id = $result->state_id;
            }
        }
        
        return $state_id;
    }
}
