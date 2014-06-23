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
// Here is where you would require wp-load but Wordpress.org doesn't allow this.
// Just edit this line to require it if you want to use the font loading script
// in the command line. Coming soon: Admin Interface to implement this properly.

/*
 * Pass to the DOMPDF load_font command line tool
 */
require_once "dompdf/load_font.php";
