<?php
 
// admin show channel, playlist
if(!function_exists('cactus_edit_columns')) { 
	function cactus_edit_columns($columns) {
	  return array_merge( $columns, 
				array('ct-channel' => esc_html__('Channel','cactus')) ,
				array('ct-playlist' => esc_html__('Playlist','cactus')) 
		  );
	}
}
add_filter('manage_posts_columns' , 'cactus_edit_columns');
if(!function_exists('ct_custom_columns')) {
	// return the values for each coupon column on edit.php page
	function ct_custom_columns( $column ) {
		global $post;
		global $wpdb;
		$channel_id = get_post_meta($post->ID,'channel_id', true );
		$channel_name = ''; 
		if(is_array($channel_id) && !empty($channel_id)){
			foreach($channel_id as $channel_it){
				if($channel_name==''){
					$channel_name .= '<a href="'.get_permalink($channel_it).'">'.get_the_title($channel_it).'</a>';
				}else{
					$channel_name .= ', <a href="'.get_permalink($channel_it).'">'.get_the_title($channel_it).'</a>';
				}
			}
		}elseif($channel_id!=''){
			$channel_id =explode(",",$channel_id);
			foreach($channel_id as $channel_it){
				if($channel_name==''){
					$channel_name .= '<a href="'.get_permalink($channel_it).'">'.get_the_title($channel_it).'</a>';
				}else{
					$channel_name .= ', <a href="'.get_permalink($channel_it).'">'.get_the_title($channel_it).'</a>';
				}
			}
		}
		$playlist_id = get_post_meta($post->ID,'playlist_id', true );
		$playlist_name = ''; 
		if(is_array($playlist_id) && !empty($playlist_id)){
			foreach($playlist_id as $playlist_it){
				if($playlist_name==''){
					$playlist_name .= '<a href="'.get_permalink($playlist_it).'">'.get_the_title($playlist_it).'</a>';
				}else{
					$playlist_name .= ', <a href="'.get_permalink($playlist_it).'">'.get_the_title($playlist_it).'</a>';
				}
			}
		}elseif($playlist_id!=''){
			$playlist_id =explode(",",$playlist_id);
			foreach($playlist_id as $playlist_it){
				if($playlist_name==''){
					$playlist_name .= '<a href="'.get_permalink($playlist_it).'">'.get_the_title($playlist_it).'</a>';
				}else{
					$playlist_name .= ', <a href="'.get_permalink($playlist_it).'">'.get_the_title($playlist_it).'</a>';
				}
			}
		}
		switch ( $column ) {
			case 'ct-channel':
				echo $channel_name;
				break;
			case 'ct-playlist':
				echo $playlist_name;
				break;
		}
	}
	add_action( 'manage_posts_custom_column', 'ct_custom_columns' );
}
