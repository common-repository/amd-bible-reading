=== AMD Bible Reading ===
Contributors: themaster5_07
Tags: bible, kjv, devotional, daily, reading, widget, shortcode
Donate link: http://amasterdesigns.com
Requires at least: 4.0
Tested up to: 4.9.1
Requires PHP: 5.2
Stable tag: 3.1.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

While building a website for a church I started searching for a plugin that would show a daily KJV Bible reading plan on the site. I also thought it would be great if it also included a widget that would show a snippet of the daily passage and then link to the full reading page. This was expanded to show random verses and passages using shortcodes.

== Description ==
While building a website for a church I started searching for a plugin that would show a daily KJV Bible reading plan on the site. I also thought it would be great if it also included a widget that would show a snippet of the daily passage and then link to the full reading page. I could not find what I was looking for so I started the endeavor of developing one myself. Now I am distributing my own daily Bible reading plugin. Please use freely and modify to fit your needs. If you have recommendations I would love to hear them.

About AMD Bible Reading?

I have built this plugin to be the very best King James Version (KJV) Bible Daily Reading Plugin for WordPress. I have set forth to make this plugin very simple and easy to use. This plugin will generate one widget, "Daily Bible Snippet", and provides support for four shortcodes `[amd_bible_devo]`; `[amd_bible_daily]`; `[amd_bible_rand]`; and `[amd_bible]`.

Passages references are accepted in almost every variety. If you have an example of a reference that does not work like it should, please let us know. Here are some example references:

* Gen. 1-3
* Gen 1:1-10
* Genesis 1:1-5, 7, 10; 2
* 1King 1, 3, 4:5-9; 5
* First Kings 2
* II Kings 1; John 4
* John 1:1, 2:4-10
* Jude



== Installation ==
1. **Upload "amd-bible-reading"** to the "/wp-content/plugins/" directory.
2. **Activate the plugin** through the "Plugins" menu in WordPress.
3. **Select Your Reading Plan** by navigating to AMD Settings and set the 'Selected Plan' option. This will set the reading plan for the [amd_bible_daily] shortcode and the widget
4. **Set Standardize References Option** by navigating to AMD Settings, and set your preference for the 'Standardize CX References' option. Leaving this option checked will force Complex References to become formatted before displaying on the front-end using the [amd_bible_daily] shortcode or widget.
5. **Create a Bible Reading Page** by editing or creating a new page and enter the `[amd_bible_daily]` shortcode into the content and publish the page. There are 14 attributes that can be used to control how the passage is displayed. The defaults vary depending on the inline attribute. Please refer to FAQ for more information.
6. **Add the Daily Bible Snippet Widget** to your selected sidebar. There are 8 options for the Widget.
 1. Select the Reading Plan to use for the widget instance.
 2. Scripture starts with Reference inline? When this option is checked, the scripture will begin with the reference. This is helpful if using a custom widget title. 
 3. Use Reference for Title? When this option is checked, The title will default to the Reference for the daily scripture passage(s).
 4. Title: this input will be used as the title given that the reference is not used by the previous option.
 5. Limit Type: this will define how the passage is limited. The options are 'words' or 'verses'. If the limit type is set to words, the last verse will most likely be interrupted, but the content will be more of a standard length.
 6. Limit: using the limit type in the previous option this option defines how many items to display.
 7. Full Reading Page: select the page created in the previous setup step. This will show the full daily Bible reading utilizing the `[amd_bible_daily]` shortcode.
 8. Read More Text: this input controls what text is displayed on the page linked to in the previous setting. make sure to save your settings.

= Displaying the Daily Devotional Morning/Evening =
I have found Charles Spurgeon Devotionals to be very encouraging and uplifting and have decided to include them in this plugin by utilizing another shortcode `[amd_bible_devo]`. The steps to adding this into your content are very simple and can be done on any post or page. Simply add the `[amd_bible_devo]` shortcode into your content at the desired location. At this time there is no option to display a different devotion than the one that is current for the specific time of day and date.

