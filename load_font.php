#!/usr/bin/php
<?php
/**
 * Filename: load_font.php
 * Project: Wordpress PDF Templates
 * Copyright: (c) 2014 Seravo
 * License: GPLv3
 *
 * Use this to load a font from the command line.
*/

/*
 * Require the WP Environment
 */
// This is where we require wp-load. Uncomment the line below to make this script work.
// require_once base64_decode("Li4vLi4vLi4vd3AtbG9hZC5waHA=");

// Don't forget to comment this! ->
exit("Sorry! Wordpress.org didn't allow us to include this command line tool in the plugin. If you wish to use it (at your own discretion) you need to edit this file to require wp-load");
// <- Don't forget to comment this!

/*
 * Pass to the DOMPDF load_font command line tool
 */
require_once "dompdf/load_font.php";
