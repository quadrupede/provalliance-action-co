<?php
/*
 * WRI styles from widget thumbnails
 */

 header( 'Content-Type: text/css' );

?>

 ul.wri-thumbnails-widget {
    list-style: none outside;
    padding: 0;
    margin: 0;
}
 
ul.wri-thumbnails-widget li {
    padding: 4px 0;
    margin: 0;
    list-style: none;
	margin-bottom: 10px !important;
}

ul.wri-thumbnails-widget li:after {
    content: "";
    display: block;
    clear: both;
}

ul.wri-thumbnails-widget li a {
    display: block;
    font-weight: bold;
}

ul.wri-thumbnails-widget li img {
    float: right;
    margin-left: 4px;
    margin-bottom: 5px;


    box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3);
    -webkit-box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3);
    -moz-box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3);
}

ul.wri-thumbnails-widget li dl {
    margin: 0;
    font-size: 0.8751em;
    padding-left: 1em;
    border-left: 2px solid rgba(0,0,0,0.1);
}

ul.wri-thumbnails-widget li dl dt {
    float: left;
    clear: left;
    margin-right: .25em;
}

ul.wri-thumbnails-widget li dl dd {
    margin-bottom: .5em;
}

ul.wri-thumbnails-widget li .star-rating {
    float: none;
}

.wri-thumbnail-box {
	
}		

.wri-title-box {
	display: table-cell;
	padding-right: 5px;
}

