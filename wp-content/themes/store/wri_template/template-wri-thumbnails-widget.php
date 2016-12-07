<?php
/*
 * WRI template for widget thumbnails
 * Don't modify this file and directory. If you need changes own templates can be used. Your own templates 
 * should be in the main directory of your active theme, the file name must conform 
 * to the following naming convention: yarpp-template-....php
 * Please find more details about templates in Yarpp documentations.
 */
 
global $wri, $woocommerce;

if ( have_posts() ) {

	echo $before_widget;

	if ( $title )
		echo $before_title . $title . $after_title;

	echo '<ul class="wri-thumbnails-widget">';

	while ( have_posts()) {
		the_post();
		global $product;

		echo '<li>
			<div class="wri-thumbnail-box">		
				<a href="' . get_permalink() . '">'
					. '<img src="' . $wri->get_thumb_url((has_post_thumbnail() ? $wri->get_featured_image_path() : '/wp-content/plugins/yet-another-related-posts-plugin/default.png'), $args['wri_thumbnail_width'], $args['wri_thumbnail_height']) . '" alt="' . get_the_title() . '" width="' . $args['wri_thumbnail_width'] . '" height="' . $args['wri_thumbnail_height']. '">'				
					. ' ' . get_the_title() . '
				</a> ' .
			'</div>' .			
			'<div class="wri-title-box">' . cut_text(trim(strip_tags(get_the_content())),$args['wri_maximum_excerpt_characters'], ' ...') . '</div>' .  
		'</li>';
	}

	echo '</ul>';

	echo $after_widget;
}

wp_reset_postdata();

wp_enqueue_style( "wri-thumbnails-" . $dimensions['size'], (get_template_directory_uri() . '/' . 'wri_template/styles-wri-thumbnails-widget.php?' . http_build_query( array( 'width' => $dimensions['width'], 'height' => $dimensions['height'] ) ) ) );
