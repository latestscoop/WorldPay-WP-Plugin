<?php
echo '<div class="wrap">';
	echo '<h1>' . 'WorldPay PayByLink' . '</h1>';
	echo '<p>A simple shortcode that creates a payment link within your content. The user is directed to a product summary page and then directed to WolrdPay to complete the transaction.</p>';
	echo '<form method="post" action="options.php">';
        settings_fields( 'register_settings_paybylink' );
        do_settings_sections( 'register_settings_paybylink' );
//-
		echo '<h3>Settings</h3>';
		echo '<p>Company Name</p>';
		echo '<input type="text" name="paybylink_compName" class="large-text code" value="' . get_option( 'paybylink_compName') . '">';
		echo '<p>Payment page title</p>';
		echo '<input type="text" name="paybylink_page_title" class="large-text code" value="' . get_option( 'paybylink_page_title' ) . '">';
		echo '<p>Payment page name</p>';
		global $paybylink_url_id;
		echo '<p><i>' . ($paybylink_url_id != '' || !is_null($paybylink_url_id) ? ' (Page id: ' . $paybylink_url_id . ')' : '') . '</i></p>';
		echo '<input type="text" name="paybylink_page_name" class="large-text code" value="' . get_option( 'paybylink_page_name' ) . '">';
		echo '<p>Redirect page (optional)</p>';
		echo '<p><i>The shopper will be redirected to this page after payment success. If thie field is left empty the shopper will be taken back to where they started.</i></p>';
		$paybylink_redirect = get_option( 'paybylink_redirect' );
		echo '<input type="text" name="paybylink_redirect" class="large-text code" value="' . $paybylink_redirect . '">';
		echo '<p>Internal confirmation email receipt (optional)</p>';
		echo '<p><i>If thie field is left empty the organisation will not recieve a confirmation of purchase email.</i></p>';
		$paybylink_confirm_email = get_option( 'paybylink_confirm_email' );
		echo '<input type="text" name="paybylink_confirm_email" class="large-text code" value="' . $paybylink_confirm_email . '">';
//-
		echo '<h3>Encryption</h3>';
		echo '<p>Encryption key</p>';
		$paybylink_secret_key = get_option( 'paybylink_secret_key' );
		echo '<input type="text" name="paybylink_secret_key" class="large-text code" value="' . ($paybylink_secret_key == '' ? '*+_)(*&^%$£!' : $paybylink_secret_key) . '">';
		echo '<p>Encryption initialization vector</p>';
		$paybylink_secret_iv = get_option( 'paybylink_secret_iv' );
		echo '<input type="text" name="paybylink_secret_iv" class="large-text code" value="' . ($paybylink_secret_key == '' ? '!£$%^&*()_+*' : $paybylink_secret_key) . '">';
		if(get_option( 'paybylink_compName' ) != '' && $paybylink_secret_key != '' && $paybylink_secret_iv != ''){
			$encrypt_me = paybylink_crypted(get_option( 'paybylink_compName' ),'encrypt');
			echo '<p><i>ENCRYPTION TEST = Company name encrypted: ' . $encrypt_me . ' | Company name decrypted: ' . paybylink_crypted($encrypt_me,'decrypt') . '</i></p>';
		}
//-
		echo '<h3>WorldPay Settings</h3>';
		echo '<p>Form action (live)</p>';
		echo '<p><i>Default: https://secure.worldpay.com/wcc/purchase</i></p>';
		$paybylink_form_action = get_option( 'paybylink_form_action' );
		echo '<input type="text" name="paybylink_form_action" class="large-text code" value="' . ($paybylink_form_action == '' ? 'https://secure.worldpay.com/wcc/purchase' : $paybylink_form_action) . '">';
		echo '<p>Form action (test)</p>';
		echo '<p><i>Default: https://secure.worldpay.com/wcc/purchase</i></p>';
		$paybylink_form_action_test = get_option( 'paybylink_form_action_test' );
		echo '<input type="text" name="paybylink_form_action_test" class="large-text code" value="' . ($paybylink_form_action_test == '' ? 'https://secure-test.worldpay.com/wcc/purchase' : $paybylink_form_action_test) . '">';
		echo '<p>Test mode</p>';
		$paybylink_testMode = get_option( 'paybylink_testMode' );
		echo '<input type="radio" name="paybylink_testMode" value="0" ' . ($paybylink_testMode == '0' || $paybylink_testMode == '' ? 'checked="checked"' : '') . '>Off (0)&nbsp;';
		echo '<input type="radio" name="paybylink_testMode" value="100" ' . ($paybylink_testMode == '100' ? 'checked="checked"' : '') . '>On (100)&nbsp;';
		//echo '<input type="radio" name="paybylink_testMode" value="101" ' . ($paybylink_testMode == '101' ? 'checked="checked"' : '') . '>On (101)';
		echo '<p><i>Test mode applies site wide. Set-up specific short code as tests usinge the; test="on" parameter.</i></p>';
		echo '<p>Installation id</p>';
		echo '<input type="text" name="paybylink_instId" class="large-text code" value="' . get_option( 'paybylink_instId' ) . '">';
		echo '<p>Currency</p>';
		echo '<p><i>eg: GBP or USD</i></p>';
		$paybylink_currency = get_option( 'paybylink_currency' );
		echo '<input type="text" name="paybylink_currency" class="large-text code" value="' . ($paybylink_currency == '' ? 'GBP' : $paybylink_currency) . '">';
