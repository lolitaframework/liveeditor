<?php
/*
Plugin Name: Live editor
Plugin URI: live editor
Description: Plugin for adding live editting functionality to the wordpress site. 
Version: 1.0
Author: Guriev Eugen
Author URI: https://lolitaframework.com/
License: MIT
Text Domain: liveeditor
*/

// ==============================================================
// Bootstraping
// ==============================================================
if (!class_exists('\liveeditor\LolitaFramework')) {
    require_once('LolitaFramework/LolitaFramework.php');
    $lolita_framework = \liveeditor\LolitaFramework::getInstance(__DIR__);
    \liveeditor\LolitaFramework::define('BASE_DIR', $lolita_framework->baseDir());
    \liveeditor\LolitaFramework::define('BASE_URL', $lolita_framework->baseUrl());
    $lolita_framework->addModule('Configuration');
    $lolita_framework->addModule('CssLoader');
}
