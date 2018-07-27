<?php /*Template Name: paybylink*/ ?>
<?php
/*
headers
*/
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
?>
<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$paybylink_url_params_get = false;
	$paybylink_url_params_array = false;
	if(isset($_GET['pbl']) !== false){
		$paybylink_url_params_get = paybylink_crypted($_GET['pbl'],'decrypt');
		parse_str($paybylink_url_params_get, $paybylink_url_params_array);
		extract($paybylink_url_params_array);
		$cartId = $c;
		$name = $n;
		$desc = $d;
		$amount = $a;
		$button = $b;
		$paybylink_testMode = (isset($t) && $t != '' ? '100' : '0');
		$paybylink_url_params_get = paybylink_crypted($paybylink_url_params_get,'encrypt');
	}
	
	$paybylink_success = ($paybylink_redirect == '' ? $_SESSION['paybylink_button_url'] : $paybylink_redirect);
	if(isset($_GET['pay']) && $_GET['pay'] == 'successful'){
		$paybylink_confirm_email = get_option( 'paybylink_confirm_email' );
		if($paybylink_confirm_email != ''){
			$to = $paybylink_confirm_email;
			$from = (get_bloginfo('admin_email') != '' ? get_bloginfo('admin_email') : $to);
			$subject = 'WorldPay - Confirmation Email';
			$message = '<h1>' . 'Payment confirmation' . '</h1>';
			$message .= '<p>' . 'Product name: ' . $name . '</p>';
			$message .= '<p>' . 'Product description: ' . $desc . '</p>';
			$message .= '<p>' . 'Product id: ' . $cartId . '</p>';
			$message .= '<p>' . 'Price: ' . $amount . '</p>';
			$message .= '<p>' . 'Date/time: ' . date("l jS \of F Y h:i:s A") . '</p>';
			$message .= '<p>' . 'Transaction ID: ' . (isset($_GET['tid']) ? $_GET['tid'] : 'error') . '</p>';
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .="From:" . $from . "\r\n" . "Reply-To: " . $from . "\r\n";
			wp_mail( $to, $subject, $message, $headers );
		}
		header( "refresh:2;url=" . $paybylink_success);
	}
?>
<html>
<head>
<title><?php echo $paybylink_page_title; ?></title>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,300,500,700" type="text/css">
	<style>
		.container{
			border: 1px solid #555;
			border-radius: 5px;
			width: 610px;
			padding: 5px;
			margin: auto;
			text-align: center;
		}
		.continue{
			display:inline-block;
			border: 1px solid #555;
			border-radius: 5px;
			background:none;
			padding:10px 5px;
		}
		.continue:hover{cursor:pointer;}
	</style>
	<script src="http://code.jquery.com/jquery-latest.min.js"></script>
<body>
	<div class="container">
	<?php if(isset($_GET['pay']) && $_GET['pay'] == 'successful') : ?>
		<h1>Secure payment successful</h1>
		<p>You will be redirected <a href="<?php echo $paybylink_success; ?>">here</a> in one moment.</p>
	<?php else : ?>
		<h1>Secure payments</h1>
		<?php 
			if(isset($_GET['pay']) && $_GET['pay'] == 'cancelled'){
				echo '<p>Sorry your payment was cancelled or unsuccessful.</p>';
			}
		?>
		<p><?php echo 'Item name: <br>' . $name; ?></p>
		<p><?php echo 'Item cost: <br>£' . $amount; ?></p>
		<p><?php echo 'Item description: <br>' . $desc; ?></p>
		<!--Basics = http://support.worldpay.com/support/kb/bg/htmlredirect/Content/rhtml/Integrating_your_website_with.htm-->
		<!--All params = http://support.worldpay.com/support/kb/bg/htmlredirect/Content/rhtml/HTML_Redirect_parameters.htm#_Ref390088851-->
		<!--Custom params = http://support.worldpay.com/support/kb/bg/paymentresponse/pr0000.html-->
		<form action="<?php echo ($paybylink_testMode == '0' ? $paybylink_form_action : $paybylink_form_action_test); ?>" method=POST target="_self">
		<!--Test Mode | on = 100 or 101 | Test off = 0-->
			<input type="hidden" name="testMode" value="<?php echo $paybylink_testMode; ?>">
		<!--These elements are mandatory-->
			<input type="hidden" name="instId" value="<?php echo $paybylink_instId; ?>"><!--VETSKILLLTDM1 - M2 = ...7-->
			<input type="hidden" name="cartId" value="<?php echo $cartId; ?>">
			<input type="hidden" name="amount" value="<?php echo $amount; ?>">
			<input type="hidden" name="currency" value="<?php echo $paybylink_currency; ?>">
		<!-- These elements below are optional. -->
			<input type="hidden" name="desc" value="<?php echo $desc; ?>">
			<input type="hidden" name="compName" value="<?php echo $paybylink_compName; ?>">
			<!--<input type="hidden" name="name" value="Cardholders name">-->
			<!--<input type="hidden" name="compName" value="Company name">-->
			<!--<input type="hidden" name="fixContact">--><!--This fixes the address = shopper unable to change at payment-->
			<!--<input type="hidden" name="hideContact">--><!--This hides the address from the shopper at payment-->
			<!--<input type="hidden" name="address1" value="">-->
			<!--<input type="hidden" name="address2" value="">-->
			<!--<input type="hidden" name="address3" value="">-->
			<!--<input type="hidden" name="town" value="">-->
			<!--<input type="hidden" name="region" value="">-->
			<!--<input type="hidden" name="postcode" value="">-->
			<!--<input type="hidden" name="country" value="">-->
			<!--<input type="hidden" name="tel" value="">-->
			<!--<input type="hidden" name="email" value="">-->
		<!--These elements are custom-->
			<!--<input type="hidden" name="C_XXX" value="">/*custom parameter for the customers result page*/-->
			<!--<input type="hidden" name="M_XXX" value="">/*custom parameter for our payment response message*/-->
			<!--<input type="hidden" name="CM_XXX" or MC_XXX value="">/*custom parameter for the customers result page and our payment response message*/-->
			<input type="hidden" name="CM_successfulUrl" value="<?php echo $paybylink_url . '?pay=successful&pbl=' . $paybylink_url_params_get; ?>">
			<input type="hidden" name="CM_cancelledUrl" value="<?php echo $paybylink_url . '?pay=cancelled&pbl=' . $paybylink_url_params_get; ?>">
		<!---->
			<!--<input type="submit" value="<?php// echo $button; ?>">-->
			<!--<input type="submit" class="continue" value="Accept and continue to pay">-->
			<button type="submit" class="continue">Accept and continue to pay<br />&rarr;</button>
		</form>
		<p><a href="<?php echo $_SESSION['paybylink_button_url']; ?>">Cancel and return</a></p>
		<!--<img src="https://support.worldpay.com/support/images/cardlogos/poweredByWorldPay.gif" alt="Powered by WorldPay">-->
		<p style="font-size:smaller;"><i>Powered by WorldPay</i></p>
		<?php
		if($paybylink_testMode != '0'){
			echo '<p style="color:red;">Test mode on (' . $paybylink_testMode . ')</p>';
			$paybylink_url_params_get = paybylink_crypted($_GET['pbl'],'decrypt');
			parse_str($paybylink_url_params_get, $paybylink_url_params_array);
			echo '<pre>';
			print_r($paybylink_url_params_array);
			echo '</pre>';
		}
		?>
	<?php endif; ?>
	</div><!--container-->
</body>
</html>