= Displaying a random verse =
Random verses can now be displayed using the new shortcode [amd_bible_rand]. There are four main attributes that can be utilized to determine where the random verse is selected from:
* **ot** - (boolean) when setting to true this will show verses from the old testament only
* **nt** - (boolean) when setting to true this will show verses from the new testament only
* **book** - (numeric or string: book name, or book abbreviation) when set verses will originate from selected *book
* **chapter** - (numeric) when setting along with book attribute, this will determine from which chapter the verses will originate
* **most_read** - (boolean) when setting to true this will show verses from a list of the top read 100 Bible verses from a study conducted by biblegateway.com
* **essential** - (boolean) when setting to true this will show verses from a list of Bible verses essential to the Christian life. These verses cover the following topics: Victorious Life, Romans Road, Assurance, Baptism, Believe in Christ, Bible, Biblical Inspiration, Christ's Sacrifice, Dedication, Friendship, Forgiveness of Sins, Guidance, Hell, Home, Law, Local Church, One Way of Salvation, Others, Peace, Principles, Problem Solving, Procrastination, Salvation without Works, Second Coming, Victory over Satan, Witnessing

= Displaying Passages directly using shortcodes with complex references =
Bible verses and passages can now be displayed anywhere shortcodes are accepted using the shortcode `[amd_bible]Your Reference[/amd_bible]`. Replace 'Your Reference' with any simple or complex Bible Reference. There are eight attributes that can be used to control how the passage is displayed. The defaults vary depending on the inline attribute. Please refer to FAQ for more information.

= Local hosting your Libraries =
Decide to use either the default API or import and use local database library. On the AMD Settings page, you will see two settings: Use local Bible database and Use local devotional database. Before activating these check-boxes, you will first need to import the library data using the AMD Library admin page.

1. Navigate to AMD Library and download the CSV files using the two download links.
2. For the KJV Bible, choose your downloaded amdbible_kjv.csv file
3. Click the Import Bible button.
4. For the Devotional, choose your downloaded amdbible_devos.csv file
5. Click the Import Devo button.
6. Navigate to AMD Settings and check the box for 'Use local Bible database' and 'Use local devotional database'

= Editing Reading Plans =
Complex and Custom Reading Plans are Here! It is now possible to edit existing Bible Reading Plans and even add new plans. Navigate to AMD Settings -> Plan Editor, and either create a new plan or choose an existing plan to edit. New plans cannot have the same names as existing plans. After creating the plan please add the plan details before selecting the plan in AMD Settings as the chosen plan. This will prevent your widget and shortcodes from displaying no passages. Plans can either use complex references or simple references (leaving complex references unchecked). With simple references there will be two input fields for each date, starting reference and ending reference. Only simple, single verse references can be used in this format. To easily indicate the end of the chapter verse '999' can be used, for instance, 'Genesis 4:999' will show up to all of the verses in chapter 4 of Genesis. Book abbreviations can be used such as "Gen" for Genesis. With complex references, you have an additional option to upload a CSV file to easily and quickly insert your custom reading plan from your favorite source. CSV files must be comma delimited with no headers and column 1 being the day of the year and column 2 being the corresponding complex reference. Complex references can consist of varying diversities.

Be sure to Save Changes before navigating away from the plan editor page or your changes will be lost. **CAUTION!** changing a plan from a simple to complex or vice versa and saving valid data will erase the alternate settings. You could easily erase an entire plan this way.

= Restoring All Data to original =
In order to restore all data to the original installation, you will need to delete the plugin and all associated data and reinstall. To do this before uninstalling or deactivating the plugin, you will need to check the "Delete Database Data" option. This will erase any custom reading plans you may have created.

