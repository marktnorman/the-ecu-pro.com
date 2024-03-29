=== WooCommerce Pixel Manager by woopt ===
Contributors: alekv, woopt, wolfbaer
Tags: woocommerce, google ads, google analytics, facebook pixel, conversion tracking, dynamic retargeting, remarketing, pixel, facebook conversion api, woocommerce google, woocommerce facebook
Requires at least: 3.7
Tested up to: 5.8
Requires PHP: 7.2
Stable tag: 1.11.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Track visitors and conversions in Google Ads, Google Analytics, Facebook Pixel (Conversion API) and more !

== Description ==

This plugin <strong>tracks WooCommerce shop visitors and collects valuable data for conversion optimization, dynamic remarketing and reporting</strong>.

While the setup is as simple as it can get, the pixel engine under the hood is very powerful. It tracks all e-commerce events and implements all advanced pixel features like Facebook CAPI (Pro version), Google Enhanced E-Commerce, Google Shopping Cart Item Tracking and much more. For advanced users the plugin offers filters which allow to tweak the output flexibly and fine tune the behaviour to each shop.

<strong>What sets us apart from other, similar plugins?</strong>

Out of many things, probably high tracking accuracy and a simple user interface are the most important properties that set our plugin apart from others.

Additionally to the standard features offered by the tracking pixels we've developed more functional layers into the plugin which help increase measurement accuracy. For instance, if the plugin detects problems in the shop setup that might lower or prevent accurate tracking, it either fixes them seamlessly, or shows warnings with guidance on how to fix the problems.

