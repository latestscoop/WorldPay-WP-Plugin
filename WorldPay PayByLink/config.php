<?php 
    /*
    Plugin Name: WorldPay PayByLink
    Plugin URI: 
    Description: Simply add a shortcode to your content to take WorldPay card payments.
    Author: Sami Cooper
    Version: 1.1
    Author URI: 
    */
?>
<?php
/*
dashboards
*/
add_action( 'admin_menu', 'create_dashboards_paybylink' );
function create_dashboards_paybylink() {
	//add_menu_page($page_title,$menu_title,$capability,$menu_slug, callable $function = '',$icon_url = '', int $position = null )
	//add_submenu_page($parent_slug,$page_title,$menu_title,$capability,$menu_slug, callable $function = '' )
	add_submenu_page( 'options-general.php', 'WorldPay PayByLink', 'WorldPay PayByLink', 'moderate_comments', '/paybylink.php', 'dash_paybylink' );
}
function dash_paybylink(){
	include plugin_dir_path( __FILE__ ) . 'paybylink.php';
}
?>
<?php
/*
dashboard settings
*/
add_action( 'admin_init', 'register_settings_paybylink' );
function register_settings_paybylink() {
	register_setting( 'register_settings_paybylink', 'paybylink_compName' );
	register_setting( 'register_settings_paybylink', 'paybylink_page_name' ); //page = pay, successful, cancelled
	register_setting( 'register_settings_paybylink', 'paybylink_page_title' ); //page = pay, successful, cancelled
	register_setting( 'register_settings_paybylink', 'paybylink_secret_key' ); //encryption key
	register_setting( 'register_settings_paybylink', 'paybylink_secret_iv' ); //encryption initialization vector
	register_setting( 'register_settings_paybylink', 'paybylink_form_action' ); //https://secure.worldpay.com/wcc/purchase
	register_setting( 'register_settings_paybylink', 'paybylink_form_action_test' ); //https://secure-test.worldpay.com/wcc/purchase
	register_setting( 'register_settings_paybylink', 'paybylink_testMode' ); //Test on = 100 or 101 | Test off = 0
	register_setting( 'register_settings_paybylink', 'paybylink_instId' );
	register_setting( 'register_settings_paybylink', 'paybylink_currency' ); //GBP
	register_setting( 'register_settings_paybylink', 'paybylink_redirect' );
	register_setting( 'register_settings_paybylink', 'paybylink_confirm_email');
	//register_setting( 'register_settings_paybylink', 'paybylink_confirm_email', array('type'=>'string','sanitize_callback'=>'sanitize_text_field','default'=>'default') );
	//unregister_setting( 'register_settings_paybylink', 'paybylink_' );
	//unregister_setting( 'register_settings_paybylink', 'paybylink_confirm_email' );
}
global $paybylink_compName; $paybylink_compName = get_option( 'paybylink_compName' );
global $paybylink_page_name; $paybylink_page_name = get_option( 'paybylink_page_name' );
global $paybylink_page_title; $paybylink_page_title = get_option( 'paybylink_page_title' );
global $paybylink_secret_key; $paybylink_secret_key = get_option( 'paybylink_secret_key' );
global $paybylink_secret_iv; $paybylink_secret_iv = get_option( 'paybylink_secret_iv' );
global $paybylink_form_action; $paybylink_form_action = get_option( 'paybylink_form_action' );
global $paybylink_form_action_test; $paybylink_form_action_test = get_option( 'paybylink_form_action_test' );
global $paybylink_testMode; $paybylink_testMode = get_option( 'paybylink_testMode' );
global $paybylink_instId; $paybylink_instId = get_option( 'paybylink_instId' );
global $paybylink_currency; $paybylink_currency = get_option( 'paybylink_currency' );
global $paybylink_redirect; $paybylink_redirect = get_option( 'paybylink_redirect' );
global $paybylink_confirm_email; $paybylink_confirm_email = get_option( 'paybylink_confirm_email' );
global $paybylink_template; $paybylink_template = plugin_dir_path( __FILE__ ) . 'template.php';
global $paybylink_url;
global $paybylink_url_id;
if($paybylink_page_title != '' || $paybylink_page_name != ''){
	$paybylink_url = (isset($_SERVER['HTTPS']) !== false ? 'https' : 'http') . '://' . $_SERVER[HTTP_HOST] . '/' . $paybylink_page_name . '/';
	$paybylink_url_id = get_page_by_path($paybylink_page_name, OBJECT, 'page')->ID;
}
?>
<?php
/*
Add settings link to plugin page
*/
function plugin_add_settings_link( $links ) {
    $settings_link = '<a href="/wp-admin/options-general.php?page=paybylink.php">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
  	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'plugin_add_settings_link' );
