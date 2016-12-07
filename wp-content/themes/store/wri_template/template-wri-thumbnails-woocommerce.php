<?php
/*
 * WRI template for WooCommerce thumbnails
 * Don't modify this file and directory. If you need changes own templates can be used. Your own templates 
 * should be in the main directory of your active theme, the file name must conform 
 * to the following naming convention: yarpp-template-....php
 * Please find more details about templates in Yarpp documentations.
 */

if ( have_posts() ) {

	global $woocommerce;

	include 'template-wri-before_loop.php';

	printf ('<div class="woocommerce">');
	//include ($woocommerce->plugin_path() . '/templates/loop-shop.php');
	include ('template-wri-woocommerce-loop-shop.php');
	printf ('</div>');

	$wri_general_settings = get_option('wri_general_settings');
	if ( 1 != $wri_general_settings['disable_styles_wri_thumbnails_woocommerceCss'] ) {
		wp_enqueue_style( "wri-thumbnails-woocommerce", get_template_directory_uri() . '/' . 'wri_template/styles-wri-thumbnails_woocommerce.css');
	}

}
