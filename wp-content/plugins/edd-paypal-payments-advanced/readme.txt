=== Easy Digital Downloads - PayPal Payments Advanced Gateway ===
Contributors: iamdavekiss
Tags: paypal, gateway, payments, shop
Requires at least: 3.8
Tested up to: 4.8
Stable tag: 1.1.1
Author: Dave Kiss
Author URL: http://davekiss.com
Version: 1.1.1

Accept Credit Cards and PayPal directly on your WordPress site.

== Copyright ==
Copyright 2017 Dave Kiss

== Description ==

OFFER A SEAMLESS CHECKOUT EXPERIENCE
This PayPal gateway add-on allows you to accept credit cards and PayPal payments directly on your site through your PayPal Payments Advanced account. When purchasing downloads through the PayPal Payments Advanced gateway, users enter their credit card details during the checkout process and never leave your site, resulting in a better experience for the user, and more successful conversions for you.

WHAT IS PAYPAL PAYMENTS ADVANCED?
Keep customers on your site for the entire checkout process without the full burden of protecting their financial data. In addition to a merchant account and gateway in one, PayPal provides you with a secure checkout template to integrate within your website for only $5 a month. Your customers won’t know that PayPal is processing their payment, making your business look more secure and professional.

WHY PAYPAL?
The main reason I choose PayPal over any other processor is that 59 percent of my online sales have been processed using funds in a PayPal account. This number proves that buyers are familiar and comfortable with PayPal and the convenience that it offers for a seamless transaction.

== Installation ==

1. Upload `edd-paypal-payments-advanced.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==
= My payment was successful, but the order is still marked as 'pending' - what gives? =

Makes sure that the "Use Silent Post" setting is turned on in your PayPal manager. This setting can be found on the "Setup" page under your Service Settings in your PayPal manager account.

== Changelog ==
= 1.1.1 =
[Tweak] Fix an issue where changing the label from the default setting would prevent the checkout button from appearing.

= 1.1 =
[New] Restructure plugin code for better efficiency
[Tweak] Change the checkout label to "Continue to Payment" when using PayPal Payments Advanced Gateway

= 1.0.7 =
[Fix] Ensure taxes are calculated correctly on line items
[Tweak] Trim whitespace from PayPal credentials during API requests

= 1.0.6 =
[Fix] Provide better error details when a transaction can't be completed

= 1.0.5 =
[New] Added an option to process a refund in PayPal while marking an EDD payment as refunded.
[New] Updated language files

= 1.0.4 =
[New] Add an option to require the billing address during checkout
[Fix] Ensure the payment mode redirect urls contain the gateway name
[Fix] Ensure the manager username is used as the vendor while in test mode.

= 1.0.3 =
[New] Added language files
[New] Store the transaction ID in EDD's new transaction ID manager
[Fix] Switch to use the edd_is_payment_complete function to check payment status
[Tweak] Update the label for the PayPal Manager template selection setting

= 1.0.2 =
* Updated plugin endpoint location

= 1.0.1 =
* Added fields to allow for API specific users
* Relocated the license key field to the license page
* Fixed the updater to allow for automatic updates

= 1.0 =
* Initial release

== Upgrade Notice ==
= 1.1 =
If you have made any code customizations to this plugin, please verify that your changes are applied cleanly before updating in a live environment.

= 1.0 =
This is the first public release of this plugin.