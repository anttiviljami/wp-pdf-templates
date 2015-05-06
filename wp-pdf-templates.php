<?php
/**
 * Plugin Name: Wordpress PDF Templates
 * Plugin URI: https://github.com/Seravo/wp-pdf-templates
 * Description: This plugin utilises the DOMPDF Library to provide a URL endpoint e.g. /my-post/pdf/ that generates a downloadable PDF file.
 * Version: 1.3.9
 * Author: Seravo Oy
 * Author URI: http://seravo.fi
 * License: GPLv3
*/


/**
 * Copyright 2015 Seravo Oy
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/**
 * Wordpress PDF Templates
 *
 * This plugin utilises the DOMPDF Library to provide a simple URL endpoint
 * e.g. http://my-site.com/my-post/pdf/ that generates a downloadable PDF file.
 *
 * If pretty permalinks are disabled. GET parameters (e.g. ?p=1&pdf) can be used
 * instead.
 *
 * The PDF output can be customized by copying the index-pdf.php file from
 * the plugin directory to your theme and creating your own custom template for
 * PDF prints.
 *
 * Stylesheets used on the site are disabled by default, but you can define your
 * own stylesheets within the pdf-template.php file. PDF Templates can be
 * previewed as raw HTML at the /pdf-preview URL endpoint.
 *
 * For further information see readme.txt
 */

/**
 * Track plugin version number
 */
define('WP_PDF_TEMPLATES_VERSION', '1.3.9');

/**
 * Option to disable PDF caching
 *
 * This can be used for rapidly changing content that's uncacheable, such as
 * dynamically generated feeds or user-tailored views.
 */
//define('DISABLE_PDF_CACHE', true);


/**
 * Option to enable cookies on fetching the PDF template HTML.
 *
 * This might be useful if the content or access to it depends on browser
 * cookies. A possible use scenario for this could be when a login
 * authentification is required to access the content.
 */
//define('FETCH_COOKIES_ENABLED', true);


/**
 * Set PDF file cache directory
 */
$upload_dir = wp_upload_dir();
if (!defined('PDF_CACHE_DIRECTORY')) {
  define('PDF_CACHE_DIRECTORY', $upload_dir['basedir'] . '/pdf-cache/');
}


/**
 * Allow remote assets in docs
 */
if (!defined('DOMPDF_ENABLE_REMOTE'))
  define('DOMPDF_ENABLE_REMOTE', true);


/**
 * Redefine font directories
 */
if (!defined('DOMPDF_FONT_DIR'))
  define('DOMPDF_FONT_DIR', $upload_dir['basedir'] . '/dompdf-fonts/');

if (!defined('DOMPDF_FONT_CACHE'))
  define('DOMPDF_FONT_CACHE', $upload_dir['basedir'] . '/dompdf-fonts/');


/**
 * This function can be used to set PDF print support for custom post types.
 * Takes an array of post types (strings) as input. See defaults below.
 */
function set_pdf_print_support($post_types) {
  global $pdf_post_types;
  if(is_array($post_types)) {
    $pdf_post_types = $post_types;
  }
  else {
    trigger_error('Must supply array as parameter.');
  }
}

/**
 * Default post types supported are post and page
 */
set_pdf_print_support(array('post', 'page'));


/**
 * Adds rewrite rules for printing if using pretty permalinks
 */
add_action('init', '_pdf_rewrite');
function _pdf_rewrite() {
  add_rewrite_endpoint('pdf', EP_ALL);
  add_rewrite_endpoint('pdf-preview', EP_ALL);
  add_rewrite_endpoint('pdf-template', EP_ALL);
}

/**
 * Registers print endpoints
 */
add_filter('query_vars', '_get_pdf_query_vars');
function _get_pdf_query_vars($query_vars) {
  $query_vars[] = 'pdf';
  $query_vars[] = 'pdf-preview';
  $query_vars[] = 'pdf-template';
  return $query_vars;
}

