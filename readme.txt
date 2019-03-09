=== 12 Step Meeting PDF ===
Contributors: cdtoews
Donate link: https://paypal.me/cdtoews
Tags: 12 step, meeting list, pdf, 12 step meeting list
Requires at least: 4.6
Tested up to: 5.1
Stable tag: 0.1.0
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin requires '12 Step Meeting List', and allows creation of meeting list PDF

== Description ==

If you use the '12 Step Meeting List', you know the PDF generation in that plugin is lacking. I created
this plugin to help my own group, and decided to make the plugin plublicly available.

Source Code: https://github.com/cdtoews/12-step-meeting-pdf

== Installation ==

This section describes how to install the plugin and get it working.

1. Have the '12 Step Meeting List' plugin installed already from here: https://wordpress.org/plugins/12-step-meeting-list/
2. Upload the plugin files to the `/wp-content/plugins/12-step-meeting-pdf` directory, or install the plugin through the WordPress plugins screen directly.
3. Activate the plugin through the 'Plugins' screen in WordPress
4. Use the 'Meetings->PDF Generator' screen to configure & use the plugin


== Frequently Asked Questions ==

= Can I print a PDF in portrait orientation on 11X16 paper? =

Currently the plugin only supports 8.5 X 11 inches in landscape. As I have time, I plan to add other
paper sizes and orientations

= What do each of the values do in the plugin? =

* Header Text: This is text that will show up at the top of each page, not each column. So this sometimes doesn't look good to have this populated once you fold the list.
* Font Size: The size of the font you want the meetings listed in. The day listings are a font size +2.
* Margin: This is the margin around the edge of each page.
* HTML before meetings: This is anything you want printed before the meetings. this section can end up being insde a fold once you fold up your meeting list.
* HTML after meetings: This is anyting you want printed after the meetings. This section can end up being the front of a folded meeting list.
* NOTE: you can insert custom tags around meeting list by putting opening html tags in the before section, and closing tags int he after section.
* Column Count: How many columns you want printed, normally 3 or 4
* Column Padding: How much of a margin you want around EACH column. this can be helpful so the meeting text doesn't end up on a crease once the list is folded.

= I enter values in the boxes, and then try to generate PDF, why aren't my values on the PDF? =

You need to click "Save Changes" after you enter any changes. Saving changes puts the values in the database, and the PDF generator pulls those values from the database

== Screenshots ==

1. No Screenshots yet

== Changelog ==

= 0.1.0 =
* First Version
