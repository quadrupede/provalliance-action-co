<?php 
/*
Plugin Name: WooCommerce Multiple Customer Addresses
Description: Manage multiple customer shipping and billing addresses 
Author: Lagudi Domenico
Version: 4.4
*/

/* 
Copyright: WooCommerce Multiple Customer Addresses uses the ACF PRO plugin. ACF PRO files are not to be used or distributed outside of the WooCommerce Multiple Customer Addresses plugin.
*/

define('WCMCA_PLUGIN_PATH', rtrim(plugin_dir_url(__FILE__), "/") )  ;
define('WCMCA_PLUGIN_ABS_PATH', plugin_dir_path( __FILE__ ) );

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
{
	include_once( "classes/com/WCMCA_Global.php"); 
	include_once( "classes/com/WCMCA_Acf.php"); 
	if(!class_exists('WCMCA_Wpml'))
	{
		require_once('classes/com/WCMCA_Wpml.php');
		$wcmca_wpml_helper = new WCMCA_Wpml();
	}
	if(!class_exists('WCMCA_Html'))
	{
		require_once('classes/com/WCMCA_Html.php');
		$wcmca_html_helper = new WCMCA_Html();
	}
	if(!class_exists('WCMCA_Customer'))
	{
		require_once('classes/com/WCMCA_Customer.php');
		$wcmca_customer_model = new WCMCA_Customer();
	}
	if(!class_exists('WCMCA_Address'))
	{
		require_once('classes/com/WCMCA_Address.php');
		$wcmca_address_model = new WCMCA_Address();
	}
	if(!class_exists('WCMCA_MyAccountPage'))
	{
		require_once('classes/frontend/WCMCA_MyAccountPage.php');
		$wcmca_my_account_page_addon = new WCMCA_MyAccountPage();
	}
	if(!class_exists('WCMCA_CheckoutPage'))
	{
		require_once('classes/frontend/WCMCA_CheckoutPage.php');
		$wcmca_checkout_page_addon = new WCMCA_CheckoutPage();
	}
	if(!class_exists('WCMCA_OrderDetailsPage'))
	{
		require_once('classes/frontend/WCMCA_OrderDetailsPage.php');
		$wcmca_frontend_order_details_page_addon = new WCMCA_OrderDetailsPage();
	}if(!class_exists('WCMCA_Emails'))
	{
		require_once('classes/frontend/WCMCA_Emails.php');
		$wcmca_emails_addon = new WCMCA_Emails();
	}
	if(!class_exists('WCMCA_AdminOrderDetailsPage'))
	{
		require_once('classes/admin/WCMCA_AdminOrderDetailsPage.php');
		$wcmca_admin_order_details_page_addon = new WCMCA_AdminOrderDetailsPage();
	}
	if(!class_exists('WCMCA_OptionPage'))
	{
		require_once('classes/admin/WCMCA_OptionPage.php');
		$wcmca_option_page = new WCMCA_OptionPage();
	}
	if(!class_exists('WCMCA_Option'))
	{
		require_once('classes/com/WCMCA_Option.php');
		$wcmca_option_model = new WCMCA_Option();
	}
	if(!class_exists('WCMCA_UserProfilePage'))
	{
		require_once('classes/admin/WCMCA_UserProfilePage.php');
		$wcmca_user_profile_page = new WCMCA_UserProfilePage();
	}
	
	load_plugin_textdomain('woocommerce-multiple-customer-addresses', false, basename( dirname( __FILE__ ) ) . '/languages' );
	//add_action('admin_menu', 'wcmca_init_admin_panel');
	//add_action('admin_init', 'wcmca_admin_init');
}
function wcmca_admin_init()
{
	$remove = remove_submenu_page( 'woocommerce-multiple-customer-addresses', 'woocommerce-multiple-customer-addresses');
	$remove = remove_submenu_page( 'woocommerce-multiple-customer-addresses', 'woocommerce-multiple-customer-addresses-edit-user');
}	
function wcmca_init_admin_panel()
{
	global $wcmca_html_helper;
	$place = wcmca_get_free_menu_position(55, 0.1);
	
	//$hookname  = add_menu_page( __( 'WooCommerce Multiple Addresses', 'woocommerce-multiple-customer-addresses' ), __( 'WooCommerce Multiple Addresses', 'woocommerce-multiple-customer-addresses' ), 'manage_woocommerce', 'woocommerce-multiple-customer-addresses', null, '', (string)$place );
	//add_submenu_page('woocommerce-multiple-customer-addresses', __('Edit page','woocommerce-multiple-customer-addresses'), __('Edit page','woocommerce-multiple-customer-addresses'), 'edit_shop_orders', 'woocommerce-multiple-customer-addresses-edit-user', 'wcmca_render_admin_user_addresses_edit_page');

	
}

function wcmca_get_free_menu_position($start, $increment = 0.1)
{
	foreach ($GLOBALS['menu'] as $key => $menu) {
		$menus_positions[] = $key;
	}
	
	if (!in_array($start, $menus_positions)) return $start;

	/* the position is already reserved find the closet one */
	while (in_array($start, $menus_positions)) 
	{
		$start += $increment;
	}
	return $start;
}
function wcmca_var_dump($data)
{
	echo "<pre>";
	var_dump($data);
	echo "</pre>";
}
function wcmca_is_wcbcf_active()
{
	if (in_array( 'woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ))
		return true;
	
	return false;
}

?>