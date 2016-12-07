=== Woocommerce Add Countries ===
Contributors: danieledesantis
Tags: woocommerce, ecommerce, countries list, add country, add countries, more countries, rename country, rename countries, countries, country
Requires at least: 3.9.1
Tested up to: 4.5.3
Stable tag: 1.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Woocommerce Add Countries is a lightweight plugin which allows you to add new countries to the Woocommerce countries list. Requires Woocommerce.

== Description ==

Woocommerce Add Countries is a lightweight plugin which allows you to add new countries to the Woocommerce countries list.
This plugin is useful if you want to add a new country to the Woocommerce countries list, or if you need to divide a country into different zones to have a different shipping cost for each one.

Let's say, for example, that you have different shipping costs for England, Scotland, Wales and Northern Ireland. In Woocommerce you will only find United Kingdom. With "Woocommerce Add Countries" you can easily add the following countries to the list from the plugin's settings page:

`ENG, England, EU
SCT, Scotland, EU
WLS, Wales, EU
NIR, Northern Ireland, EU`

For a complete list of country codes defined in ISO 3166 standard take a look here: [ISO CODES - Wikipedia](http://en.wikipedia.org/wiki/ISO_3166-1_alpha-2).

Remember to add the continent code at the end of each line (required by the new Woocommerce Shipping Zones). Here's a list of available continent codes: AF (Africa), AN (Antarctica), AS (Asia), EU (Europe), NA (North America), OC (Oceania), SA (South America).
Example:

`NAF, New African Country, AF
NAC, New Asian Country, AS
NEC, New European Country, EU`

The plugin requires Woocommerce.

== Installation ==

= Automatic installation =

1. Go to Plugins > Add New > Upload and select the .zip file from your hard disk
2. Click the "Install now" button
3. Activate the plugin through the 'Plugins' menu in WordPress

= Manual installation =

1. Upload the plugin folder to the `/wp-content/plugins/` directory via ftp
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How do I add new countries? =

Once the plugin is installed and activated, from your dashboard navigate to Woocommerce > Add countries, then write the desired ISO code, country name and continent code separated by a comma.
Here's a list of available continent codes: AF (Africa), AN (Antarctica), AS (Asia), EU (Europe), NA (North America), OC (Oceania), SA (South America).
You can add as many countries as you want, just enter each country on a new line.

Example:

`NAF, New African Country, AF
NAC, New Asian Country, AS
NEC, New European Country, EU`

== Screenshots ==

1. Add new countries.
2. Select them from Woocommerce > Settings.
3. They will be available in your shop.

== Changelog ==

= 1.1 =
* Added support for Woocommerce shipping zones

= 1.0 =
* First release

== Upgrade Notice ==

= 1.1 =
* Added support for Woocommerce shipping zones
* Fixed minor bugs