/**
 * Flushes the rewrite rules on plugin activation and deactivation
 */
register_activation_hook(__FILE__, '_flush_pdf_rewrite_rules');
register_deactivation_hook(__FILE__, '_flush_pdf_rewrite_rules');
function _flush_pdf_rewrite_rules() {
  // flush rewrite rules
  // NOTE: You can also do this by going to Settings > Permalinks and hitting the save button
  global $wp_rewrite;
  _pdf_rewrite();
  $wp_rewrite->flush_rules(false);
}

/**
 * Creates a directory for any new fonts the user may upload
 */
register_activation_hook(__FILE__, '_init_dompdf_fonts');
function _init_dompdf_fonts() {
  // copy DOMPDF fonts to wp-content/dompdf-fonts/
  require_once "dompdf/dompdf_config.custom.inc.php";
  if(!is_dir(DOMPDF_FONT_DIR)) {
    @mkdir(DOMPDF_FONT_DIR);
  }
  if(!file_exists(DOMPDF_FONT_DIR . '/dompdf_font_family_cache.dist.php')) {
    copy(
      dirname(__FILE__) . '/dompdf/lib/fonts/dompdf_font_family_cache.dist.php',
      DOMPDF_FONT_DIR . '/dompdf_font_family_cache.dist.php'
      );
  }
}

/**
 * Applies print templates
 */
add_action('template_redirect', '_use_pdf_template');
function _use_pdf_template() {
  global $wp_query, $pdf_post_types;

  if(in_array(get_post_type(), $pdf_post_types)) {

    if (isset($wp_query->query_vars['pdf-template'])) {

      // Substitute the PDF printing template

      // disable scripts and stylesheets
      // NOTE: We do this because in most cases the stylesheets used on the site
      // won't automatically work with the DOMPDF Library. This way you have to
      // define your own PDF styles using <style> tags in the template.
      add_action('wp_print_styles', '_remove_dep_arrays', ~PHP_INT_MAX);
      add_action('wp_print_scripts', '_remove_dep_arrays', ~PHP_INT_MAX);
      add_action('wp_print_footer_scripts', '_remove_dep_arrays', ~PHP_INT_MAX);

      // disable the wp admin bar
      add_filter('show_admin_bar', '__return_false');
      remove_action('wp_head', '_admin_bar_bump_cb');

      // use the print template
      add_filter('template_include', '_locate_pdf_template');

    }

    // our post permalink
    $link = get_the_permalink();

    if(isset($wp_query->query_vars['pdf']) || isset($wp_query->query_vars['pdf-preview'])) {

      if( defined('FETCH_COOKIES_ENABLED') && FETCH_COOKIES_ENABLED ) {

        // we want a html template
        $header = 'Accept:text/html' . "\n";

        // pass cookies from current request
        if( isset( $_SERVER['HTTP_COOKIE'] ) ) {
          $header .= 'Cookie: ' . $_SERVER['HTTP_COOKIE'] . "\n";
        }

        // create a request context for file_get_contents
        $context = stream_context_create(array(
          'http' => array(
            'method' => 'GET',
            'header' => $header,
          )
        ));

        // load the generated html from the template endpoint
        $html = file_get_contents($link . (strpos($link, '?') === false ? '?' : '&') . 'pdf-template', false, $context);
      }

      else {
        // request without cookies
        $html = file_get_contents($link . (strpos($link, '?') === false ? '?' : '&') . 'pdf-template');
      }

      // process the html output
      $html = apply_filters('pdf_template_html', $html);

      // pass for printing
      _print_pdf($html);

    }

  }
}

/**
 * Locates the theme pdf template file to be used
 */
