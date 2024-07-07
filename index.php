<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js)$/', $_SERVER["REQUEST_URI"])) {
    return false; 
} else {
    include_once("resources/templates/redirect.html");
}
