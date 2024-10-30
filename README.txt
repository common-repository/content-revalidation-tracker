=== Content Revalidation Tracker ===
Contributors: Dropndot
Donate link: https://dropndot.com
Tags: revalidation, content, API, post updates, user updates
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Content Revalidation Tracker keeps your external systems (e.g., Next.js) up-to-date with the latest changes to posts, pages, and user profiles on your WordPress site.

== Description ==

Introducing the "Content Revalidation Tracker" plugin – your solution to keeping your external systems (e.g., `Next.js`) synchronized with the latest changes on your WordPress site! Automatically track updates to posts, pages, and user profiles, and send relevant data to an external API for revalidation.

= Features =

* Track updates to posts, pages, and user profiles.
* Send revalidation requests to an external API.
* Easy-to-use settings page to configure the external API endpoint and secret key.

== Installation ==

1. Download the plugin ZIP file.
2. Log in to your WordPress admin panel.
3. Navigate to Plugins > Add New.
4. Click Upload Plugin and select the downloaded ZIP file.
5. Click Install Now and then Activate the plugin.

== Configuration ==

1. After activating the plugin, go to Settings > Content Revalidation Tracker.
2. Enter your external API domain in the Domain field (e.g., `https://example.com`).
3. Enter your API secret key in the Secret Key field (e.g., `ad43d49b3f2e4847a6f6`).
4. Click Save Changes.

== Usage ==

Once configured, the plugin will automatically send revalidation requests to your external API whenever the following actions occur:
* **Posts and Pages:** When a post or page is created, updated, or deleted.
* **User Profiles:** When a user profile is created, updated, or deleted.

== Frequently Asked Questions ==

= How do I configure the plugin? =

Navigate to Settings > Content Revalidation Tracker and enter your API domain and secret key.

= What happens if the API request fails? =

The plugin logs the error, which can be reviewed in the WordPress error log for debugging.

= Can I customize the API request? =

Currently, the plugin sends predefined parameters in the API request. For advanced customization, you can modify the plugin code.

= Is this plugin compatible with custom post types? =

Yes, the plugin tracks all post types, including custom post types.

== Screenshots ==

1. Plugin settings page.

== Changelog ==

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
Initial release of the Content Revalidation Tracker plugin.

== Screenshots ==

1. **Settings Page:** Configure your API domain and secret key.
2. **Error Logs:** View API request errors and responses in the WordPress error log.

== Support ==

For support and troubleshooting, please visit [Dropndot Solutions](https://dropndot.com).
