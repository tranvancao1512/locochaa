<?php
/* Register shortcode with Visual Composer */
add_action( 'after_setup_theme', 'reg_member' );
function reg_member(){
	if(function_exists('vc_map')){
	vc_map( array(
	   "name" => esc_html__("Member", 'cactusthemes'),
	   "base" => "member",
	   "class" => "",
	   "controls" => "full",
	   "category" => esc_html__('Content', 'cactusthemes'),
	   "params" => array(
		  array(
			 "type" => "textfield",
             "admin_label" => true,
			 "holder" => "div",
			 "class" => "",
			 "heading" => esc_html__("ID Member", 'cactusthemes'),
			 "param_name" => "id",
			 "value" => '',
			 "description" => esc_html__('Enter ID of Member', 'cactusthemes'),
		  ),
          array(
			 "type" => "dropdown",
             "admin_label" => true,
			 "holder" => "div",
			 "class" => "",
			 "heading" => esc_html__("Link To Single Post"),
			 "param_name" => "link",
			 "value" => array(1 => 'Yes', 0 => 'No'),
             "default" => 0,
			 "description" => esc_html__('Put Link To Single Post on thumbnail image and heading', 'cactusthemes'),
		  ),
	   )
	));
	}
}