?>
<?php
/*
payment page + template
*/
if($paybylink_page_title != '' || $paybylink_page_name != ''){
	$new_page = array(
		'post_title'    => wp_strip_all_tags( $paybylink_page_title ),
		'post_name'    => wp_strip_all_tags( $paybylink_page_name),
		'post_content'  => $paybylink_template,
		'post_status'   => 'publish',
		'post_author'   => get_current_user_id(),
		'post_type'     => 'page',
		//'page_template'     => $paybylink_template,
	);
	if(!isset($paybylink_url_id)){
		$new_page_id = wp_insert_post($new_page);
		$paybylink_url_id = get_page_by_path($paybylink_page_name, OBJECT, 'page')->ID;
	}
	function paybylink_include_template_file($template){
		global $paybylink_template;
		global $paybylink_url_id;
		if(is_page($paybylink_url_id)){
			$template = $paybylink_template;
		}
		return $template;
	}
	add_filter( 'template_include', 'paybylink_include_template_file', 99 );
}
?>
<?php
/*
encryption
*/
function paybylink_crypted( $string, $action = 'encrypt' ) {
	if($string != '' || $string != false){
		global $paybylink_secret_key;
		global $paybylink_secret_iv;
		if($paybylink_secret_key && $paybylink_secret_iv){
			$output = false;
			$encrypt_method = "AES-256-CBC";
			$key = hash( 'sha256', $paybylink_secret_key );
			$iv = substr( hash( 'sha256', $paybylink_secret_iv ), 0, 16 );

			if( $action == 'encrypt' ) {
				$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
			}
			else if( $action == 'decrypt' ){
				$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
			}

			return $output;
		}
	}
}
?>
<?php
/*
Short code
*/
//https://code.tutsplus.com/tutorials/wordpress-shortcodes-the-right-way--wp-17165
function test_return(){
	global $paybylink_secret_key;
	return 'test_' . $paybylink_secret_key;
}
function paybylink_shortcode($atts,$content = null){
	global $paybylink_url;
	
	$paybylink_button_url = (isset($_SERVER['HTTPS']) !== false ? 'https' : 'http') . '://' . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI];
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['paybylink_button_url'] = $paybylink_button_url;
	extract(shortcode_atts(array(
		'cartid' => 'no cartid',
		'name' => 'no name',
		'desc' => 'no desc',
		'amount' => 'no amount',
		'button' => 'Pay now',
		'test' => 'off',
	),$atts));
	$paybylink_url_params = 'c=' . $cartid . '&' . 'n=' . $name . '&' . 'd=' . $desc . '&' . 'a=' . $amount . '&' . 'b=' . $button;
	$paybylink_url_params .= ($test == 'on' ? '&t=' . 'on' : '');
	$paybylink_url_params = paybylink_crypted($paybylink_url_params,'encrypt');
	$output = '<a href="' . $paybylink_url . '?pbl=' . $paybylink_url_params . '">' . $button . '</a>';
	if($cartid == '' || $name == '' || $desc == '' || $amount == '' || $button == ''){
		return '<p>Error: mandatory short code details are missing.</p>';
	}else{
		return $output;
	}
}
add_shortcode("paybylink","paybylink_shortcode");
?>
<?php
/*
uninstall / deactivate
*/
	register_uninstall_hook(__FILE__, 'paybylink_uninstall');
	register_deactivation_hook(__FILE__, 'paybylink_uninstall');
	function paybylink_uninstall(){
		global $paybylink_url_id;
		wp_delete_post( $paybylink_url_id, true );
	}
?>