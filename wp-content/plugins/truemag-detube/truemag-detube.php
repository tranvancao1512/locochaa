<?php
   /*
   Plugin Name: TrueMAG - Detube Escape
   Plugin URI: http://www.cactusthemes.com
   Description: Help you escape from detube to TrueMAG
   Version: 2.6
   Author: Cactusthemes
   Author URI: http://www.cactusthemes.com
   License: GPL2
   */
add_action('init', 'detube_escape');
function detube_escape(){
	$args = array(
		'post_type' => 'post',
		'posts_per_page' => 100,
		'post_status' => 'publish',
		'tax_query' => array(
			array(
				'taxonomy' => 'post_format',
				'field' => 'slug',
				'terms' => 'post-format-video',
			)
		),
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'tm_video_url',
				'value' => '',
				'compare' => 'NOT EXISTS'
			),
			array(
				'key' => 'tm_video_code',
				'value' => '',
				'compare' => 'NOT EXISTS'
			),
			array(
				'key' => 'tm_video_file',
				'value' => '',
				'compare' => 'NOT EXISTS'
			)
		)
	);
	$de_query = new WP_Query( $args );
	if ( $de_query->have_posts() ) {
		while ( $de_query->have_posts() ) {
			$de_query->the_post();
			//update video url
			if($meta = get_post_meta( get_the_ID(), 'dp_video_url', true )){
				update_post_meta(get_the_ID(), 'tm_video_url', $meta);
			}
			
			//update video embed code
			if($meta = get_post_meta( get_the_ID(), 'dp_video_code', true )){
				update_post_meta(get_the_ID(), 'tm_video_code', $meta);
			}
			
			//update video file
			if($meta = get_post_meta( get_the_ID(), 'dp_video_file', true )){
				update_post_meta(get_the_ID(), 'tm_video_file', $meta);
			}
			
			//update views
			if($meta = get_post_meta( get_the_ID(), 'views', true )){
				update_post_meta(get_the_ID(), '_count-views_all', $meta);
			}
			
			//update like counts
			if($meta = get_post_meta( get_the_ID(), 'likes', true )){
				global $wpdb;
				$query = "INSERT INTO {$wpdb->prefix}wti_like_post SET ";
				$query .= "post_id = '" . get_the_ID() . "', ";
				$query .= "value = '".$meta."', ";
				$query .= "date_time = '" . date('Y-m-d H:i:s') . "', ";
				$query .= "ip = ''";
				$success = $wpdb->query($query);
			}
		}
	} else {
		// no posts found
	}
	wp_reset_postdata();
	return true;
}