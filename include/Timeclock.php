<?php
/*
 Copyright (C) 2019 Jared H. Hudson
 
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

class Timeclock extends Form {        
    function __construct(PDO &$authdb, &$person_id) {
        $this->authdb = &$authdb;
        $this->person_id = &$person_id;
    }
    
    function status() {
        $stmt = $this->authdb->prepare('SELECT DISTINCT * FROM timeclock where person_id=:person_id;');
        $stmt->bindParam(':person_id', $this->person_id, PDO::PARAM_INT);
        $success = $stmt->execute();
        if ($success === true) {
            $this->timeclock = $stmt->fetchAll();
        } else {
            print_r($stmt->errorInfo());
        }
        $stmt->closeCursor();
        
        print_r($this->timeclock);
    }
    
    function clockin() {
        $this->authdb->prepare('INSERT INTO timeclock (clock_in, purpose_id, person_id) VALUES (\'now\',?,?);');
    }
    
    function clockout() {
        
    }
    
    function apply() {
        $keys = array();
        $l = count($this->questions);
        for ($i=0; $i < $l; ++$i) {
            array_push($keys, $this->questions[$i][0]);
        }
        
        $form_completed_status = true;
        foreach ($keys as $i => $value) {
            $this->variables[$value] = getRequest($value);

            if (empty($this->variables[$value]) && $this->questions[$i]['optional'] == null) {
                $this->setError($value, 'required');
                $form_completed_status = false;
            }
        }
        
        if ($form_completed_status === true) {
            $bool_stmt = $this->authdb->prepare('INSERT INTO answers (question_id, bool_answer, answered_by) VALUES (?,?,?);');
            $text_stmt = $this->authdb->prepare('INSERT INTO answers (question_id, text_answer, answered_by) VALUES (?,?,?);');
            
            foreach ($keys as $i => $value) {
                if ($this->questions[$i]["answer_type"] === "boolean") {
                    $stmt = $bool_stmt;
                } else if ($this->questions[$i]["answer_type"] === "text") {
                    $stmt = $text_stmt;
                } else {
                    print('ERROR: Unexpected answer type.');
                    print('$i = '.$i.'<br>');
                    print('$value = '.$value.'<br>');
                    print_r($this->questions);
                    print_r($this->variables);
                    return false;                        
                }
                $success = $stmt->execute(array($value, $this->variables[$value], $this->person_id));
                if ($success !== true) {
                    print('<b>ERROR: Error inserting answers.</b><br>');
                    print_r($stmt->errorInfo());
                    return false;
                }
            }
        }
        return true;
    }
    
}
