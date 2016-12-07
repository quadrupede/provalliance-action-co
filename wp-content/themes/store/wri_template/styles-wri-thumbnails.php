<?php
/*
 * WRI styles from thumbnails
 */

$height = 150;  
$width = 150; 

if ( isset($_GET['height']) )
	$height = (int) $_GET['height'];
if ( isset($_GET['width']) )
	$width = (int) $_GET['width'];


$margin = 5;
$width_with_margins = $width + 2 * $margin;
$height_with_text = $height + 50;
$extramargin = 7;

header( 'Content-Type: text/css' );

?>

.wri-thumbnails-group {
	vertical-align: top;
}

.wri-thumbnails-group .wri-thumbnail, .wri-thumbnail-default, .wri-thumbnail-title {
	display: inline-block;
}

ul.wri-thumbnails-group {
	margin: 0 0 1em;
	padding: 0;
	list-style: none outside;

}

.wri-thumbnails-group .wri-thumbnail {
	margin: 0 3.8% 2.992em 0;
	padding: 0;
	position: relative;
	width: 21.5%;
	margin-left: 0;
	text-align: center; 
	vertical-align: top;
}

.wri-thumbnails-group li.last_in_row {
	margin-right: 0;
}

.wri-thumbnails-group li.first_in_row {
	clear: both;
}

.wri-thumbnails-group .wri-thumbnail a img {
	width: 100%;
	height: auto;
}

.wri-thumbnail > img, .wri-thumbnail-default {
}
.wri-thumbnails-group .wri-thumbnail > img, .wri-thumbnails-group .wri-thumbnail-default {
	display: block;
}
.wri-thumbnails-group .wri-thumbnail-title {
	margin-top: 0px;
}

.wri-thumbnail-default {
	overflow: hidden;
}
.wri-thumbnail-default > img {
	min-height: <?php echo $height; ?>px;
	min-width: <?php echo $width; ?>px;
}

.wri-title {
	clear: both;	
}

.wri-item-columns-2 ul.wri-thumbnails-group li.wri-thumbnail{
	width:48%;
}

.wri-item-columns-3 ul.wri-thumbnails-group li.wri-thumbnail{
	width:29.5%;
}

.wri-item-columns-4 ul.wri-thumbnails-group li.wri-thumbnail{
	width: 22.05%;
}

.wri-item-columns-5 ul.wri-thumbnails-group li.wri-thumbnail{
	width:16.9%;
}

.wri-specific-item-columns-2 ul.wri-thumbnails-group li.wri-thumbnail{
	width:48% !important;
}

.wri-specific-item-columns-3 ul.wri-thumbnails-group li.wri-thumbnail{
	width:30.75% !important;
}

.wri-specific-item-columns-4 ul.wri-thumbnails-group li.wri-thumbnail{
	width: 22.05% !important;
}

.wri-specific-item-columns-5 ul.wri-thumbnails-group li.wri-thumbnail{
	width:16.9% !important;
}

.wri_content_clear_both{
	clear: both;
}
