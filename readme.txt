=== 12 Step Meeting PDF ===
Contributors: cdtoews
Donate link: https://paypal.me/cdtoews
Tags: 12 step,12 step meeting, 12 step meetings, meeting list, 12 step meeting list
Requires at least: 4.7
Tested up to: 6.1.1
Stable tag: 1.0.3
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

* Page Layout: This lets you choose between columns1, columns2 and table formats
* Filter how: This determines if you are white-listing or black-listing types from the "filter what"
* Filter what: this determines which meeting types are filtered
* Paper size: some buttons to populate page width & height
* Page Width: self explanatory
* Page Height: self explanatory
* Font Size: The size of the font you want the meetings listed in. The day listings are a font size +2.
* Header Font Size: font size in header text, if a header text is used
* Margin: This is the margin around the edge of each page.
* Automatically determine optimum font size: This will attempt to determine the largest font can be used to get the desired page count. works only for columns formats
* Use Custom Meeting HTML: This will determine if the meeting text will use the custom HTML
* Meeting HTML: This is the custom HTML that will be used to create each meeting text. For each item you want listed, use the variables listed to the left. for example if you enter __title__ (note, 2 underscores on either side), when the meeting is rendered, it will replace that text with the actual title of the meeting.
* Header Text: (column layout only) This is text that will show up at the top of each page, not each column. So this sometimes doesn't look good to have this populated once you fold the list.
* Column2 Indent: This is the amount the pdf will indent to allow for the time. This might need to be changed if you change font size
* HTML before meetings: (column layout only) This is anything you want printed before the meetings. this section can end up being insde a fold once you fold up your meeting list.
* HTML on Specific Column: this section allows you to specify a column to put some html. this lets you fold your list funky ways
       * enabled: this enables or disables this feature
       * page number: this determines which page number has the custom column html (page numbers start at 1)
       * column number: this determines what column number has the custom column html (column numbers start at 1)
       * note that if the page and column numbers are not reached during parsing of meetings, the column html will not be included
* HTML after meetings: (column layout only) This is anything you want printed after the meetings. This section can end up being the front of a folded meeting list.
* **NOTE**: the HTML before and after meetings is rendered a div at a time, put all formatting inside your divs. A div will not be split across two columns
* Column Count: (column layout only) How many columns you want printed, normally 3 or 4
* Column Padding: (column layout only) How much of a margin you want around EACH column. this can be helpful so the meeting text doesn't end up on a crease once the list is folded.
* Starting Page Number: (table layout only) Let's you choose starting page number if you have other pages you will attach
* Include Type Index: (table layout only) Let's you include type index at the end of pdf
* Save a copy of File: Do you want a copy of the file automatically saved to the server
* File Name: path & name of file to save. The path you enter will be appended to the Home Path of your wordpress instance

= How do I determine the best font size to fit all meetings on 2 pages =

in version 0.1.6 there is a somewhat experimental feature that determines the optimal font size. The optimal font size is the size at which adding 0.1 to it would make a page greater than your desired page count.
I use this to make a 2 page list, so I can print it double sided and use a single page.
What it does:
* takes the currently set font size and makes a pdf and sees how many pages it is
* If the page count is equal to or less then the desired page count, it increases the font size a certain amount
* If the page count is greater than the desired page count, it decreases the font size a certain amount
* Once it increases and decreases it will find the largest font size that will fit on the desired page count
* This can take 30-60 seconds since it is making multiple PDF's to check sizes
* Once it determines the optimal font size, it sets that font size in your settings, and makes your PDF

== Screenshots ==

1. No Screenshots yet

== Changelog ==

= 1.0.2 =
* now supports php 8
* fixed bug with "text in a certain column" not properly showing in gui

= 0.3.1 =
* Added ability to filter by attendance type (online, in person, hybrid)

= 0.3.0 =
* fixed bug, thanks to Josh Reisner

= 0.2.4 =
* added feature for html on arbitrary page/column (by request of Regina AA)

= 0.2.3 =
* fixed bug in table1 output (thanks @webmasteraaneok)

= 0.2.2 =
* gave human friendly description for type selections

= 0.2.1a =
* fixed bug with empty selected types

= 0.2.1 =
* Added ability to filter by meeting type. whitelist and blacklist capabilities
* Added ability to include conference URL and conference phone number with the Custom Meeting HTML

= 0.2.0 =
* removed extra period if notes or location_notes ended in a period.


= 0.1.8 =
* by Request of AA of Ft. Worth AA:
* Added switch to disable new page for every region on Table1 format

= 0.1.7 =
* formatting fix: Meeting text was adding periods at the end of blank notes and location notes
* by Request of AA of Greensboro, NC:
* Adding ability for custom meeting html
* added column2 format with meeting indented, and time in the indent
* Added ability to also save file to desired filename

= 0.1.6 =
* bug fix for editors not loading in https

= 0.1.4 =
* fixed floating footer on admin page
* hiding variables on page if different format selected
* option to auto-determine optimal font size for # of pages desired, column layout only

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

= 0.1.7 =
by Request of AA of Ft. Worth AA:
Added switch to disable new page for every region on Table1 format

= 0.1.7 =
Version 0.1.7 small formatting fixes, minor bug fixes
by Request of AA of Greensboro, NC:
Added columns2 format for sub-grouping by time
Added custom meeting HTML
Added ability to also save file to desired filename

= 0.1.6 =
Version 0.1.5 bug fix for editors not loading

= 0.1.4 =
Version 0.1.4 fixes floating footer, hides unused settings, and auto-determine optimal font size for desired number of pages

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
