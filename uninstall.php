<?php
/**
 * Filename: uninstall.php
 * Project: Wordpress PDF Templates
 * Copyright: (c) 2014 Seravo
 * License: GPLv3
 *
 * This file gets called when the plugin is uninstalled from Wordpress.
*/

/*
 * If not called by Wordpress, do nothing
 */
if (!defined('WP_UNINSTALL_PLUGIN')) {
  exit();
}

$upload_dir = wp_upload_dir();

/*
 * Remove the font directory created by this plugin
 */
rrmdir($upload_dir['basedir'] . '/dompdf-fonts');

/*
 * Handles recursive remove.
 */
function rrmdir($dir) {
  foreach(glob($dir . '/*') as $file) {
    if(is_dir($file)) rrmdir($file); else unlink($file);
  } rmdir($dir);
}
