<?php
/*
 * Copyright (C) 2018 Jared H. Hudson
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
class People extends Form {
    function __construct(PDO &$authdb) {
        $this->authdb = & $authdb;
        $this->variables = array ();
        $this->states = new States($authdb);
        $this->countries = new Countries($authdb);
    }
    function list() {
        $stmt = $this->authdb->query("SELECT first_name,middle_name,last_name from people order by first_name,last_name,middle_name");
        if ($stmt !== false) {
            $results = $stmt->fetchAll();
            $stmt->closeCursor();
        } else {
            $results = false;
        }

        return $results;
    }
    function apply() {
        $keys = array (
                'first_name',
                'middle_name',
                'last_name',
                'address1',
                'address2',
                'city',
                'state',
                'state_other',
                'zipcode',
                'country',
                'country_other',
                'phone',
                'email',
                'dob',
                'guardian_first_name',
                'guardian_middle_name',
                'guardian_last_name',
                'guardian_phone',
                'guardian_relationship',
                'emergency_first_name',
                'emergency_middle_name',
                'emergency_last_name',
                'emergency_phone',
                'emergency_relationship',
                'type'
        );

        foreach ($keys as $i => $value) {
            $this->variables [$value] = getRequest($value);
        }
        if (getRequest('copy_emergency_guardian') === 'checked') {
            $this->copy('emergency_first_name', 'guardian_first_name');
            $this->copy('emergency_middle_name', 'guardian_middle_name');
            $this->copy('emergency_last_name', 'guardian_last_name');
            $this->copy('emergency_phone', 'guardian_phone');
            $this->copy('emergency_relationship', 'guardian_relationship');
        }

        $form_completed_status = true;
        $required_keys = array (
                'first_name',
                'dob',
                'email',
                'emergency_first_name',
                'emergency_phone'
        );
        foreach ($required_keys as $i => $value) {
            if (empty($this->variables [$value])) {
                $this->var_errors [$value] = 'required';
                $form_completed_status = false;
            }
        }

        $dob = $this->get('dob');
        if ($dob != '') {
            $systemTimeZone = exec('date +%Z');
            $sysdtz = new DateTimeZone($systemTimeZone);

            $dt = new DateTime($dob, $sysdtz);
            $now = new DateTime(null, $sysdtz);

            $di = date_diff($dt, $now);
            $this->variables ['age'] = $di->y;

            global $coppa_age;
            if ($this->variables ['age'] < $coppa_age) {
                return false;
            }
        }

        if ($form_completed_status === true) {
            // Performing both lookups first so that error states can be set if needed for display purposes
            $state_id = $this->states->lookup($this->variables ['state']);
            if ($state_id !== 0) {
                $this->variables ['state_id'] = $state_id;
            }

            $country_id = $this->countries->lookup($this->variables ['country']);
            if ($country_id !== 0) {
                $this->variables ['country_id'] = $country_id;
            }

            if ($state_id === 0 || $country_id === 0) {
                return false;
            }

            if (isset($this->variables ['guardian_first_name']) && ! empty($this->variables ['guardian_first_name'])) {
                $stmt = $this->authdb->prepare('INSERT INTO people (first_name,middle_name,last_name,phone,person_type) VALUES (?,?,?,?,?)');
                $keys = array (
                        'guardian_first_name',
                        'guardian_middle_name',
                        'guardian_last_name',
                        'guardian_phone',
                        'guardian_type'
                );
                $vars = array ();
                $this->variables ['guardian_type'] = 'guardian contact';
                foreach ($keys as $i => $value) {
                    if (isset($this->variables [$value])) {
                        array_push($vars, $this->variables [$value]);
                    } else {
                        array_push($vars, null);
                    }
                }
                $success = $stmt->execute($vars);
                if ($success === true) {
                    $this->variables ['guardian_id'] = $this->authdb->lastInsertId('people_person_id_seq');
                } else {
                    $error = $stmt->errorInfo();
                    $this->var_errors ['guardian'] = $error [0] . ' ' . $error [1] . ' ' . $error [2];
                }
            }

            $stmt = $this->authdb->prepare('INSERT INTO people (first_name,middle_name,last_name,phone,person_type) VALUES (?,?,?,?,?)');
            $keys = array (
                    'emergency_first_name',
                    'emergency_middle_name',
                    'emergency_last_name',
                    'emergency_phone',
                    'emergency_type'
            );
            $vars = array ();
            $this->variables ['emergency_type'] = 'emergency contact';
            foreach ($keys as $i => $value) {
                if (isset($this->variables [$value])) {
                    array_push($vars, $this->variables [$value]);
                } else {
                    array_push($vars, null);
                }
            }
            $success = $stmt->execute($vars);
            if ($success === true) {
                $this->variables ['emergency_id'] = $this->authdb->lastInsertId('people_person_id_seq');
            } else {
                echo 'fail';
                $error = $stmt->errorInfo();
                $this->var_errors ['emergency'] = $error [0] . ' ' . $error [1] . ' ' . $error [2];
            }
            $stmt->closeCursor();

            $stmt = $this->authdb->prepare('INSERT INTO people (first_name,middle_name,last_name,address1,address2,city,state_id,state_other,zipcode,country_id,country_other,phone,email,dob,guardian_id,guardian_relationship,emergency_id,emergency_relationship,person_type) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');

            $keys = array (
                    'first_name',
                    'middle_name',
                    'last_name',
                    'address1',
                    'address2',
                    'city',
                    'state_id',
                    'state_other',
                    'zipcode',
                    'country_id',
                    'country_other',
                    'phone',
                    'email',
                    'dob',
                    'guardian_id',
                    'guardian_relationship',
                    'emergency_id',
                    'emergency_relationship',
                    'type'
            );
            $vars = array ();
            $this->variables ['type'] = 'volunteer';
            foreach ($keys as $i => $value) {
                if (isset($this->variables [$value]) && ! empty($this->variables [$value])) {
                    $stmt->bindValue($i + 1, $this->variables [$value]);
                } else {
                    $stmt->bindValue($i + 1, null, PDO::PARAM_NULL);
                }
            }
            $success = $stmt->execute();

            if ($success === true) {
                $_SESSION ['person_id'] = $this->authdb->lastInsertId('people_person_id_seq');
                return true;
            } else {
                $error = $stmt->errorInfo();
                if ($error [0] === "23505") {
                    $this->var_errors ['people'] = 'E-mail address already in-use.';
                } else {
                    $this->var_errors ['people'] = $error [0] . ' ' . $error [1] . ' ' . $error [2];
                }
                return false;
            }
        }

        // Failed to complete form correctly
        return false;
    }
    function find($email='', $username='') {
        $stmt = $this->authdb->prepare("select p.email,a.username from people as p inner join authentication as a using (person_id) where email = :email or username = :username;");

        if ($stmt !== false) {
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':username', $username);
            $results = $stmt->fetchAll();
            $stmt->closeCursor();
        } else {
            
            $results = false;
        }

        return $results;
    }
    function showInput($name, $placeholder, $type = 'text', $required = false, $misc = '') {
        echo '<input ' . $misc . ' type="' . $type . '" name="' . $name . '" id="' . $name . '" placeholder="' . $placeholder . '" value="' . $this->get($name) . '" ';
        if ($required === true) {
            echo 'required="required"';
        }
        echo '/>';
        showError($this->error($name));
    }
    function showStateList() {
        $this->states->showList('class="label" onchange="if (this.selectedIndex==5) showStateOther(); else hideStateOther();"');
    }
    function showCountryList() {
        $this->countries->showList('class="label" onchange="if (this.selectedIndex==5) showCountryOther(); else hideCountryOther();"');
    }
}
