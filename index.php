<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Serve the requested resource as-is if it is a static file.
if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js)$/', $_SERVER["REQUEST_URI"])) {
    return false; 
} else {
    include_once("resources/templates/index.html"); // Ensure this is the correct path to your main HTML file
}
