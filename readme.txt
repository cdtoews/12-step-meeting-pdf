=== 12 Step Meeting PDF ===
Contributors: cdtoews
Donate link: https://paypal.me/cdtoews
Tags: 12 step,12 step meeting, 12 step meetings, meeting list, 12 step meeting list
Requires at least: 4.7
Tested up to: 5.2
Stable tag: 0.1.3
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
This plugin requires '12 Step Meeting List', and allows creation of meeting list PDF

== Description ==

If you use the '12 Step Meeting List' plugin, this plugin will help with printing 
meeting lists. I created this plugin to help my own group, and decided to 
make the plugin publicly available. You can choose the page size and orientation.
You can use the column layout that we use, or I have added the nyintergroup
format that meeting-guide made: https://github.com/meeting-guide/nyintergroup
Eventually other formats will become available. Let me know if you have 
specific format requests.

Source Code: https://github.com/cdtoews/12-step-meeting-pdf

== Installation ==

This section describes how to install the plugin and get it working.

1. Have the '12 Step Meeting List' plugin installed already from here: https://wordpress.org/plugins/12-step-meeting-list/
2. Upload the plugin files to the `/wp-content/plugins/12-step-meeting-pdf` directory, or install the plugin through the WordPress plugins screen directly.
3. Activate the plugin through the 'Plugins' screen in WordPress
4. Use the 'Meetings->PDF Generator' screen to configure & use the plugin


== Frequently Asked Questions ==

= Can I print a PDF in portrait orientation on 11X16 paper? =

Yes, in the version 0.1.1 custom paper sizes/orientations were added.

= Can I include meetings on the printed list without having them on the website? =

Yes, Any meetings you want included in the printed list, and not included in the website, just create the meeting as normal, and set the Visibility to private.

= What do each of the values do in the plugin? =

* Page Layout: This lets you choose between column and table formats
* Page Width: self explanatory
* Page Height: self explanatory
* Font Size: The size of the font you want the meetings listed in. The day listings are a font size +2.
* Margin: This is the margin around the edge of each page.
* Header Text: (column layout only) This is text that will show up at the top of each page, not each column. So this sometimes doesn't look good to have this populated once you fold the list.
* HTML before meetings: (column layout only) This is anything you want printed before the meetings. this section can end up being insde a fold once you fold up your meeting list.
* HTML after meetings: (column layout only) This is anything you want printed after the meetings. This section can end up being the front of a folded meeting list.
* **NOTE**: the HTML before and after meetings is rendered a div at a time, put all formatting inside your divs. A div will not be split across two columns 
* Column Count: (column layout only) How many columns you want printed, normally 3 or 4
* Column Padding: (column layout only) How much of a margin you want around EACH column. this can be helpful so the meeting text doesn't end up on a crease once the list is folded.
* Starting Page Number: (table layout only) Let's you choose starting page number if you have other pages you will attach 
* Include Type Index: (table layout only) Let's you include type index at the end of pdf

= I enter values in the boxes, and then try to generate PDF, why aren't my values on the PDF? =

You need to click "Save Changes" after you enter any changes. Saving changes puts the values in the database, and the PDF generator pulls those values from the database

== Screenshots ==

1. No Screenshots yet

== Changelog ==

= 0.1.4 =
* fixed floating footer on admin page
* hiding variables on page if different format selected
* automagically determining optimal font size for # of pages desired (in process)

= 0.1.3 =
* Some changes to data structure
* Links to sample PDF's
* HTML editor in PDF page
* Javascript to try to stop users from submitting for PDF without saving values

= 0.1.2 =
* Parsing state and city from address, not region and sub-region 
* Included NY Intergroup table format from https://github.com/meeting-guide/nyintergroup

= 0.1.1 =
* Better management of meeting text within columns 
* Custom page sizes/orientations
* Meetings with visibility set to Private will be included in PDF but not the website, This requires 12-step-meeting-list at least 3.3.3

= 0.1.0 =
* First Version

== Upgrade Notice ==

= 0.1.3 =
Version 0.1.3 Gives you some sample PDF's as well as an inline HTML editor on the page

= 0.1.2 =
Version 0.1.2 parses city and state from address, not region/sub-region. Also
a second format was added to print.

= 0.1.1 =
Version 0.1.1 gives you more functionality, better text continuity in columns, 
and ability to have meetings print and not be public on website (12-step-meeting-list v 3.3.3 required)

= 0.1.0 =
This is the first publicly released version.
