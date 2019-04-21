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
    function __construct(PDO &$authdb, &$person_id = NULL) {
        $this->authdb = &$authdb;
        $this->person_id = &$person_id;
        $this->timeclock = Array();
    }

    function status($limit = 'ALL', $offset = 0) {
        if (is_numeric($limit)) {
            $stmt = $this->authdb->prepare("SELECT date_trunc('seconds',t.clock_in)::timestamp as clock_in,date_trunc('seconds',t.clock_out)::timestamp as clock_out,p.purpose,date_trunc('seconds', age(t.clock_out,t.clock_in)) as length FROM timeclock as t,purposes as p WHERE person_id=:person_id and t.purpose_id = p.purpose_id ORDER BY person_id, clock_in DESC limit :limit OFFSET :offset;");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        } else {
            $stmt = $this->authdb->prepare('SELECT t.*,p.purpose,date_trunc(\'seconds\', age(t.clock_out,t.clock_in)) as length FROM timeclock as t,purposes as p WHERE person_id=:person_id and t.purpose_id = p.purpose_id ORDER BY person_id, clock_in DESC OFFSET :offset;');
        }
        $stmt->bindParam(':person_id', $this->person_id, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $success = $stmt->execute();
        if ($success === true) {
            if ($stmt->rowCount()) {
                $this->timeclock = $stmt->fetchAll();
                if (!empty($this->timeclock[0]['clock_in']) && empty($this->timeclock[0]['clock_out'])) {
                    return true;
                }
            }
        } else {
            print_r($stmt->errorInfo());
        }
        $stmt->closeCursor();
        return false;
    }

    /* This function, `statusAll`, should give a list of all the signed in people in the
     * application. This data will be used as the main data seen when
     * navigating to the timeclock site.
     */
    function statusAll($limit = 'ALL', $offset = 0) {
        if (is_numeric($limit)) {
            $stmt = $this->authdb->prepare("SELECT people.first_name as first_name,people.last_name as last_name,date_trunc('seconds',t.clock_in)::timestamp as clock_in,date_trunc('seconds',t.clock_out)::timestamp as clock_out,p.purpose as purpose FROM purposes as p, timeclock as t LEFT OUTER JOIN people USING (person_id) WHERE t.clock_out is NULL AND (t.purpose_id = p.purpose_id) ORDER BY clock_in DESC limit :limit OFFSET :offset;");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        } else {
            $stmt = $this->authdb->prepare("SELECT people.first_name as first_name,people.last_name as last_name,date_trunc('seconds',t.clock_in)::timestamp as clock_in,date_trunc('seconds',t.clock_out)::timestamp as clock_out,p.purpose as purpose FROM purposes as p, timeclock as t LEFT OUTER JOIN people USING (person_id) WHERE t.clock_out is NULL AND (t.purpose_id = p.purpose_id) ORDER BY clock_in DESC OFFSET :offset;");
        }
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $success = $stmt->execute();

        if ($success === true) {
            if ($stmt->rowCount()) {
                $this->clockedin_people = $stmt->fetchAll();
                return true;
            }
        } else {
            print_r($stmt->errorInfo());
        }
        $stmt->closeCursor();
        return false;
    }

    function clockin() {
        $purpose_id = getRequest('purpose_id', 1);
        $stmt = $this->authdb->prepare('INSERT INTO timeclock (clock_in, purpose_id, person_id) VALUES (\'now\',?,?);');
        $success = $stmt->execute(array($purpose_id, $this->person_id));
        if ($success !== true) {
            print('<b>ERROR: Error in clockin.</b><br>');
            print_r($stmt->errorInfo());
            return false;
        }

        return true;
    }

    function clockout() {
        $clocked_in = $this->status();
        if ($clocked_in) {
            $timeclock_id = $this->timeclock[0]['timeclock_id'];
            $stmt = $this->authdb->prepare('UPDATE timeclock SET clock_out = \'now\' WHERE timeclock_id = :timeclock_id;');
            $stmt->bindParam(':timeclock_id', (int)$timeclock_id, PDO::PARAM_INT);
            $success = $stmt->execute();

            if ($success !== true) {
                    print('<b>ERROR: Error in clockout.</b><br>');
                    print_r($stmt->errorInfo());
                    return false;
            }
            return true;
        }
        return false;
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
