<?php

class Purposes extends Form {
	function __construct(PDO &$authdb) {
		$this->authdb =& $authdb;
		$this->variables = array();
	}
	
	function list() {
		$person_id = getSession('person_id');
		$stmt = $this->authdb->prepare("SELECT purpose_id,purpose FROM purposes,people where type_required<person_type and person_id = ?;");
		$success = $stmt->execute(array($person_id));
		
		if ($success === true) {
			if ($stmt !== false) {
				$result = $stmt->fetchAll();
				$stmt->closeCursor();
				return $result;
			}
		}
		return false;
	}
	
    function showList($misc='') {
    	echo "<select name=purpose_id ".$misc.">\n";
    	
    	$keys = $this->list();
    	if ($keys !== false) {
	    	foreach ($keys as $value) {
	   			echo '<option value="'.$value['purpose_id'].'">'.$value['purpose']."</option>\n";
	    	}
    	}
    	echo "</select>\n";
    	showError($this->error('purpose'));
    }
}
