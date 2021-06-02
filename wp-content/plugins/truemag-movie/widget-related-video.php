<?php

class Related_Video_Widget extends WP_Widget {	

	function __construct() {
    	$widget_ops = array(
			'classname'   => 'related_videos_widget', 
			'description' => __('Related News','cactusthemes')
		);
    	parent::__construct('related_videos', __('TM-Related News/Video','cactusthemes'), $widget_ops);
	}
	function widget($args, $instance) {
		wp_enqueue_script( 'jquery-isotope');
		$cache = wp_cache_get('widget_related_videos', 'widget');		
		if ( !is_array($cache) )
			$cache = array();

		if ( !isset( $argsxx['widget_id'] ) )
			$argsxx['widget_id'] = $this->id;
		if ( isset( $cache[ $argsxx['widget_id'] ] ) ) {
			echo $cache[ $argsxx['widget_id'] ];
			return;
		}

		ob_start();
		extract($args);
		$tags  		= empty($instance['tags']) ? '' : $instance['tags'];
		$related_by  		= empty($instance['related_by']) ? 'tags' : $instance['related_by'];
		$postformat 		= empty($instance['postformat']) ? '' : $instance['postformat'];
		$title 			= empty($instance['title']) ? '' : $instance['title'];
		$title          = apply_filters('widget_title', $title);
		$orderby 		= empty($instance['sortby']) ? '' : $instance['sortby'];		
		$count 		= empty($instance['count']) ? '' : $instance['count'];
		$posttypes = 'post';
		if(function_exists('ot_get_option')){ $themes_pur = ot_get_option('theme_purpose');}
		if($themes_pur == '0')
		{
			$postformat = '';
		}
		if(is_single()){
			if($tags == ''){
				if($related_by == 'tags'){
					$posttags = get_the_tags();
					if ($posttags) {
						foreach($posttags as $tag) {
							$tags .= ',' . $tag->slug; 
						}
						$tags = substr($tags, 1); 
					}
				} else {
					$categories = get_the_category();
					if ( ! $categories || is_wp_error( $categories ) )
						$categories = array();
					
					foreach($categories as $cat){
						$tags .= ',' . $cat->term_id;
					}
					
					$tags = substr($tags, 1); 
				}
			}
		}
		
		$html = '';

		if(class_exists('CT_ContentHelper')){
			$contentHelper = new CT_ContentHelper;

			if(method_exists($contentHelper, 'get_posts_by_tags')){
				// since 4.2.9.8
				if($related_by == 'tags'){
					$the_query = $contentHelper->get_posts_by_tags($posttypes, $tags, $postformat, $count, $orderby, is_single() ? array(get_the_ID()) : '');
				} else {
					$the_query = $contentHelper->get_posts_by_categories($posttypes, $tags, $postformat, $count, $orderby, is_single() ? array(get_the_ID()) : '');
				}
			} else {
				$the_query = $contentHelper->tm_get_related_posts($posttypes, $tags, $postformat, $count, $orderby, $args = array());
			}
			
			if($the_query->have_posts()):
			
				$html = $before_widget;
				if ( $title ) $html .= $before_title . $title . $after_title; 
				
				$html .= '
					<div class="widget-content">
					<div class="list-rating-item row">
				';
				
				$item_video = new CT_ContentHtml; 
				$i = 0;
				while($the_query->have_posts()): $the_query->the_post();$i++;
					$html .= $item_video->get_item_related($postformat, $themes_pur);
				endwhile;
				$html .= '</div>
				</div>';
			endif;
			
			$html .= $after_widget;
		}
		wp_reset_postdata();
		$cache[$argsxx['widget_id']] = ob_get_flush();
		wp_cache_set('widget_popular_videos', $cache, 'widget');
		
		if(is_singular('post') && $tags != ''){
			echo $html;
		}
	}
	
