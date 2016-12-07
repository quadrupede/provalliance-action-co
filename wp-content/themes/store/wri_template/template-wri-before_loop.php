<?php
/*
 * WRI sub-template
 * Don't modify this file and directory. If you need changes own templates can be used. Your own templates 
 * should be in the main directory of your active theme, the file name must conform 
 * to the following naming convention: yarpp-template-....php
 * Please find more details about templates in Yarpp documentations.
 */

global $wri_no_result, $wri_general_settings;

if ( !$this->diagnostic_using_thumbnails() )
	$this->set_option( 'manually_using_thumbnails', true );

$options = array( 'thumbnails_default', 'no_results', 'wri_title', 'wri_before_title_tags', 'wri_after_title_tags', 'wri_no_result_display_text', 'wri_thumbnail_columns_number', 'wri_widget_mode' );
extract( $this->parse_args( $args, $options ) );

if ( empty($thumbnails_default) )
	$thumbnails_default = get_header_image();

$dimensions = $this->thumbnail_dimensions();

$yarpp_option = get_option('yarpp');


if ( have_posts() || !empty($wri_no_result_display_text) ) {
		
	if ( $wri_title != null ) {
		$title = $wri_before_title_tags . $wri_title . $wri_after_title_tags;
	} else if ('1' == $wri_general_settings['use_yarpp_title'] and !$wri_widget_mode) {
		$title = $yarpp_option['before_related'] . $yarpp_option['after_related'];		
	}

	$output .= '<div class="wri-title">' . $title . '</div>';

	if (!have_posts()) {
		$output .= '<div>' . $wri_no_result_display_text . '</div>';
	}
}

if (!have_posts()) {
	$wri_no_result = TRUE;
} else {
	$wri_no_result = FALSE;	
} 	
