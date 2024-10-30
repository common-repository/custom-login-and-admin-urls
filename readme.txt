=== Plugin Name ===
Contributors: lightningspirit
Donate link: http://vcarvalho.com/
Tags: plugin, security, login, admin, wp-admin, wp-login, security by obscurity, custom url, mu, multisite, network
Requires at least: 3.2
Tested up to: 3.2.1
Stable tag: 0.0

AVOID TO USE THIS PLUGIN! Change wp-admin and wp-login URL to any of your choice. Security by obscurity!

== Description ==

**This plugin is DEAD until further development.**
**Don't use it as this can lead your WordPress instalation into problems.**


With this plugin you can change wp-admin and wp-login.php to any of your choice.
You can have `http://example.org/login` instead of `http://example.org/wp-login.php` or `http://example.org/admin/` instead of `http://example.org/wp-admin/`

You just need to enter into `options-permalink.php`page and set the variables.
After the setup wp-login.php and wp-admin path are not anymore available and you should use the newest onews you set.

This works with Multisite Networks. You can set a default value in Options page of Multisite settings section.

Languages available: English, Português, Español, Italiano, Deutsch.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `custom-login-and-admin-urls` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Permalink Options page inside the administration area
4. Set the fields Login Base and Admin Base to the desired ones

== Frequently Asked Questions ==

= Does this work with Multisite =
Yes, It works with multisite networks.

= What about the default URLs? =
Well, after you active your plugin the defaults goes to `login`and `admin` URLs.
For example, you should be able to login in `http://example.org/login` and enter in administration area `http://example.org/admin/`


== Screenshots ==
1. Wordpress Login page with custom URL.
2. Blog Permalinks Options page, where you are able to set your custom URL.
3. Multisite Options page, where you can set a default login URL for all new blogs.

== Changelog ==

= 0.1.2 =
* Bugfix: Login wasn't redirect from wp-admin. Throw a filter error.

= 0.1.1 =
* Bugfixes: login not working properly after install
* Add Multisite default value

= 0.1 =
* Sets basic login and admin redirects.
* Able to change links through permalink options page.

== Upgrade Notice ==

= 0.1.1 =
* Bugfixes: login not working properly after install
* Add Multisite default value
