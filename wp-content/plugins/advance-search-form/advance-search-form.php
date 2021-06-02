<?php
/*
Plugin Name: Advance Search Form
Plugin URI: http://www.cactusthemes.com
Description: Enhance default Wordpress Search Form
Version: 1.4.9
Author: CactusThemes
Author URI: http://www.cactusthemes.com
*/



require_once 'widget-template-loader.class.php';  

/* FOR CONFIGURATION 
 *
 */
define('ASF_LOAD_JQUERY',1); // 0: Do not load jQuery; 1: Load jQuery
 /*
 * END CONFIGURATION
 * do not edit below this line unless you know what you are doing!!!
 *
 */

class advance_search_form_widget extends WP_Widget {
	function __construct() {
    	$widget_ops = array(
			'classname'   => 'widget-asf', 
			'description' => __('Displays an advance search form','cactusthemes')
		);
    	parent::__construct('advance_search_form', __('Advance Search Form','cactusthemes'), $widget_ops);
	}
	function widget( $args, $instance ) {
        extract($args);	
		
		$title = strip_tags($instance['title']);
		$label = strip_tags($instance['label']);
		$default_text = strip_tags($instance['default_text']);
		$button_text = strip_tags($instance['button_text']);
		$search_for = get_option('asf-post-types',array());
		$show_categories = isset($instance['show_categories']) ? $instance['show_categories'] : 0;	
		$show_tag_filter = isset($instance['show_tag_filter']) ? $instance['show_tag_filter'] : 1;
		$highlight_results = isset($instance['highlight_results']) ? $instance['highlight_results'] : 1;
		$select_categories = get_option('asf-categories',array());
		$suggestion = isset($instance['suggestion']) ? $instance['suggestion'] : 0;
		$load_css = isset($instance['load_css']) ? $instance['load_css'] : 0;
		$search_word = $default_text;
		$search_cat = '';
		$s = isset($_GET['s']) ? $_GET['s'] : '';
		$s = esc_html( $s );
		if(!empty($s)){
			// if we are in search result page
			$search_word = $s;
			$search_cat = isset($_GET['cat']) ? $_GET['cat'] : '';
			$search_cat = esc_html( $search_cat );
		}
		
		/* ECHO FORM */
		
        echo $before_widget;
		
		$title = apply_filters('widget_title', $title);
		
        if ( !empty( $title ) ) {
            echo $before_title . $title . $after_title; }
?>

<?php
		if($load_css){
			wp_enqueue_style('asf-searchform-css',plugins_url('searchform.css', __FILE__));
		}
		
		if($suggestion){
			wp_enqueue_style('asf-suggestion-css',plugins_url('suggestion.css', __FILE__));
		}
	?>
		<?php 
		if(get_option('asf-custom-css') != ''){
		?>
			<style type="text/css" scope="scope">
			<?php echo get_option('asf-custom-css');?>
			</style>
		<?php
		}
		include CT_Widget_Template_Loader::getTemplateHierarchy('widget-asf.view');
    }
	
