<?php

function cactusthemes_scripts_styles_child_theme() {
	global $wp_styles;
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css',array('bootstrap','colorbox','tooltipster','fontastic-entypo','google-font-Oswald'));
}
add_action( 'wp_enqueue_scripts', 'cactusthemes_scripts_styles_child_theme' );

add_image_size('poster-small',134,198, true); // movie poster
add_image_size('poster-medium',254,377, true); // movie poster
add_image_size('poster-big',370,540, true); // movie poster

require_once 'inc/classes/class.content-html.php';

add_action('init','truemagchild_init');
function truemagchild_init(){
	add_filter('truemag_video_thumb_size', 'truemagchild_video_thumb_size_filter', 10, 2);
}

/**
 * change thumbnail size into Poster ratio
 */
if(!function_exists('truemagchild_video_thumb_size_filter')){
	function truemagchild_video_thumb_size_filter($size, $context){
		switch($context){
			case 'channel-video':
			case 'channel-playlist':
			case 'loop':
				if($size == 'thumb_520x293'){
					$size = 'poster-big';
				}
				break;
		}
		
		return $size;
	}
}