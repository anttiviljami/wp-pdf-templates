Wordpress PDF Templates
=====================

This plugin utilises the DOMPDF Library to provide a simple URL endpoint (e.g. http://my-site.com/my-post/pdf/) that generates a downloadable PDF file.

If pretty permalinks are disabled. GET parameters (e.g. ?p=1&pdf) can be used instead.

The PDF output can be customised by copying the index-pdf.php file from the plugin directory to your theme and creating your own custom template for PDF prints.

Stylesheets used on the site are disabled by default, but you can define your own stylesheets within the index-pdf.php file. PDF Templates can be previewed as raw HTML at the /pdf-preview URL endpoint.

For further information see **readme.txt**.

## Installation from WordPress admin

Download and activate the plugin.

Installation done! You can now navigate to any post or page on your website and append /pdf/ (or &pdf if not using pretty permalinks) to the URL to view a glorious PDF version of it.

## Installation with Git

From the command line, cd to wp-content/plugins and run the following commands. 
```sh
git clone https://github.com/Seravo/wp-pdf-templates.git
cd wp-pdf-templates
git submodule update --init --recursive
```
Activate plugin in the WordPress admin

Installation done! 
