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

header("Cache-Control: max-age=0,no-cache,no-store,post-check=0,pre-check=0");

error_reporting(E_ALL);

// Disable on production copy of code
ini_set("display_errors", "On");
ini_set("session.cookie_lifetime", 0);
ini_set("session.use_cookies", "On"); 
ini_set("session.use_only_cookies", "On"); 
ini_set("session.use_strict_mode", "On"); 
ini_set("session.cookie_httponly", "On"); 
ini_set("session.cookie_secure", "On"); 
ini_set("session.use_trans_sid", "Off"); 
ini_set("session.cache_limiter", "nocache"); 
ini_set("session.sid_length", "48"); 
ini_set("session.sid_bits_per_character", "6"); 
ini_set("session.hash_function", "sha256"); 
    
include_once 'include/config.inc.php';

spl_autoload_register(function ($class_name) {
    include 'include/' . $class_name . '.php';
});
    
function showError($msg) {
    if ($msg) {
        echo '<span class=error>'.$msg.'</span> ';
    }
}

function &getRequest($key, $defaultValue = '') {
    if (isset($_REQUEST[$key])) {
        return $_REQUEST[$key];
    } else {
        return $defaultValue;
    }
}

function &getSession($key, $defaultValue = '') {
    if (isset($_SESSION[$key])) {
        return $_SESSION[$key];
    } else {
        return $defaultValue;
    }
}

function authorized() {
    if (getSession('authorized') == true && getSession('person_id') > 0) {
        return true;
    }
    return false;
}