== Frequently Asked Questions ==
= What does AMD represent? =
AMD stands for A Master Designs. AMD is a dream to one day be a fully self-supported missionary like Paul the apostle making tents. My name is Anthony Master and I am just a master designing for THE MASTER!

= Is an External API required for Bible and Devotional Library =
As of version 3.0 an API is loaded by default utilizing http://api.amasterdesigns.com URL. The administrator can load the libraries locally and set two options to use local libraries instead of externally loading passages from the API. For more information and instructions please see Local hosting your Libraries in the description or on the About AMD admin page.

= Can other Bible Versions be supported or added? =
Personally I only support the King James Version (KJV) of the Bible. This plugin and the source code is open source by nature which gives you the permission to edit this plugin to meet your needs as you see fit for your site.

= Will other Devotionals be supported =
I am open minded to adding additional devotionals to my API and adding the hash value of the correlating CSV for import. Bottom line, if you know of any good devotionals that you would recommend, please do so in the support forum. If you can find a database or CSV equivalent of the devotional that would also help greatly. 

= What are the attributes that I can use with `[amd_bible_daily]` shortcode? =
There are 14 attributes that can be used to control how the passage is displayed. The defaults vary depending on the inline attribute. Please see the below list for default and accepted values

* If inline is FALSE (Default setting):
 * show_book = true
 * show_chapter = true
 * show_verse = true
 * reference_before = true
 * reference_after = false
 * form_before = true
 * form_after = true
* If inline is set to TRUE
 * show_book = false
 * show_chapter = false
 * show_verse = false
 * reference_before = false
 * reference_after = true
 * form_before = false
 * form_after = false

* limit = 0 (unlimited)
* limit_type = '' (Acceptable settings are 'words' or 'verses')
* plan = (default set in AMD settings)
* day = (current day)(priority)
* date = (current date)
* date_format = 'D., M. j, Y' (Accepts valid date format string)
* no_reading_text = "There is no reading scheduled for this day. Use this day to catch up or read ahead."

= What are the attributes that I can use with `[amd_bible]` shortcode? =
There are eight attributes that can be used to control how the passage is displayed. The defaults vary depending on the inline attribute. Please see the below list for default and accepted values:

* If inline is TRUE (Default setting):
 * show_book = false
 * show_chapter = false
 * show_verse = false
 * reference_before = false
 * reference_after = true
* If inline is set to FALSE
 * show_book = true
 * show_chapter = true
 * show_verse = true
 * reference_before = true
 * reference_after = false

* limit = 0 (unlimited)
* limit_type = '' (Acceptable settings are 'words' or 'verses')

== Screenshots ==
1. AMD Settings
2. Plan Editor Create New Plan
3. Plan Editor Simple Plan
4. Plan Editor Complex Plan
5. AMD Library Exporting & Importing
6. Shortcodes random verse admin
7. Shortcodes random verse public
8. Daily Reading public view
9. Widget admin view
10. Widget public view

== Changelog ==
= Version 3.1.5 =
* Tested with WordPress 4.9.1

= Version 3.1.4 =
* Fixed bug where undefined variable was present when using random verse shortcode when Standardize CX References not enabled. Thanks to @laplander for bringing this bug to light.
* Tested with WordPress 4.8.2
* Added 'Requires PHP' version 5.2 tag

= Version 3.1.3 =
* Tested with WordPress 4.8

= Version 3.1.2 =
* Fixes plugin to correctly work with WordPress Multi-Site Networks
 * Updated databases prefixes to use base prefix for multisite installations
 * Require network activate set in plugin header for multisite installations
 * Created network settings page for Delete data option and use local data options
 * Added warning messages to admin plan editor page and admin library page.
 * Added notice to about page that only super admins can edit plans.
 * Updated user capabilities required to access plan editor admin page and library admin page.
 * Current setup on mutlisites require network administrator (super admin) to check option to delete data on plugin delete, change options to use local data for Bible and devotionals, upload libraries, and edit plans. Site administrators have access to  change plan settings. Default reading plans can be different from site to site but share the same data for plans, devotionals, and Bible text if local options is enabled by network administrator.
