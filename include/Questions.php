<?php

class Questions extends Form {        
    function __construct(PDO &$authdb, &$person_id) {
        $this->authdb = &$authdb;
        $this->person_id = &$person_id;
        $this->newQuestionCount = 0;
        
        $stmt = $this->authdb->prepare('SELECT * FROM questions q LEFT JOIN answers a ON (a.answered_by=:person_id AND q.question_id=a.question_id);');
        $stmt->bindParam(':person_id', $this->person_id, PDO::PARAM_INT);
        $success = $stmt->execute();
        if ($success === true) {
            $this->questions = $stmt->fetchAll();            
        } else {
            print_r($stmt->errorInfo());
        }
        $stmt->closeCursor();
    }
    
    function newQuestions() {
        if ($this->newQuestionCount == 0) {
            $stmt = $this->authdb->prepare('SELECT count(*) FROM questions q LEFT JOIN answers a ON (a.answered_by=:person_id AND q.question_id=a.question_id) where answer_id is null;');
            $stmt->bindParam(':person_id', $this->person_id);
            $success = $stmt->execute();
            if ($success === true) {
                $results = $stmt->fetchObject();
                $this->newQuestionCount = $results->count;
            } else {
                print_r($stmt->errorInfo());
            }
            $stmt->closeCursor();
        }
        
        if ($this->newQuestionCount > 0) {
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

            if (empty($this->variables[$value]) && $this->questions[$i]['optional'] == null) {
                $error = $this->setError($value, 'required');
                $form_completed_status = false;
            }
        }
        
        if ($form_completed_status === true) {            
            $bool_stmt = $this->authdb->prepare('INSERT INTO answers (question_id, bool_answer, answered_by) VALUES (?,?,?);');
            $text_stmt = $this->authdb->prepare('INSERT INTO answers (question_id, text_answer, answered_by) VALUES (?,?,?);');
            
            foreach ($keys as $i => $value) {
                if (!empty($this->variables[$value])) {
                    if ($this->questions[$i]["answer_type"] === "boolean") {
                        $success = $bool_stmt->execute(array($i, $this->variables[$value], $this->person_id));
                    } else if ($this->questions[$i]["answer_type"] === "text") {
                        $success = $text_stmt->execute(array($i, $this->variables[$value], $this->person_id));
                    } else {
                        print('ERROR: Unexpected answer type.');
                        print('$i = '.$i.'<br>');
                        print_r($this->questions);
                        print_r($this->variables);
                        return false;                        
                    }
                    
                    if ($success !== true) {
                        print('ERROR: Error inserting answers.<br>');
                        $error = $stmt->errorInfo();
                        $this->var_errors['auth'] = $error[0].' '.$error[1].' '.$error[2];
                        return false;
                    }
                }
            }
        }
        return true;
    }
    
}
