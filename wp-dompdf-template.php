<?php
/**
 * Plugin Name: Wordpress PDF Templates
 * Plugin URI: http://seravo.fi
 * Description: This plugin utilises the DOMPDF Library to provide a URL endpoint e.g. /my-post/pdf/ that generates a downloadable PDF file.
 * Version: 1.0
 * Author: Antti Kuosmanen (Seravo Oy)
 * Author URI: http://seravo.fi
 * License: GPLv3
*/


/**
 * Copyright 2014 Antti Kuosmanen / Seravo Oy
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

/*
 * Track plugin version number
 */
define('WP_DOMPDF_TEMPLATE_VERSION', '1.0');

/*
 * Always use the DOMPDF HTML5 parser option
 */
define('DOMPDF_ENABLE_HTML5PARSER', true);


/*
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

/*
 * Default post types supported are post and page
 */
set_pdf_print_support(array('post', 'page'));


/*
 * Adds rewrite rules for printing if using pretty permalinks
 */
add_action('init', '_pdf_rewrite');
function _pdf_rewrite() {
  add_rewrite_endpoint('pdf', EP_ALL);
  add_rewrite_endpoint('pdf-preview', EP_ALL);
}

/*
 * Registers print endpoints
 */
add_filter('query_vars', '_get_pdf_query_vars');
function _get_pdf_query_vars($query_vars) {
  $query_vars[] = 'pdf';
  $query_vars[] = 'pdf-preview';
  return $query_vars;
}

/*
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


/*
 * Applies print templates to the pages and starts the output buffer
 */
add_action('template_redirect', '_use_pdf_template');
function _use_pdf_template() {
  global $wp_query;

  if (isset($wp_query->query_vars['pdf']) || isset($wp_query->query_vars['pdf-preview'])) {

    //check to see if post type is supported
    global $pdf_post_types;

    if(in_array(get_post_type(), $pdf_post_types)) {

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

      // output generator meta to help debugging
      add_action('wp_head', function() {
        echo "\n\n" . '<meta name="generator" content="Wordpress PDF Downloads Version ' . WP_DOMPDF_TEMPLATE_VERSION . '">' . "\n\n     ";
      });

      // use the print template
      add_filter('template_include', function($template) {

        // locate proper template file
        // NOTE: this only works if the standard template file exists as well
        // i.e. to use single-product-pdf.php you must also have single-product.php
        $pdf_template = str_replace('.php', '-pdf.php', basename($template));

        $template_path = plugin_dir_path(__FILE__) . 'index-pdf.php';
        if(file_exists(get_stylesheet_directory() . '/' . $pdf_template)) {
          $template_path = get_stylesheet_directory() . '/' . $pdf_template;
        }
        else if(file_exists(get_template_directory() . '/' . $pdf_template)) {
          $template_path = get_template_directory() . '/' . $pdf_template;
        }
        else if(file_exists(plugin_dir_path(__FILE__) . $pdf_template)) {
          $template_path = plugin_dir_path(__FILE__) . $pdf_template;
        }

        return $template_path;

      });

      // start output buffer
      ob_start();
      add_action('wp_footer', '_pdf_buffer_end');

    }
  }
}


/*
 * Removes all scripts and stylesheets
 */
function _remove_dep_arrays() {
  global $wp_scripts, $wp_styles;
  $wp_scripts = $wp_styles = array();
}


/*
 * Passes output buffer to DOMPDF Lib
 */
function _pdf_buffer_end() {
  // capture and terminate output buffer
  $html = ob_get_contents();
  ob_end_clean();

  // process the html output
  $html = preg_replace('/src\s*=\s*"\//', 'src="' . home_url('/'), $html);
  $html = preg_replace('/src\s*=\s*\'\//', "src='" . home_url('/'), $html);

  // pass for printing
  _print_pdf($html);
}


/*
 * Handles the PDF Conversion
 */
function _print_pdf($html) {
  global $wp_query;

  if (!headers_sent()) {
    // Disable Caching for PDF output
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
  }

  if (isset($wp_query->query_vars['pdf'])) {
    // convert to PDF

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
    $dompdf->stream(get_the_title() . '.pdf');

  }

  else {
    // print the HTML raw
    echo $html;
  }

}