	function form( $instance ) {		
		$title = strip_tags(isset($instance['title']) ? $instance['title'] : '');
		$label = strip_tags(isset($instance['label']) ? $instance['label'] : '');
		$default_text = strip_tags(isset($instance['default_text']) ? $instance['default_text'] : '');
		$button_text = strip_tags(isset($instance['button_text']) ? $instance['button_text'] : '');			
		$show_categories = isset($instance['show_categories']) ? $instance['show_categories'] : 0;
		$load_css = isset($instance['load_css']) ? $instance['load_css'] : 0;
		$suggestion = isset($instance['suggestion']) ? $instance['suggestion'] : 0;
?>
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>">
                <?php echo __('Title: ','cactusthemes'); ?><input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text"
                value="<?php echo  esc_attr($title);?>" width="100" class="widefat" />
		</label></p>
		<p>
		<label for="<?php echo $this->get_field_id('label'); ?>">
                <?php echo __('Label: ','cactusthemes'); ?>
            <input id="<?php echo $this->get_field_id('label'); ?>" name="<?php echo $this->get_field_name('label'); ?>" type="text" value="<?php echo  esc_attr($label);?>" width="100"  class="widefat" />
         </label></p>
		 <p>
		<label for="<?php echo $this->get_field_id('default_text'); ?>">		
                <?php echo __('Default Text: ','cactusthemes'); ?><input id="<?php echo $this->get_field_id('default_text'); ?>" name="<?php echo $this->get_field_name('default_text'); ?>" type="text" value="<?php echo  esc_attr($default_text);?>" width="100" class="widefat"  />
         </label>
		 </p>
		 <p>
		<label for="<?php echo $this->get_field_id('button_text'); ?>">
                <?php echo __('Button Text: ','cactusthemes'); ?><input id="<?php echo $this->get_field_id('button_text'); ?>" name="<?php echo $this->get_field_name('button_text'); ?>" type="text" value="<?php echo  esc_attr($button_text);?>" width="100" class="widefat"  />
         </label>
		 </p>
		 <p>
		<label for="<?php echo $this->get_field_id('show_categories'); ?>">
                <?php echo __('Show categories list: ','cactusthemes'); ?><input id="<?php echo $this->get_field_id('show_categories'); ?>" name="<?php echo $this->get_field_name('show_categories'); ?>" type="checkbox" <?php if($show_categories) echo 'checked="checked"';?> />
         </label>
		 </p>
		 <p>
		<label for="<?php echo $this->get_field_id('load_css'); ?>">
                <?php echo __('Load default CSS: ','cactusthemes'); ?><input id="<?php echo $this->get_field_id('load_css'); ?>" name="<?php echo $this->get_field_name('load_css'); ?>" type="checkbox" <?php if($load_css) echo 'checked="checked"';?> />
         </label>
		 </p>
		 <p>
		<label for="<?php echo $this->get_field_id('suggestion'); ?>">
                <?php echo __('Search suggestion: ','cactusthemes'); ?><input id="<?php echo $this->get_field_id('suggestion'); ?>" name="<?php echo $this->get_field_name('suggestion'); ?>" type="checkbox" <?php if($suggestion) echo 'checked="checked"';?> />
         </label>
		 </p>
<?php
    }
}
add_action( 'widgets_init', create_function( '', 'return register_widget("advance_search_form_widget");' ) );
add_shortcode( 'advance-search', 'parse_advance_search_form' );
function parse_advance_search_form($att){	
	// get options
	$label =get_option('asf-label');;
	$default_text = get_option('asf-placeholder-text');
	$button_text = get_option('asf-button-text');
	$search_for = get_option('asf-post-types',array());
	$show_categories = get_option('asf-show-categories');;
	$show_tag_filter = get_option('asf-show-tag-filter');
	$highlight_results = get_option('asf-highlight-results');
	$select_categories = get_option('asf-categories',array());
	$suggestion = get_option('asf-ajax-suggestion');
	$search_word = $default_text;
	$search_cat = '';
	$s = isset( $_GET['s'] ) ? $_GET['s'] : '';
	$s = esc_attr( $s );

	if(!empty($s)){
		// if we are in search result page
		$search_word = $s;
		$search_cat  = isset( $_GET['cat'] ) ? $_GET['cat'] : '';
		$search_cat = esc_attr( $search_cat );
	}
?>
	<?php
		if(get_option('asf-load-css')){
			wp_enqueue_style('asf-searchform-css',plugins_url('searchform.css', __FILE__));
		}
		
		if($suggestion){
			wp_enqueue_style('asf-suggestion-css',plugins_url('suggestion.css', __FILE__));
		}
	?>
	<?php 
if(get_option('asf-custom-css') != ''){
?>
	<style type="text/css" scope="scope">
	<?php echo get_option('asf-custom-css');?>
	</style>
<?php
}
	include CT_Widget_Template_Loader::getTemplateHierarchy('shortcode-asf.view');
?>
	
<?php
}

/* FRONT-END */
function searchFilter($query) {

// In BBPress, the query to list topics,replies is also 'search' query, so we detect this case 

$the_query = $query->query;

$bbpress_search = false;

if(isset($the_query['post_type']) && $query->is_search && (($the_query['post_type'] == 'topic' && $the_query['meta_key'] == '_bbp_last_active_time') || (is_array($the_query['post_type']) && count(array_intersect($the_query['post_type'],array('topic','reply')))))) $bbpress_search = true;

	if(!$bbpress_search && (!is_admin() && $query->is_main_query() && $query->is_search)){
		// Filter search in front-end only		
		$post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
		$post_type = esc_html( $post_type );
		if (!$post_type) {
			$post_type = 'any';
		}
		
		$query->set('post_type', $post_type);
		
		// remove category search		
		$cat = isset($_GET['cat']) ? $_GET['cat'] : '';
		$cat = esc_html( $cat );
		
		if(empty($cat)){
			// if $cat is empty, maybe it's because users choose to search all
			// check if "All Categories" is a number of categories
			$selected_categories = get_option('asf-categories',array());
			if(!empty($selected_categories) && count($selected_categories) > 0){
				$cat = implode(',',$selected_categories);
			}			
		}

		if(!empty($cat)){
			$query->query['cat'] = null;
			$query->query_vars['cat'] = null;
			$query->query_vars['category_in'] = null;
			
			if(!is_array($cat)){
				$cat = explode(',',$cat);
			}				
			
			$query->set('tax_query', 
					array(
						array(
						'taxonomy' => 'category',
						'terms'	=> $cat,
						'field'	=> 'id',
						'operator'	=> 'IN'
						)
					)
				);
		} else {
			$query->tax_query->queries[]['taxonomy'] = 'category';
		}
	}
	
    return $query;
};

