<?php
/*
 * WRI template for widget thumbnails
 * Don't modify this file and directory. If you need changes own templates can be used. Your own templates 
 * should be in the main directory of your active theme, the file name must conform 
 * to the following naming convention: yarpp-template-....php
 * Please find more details about templates in Yarpp documentations.
 */

include 'template-wri-before_loop.php'; 

if (have_posts()) {
 
	$output .= '<ul class="wri-thumbnails-group">' . "\n";
	
	$i = 0;
	
	while (have_posts()) {
		the_post();

		$i = $i + 1;
		
		if ( 0 == $i % 4 ) {
			$last_in_row = 'last_in_row';
		} else {
			$last_in_row = '';  
		}
			

		$output .= "<li class='wri-thumbnail " .  $last_in_row . "'>"; 
		$output .= "<a href='" . get_permalink() . "' title='" . the_title_attribute('echo=0') . "'>" . "\n"; 

		$post_thumbnail_html = '';
		if ( has_post_thumbnail() ) {
			if ( $this->diagnostic_generate_thumbnails() )
				$this->ensure_resized_post_thumbnail( get_the_ID(), $dimensions );
			$post_thumbnail_html = get_the_post_thumbnail( null, array(150, 150) );  //todo
		}
		
		if ( trim($post_thumbnail_html) != '' )
			$output .= $post_thumbnail_html;
		else
			$output .= '<img width="150" height="150" src= "' . esc_url($thumbnails_default) . '" class="attachment-150x150 wp-post-image"/>'; 

		$output .= '<span class="wri-thumbnail-title">' . get_the_title() . '</span>';
		$output .= '</a>' . "\n";
		$output .= '</li>' . "\n"; 

	}
	$output .= "</ul>\n"; 
} else {
	$output .= $no_results;
}

include 'template-wri-after_loop.php'; 