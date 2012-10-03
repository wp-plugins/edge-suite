=== Edge Suite ===
Contributors: ti2m
Tags: media, animation, interactive, adobe edge animate, edge animate, edge, embed, integration
Requires at least: 4.3
Tested up to: 4.3
Stable tag: /trunk/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Manage and view your Adobe Edge Animate compositions on your website.


== Description ==

Upload of compositions through a zipped archive. Integrate Adobe Edge Animate compositions seamlessly into your website.

Since Edge Animate itself is still in pre-release this plugin is a moving target as well and should therefore not yet be used in production.

There have been problems with unzipping composition archives on shared hosting plans due to the wordpress filesystem. More detailed info is available in the FAQ section.

Roadmap: FTP Filesystem support, data injection

Please help to review and test the plugin. Feedback is appreciated.

Resources:
- <a href="http://edgedocks.com/content/edge-suite-integrate-edge-animate-wordpress">tutorial</a> on how to install and use Edge Suite
- General resources on Edge Docks <a href="http://edgedocks.com/edge_suite_wp">EdgeDocks.com</a>

== Features ==

* Upload Edge Animate compositions within one zipped archive
* Manage all compositions
* Easy placement of compositions on the website

== Frequently Asked Questions ==

= Animations don't show up =
Uploading worked but nothings shows up on the page. Things to check:

* Edge Suite doesn't support minified/published projects yet. Just zip the raw project folder (without the publish folder)

* Look at the source code of the page and search for:

* "stage" - You should find a div container, if so HTML rendering went fine.

* "_preloader" - You should find a script tag, if so JS inclusion went fine.

* If "stage" or "_preloader" are not found, disable other plugins for testing to check if they might interferer.

* Open the debug console in Chrome (mac: alt + cmd + j) or Firefox and check for JavaScript errors.

* For testing remove all other fancy JavaScript like galleries, slideshows, etc. that are placed alongside the animation, the JS might collide.

= Head Cleaner: Animations don't show up =

Head Cleaner basically skips the processing of edge_suite_header() which is needed to inject the Edge Javascript.
Under Settings > Head Cleaner > Active Filters check the box "Don't process!" for "edge_suite_header" and click "Save options".
This stops Head Cleaner from "processing" ede_suite_header(), which basically means allowing edge_suite_header() (reverse logic).

= PHP ZipArchive not found =

zip.so needs to be installed as a PHP library

== Installation ==

1. IMPORTANT: Backup your complete wordpress website, this module is in early development state!
1. Install the Edge Suite plugin as any other wordpress plugin.
1. Make sure /wp-content/uploads/edge_suite was created and is writable.
1. Backup your complete theme folder.
1. Find the header.php file in your theme.
1. Insert the following snippet in the header section where the compositions should appear (inside php tags):
      if(function_exists('edge_suite_view')){echo edge_suite_view();}	
	
1.  Placing the code within in a link tag (<a href=""...) can cause problems when the composition is interactive.
1.  You might also want to remove code that places other header images e.g. calls to header_image() or get_header_image() in case the composition should be the only thing in the header.
1. Zip the main folder of the composition that you want to upload.
1. Go to "Edge Suite > Manage", select the archive and upload it.
1. Upload as many composition as you want.
1. After uploading, the compositions can be placed in multiple ways on the website:
    * Default: A composition that should be shown on all pages can be selected on the "Edge Suite > settings" page under "Default composition".
    * Homepage: A composition that is only meant to show up on the homepage can also be selected there.
    * Page/Post: In editing mode each post or a page has a composition selection that, when chosen, will overwrite the default composition.


== Support ==

Please report any bugs to the Edge Suite support queue on wordpress.


== Changelog ==

= 0.2 =
Change of filesystem usage
