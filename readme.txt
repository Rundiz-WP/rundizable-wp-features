=== Rundizable WP Features ===
Contributors: okvee
Tags: disable features, pages, media, front-end
Requires at least: 4.0
Tested up to: 4.9.6
Stable tag: 0.2
Requires PHP: 5.5
License: MIT
License URI: https://opensource.org/licenses/MIT

Disable WordPress features such as pages, media, front-end.

== Description ==
If you want to make WordPress as a backoffice system, this plugin can help you disable WordPress features you do not want. It is working great with Disable Blogging plugin ( https://wordpress.org/plugins/disable-blogging/ ).
This plugin can:
- disable pages, media, front-end.
In case that you disable front-end it will be redirect all URL in the front-end to wp-admin page.

= System requirement =
PHP 5.5 or higher
WordPress 4.0 or higher

== Installation ==
1. Upload "rundizable-wp-features" folder to the "/wp-content/plugins/" directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Access plugin setup page.
4. Follow setup instruction on screen.

== Frequently Asked Questions ==
= Multi-site support? =
Yes, it is.

== Screenshots ==
1. Settings page.

== Changelog ==
= 0.2 =
2018-06-22

* Remove Media widgets if disabled Media section.
* Remove Pages widget if disabled Pages section.

= 0.1 =
2018-06-13

* Add condition if doing cron then do not redirect if front-end is disabled.
* The beginning