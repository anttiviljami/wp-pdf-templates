#!/usr/bin/php
<?php
/**
 * Filename: load_font.php
 * Project: Wordpress PDF Templates
 * Copyright: (c) 2014 Seravo Oy
 * License: GPLv3
 *
 * Use this to load a font from the command line.
*/

/*
 * Require the WP Environment
 */

// This is where we require wp-load. Just uncomment the line below.
require_once "../../../wp-load.php";

/*
 * Pass to the DOMPDF load_font command line tool
 */
require_once "dompdf/load_font.php";