add_filter('pre_get_posts','searchFilter');

add_action( 'wp_enqueue_scripts', 'add_advance_search_form_media' );
function add_advance_search_form_media(){
	if(!wp_script_is('advance-search')){
		wp_register_script('advance-search',plugins_url('searchform.js', __FILE__),array('jquery'));
		wp_enqueue_script('advance-search');
		wp_register_script('mousewheel',plugins_url('jquery.mousewheel.js', __FILE__),array('jquery'));
		wp_enqueue_script('mousewheel');
		// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
		wp_localize_script( 'advance-search', 'asf', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}
	
	if(ASF_LOAD_JQUERY && !wp_script_is('jQuery') && !wp_script_is('jquery')){
		// if jQuery has not been enqueued, we need to enqueue it
		wp_enqueue_script('jquery');
	}
}

/*
 * Count all tags in a category, including tags in sub-categories
 * PARAMS
 * $id: 	category id
 */
function asf_get_all_tags_in_category($id){
	global $wpdb;
	
	// get child categories is has any
	if($id != 0){
		// get category taxonomy		
		$childs = get_categories(array('parent'=>$id));
	} else {
		$childs = get_categories(array('hierarchical'=>false));
	}
	
	$tags = array();
	if(count($childs) > 0){
		foreach($childs as $child){
			$tags = array_merge($tags,asf_get_all_tags_in_category($child->cat_ID));
		}
	}
	if($id != 0){
		$tags1 = $wpdb->get_results
		("
			SELECT DISTINCT terms2.term_id as tag_id, terms2.name as tag_name, terms2.slug as tag_slug, null as tag_link
			FROM
				wp_posts as p1
				LEFT JOIN wp_term_relationships as r1 ON p1.ID = r1.object_ID
				LEFT JOIN wp_term_taxonomy as t1 ON r1.term_taxonomy_id = t1.term_taxonomy_id
				LEFT JOIN wp_terms as terms1 ON t1.term_id = terms1.term_id,

				wp_posts as p2
				LEFT JOIN wp_term_relationships as r2 ON p2.ID = r2.object_ID
				LEFT JOIN wp_term_taxonomy as t2 ON r2.term_taxonomy_id = t2.term_taxonomy_id
				LEFT JOIN wp_terms as terms2 ON t2.term_id = terms2.term_id
			WHERE
				t1.taxonomy = 'category' AND p1.post_status = 'publish' AND terms1.term_id = ".$id." AND
				t2.taxonomy = 'post_tag' AND p2.post_status = 'publish'
				AND p1.ID = p2.ID
			ORDER by tag_name
		");
		$count = 0;
		foreach ($tags1 as $tag) {
			$tags1[$count]->tag_link = get_tag_link($tag->tag_id);
			$count++;
		}
		
		$tags = array_merge($tags,$tags1);
	}
	
	// filter duplicate tags
	$result = array();
	foreach($tags as $tag){
		$not_added = true;
		foreach($result as $res){
			if($tag->tag_name == $res->tag_name){
				$not_added = false;
				break;
			}
		}
		if($not_added){
			$result[] = $tag;
		}
	}
	return $result;
}

/* Add ajax function for auto suggestion 
 * Since 1.1
 */
add_action('wp_ajax_asf_suggestion', 'asf_suggestion_callback');
add_action('wp_ajax_nopriv_asf_suggestion', 'asf_suggestion_callback');

function asf_suggestion_callback() {
	global $wpdb; // this is how you get access to the database

	$s =  $_POST['s'];
	$cat = $_POST['cat'];
	$tag = $_POST['tag'];
	
	// get custom word
	$words = explode("\r\n",get_option('asf-custom-words'));
	// get post titles
	
	$max = get_option('asf-ajax-count',20);
	if(!is_numeric($max) || $max < 0) $max = 20;
	
	$args = array('s'=>$s,'posts_per_page'=>$max);
	
	if(!empty($tag)){
		$args['tag'] = $tag;
	}
	
	
	if(empty($cat)){
		// if $cat is empty, maybe it's because users choose to search all
		// check if "All Categories" is a number of categories
		$selected_categories = get_option('asf-categories',array());
		if(!empty($selected_categories) && count($selected_categories) > 0){
			$cat = implode(',',$selected_categories);
		}
	}	
	
	if(!empty($cat)){
		$args['cat'] = $cat;
	}
	
	$asf_query = new WP_Query($args);

	$html = '<ul>';	
	if($asf_query->have_posts()){
		//$html = '<ul><li>'. $asf_query->found_posts . '</li></ul>';echo $html;die();
		$idx = 1;
		while($asf_query->have_posts()){
			$asf_query->next_post();
			_log($asf_query->post->post_title . " : " . $s);
			if(strpos(strtolower($asf_query->post->post_title),strtolower($s)) !== false){
				$count++;
				$html .= '<li><a href="' . get_permalink($asf_query->post->ID) . '">';
				$html .=	str_replace($s,'<strong>'.$s.'</strong>',$asf_query->post->post_title);
				$html .= '</a></li>';
				$idx++;
			}
		}
		//$html = '<ul><li>'. $idx . '</li></ul>';echo $html;die();
	}
	wp_reset_query();
    
    // search for tags
	$terms = get_terms('post_tag',array('search'=>$s));
	if(count($terms) > 0){
		foreach($terms as $term){
			$html .= '<li><a href="' . get_term_link($term,'post_tag') . '">';
			$html .= str_replace($s,'<strong>'.$s.'</strong>',$term->name);
			$html .= '</a></li>';
		}
	}
	
	// search for categories
	$terms = get_terms('category',array('search'=>$s));
	if(count($terms) > 0){
		foreach($terms as $term){
			$html .= '<li><a href="' . get_term_link($term,'category') . '">';
			$html .= str_replace($s,'<strong>'.$s.'</strong>',$term->name);
			$html .= '</a></li>';
		}
	}
	
	if(count($words) > 0){
		foreach($words as $w){
			if(strpos(strtolower($w),strtolower($s)) !== false){
				$html .= '<li><a href="javascript:void(0)" onclick="suggestion_onItemClick(this);">';
				$html .= 	str_replace($s,'<strong>'.$s.'</strong>',$w);
				$html .= '</a></li>';
			}
		}
	}
	
	$html .= '</ul>';
	
    echo $html;

	die(); // this is required to return a proper result
}

/* ADMIN - Setting page */
define('_DS_', DIRECTORY_SEPARATOR);
require_once dirname(__FILE__) . _DS_ . 'options.php';

if ( is_admin() ){ // admin actions
  add_action( 'admin_menu', 'add_asf_menu' );
  add_action( 'admin_init', 'register_asf_settings' );
} else {
  // non-admin enqueues, actions, and filters
}

function asf_enqueue_admin_media($hook) {
	if($hook == 'toplevel_page_advance-search-form/advance-search-form'){
        wp_register_style( 'asf_wp_admin_css', plugin_dir_url( __FILE__ ) . '/admin/options-style.css', false, '1.0.0' );
        wp_enqueue_style( 'asf_wp_admin_css' );
	}
}
add_action( 'admin_enqueue_scripts', 'asf_enqueue_admin_media' );

function add_asf_menu(){
	//create new top-level menu
	add_menu_page('Advance Search Form Settings', 'ASF Settings', 'administrator', __FILE__, 'asf_settings_page',plugins_url('/search24x24.png', __FILE__));
}
function register_asf_settings(){
	//register our settings
	register_setting( 'asf-settings-group', 'asf-custom-words' );
	register_setting( 'asf-settings-group', 'asf-include-tag' );
	register_setting( 'asf-settings-group', 'asf-post-types' );
	register_setting( 'asf-settings-group', 'asf-categories' );
	register_setting( 'asf-settings-group', 'asf-show-categories' );
	register_setting( 'asf-settings-group', 'asf-custom-css' );
	register_setting( 'asf-settings-group', 'asf-ajax-count' );	
	register_setting( 'asf-settings-group', 'asf-show-tag-filter' );	
	register_setting( 'asf-settings-group', 'asf-highlight-results' );	
	register_setting( 'asf-settings-group', 'asf-ajax-suggestion' );	
	register_setting( 'asf-settings-group', 'asf-button-text' );	
	register_setting( 'asf-settings-group', 'asf-placeholder-text' );	
	register_setting( 'asf-settings-group', 'asf-label' );	
	register_setting( 'asf-settings-group', 'asf-load-css' );	
}

function _log($str){
	$file = dirname(__FILE__) . '\log.txt';
	// Write the contents back to the file
	file_put_contents($file, $str . "\r\n",FILE_APPEND | LOCK_EX);
}
?>