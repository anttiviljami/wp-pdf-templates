=== Wordpress PDF Templates ===
Contributors: Zuige
Tags: pdf, dompdf, templates, print
Donate link: http://seravo.fi/
Requires at least: 3.9.1
Tested up to: 3.9.1
Stable tag: 1.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin utilises the DOMPDF Library to provide a URL endpoint e.g. /my-post/pdf/ that generates a downloadable PDF file.

== Description ==

Wordpress PDF Templates

This plugin utilises the DOMPDF Library to provide a simple URL endpoint e.g. http://my-site.com/my-post/pdf/ that generates a downloadable PDF file.

If pretty permalinks are disabled. GET parameters (e.g. ?p=1&pdf) can be used instead.

The PDF output can be customized by copying the index-pdf.php file from the plugin directory to your theme and creating your own custom template for PDF prints.

Stylesheets used on the site are disabled by default, but you can define your own stylesheets within the pdf-template.php file. PDF Templates can be previewed as raw HTML at the /pdf-preview URL endpoint.

Source available at https://github.com/anttiviljami/wp-pdf-templates

== Installation ==

1. Download and activate the plugin.
2. Installation done! You can now navigate to any post or page on your website and append /pdf/ (or &pdf if not using pretty permalinks) to the URL to view a glorious PDF version of it.

== Frequently Asked Questions ==

= I don't like the way my PDF printing looks. How do I change it? =

Just copy the index-pdf.php from wp-content/plugins/wp-pdf-templates/ to your theme directory and start editing!

If you wish to define different templates for different post types, you can do that too! Let's say you wish to create a new PDF template for pages. Just create a file called 'page-pdf.php' and create your template there. Note that this only works when a page.php exists in your theme.

= How do I enable PDF Templates for custom post types? =

You can define supported post types in your theme functions.php with `set_pdf_print_support($post_types)`

The set_pdf_print_support function takes an array of post types (strings) as a parameter.

Example:
`// add pdf print support to post type 'product'
if(function_exists('set_pdf_print_support')) {
  set_pdf_print_support(array('post', 'page', 'product'));
}`

= Can I change the PDF output paper size, orientation or DPI? =

Yes! You can define settings for the DOMPDF Library by editing your wp-config.php.

Example usage:
`// use landscape A4 sized paper @ 180 DPI
define('DOMPDF_PAPER_SIZE', 'A4');
define('DOMPDF_PAPER_ORIENTATION', 'landscape');
define('DOMPDF_DPI', 180);`

See DOMPDF documentation for more options.

= My fonts don't show in the PDF. Can I fix that? =

DOMPDF needs the proper font files to generate PDF files. There's a font adder utility built in to DOMPDF you can use to import any TrueType fonts. See this link for instructions: https://code.google.com/p/dompdf/wiki/Installation

== Screenshots == 

None yet.

== Changelog ==

= 1.0 =
Initial release to WordPress.org

Note that complete commit log is available at https://github.com/seravo/wp-pdf-templates/commits/master

== Upgrade Notice ==

= 1.0 =
Please upgrade Wordpress PDF Templates to the newest version. It won't break anything. Promise!