	function flush_widget_cache() {
		wp_cache_delete('widget_custom_type_posts', 'widget');
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['tags'] = esc_attr($new_instance['tags']);
		$instance['related_by'] = esc_attr($new_instance['related_by']);
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['postformat'] = esc_attr($new_instance['postformat']);
		$instance['sortby'] = esc_attr($new_instance['sortby']);
		$instance['count'] = absint($new_instance['count']);
		return $instance;
	}
	
	
	
	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$postformat = isset($instance['postformat']) ? esc_attr($instance['postformat']) : '';
		$count = isset($instance['count']) ? absint($instance['count']) : 3;
		$sortby = isset($instance['sortby']) ? esc_attr($instance['sortby']) : '';
		$tags = isset($instance['tags']) ? esc_attr($instance['tags']) : '';
		$related_by = isset($instance['related_by']) ? esc_attr($instance['related_by']) : 'tags';
		if(function_exists('ot_get_option')){ $themes_pur = ot_get_option('theme_purpose');}
?>
        <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','cactusthemes'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" 
        value="<?php echo $title; ?>" /></p>
      <!-- /**/-->
		<?php if ($themes_pur!='0') { ?>
        <p>
        <label for="<?php echo $this->get_field_id("postformat"); ?>">
        <?php _e('Post format','cactusthemes');	 ?>:
        <select id="<?php echo $this->get_field_id("postformat"); ?>" name="<?php echo $this->get_field_name("postformat"); ?>">
          <option value="video"<?php selected( $postformat, "video" ); ?>><?php _e('Video','cactusthemes'); ?></option>
          <option value="standard"<?php selected( $postformat, "standard" ); ?>><?php _e('News','cactusthemes'); ?></option>
          <option value=""<?php selected( $postformat, "" ); ?>><?php _e('Both','cactusthemes'); ?></option>
        </select>
       </label>
        </p>
		<?php }?>
        <p>
        <label for="<?php echo $this->get_field_id("tags"); ?>">
        <?php _e('Tags or Categories', 'cactusthemes');?>:
        </label>
        <input class="widefat" id="<?php echo $this->get_field_id('tags'); ?>" name="<?php echo $this->get_field_name('tags'); ?>" type="text"  value="<?php echo $tags; ?>" />
		<span class="desc"><?php esc_html_e('List of Tag Slugs or Category IDs, separated by a comma. If empty, Tags or Categories of current Post will be used', 'cactusthemes');?></span>
        </p>
		
		<p>
        <label for="<?php echo $this->get_field_id("related_by"); ?>">
        <?php _e('Related By','cactusthemes');	 ?>:
        <select id="<?php echo $this->get_field_id("related_by"); ?>" name="<?php echo $this->get_field_name("related_by"); ?>">
          <option value="tags"<?php selected( $related_by, "tags" ); ?>><?php _e('Tags', 'cactusthemes'); ?></option>
          <option value="categories"<?php selected( $related_by, "categories" ); ?>><?php _e('Categories', 'cactusthemes'); ?></option>
        </select>
       </label>
        </p>
        
        <p>
        <label for="<?php echo $this->get_field_id("sortby"); ?>">
        <?php _e('Sort by','cactusthemes');	 ?>:
        <select id="<?php echo $this->get_field_id("sortby"); ?>" name="<?php echo $this->get_field_name("sortby"); ?>">
          <option value="date"<?php selected( $instance["sortby"], "date" ); ?>><?php _e('Date','cactusthemes'); ?></option>
          <option value="rand"<?php selected( $instance["sortby"], "rand" ); ?>><?php _e('Random','cactusthemes'); ?></option>
        </select>
       </label>
        </p>
<!--abc-->    
        <p><label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number of posts:','cactusthemes'); ?></label>
        <input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php 
		echo $count; ?>" size="3" /></p>
<!--//-->
<?php
	}
}

// register RecentPostsPlus widget
add_action( 'widgets_init', 'tm_register_widget_Related_Video_Widget' );

function tm_register_widget_Related_Video_Widget() {
	return register_widget("Related_Video_Widget");
}