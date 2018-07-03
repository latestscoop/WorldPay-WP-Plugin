# WorldPay-WP-Plugin
A simple WordPress plugin that enables a WorldPay payment link to be added to your content via a shortcode. 

After installing and activating the plugin, visit the settings page and enter details such as:
+Company Name
+WorldPay installation id
+Currency code
+Redirect page
+AES-256-CBC encryption hashes (key & initialization vector)

An example shotcode can be found on the setting page. The short code contains the following details:
+Product id
+Product name
+Product description
+Product price
+Payment button text

The shortcode creates a payment link that directs the user to a payment summary page (page and template added on install). The customer then has the option to cancel and return to the previous page, or continue to WorlPay for payment.

Custom paramaters are sent to WorldPay which place payment success and payment failure/cancel links within the cart, allowing the user to return to the site. These links are enabled by custom WorldPay payment pages (resultC.html / resultC.html), which are also included. All available WorldPay parameters are listed within template.php, with the unused params commented out.

Test mode is availableon on a per shortcode basis or site-wide. Test mode transfers payments to WorldPay test environment. A possible update should include the ability to disable and hide all active payment shortcodes.