//-
    	submit_button();		
    echo '</form>';
//-
	echo '<h3>Shortcode and button builder</h3>';
	echo '<p>Complete all mandatory details to build the secure URL to your product purchase page:</p>';
	$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
	$full_url = $protocol."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$paybylink_url_c = (isset($_POST['paybylink_url_c']) ? $_POST['paybylink_url_c'] : '');
	$paybylink_url_n = (isset($_POST['paybylink_url_n']) ? $_POST['paybylink_url_n'] : '');
	$paybylink_url_d = (isset($_POST['paybylink_url_d']) ? $_POST['paybylink_url_d'] : '');
	$paybylink_url_a = (isset($_POST['paybylink_url_a']) ? $_POST['paybylink_url_a'] : '');
	$paybylink_url_b = (isset($_POST['paybylink_url_b']) ? $_POST['paybylink_url_b'] : '');
	$paybylink_url_t = (isset($_POST['paybylink_url_t']) ? 'test=""' . $_POST['paybylink_url_t'] . '"' : '');
	if($paybylink_url_c && $paybylink_url_n && $paybylink_url_d && $paybylink_url_a && $paybylink_url_b){
		$demo_shortcode_builder = '[paybylink cartid="' . $paybylink_url_c . '" name="' . $paybylink_url_n . '" desc="' . $paybylink_url_d . '" amount="' . $paybylink_url_a . '" button="' . $paybylink_url_b . '" ' . $paybylink_url_t . ']';
		echo '<p style="color:green;">Your shortcode: ' . $demo_shortcode_builder . '</p>';
		echo '<p style="color:green;">Your Button: ' .  do_shortcode($demo_shortcode_builder) . '</p>';
	}else{
		echo '<span style="color:red;">Please complete all mandatory fields</span>';
	}
	echo '<form method="post" action="' . $full_url . '">';
		echo '<p>Product ID:</p>';
		echo '<input type="text" name="paybylink_url_c" class="large-text code" value="' . $paybylink_url_c . '" placeholder="The unique product code">';
		echo '<p>Product name:</p>';
		echo '<input type="text" name="paybylink_url_n" class="large-text code" value="' . $paybylink_url_n . '" placeholder="An easily identifiable name">';
		echo '<p>Product description:</p>';
		echo '<input type="text" name="paybylink_url_d" class="large-text code" value="' . $paybylink_url_d . '" placeholder="A description of the item">';
		echo '<p>Product ammount (1.00):</p>';
		echo '<input type="text" name="paybylink_url_a" class="large-text code" value="' . $paybylink_url_a . '" placeholder="The cost of the item">';
		echo '<p>Button text:</p>';
		echo '<input type="text" name="paybylink_url_b" class="large-text code" value="' . $paybylink_url_b . '" placeholder="The link text that\'s displayed to the user">';
		echo '<p>Test mode (optional):</p>';
		echo '<select name="paybylink_url_t">';
			echo '<option value="">off</option>';
			echo '<option value="on" ' . ($paybylink_url_t != '' ? 'slected' : '') . '>on</option>';
		echo '</select>';
		echo '<br><br>';
		echo '<input type="submit" name="buildit" id="buildit" class="button button-primary" value="Build it!">';
	echo '</form>';
//-
	echo '<br><br>';
	echo '<details>';
	echo '<summary><b>Shortcode examples for testing</b></summary>';
	//echo paybylink_shortcode('cartId=cartId','');
	echo '<p><b>Example:</b></p>';
	$demo_shortcode = '[paybylink cartid="product id" name="product name" desc="product description" amount="1.00" button="BUY NOW"]';
	echo '<p>' . $demo_shortcode . '</p>';
	echo '<p>' .  do_shortcode($demo_shortcode) . '</p>';
	echo '<p><b>Example with test mode on:</b></p>';
	$demo_shortcode_test = '[paybylink cartid="product id" name="product name" desc="product description" amount="1.00" button="BUY NOW" test="on"]';
	echo '<p>' . $demo_shortcode_test . '</p>';
	echo '<p>' .  do_shortcode($demo_shortcode_test) . '</p>';
	global $paybylink_url;
	echo '<p><b>On page confirmations:</b></p>';
	echo '<p>' .  '<a target="_blank" href="' . $paybylink_url . '?pay=successful&pbl=K3FyN2VldzBPL1pvZi9kdE0wZC9ZaC8yR2UwbnlRV09DMzl5UUdyam5sYUkzdzU4eG9SZWhBZmNrVzBXV0E3dE0wQVNEV3dzQmk0aXJqY0tKSnBVRTJRQ1ZQK0hLWG9BMGZBZ2dwdUE0c0E9&pid=product%20id&tid=1234567890">Successful</a>';
	echo ' | ';
	echo '<a target="_blank" href="' . $paybylink_url . '?pay=cancelled&pbl=K3FyN2VldzBPL1pvZi9kdE0wZC9ZaC8yR2UwbnlRV09DMzl5UUdyam5sYUkzdzU4eG9SZWhBZmNrVzBXV0E3dE0wQVNEV3dzQmk0aXJqY0tKSnBVRTJRQ1ZQK0hLWG9BMGZBZ2dwdUE0c0E9">Cancelled (test mode)</a>' . '</p>';
	echo '</details>';
echo '</div>';
?>