<?php

class Questions extends Form {        
    function __construct(PDO &$authdb, &$person_id) {
        $this->authdb = &$authdb;
        $this->person_id = &$person_id;
    }
    
    function newQuestions() {
        $stmt = $this->authdb->prepare('SELECT * FROM questions q LEFT JOIN answers a ON (a.answered_by=:person_id AND q.question_id=a.question_id AND a.answer_id is null);');
        $stmt->bindParam(':person_id', $this->person_id);
        $success = $stmt->execute();
        if ($success === true) {
            $this->questions = $stmt->fetchAll();
            return true;
        } else {
            return false;
        }
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
            if (empty($this->variables[$value]) && $this->questions[$i]['optional'] = 'f') {
                $error = &$this->error($value);
                $error = 'required';
                $form_completed_status = false;
            }
        }
        
        if ($form_completed_status === true) {
            echo 'found';
            $stmt = $this->authdb->prepare('INSERT INTO authentication (person_id,username,password,pin) VALUES (?,?,?,?)');
            $hashed_password = password_hash($this->variables['password'], PASSWORD_DEFAULT, array('cost' => 12));
            $hashed_pin = password_hash($this->variables['pin'], PASSWORD_DEFAULT, array('cost' => 12));
            $success = $stmt->execute(array($person_id, $this->variables['username'], $hashed_password, $hashed_pin));
            if ($success === true) {
                return true;
            } else {
                $error = $stmt->errorInfo();
                $this->var_errors['auth'] = $error[0].' '.$error[1].' '.$error[2];
                return false;
            }
        }
        
        return $form_completed_status;
    }
    
}
