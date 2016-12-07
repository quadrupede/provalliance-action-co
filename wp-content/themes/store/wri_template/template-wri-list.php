<?php
/*
 * WRI template for widget list
 * Don't modify this file and directory. If you need changes own templates can be used. Your own templates 
 * should be in the main directory of your active theme, the file name must conform 
 * to the following naming convention: yarpp-template-....php
 * Please find more details about templates in Yarpp documentations.
 */

include 'template-wri-before_loop.php'; 

if (have_posts()) {
 
	$output .= '<ul class="wri-related">' . "\n"; 

	while (have_posts()) {
		the_post();

		$output .= $yarpp_option['before_title'];
		$output .= "<a href='" . get_permalink() . "'>" . $post->post_title .  "</a>" . "\n";
		$output .= $yarpp_option['after_title'];

	}

	$output .= "</ul>"; 
}

include 'template-wri-after_loop.php'; 