Read more about the importance of tracking accuracy over [here](https://docs.woopt.com/wgact/?utm_source=wordpress.org&utm_medium=wooptpm-plugin-page&utm_campaign=woopt-pixel-manager-docs&utm_content=tracking-accuracy#/faq?id=why-is-tracking-accuracy-so-important).

<strong>The plugin comes with integrations for:</strong>

* Google Ads
* Google Analytics (Universal and Google Analytics 4)
* Facebook Ads Pixel
* Google Optimize
* HotJar
* Microsoft Ads (Pro version)
* Twitter Ads (Pro version)
* Pinterest Ads (Pro version)
* Snapchat Ads (Pro version)
* TikTok Ads (Pro version)

<strong>Highlights</strong>

* Facebook Conversion API (CAPI) (Pro version)
* Google Ads Enhanced Conversions (Pro version)
* Google Analytics Enhanced E-Commerce (Pro version)
* Precise measurement by preventing duplicate reporting effectively, excluding admins and shop managers from tracking, and not counting failed payments.
* Collects dynamic remarketing audiences for dynamic retargeting (Google Ads, Facebook, etc.)
* Implements the new Google Add Cart Data functionality. More info about the new feature: [add cart data to the conversion](https://support.google.com/google-ads/answer/9028254)
* Support for various cookie consent management systems

<strong>Free Features</strong>

* Google Ads Conversion Tracking
* Google Ads Dynamic Remarketing
* Google Ads Cart Item Tracking
* Google Shopping New Customer Parameter
* Google Analytics Universal (includes purchase tracking)
* Google Analytics 4 (includes purchase tracking)
* Google Optimize
* Facebook Pixel
* Facebook Remarketing Events
* Hotjar Pixel
* Basic Order Deduplication
* Many useful filters that help tweak the plugin output
* Works with lazy loaded product lists

Have a look at the full feature list over [here](https://docs.woopt.com/wgact/#/features?id=available-features).

<strong>Premium Features</strong>

* Facebook CAPI
* Facebook Microdata Output
* Google Analytics Universal and Google Analytics 4 Enhanced E-Commerce
* Google Consent Mode
* Google Dynamic Remarketing Choice for all Business Verticals
* Google Ads Enhanced Conversions
* Microsoft Advertising Pixel with Purchase and all Remarketing Events
* Twitter Ads Pixel with Purchase and all Remarketing Events
* Pinterest Ads Pixel with Purchase and all Remarketing Events
* Snapchat Ads Pixel with Purchase and all Remarketing Events
* TikTok Ads Pixel with Purchase and all Remarketing Events
* Advanced Order Deduplication

Have a look at the full feature list over [here](https://docs.woopt.com/wgact/#/features?id=available-features).

Are you interested to purchase the [Pro version](https://woopt.com)? Come and visit us [here](https://woopt.com).

<strong>Plugin Compatibility</strong>

The plugin supports and works with following third party plugins.

* WooCommerce Brands
* WooCommerce Composite Products
* WooCommerce Google Product Feed
* WooCommerce Wishlists
* YITH WooCommerce Brands️
* YITH WooCommerce Wishlist️
* Woo Discount Rules️
* WP Marketing Robot Feed Manager

<strong>Documentation</strong>

Link to the full documentation of the plugin: [Open the documentation](https://docs.woopt.com/wgact/?utm_source=wp-org&utm_medium=documentation-link&utm_campaign=wp-org-documentation-link#/)

<strong>Roadmap</strong>

Take a look at our [roadmap](https://app.productstash.io/woopt-woocommerce-pixel-manager) to see what's coming next.

<strong>Cookie Consent Management</strong>

The plugin uses data from several Cookie Consent Management plugins to manage approvals and disapprovals for injection of marketing pixels.

It works with the following Cookie Consent Management plugins out of the box:

* [Cookie Notice](https://wordpress.org/plugins/cookie-notice/)
* [Cookie Law Info](https://wordpress.org/plugins/cookie-law-info/)
* [GDPR Cookie Compliance](https://wordpress.org/plugins/gdpr-cookie-compliance/)
* [Borlabs Cookie](https://borlabs.io/borlabs-cookie/) (from version 2.1.0)
  [Borlabs Cookie Setup](https://docs.woopt.com/wgact/?utm_source=wp-org&utm_medium=documentation-link&utm_campaign=wp-org-documentation-link&utm_content=borlabs-cookie#/consent-mgmt/borlabs-cookie)

<strong>Requirements</strong>

[List of requirements](https://docs.woopt.com/wgact/?utm_source=wp-org&utm_medium=documentation-link&utm_campaign=wp-org-documentation-link&utm_content=requirements#/requirements)

== Installation ==

1. Upload the plugin directory into your plugins directory `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Get the Google Ads conversion ID and the conversion label. You will find both values in the Google Ads conversion tracking code. [Get the conversion ID and the conversion label](https://www.youtube.com/watch?v=p9gY3JSrNHU)
4. In the WordPress admin panel go to WooCommerce and then into the 'Google Ads Conversion Tracking' menu. Please enter the conversion ID and the conversion label into their respective fields.

== Frequently Asked Questions ==

= Is there detailed documentation for the plugin? =

Yes. Head over to this link: [Documentation](https://docs.woopt.com/wgact/?utm_source=wp-org&utm_medium=documentation-link&utm_campaign=wp-org-documentation-link#/)

= How do I check if the plugin is working properly? =

1. Turn off any kind of caching and / or minification plugins.
2. Log out of the shop.
3. Turn off any kind of ad or script blocker in your browser.
4. Search for one of your keywords and click on one of your ads.
5. Purchase an item from your shop.
6. Wait up to 48 hours until the conversion shows up in Google Ads. (usually takes only a few hours)

With the Google Tag Assistant you will also be able to see the tag fired on the thankyou page.

= I get a fatal error and I am running old versions of WordPress and/or WooCommerce. What can I do? =

As this is a free plugin we don't support old versions of WordPress and WooCommerce. You will have to update your installation.

= I am using an offsite payment gateway and the conversions are not being tracked. What can I do? =

We don't support if an offsite payment gateway is in use. The reason is that those cases can be complex and time consuming to solve. We don't want to cover this for a free plugin. We do not recommend offsite payment gateways anyway. A visitor can stop the redirection manually which prevents at least some conversions to be tracked. Also offsite payment gateways are generally bad for the conversion rate.

= I've done everything right in the test, but it still doesn't work. What can I do? =

Here is a non-exhaustive list of causes that might interfere with the plugin code.

* Minification plugins try to minify the JavaScript code of the plugin. Not all minification plugins do this good enough and can cause problems. Turn off the JavaScript minification and try again.
* Caching could also cause problems if caching is set too aggressively. Generally don't ever enable HTML caching on a WooCommerce shop, as it can cause troubles with plugins that generate dynamic output.

= I see issues in the backend of my shop. Admin pages get rendered weird, and popups don't go away when I click to close them. How can I fix this? =

You probably have some script or ad blocker activated. Deactivate it and the issues should go away. Usually you can disable the blocker for just that particular site (your WooCommerce back end).

Our plugin injects tracking pixels on the front end of WooCommerce shops. As a consequence scripts of our plugin have been added to some privacy filter lists. The idea is to prevent the scripts running if a shop visitor has some ad blocker enabled and wants to visit the front end of the shop. This is totally ok for visitors of the front end of the shop. But, it becomes an issue for admins of the shop who have a blocker activated in their browser and visit the backend of the shop.

Unfortunately there is no way for us to generally approve our scripts in all blockers for the WooCommerce back end.

Therefore we recommend admins of the shop to exclude their own shop from the blocker in their browser.

= Where can I report a bug or suggest improvements? =

Please post your problem in the WGACT Support forum: http://wordpress.org/support/plugin/woocommerce-google-adwords-conversion-tracking-tag
You can send the link to the front page of your shop too if you think it would be of help.

== Screenshots ==

1. Settings page

== Changelog ==

= 1.11.2 =


* New: Added shortcodes for Snapchat and TikTok
* New: Saving referrer in a wooptpm cookie

* Tweak: Moved Freemius to composer vendor directory
* Tweak: Improved handler to save cookies for FB session
* Tweak: Added fallback to get an order from the order received page in case a plugin messes with query vars


= 1.11.1 =  05.08.2021

* New: Automatic cache purge for Kinsta on plugin updates and saved settings
* Tweak: Added safeguard in order to prevent a fatal error when coming across a variation that doesn't properly link to a parent product
* Tweak: Added filters to suppress certain admin notifications
* Tweak: Changed global for notifications db name
* Tweak: Changed function declaration for varExists in order to work around a Google Closure bug

* Fix: Built in a safeguard for the debug info in case no order exists yet in the shop

= 1.11.0 =  26.07.2021


* Tweak: Added coupons to GA UA and GA4 purchase events
* Tweak: Avoid an issue when trying to read WP Rocket options when no options exist yet
* Tweak: Removed some unnecessary parameters from Google Ads purchase confirmation script
* Tweak: Output purchase value with max two decimals

* Fix: Fixed some events on cart page for variable products

= 1.10.11 =  19.07.2021


* New: Filter for order items
* New: Implemented warning for incompatible plugins

* Tweak: Refactored wooptpm.setCookie() so that it can create session cookies

* Fix: Output of product prices as float
* Fix: Added string to float conversion in wooptpm_get_order_item_price to make sure a float is returned

= 1.10.10 =  7.7.2021


* New: Automatically flush cache of caching plugins and platforms on plugin settings changes
* New: Report hosting platform in debug info
* Tweak: Added new compatibility exclusions for WP Rocket
* Tweak: Added option to filter GA4 parameters

= 1.10.9 =

* New: Filter for custom brand taxonomy
* New: Added payment gateway usage and successful redirect stats to free version

* Tweak: Refactored several elements in order to render correctly in RTL environments
* Tweak: If product belongs to multiple categories, output them in a comma separated list for GA
* Tweak: Automatically detect if mini cart is being used, and depending on this, enable or disable wooptpm_get_cart_items
* Tweak: Caching cart contents in sessionStorage in order to avoid as many API calls to backend as possible

* Fix: Fixed version check for db upgrade function in case someone has been using one of the earliest versions of the plugin

= 1.10.8 =

* Tweak: Fallback on PayPal check if option doesn't exist in the db
* Tweak: Removed manual function override from Freemius source
* Tweak: Added page type output on order received page
* Tweak: Switched data wooptpmProductId output on product pages to meta tag for main product

* Fix: Fixed ViewContent id_type variable for Facebook, GA UA and GA4
* Fix: Added dynamic remarketing purchase script

= 1.10.7 =


* New: Added hook_suffix into debug info

* Tweak: Adjusted the $hook_suffix check in admin.php because on some installs the $hook_suffix output is buggy

= 1.10.6 =


* New: Added conversion accuracy warning if the PayPal standard payment gateway is active
* New: Added support for WooCommerce composite products

* Tweak: Implemented better way to reference .js and .css files
* Tweak: Removed jquery.cookie.js dependency

= 1.10.5 =

* Tweak: parseFloat for value in Facebook and Pinterest just in case no proper input is received
* Tweak: Added an exclusion to fix an issue with Microsoft Ads, caused by Cloudflare Rocket Loader
* Tweak: Removed launch deal code
* Tweak: Cleaned up some cruft
* Tweak: Added new element selector for the intersection observer for product templates that pack the wooptpmProductId into a child > child hierarchy
* Fix: Added default quantity if quantity field has been removed on a product page
* Fix: Switched to treat order IDs as strings. Otherwise it would throw an error if a shop owner changes order IDs to also use letters.

= 1.10.4 =

* New: Added filter for Google cross domain filter setting
* Tweak: Added more safeguards in case WC processes a product ID which is not a product
* Fix: Product data output on some pages triggered a critical error

= 1.10.3 =

* Tweak: Switch to use different hook on product pages to output main product data

= 1.10.2 =

* Tweak: Renamed and deprecated several old filters



= 1.10.1 =

* New: Added a debug test to see if the server can reach the outside world with a wp_remote_get() request

* Tweak: Added better fallback for cookie retrievals if cookies can't be saved in the session
* Fix: Fire view_item event on products that are out of stock
* Fix: In case the redirect check returns an array of redirects, we return the last array member.

= 1.10.0 =


* Tweak: Only show the rating notice to admins of the shop.
* Tweak: Added more exclusions for WP Rocket
* Tweak: Added exclusions for WP Optimize
* Tweak: Additional exclusions for Autoptimize
* Tweak: Removed hit on for parent IDs on a variable product page

= 1.9.6 =


* Tweak: Added one more tweak for LiteSpeed Cache users

* Fix: Changed reading options from database to passing options from primary instance in order to avoid options caching issues on saving.

= 1.9.5 =


* Tweak: Added dependencies to enqueued scripts
* Tweak: Improved the parent selector for the mutation observer for lazy loaded products
* Tweak: Implemented better product selector for modification observer
* Tweak: Added new selector for related products



= 1.9.4 =



* Tweak: Finalized improvement for front-end error handling
* Tweak: Added a JS modification exclusion for Autoptimize in order to prevent our script to get modified and broken
* Fix: Fixed a selector for cart items on the cart page which caused on certain custom shop templates to trigger an error

= 1.9.3 =

* Tweak: Added one more layer of safeguards if wooptpm.js can't evaluate the current productId

= 1.9.2 =

* Tweak: Added one more safeguard if wooptpm.js can't evaluate the current productId

= 1.9.1 =


* Tweak: Added some some safeguards in order to stop processing in case no productId can be evaluated
* Tweak: Removed deprecated "disable gtag insertion" feature entirely


= 1.9.0 =



* Tweak: Additional caching exclusions for SG Optimizer
* Tweak: Changed the gtag code in order to make it better testable
* Tweak: Moved some scripts to the footer
* Tweak: Improved add_to_cart trigger
* Tweak: Refactored view_item_list event entirely to be unaffected by caching mechanisms
* Tweak: Added a new view_item_list trigger with some interesting options
* Fix: Fixed front-end triggers for Google and Facebook to only fire if the pixels are enabled
* Fix: Fixed an array check if an old WP Rocket version was installed and threw a notice about a missing array index
* Fix: Output correct price if WPML Multilingual with Multi-currency is running

= 1.8.28 =

* New: Filter to switch Google Analytics ID output to SKU
* New: Process discounted order item price for GA if Woo Discount Rules is active


* Tweak: Moved getCartItems to document.load event
* Tweak: Added Freemius purchase conversion
* Tweak: Avoid number output with too many decimals
* Tweak: More reliable method to get order from order received page

* Fix: Proper variable types for purchase confirmation variables
* Fix: Initialize wooptpmDataLayer.pixels early, so that all pixels can use it
* Fix: Replaced $order->get_id() with $order->get_order_number in order to fix a bug on a small subset of shops
* Fix: Get proper WP db prefix for refunds SQL query

= 1.8.27 =

* Tweak: LD check

= 1.8.26 =

* Tweak: Implemented permanent compatibility mode for SG Optimizer
* Tweak: Implemented permanent compatibility mode for LiteSpeed Cache
* Tweak: Refactored the Facebook pixel and events

= 1.8.25 =

* Tweak: Implemented permanent compatibility mode for WP Rocket
* Fix: Refactored a JavaScript regex that was not working in Safari

= 1.8.24 =

* Fix: Added a function that should be available in the free version conversion_pixels_already_fired_html

= 1.8.23 =

* Fix: Include output of variable products into the visible_products object
* Fix: Added a missing opening tag for shops that are still using the gtag deactivation option

= 1.8.22 =



* Tweak: Partially decoupled pixels from pixel manager
* Tweak: Refactored browser e-commerce events into pubsub

* Fix: Under some circumstances rating_done is not set in the wgact_ratings option. This fix adds this default option.
* Fix: Fixed the GA 4 config command

= 1.8.21 =


* New: Added output of related up- and cross-sell product view_item_list list events for Google Ads dynamic remarketing
* New: Added &nodedupe URL parameter for testing the order confirmation page
* Tweak: Build in a fallback for misconfigured variable products that trigger an "Error: Call to a member function get_sku() on bool"

= 1.8.20 =

* Fix: Fixed the Google Analytics config filter

= 1.8.19 =

* New: Added Pro feature demo mode
* New: Added shortcodes for tracking leads and similar conversions
* New: Filter to adjust Google Analytics config parameters

= 1.8.18 =


* New: Google Analytics link attribution
* Tweak: Bumping up WP supported version to 5.7
* Tweak: Some code syntax cleanup


= 1.8.17 =

* Tweak: Remove some freemius options

= 1.8.16 =


* New: Output the variation ID for dynamic remarketing
* New: Maximum compatibility mode

* Tweak: Switched logic to activate conversion cart data automatically when merchant center ID is set
* Tweak: Made Google Analytics always receive the post ID as product ID because this is more robust
* Tweak: Removed some unnecessary text output in the settings
* Fix: Script blocker documentation link

= 1.8.15 =


* Fix: Determine correctly new customer for shopping cart data on new customers who paid immediately
* Tweak: Created new trait to calculate brand for product
* Tweak: Added a new array with additional product attributes (like brand which is calculated)
* Tweak: added ability to load traits in autoload.php
* Tweak: Bumped up WC version
* Tweak: Added an additional is_array() check in order to suppress a PHP 7.4 notice when checking the environment

= 1.8.14 =

* Tweak: Make the <noptimize> tag only appear if Autoptimize is active
* Tweak: Removed a duplicate filter
* Fix: Moved get_cart() query into is_cart() condition

= 1.8.13 =

* New: Filter to prevent conversion pixels to fire on purchase confirmation page
* Tweak: Replaced _e() with echo where necessary
* Tweak: Syntax cleanup

= 1.8.12 =

* Tweak: Calculate filtered order total for all pixels

= 1.8.11 =


* Fix: Removed a function call where the function was missing

= 1.8.10 =


* New: Added basic order deduper
* New: Google Shopping new_customer parameter
* New: Added switch to enable transaction deduping (default enabled)
* Tweak: Product identifier output now for all the same
* Tweak: Adjusted the HTML comment output
* Tweak: Added new cookie for Borlabs Cookie
* Tweak: Made some input elements clickable
* Tweak: Moved check for failed payments and admin and shop manager up to the pixel manager

= 1.8.9 =

* Tweak: readme.txt links
* Tweak: fallback to post ID in case the SKU is not set
* Tweak: Adjusted the HTML comments
* Tweak: Don't inject cart scripts on cart page if cart is empty

= 1.8.8 =


* Tweak: Bumped up version
* Tweak: Changed regex for GMC IDs to allow 7 digit IDs
* Tweak: Improved speed to hide script blocker warning
* Tweak: Adjusted documentation links

= 1.8.7 =

* Fix: Added new classes to SVN

= 1.8.6 =

* Tweak: Code cleanup
* Tweak: Adjusted doc links

= 1.8.5 =


* New: Hotjar pixel

= 1.8.4 =


* Tweak: Renamed subsection 'Order Logic' to 'Shop'
* Tweak: Refactored debug info
* Tweak: Added WP Rocket JavaScript concatenation to debug info

= 1.8.3 =



= 1.8.2 =

* New: Check for WP Rocket JavaScript concatenation
* New: Added filter which helps adding multiple additional conversion IDs and labels

= 1.8.1 =

* Fix: Version number
* Fix: FB default pixel id

= 1.8.0 =

* New: Google Analytics UA standard beta
* New: Google Analytics 4 beta
* New: Google Optimize beta
* New: Activation indicators
* Tweak: Put admin scripts into header for faster rendering
* Fix: Detect proper admin path in tabs.js

= 1.7.13 =

* New: Facebook pixel
* Tweak: Adjust db and bump up to version 3
* Tweak: Introduced Pixel_Manager and restructured Google Ads class

= 1.7.12 =

* Fix: Removed namespace for main class because it was conflicting with freemius in some cases

= 1.7.11 =

* Fix: Directory name fix
* New: Warning message if an ad- or script-blocker is active
* Tweak: Improved one of the db saving functions
* Tweak: Start using namespaces


= 1.7.10 =

* Fix: child theme detection

= 1.7.9 =

* Fix: Roll back to 1.7.7 since namespace don't work everywhere
* Fix: child theme detection

= 1.7.8 =

* New: Warning message if an ad- or script-blocker is active
* Tweak: Improved one of the db saving functions
* Tweak: Start using namespaces

= 1.7.7 =

* Fix: Don't show the rating popup if an admin uses a script blocker

= 1.7.6 =

* Fix: Improved check if dynamic remarketing settings already has been set before checking for it.
* Fix: Saving to the database threw sometimes warnings that have been fixed.
* Tweak: Styling changes

= 1.7.5 =

* New: Added checks for freemius servers
* New: Dynamic remarketing pixels
* New: Deactivation trigger for the WGDR plugin if dynamic remarketing is enabled
* Fix: Adjusted the cookie name for Cookie Law Info
* Fix: Improved detection if WooCommerce is active on multisite
* Fix: Fixed default setting for conversion_id
* Tweak: Added back rating testing code
* Tweak: Adjusted some links
* Tweak: Code style cleanups

= 1.7.4 =

* Fix: Fixed the ask for rating constant

= 1.7.3 =

* Fix: Don't open the rating page if user clicks on already done
* Tweak: Backward compatibility to PHP 7.0

= 1.7.2 =

* Fix: Fixed a printf syntax error that caused issues on some installations

= 1.7.1 =

* Tweak: Removed deletion of settings on uninstall in order to preserve the settings

= 1.7.0 =

* New: Added German translations
* Fix: Reversed some code in freemius to make it compatible with older versions of PHP (< PHP 7.2)
* Fix: Fixed the uninstall hook for it to work with freemius
* Tweak: Added some comments for translators
* Tweak: Removed old language packs
* Tweak: Add gtag config if gtag insertion is disabled
* Tweak: Rating request improved
* Tweak: Removed plugin ads
* Tweak: Added documentation
* Tweak: Updated db scheme
* Tweak: Merge new default options recursively
* Tweak: On save merge new and old options recursively, set missing checkbox options to zero, omit db_version

= 1.6.17 =

* Tweak: Reactivate freemius

= 1.6.16 =

* Fix: Deactivate freemius

= 1.6.15 =

* Tweak: Adjusted freemius main slug for plugin

= 1.6.14 =

* New: Implemented Freemius telemetry
* Tweak: Adjustments to the descriptions and links to new documentation
* Tweak: Only run if WooCommerce is active

= 1.6.13 =

* New: Implemented framework for sections and subsections
* Tweak: Some code cleanup
* Tweak: Made strings more translation friendly
* Tweak: Properly escaped all translatable strings
* Fix: Textdomain

= 1.6.12 =

* New: Plugin version output into debug info
* Fix: Conversion id validation
* Tweak: Moved JavaScript to proper enqueued scripts
* Tweak: Bumped up WC and WP versions

= 1.6.11 =

* New: Tabbed settings
* New: Debug information
* Tweak: Code style adjustments

= 1.6.10 =

* Fix: Disabled some error_log invocation since it can cause issues in some rare server configurations

= 1.6.9 =

* Fix: Re-enabled settings link on plugins page

= 1.6.8 =

* Fix: Changed how Borlabs Cookie activation works

= 1.6.7 =

* Fix: Implemented check for Borlabs minimum version

= 1.6.6 =

* New: Added option to disable the pixel with a filter add_filter( 'wgact_cookie_prevention', '__return_true' )
* New: Added Borlabs cookie management approval for marketing
* Tweak: Refactored the code into classes

= 1.6.5 =

* Tweak: Removed duplicate noptimize tag
* Tweak: Removed CDATA fix since it is not necessary anymore with the new conversion tag

= 1.6.4 =

* Fix: Fixed the calculation for the non-default order total value (which includes tax and shipping)

= 1.6.3 =

* Info: Tested up to WP 5.4

= 1.6.2 =

* Tweak: More reliable method to detect the visitor country added

= 1.6.1 =

* New: Add Cart Data feature
* New: Added a switch to disable the insertion of the gtag
* Tweak: Added more descriptions on the settings page
* Tweak: Code optimisations

= 1.5.5 =

* Tweak: Made the conversion ID and label validation code more robust

= 1.5.4 =

* Tweak: Updated function that inserts the settings link on the plugins overview page

= 1.5.3 =

* Info: Tested up to WP 5.2

= 1.5.2 =

* Fix: Correctly calculate the value when no filter is active

= 1.5.1 =

* Tweak: Re-enabled order value filter

= 1.4.17 =

* Info: Tested up to WP 5.1

= 1.4.16 =

* Info: Updated a few text strings

= 1.4.15 =

* Info: Changing name from AdWords to Google Ads

= 1.4.14 =

* Info: Tested up to WC 3.5.3

= 1.4.13 =

* Info: Tested up to WC 3.5.2

= 1.4.12 =

* Tweak: bumping up the WC version

= 1.4.11 =

* Tweak: remove some debug code
* fix: properly save the order_total_logic option

= 1.4.10 =

* Tweak: switched sanitization function to wp_strip_all_tags

= 1.4.9 =

* Tweak: Added input validation and sanitization
* Tweak: Added output escaping

= 1.4.8 =

* Tweak: Added discounts into order value calculation

= 1.4.7 =

* New: Switched over to the newest version of the AdWords conversion tracking pixel

= 1.4.6 =

* Tweak: Disabled minification through Autoptimize

= 1.4.5 =

* Tweak: Order ID back in apostrophes

= 1.4.4 =

* Tweak: Switched on JavaScript tracking with a fix for the CDATA bug http://core.trac.wordpress.org/ticket/3670
* Tweak: The correct function is being used to get the currency depending on the WooCommerce version
* Fix: Added missing </noscript> tag

= 1.4.3 =

* Tweak: Remove campaign URL parameter

= 1.4.2 =

* Fix: Backward compatibility for $order->get_currency()

= 1.4.1 =

* Tweak: Making the plugin PHP 5.4 backwards compatible
* Fix: Fixing double counting check logic

= 1.4 =

* New: Ask kindly for a rating of the plugin
* New: Add a radio button to use different styles of order total
* Tweak: Consolidate options into one array
* Tweak: Code cleanup

= 1.3.6 =

* New: WordPress 4.8 compatibility update
* Tweak: Minor text tweak.

= 1.3.5 =

* Fix: Fixed a syntax error that caused issues on some installations.

= 1.3.4 =

* Tweak: Added some text output to make debugging for users easier.

= 1.3.3 =

* Tweak: Refurbishment of the settings page

= 1.3.2 =

* New: Uninstall routine

= 1.3.1 =

* New: Keep old deduplication logic in the code as per recommendation by AdWords

= 1.3.0 =

* New: AdWords native order ID deduplication variable

= 1.2.2 =

* New: Filter for the conversion value

= 1.2.1 =

* Fix: wrong conversion value fix

= 1.2 =

* New: Filter for the conversion value

= 1.1 =

* Tweak: Code cleanup
* Tweak: To avoid over reporting only insert the retargeting code for visitors, not shop managers and admins

= 1.0.6 =

* Tweak: Switching single pixel function from transient to post meta

= 1.0.5 =

* Fix: Adding session handling to avoid duplications

= 1.0.4 =

* Fix: Skipping a tag version

= 1.0.3 =

* Fix: Implement different logic to exclude failed orders as the old one is too restrictive

= 1.0.2 =

* Fix: Exclude orders where the payment has failed

= 1.0.1 =

* New: Banner and icon
* Update: Name change

= 1.0 =

* Update: Release of version 1.0!

= 0.2.4 =

* Update: Minor update to the internationalization

= 0.2.3 =

* Update: Minor update to the internationalization

= 0.2.2 =

* New: The plugin is now translation ready

= 0.2.1 =

* Update: Improving plugin security
* Update: Moved the settings to the submenu of WooCommerce

= 0.2.0 =

* Update: Further improving cross browser compatibility

= 0.1.9 =

* Update: Implemented a much better workaround tor the CDATA issue
* Update: Implemented the new currency field
* Fix: Corrected the missing slash dot after the order value

= 0.1.8 =

* Fix: Corrected the plugin source to prevent an error during activation

= 0.1.7 =

* Significantly improved the database access to evaluate the order value.

= 0.1.6 =

* Added some PHP code to the tracking tag as recommended by Google.

= 0.1.5 =

* Added settings field to the plugin page.
* Visual improvements to the options page.

= 0.1.4 =

* Changed the woo_foot hook to wp_footer to avoid problems with some themes. This should be more compatible with most themes as long as they use the wp_footer hook.

= 0.1.3 =

* Changed conversion language to 'en'.

= 0.1.2 =

* Disabled the check if WooCommerce is running. The check doesn't work properly with multisite WP installations, though the plugin does work with the multisite feature turned on.
* Added more description in the code to explain why I've build a workaround to not place the tracking code into the thankyou template of WC.

= 0.1.1 =

* Some minor changes to the code

= 0.1 =

* This is the initial release of the plugin. There are no known bugs so far.
