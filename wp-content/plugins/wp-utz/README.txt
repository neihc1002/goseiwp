=== Plugin Name ===
Contributors: khill07
Donate link: http://www.wpcode.center/donate/
Tags: timezone, time zones, user timezone, time
Requires at least: 5.4.0
Tested up to: 5.4.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows the site-level timezone to be changed at the user profile level

== Description ==

WordPress displays all dates and times based on a site-level timezone setting configured in the admin back-end. The problem is when you user base handles people from all over the world date and times really don't mean much any more.

This plugin enables the user to select their own timezone and it handles the date and time conversion in both the front-end and admin section of the site.

The plan is to start out simple and improve from there. Currently, due to some complexities with time conversions, this plugin requires your site-level timezone to be set to UTC+0, UTC or some other timezone with no UTC offset. You can find this setting in the admin section under Settings -> General.

Also, please note that if you are changing your site-level timezone, this will not update any data in the database.  WordPress simply assumes all date and times entered, where entered in UTC.  This will unfortunately shifted all date and times by the number of hours your old timezone was offset from UTC.  But please keep in mind that storing date/times in UTC is a good habit and all new date and times entered correct with not shift. 

== Screenshots ==

1. Plugin Settings
2. User Profile Settings
3. Admin Toolbar Local and Site times

== Changelog ==

= 1.0.0 =
* First Release