function _locate_pdf_template($template) {

  // locate proper template file
  // NOTE: this only works if the standard template file exists as well
  // i.e. to use single-product-pdf.php you must also have single-product.php

  // @TODO: Utilise a template wrapper like this one: https://roots.io/sage/docs/theme-wrapper/
  $pdf_template = str_replace('.php', '-pdf.php', basename($template));

  if(file_exists(get_stylesheet_directory() . '/' . $pdf_template)) {
    $template_path = get_stylesheet_directory() . '/' . $pdf_template;
  }
  else if(file_exists(get_template_directory() . '/' . $pdf_template)) {
    $template_path = get_template_directory() . '/' . $pdf_template;
  }
  else if(file_exists(plugin_dir_path(__FILE__) . $pdf_template)) {
    $template_path = plugin_dir_path(__FILE__) . $pdf_template;
  }
  else if(file_exists(get_stylesheet_directory() . '/' . 'index-pdf.php')) {
    $template_path = get_stylesheet_directory() . '/' . 'index-pdf.php';
  }
  else if(file_exists(get_template_directory() . '/' . 'index-pdf.php')) {
    $template_path = get_template_directory() . '/' . 'index-pdf.php';
  }
  else {
    $template_path = plugin_dir_path(__FILE__) . 'index-pdf.php';
  }
  return $template_path;

}


/**
 * Removes all scripts and stylesheets
 */
function _remove_dep_arrays() {
  global $wp_scripts, $wp_styles;
  $wp_scripts = $wp_styles = array();
}


/**
 * Filters the html generated from the template for printing
 */
add_filter('pdf_template_html', '_process_pdf_template_html');
function _process_pdf_template_html($html) {

  // relative to absolute links
  $html = preg_replace('/src\s*=\s*"\//', 'src="' . home_url('/'), $html);
  $html = preg_replace('/src\s*=\s*\'\//', "src='" . home_url('/'), $html);

  return $html;
}


/**
 * Handles the PDF Conversion
 */
function _print_pdf($html) {
  global $wp_query;

  if (isset($wp_query->query_vars['pdf'])) {
    // convert to PDF

    $filename = get_the_title() . '.pdf';
    $cached = PDF_CACHE_DIRECTORY . get_the_title() . '-' . substr(md5(get_the_modified_time()), -6) . '.pdf';

    // check if we need to generate PDF against cache
    if(( defined('DISABLE_PDF_CACHE') && DISABLE_PDF_CACHE ) || ( isset($_SERVER['HTTP_PRAGMA']) && $_SERVER['HTTP_PRAGMA'] == 'no-cache' ) || !file_exists($cached) ) {

      // we may need more than 30 seconds execution time
      set_time_limit(60);

      // include the library
      require_once plugin_dir_path(__FILE__) . 'dompdf/dompdf_config.inc.php';

      // html to pdf conversion
      $dompdf = new DOMPDF();
      $dompdf->set_paper(
        defined('DOMPDF_PAPER_SIZE') ? DOMPDF_PAPER_SIZE : DOMPDF_DEFAULT_PAPER_SIZE,
        defined('DOMPDF_PAPER_ORIENTATION') ? DOMPDF_PAPER_ORIENTATION : 'portrait');
      $dompdf->load_html($html);
      $dompdf->set_base_path(get_stylesheet_directory_uri());
      $dompdf->render();

      if(defined('DISABLE_PDF_CACHE') && DISABLE_PDF_CACHE) {
        //just stream the PDF to user if caches are disabled
        return $dompdf->stream($filename, array("Attachment" => false));
      }

      // create PDF cache if one doesn't yet exist
      if(!is_dir(PDF_CACHE_DIRECTORY)) {
        @mkdir(PDF_CACHE_DIRECTORY);
      }

      //save the pdf file to cache
      file_put_contents($cached, $dompdf->output());
    }

    //read and display the cached file
    header('Content-type: application/pdf');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . filesize($cached));
    header('Accept-Ranges: bytes');
    readfile($cached);

  }

  else {
    // print the HTML raw
    echo $html;
  }

  // kill php after output is complete
  die();

}

