<?php
/*
Plugin Name: Live editor
Plugin URI: live editor
Description: Plugin for adding live editting functionality to the wordpress site. 
Version: 1.0
Author: Guriev Eugen
Author URI: https://lolitaframework.com/
License: GPLv2 or later
Text Domain: liveeditor
*/

// ==============================================================
// Bootstraping
// ==============================================================
if (! class_exists('LolitaFramework')) {
    require_once 'LolitaFramework/LolitaFramework.php';
}
$lolita_framework = \liveeditor\LolitaFramework::getInstance();
$lolita_framework->addModule('Configuration');
$lolita_framework->addModule('Widgets');
$lolita_framework->addModule('CssLoader');