* Removed date_default_timezone_set and utilized date_i18n instead of date function throughout
* Spell-checked

= Version 3.1.1 =
* Fixed bug where fatal error was thrown on activation on servers running PHP < 5.5.0

= Version 3.1 =
* Added buttons to TinyMCE editor to quickly add AMD Bible Reading shortcodes
* Added links support, donate, and rate links to plugins list in admin
* Added admin dashboard widget to display daily Bible reading.
* Added admin dashboard widget to display daily Devotional.
* Added option in settings page for Todays Full Reading page.
* Updated readme for formatting errors.

= Version 3.0.1 =
* Changed name to AMD Bible Reading
* Security Updates
 * removed unnecessary and insecure csv-process.php that was moved into function amdbible_import_csv function in version 3.0
 * check user capabilities added in insert-devos-empty.php
 * check user capabilities added in insert-keys.php
 * check user capabilities added in insert-kjv-empty.php
 * check user capabilities added in insert-plans.php
 * check user capabilities added in uninstall.php
 * check user capabilities added to functions requiring access levels.
 * sanitized input throughout plugin.
* Bug Fixes
* Cleaned up code

= Version 3.0 API Functionality =
* Added Library Settings Page
* Added ability to export Bible
* Added ability to export Devotional
* Added ability to import Bible
* Added ability to import Devotional
* Added link to KJV Bible in CSV format found at http://amasterdesigns.com/amdbible_kjv/
* Added link to Devotional in CSV format found at http://amasterdesigns.com/amdbible_devos/
* Removed insert-kjv.php and uploaded file to http://amasterdesigns.com/insert-kjv/ for public download and use.
* Removed insert-devos.php and uploaded file to http://amasterdesigns.com/insert-devos/ for public download and use.
* Added setting to use local bible database.
* Added setting to use local devotional database.
* Remove KJV and Devotional data from plugin install database data.
* Configure backup utilization of API found at http://api.amasterdesigns.com if local database of bible and devotional are not present.
* Update Screenshots

= Version 2.1 Feature Enhancements =
* Added ability to edit plan description
* Added ability to delete plans but not default plans
* Added option in widget to select plan
* Added option for chapter number and verse number colors
* Added shortcode for direct passage using complex reference
* Removed 64001015 (3 John 1:15) empty verse from KJV Bible database
* Updated Database Version to 2.1
* Added ability to export plan as CSV
* Added option parameters to daily reading shortcode and direct passage shortcode - see about AMD for more information
* Updated amdbible_get_plan function to return plan 1 if the given attribute in the function is not a current plan
* Adjusted amdbible_date_form function to work on pages where permalinks contain variables.
* Added shortcode and widget for random verse or chapter of any chapter, book, or of the entire Bible.
* Added ability to leave blanks on reading plan for days where there is no reading scheduled.
* Updated About AMD page with revised instructions and shortcodes.

= Version 2.0.1 Bug Fixes =
**Warning** Updating to this version will delete any changes made to reading plans. Please make notes of custom plans before updating plugin.
* Updated Database Version to 2.0.1
 * Added Unique Constraint to p (plan) and d (day) on amdbible_plans table to properly allow updating without creating duplicate entries
 * Updated incorrect abbreviations for Judges from 'Jud' to 'Judg'
* Updated amdbible_daily_passage function to stop after first loop

= Version 2.0 Settings Functionality =
* Created Settings Pages
* Created Plan Creator/Editor Page
* Created About Page for directions of use
* Enabled Complex References in Reading Plan

= Version 1.0 Beta Release =
* Initial Release

= Credits =
Andrew Rodgers - Banner images

== Upgrade Notice ==
Added TinyMCE buttons for shortcodes and admin dashboard widgets.