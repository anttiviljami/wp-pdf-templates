=== WordPress PDF Templates ===
Contributors: Zuige
Tags: pdf, dompdf, templates, print, seravo
Donate link: http://seravo.fi/
Requires at least: 4.0
Tested up to: 4.4.2
Stable tag: 1.4.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin utilises the DOMPDF Library to provide a URL endpoint e.g. /my-post/pdf/ that generates a downloadable PDF file.

== Description ==

WordPress PDF Templates

This plugin utilises the DOMPDF Library to provide a simple URL endpoint e.g. http://my-site.com/my-post/pdf/ that generates a downloadable PDF file.

If pretty permalinks are disabled. GET parameters (e.g. ?p=1&pdf) can be used instead.

The PDF output can be customized by copying the index-pdf.php file from the plugin directory to your theme and creating your own custom template for PDF prints.

Stylesheets used on the site are disabled by default, but you can define your own stylesheets within the pdf-template.php file. PDF Templates can be previewed as raw HTML at the /pdf-preview URL endpoint.

Source available at https://github.com/anttiviljami/wp-pdf-templates

== Installation ==

1. Download and activate the plugin.
2. Installation done! You can now navigate to any post or page on your website and append /pdf/ (or &pdf if not using pretty permalinks) to the URL to view a glorious PDF version of it.

== Frequently Asked Questions ==

= I activated the plugin but can't see any difference. What do I do? =

WordPress PDF Templates works quietly in the backround without cluttering your wp-admin with unnecessary menus and views.

To see this plugin in action, try navigating to any post or page on your site and appending /pdf/ to the URL.

= My PDF is displaying the wrong post content =

Is your content access-restricted? In that case, all you need to do is enable cookies for the plugin with `define('FETCH_COOKIES_ENABLED', true);` in your wp-config.php

You can also try clearing the PDF cache by hard-refreshing your browser or disabling the PDF cache altogether with `define( 'DISABLE_PDF_CACHE', true )`

= How do I enable PDF Templates for custom post types? =

You can define supported post types in your theme functions.php with `set_pdf_print_support($post_types)`

The set_pdf_print_support function takes an array of post types (strings) as a parameter.

Example:
`// add pdf print support to post type 'product'
if(function_exists('set_pdf_print_support')) {
  set_pdf_print_support(array('post', 'page', 'product'));
}`

= I don't like the way my PDF printing looks. How do I change it? =

Just copy index-pdf.php from wp-content/plugins/wp-pdf-templates/ into your theme directory and start editing!

If you wish to define different templates for different post types, you can do that too! Let's say you wish to create a new PDF template for pages. Just create a file called 'page-pdf.php' and create your template there. Note that this only works when a page.php exists in your theme.

= Can I change the PDF output paper size, orientation or DPI? =

Yes! You can define settings for the DOMPDF Library by editing your wp-config.php.

Example:
`// use landscape A4 sized paper @ 180 DPI
define('DOMPDF_PAPER_SIZE', 'A4');
define('DOMPDF_PAPER_ORIENTATION', 'landscape');
define('DOMPDF_DPI', 180);`

See DOMPDF documentation for more options.

= My fonts don't show in the PDF. Can I fix that? =

DOMPDF needs the proper font files to generate PDF files. There's a font adder utility built in to DOMPDF you can use to import any TrueType fonts. See this link for instructions: https://code.google.com/p/dompdf/wiki/Installation

== Screenshots ==

1. See example use of this plugin here: http://vetrospace.com/

== Changelog ==

= 1.4.1
* Automatically fall back to http if https doesn't work

= 1.4.0
* Updated to newest version of DOMPDF, attribute plugin to @anttiviljami instead of Seravo

= 1.3.9 =
* Just marking compatibility and small cleanup

= 1.3.7 =
* Cookies are now passed as a raw header for a simpler system

= 1.3.6 =
* Added more cookie logic and a fix for non-encoded cookies
* General cleanup of documentation & code

= 1.3.5 =
* Request cookie relaying is now optional and disabled by default
* If this update causes any issues, try enabling the constant FETCH_COOKIES_ENABLED

= 1.3.4 =
* Custom fonts are now retained in plugin updates

= 1.3 =
* PHP 5.2 compatibility added, upgraded to newest version of dompdf lib

= 1.2.1 =
* Fixes issue with pretty permalinks disabled. Thanks Triskal!

= 1.2 =
* Cookies added to template requests

= 1.1 =
* HTML output is no longer based on output buffering, which makes everything more stable

= 1.0 =
* Initial release to WordPress.org

== Upgrade Notice ==

= 1.0 =
Please upgrade WordPress PDF Templates to the newest version. It won't break anything. Promise!
