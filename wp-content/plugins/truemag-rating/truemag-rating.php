<?php
   /*
   Plugin Name: TrueMAG Rating
   Plugin URI: http://www.cactusthemes.com
   Description: A Rating plugin
   Version: 3.3.2.6
   Author: CactusThemes
   Author URI: http://www.cactusthemes.com
   License: Commercial
   */

define( 'TMR_PATH', plugin_dir_url( __FILE__ ) );
if(!class_exists('OT_Loader')){
	//require_once ('option-tree/ot-loader.php');
}

require_once ('admin/plugin-options.php');
// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

class trueMagRating{
	public $tmr_id = 1;
	//construct
	public function __construct()
    {
		add_action( 'wp_enqueue_scripts', array( $this, 'tmr_frontend_scripts' ) );
		add_action( 'after_setup_theme', array( $this, 'tmr_post_meta' ) );
		add_action( 'save_post', array( $this, 'tm_review_save_post') );
		add_shortcode( 'tmreview', array( $this, 'tmr_shortcode' ) );
		add_filter( 'the_content', array( $this, 'tmr_the_content_filter'), 20 );
		add_filter( 'mce_external_plugins', array(&$this, 'regplugins'));
		add_filter( 'mce_buttons_3', array(&$this, 'regbtns') );
    }
	/*
	 * Setup and do shortcode
	 */
	function tmr_shortcode($atts,$content=""){
		$tmr_options = $this->tmr_get_all_option();
		$tmr_criteria = $tmr_options['tmr_criteria']?explode(",", $tmr_options['tmr_criteria']):'';
		$tmr_float = isset($atts['float'])? $atts['float']:$tmr_options['tmr_float'];
		$tmr_title = isset($atts['title'])? $atts['title']:(get_post_meta(get_the_ID(),'review_title',true)?get_post_meta(get_the_ID(),'review_title',true):$tmr_options['tmr_title']);
		ob_start();
		if(isset($atts['post_id'])){
			$post_id=$atts['post_id'];
		}else{
			global $post;
			$post_id=$post->ID;
		}
		if(get_post_meta($post_id,'taq_review_score',true)){
		?>
        <div id="tmr<?php echo $this->tmr_id; ?>" class="tmr-wrap tmr-float-<?php echo $tmr_float ?>">
			<div class="tmr-inner">
            	<div class="tmr-head"><h3><?php echo $tmr_title; ?></h3></div>
                <div class="tmr-criteria">
                <?php if($tmr_criteria){
					foreach($tmr_criteria as $criteria){
						$point = get_post_meta($post_id,'review_'.sanitize_title($criteria),true);
						if($point){
						?>
                            <div class="tmr-item">
                            <h6><?php echo $criteria ?></h6>
                                <span class="tmr-stars">
                                   <?php $this->tmr_draw_star($point); ?> 
                                </span>
                            </div>
                        <?php
						}
					}
				}
				
				if($custom_review = get_post_meta($post_id,'custom_review',true)){
					foreach($custom_review as $review){
						if($review['review_point']){ ?>
							<div class="tmr-item">
                            <h6><?php echo $review['title'] ?></h6>
                                <span class="tmr-stars">
                                   <?php $this->tmr_draw_star($review['review_point']); ?> 
                                </span>
                            </div>
						<?php }
					}
				}
				?>
                </div>
                <div class="tmr-foot">
                	<div class="tmr-foot-inner">
                        <div class="tmr-summary"><?php echo get_post_meta($post_id,'final_summary',true) ?></div>
                        <div class="tmr-final">
                            <span class="tmr-stars">
                                <?php $this->tmr_draw_star(get_post_meta($post_id,'taq_review_score',true)); ?> 
                            </span>
                            <div class="clear"></div>
                            <div class="tmr-final-review"><?php echo get_post_meta($post_id,'final_review',true) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!--/tmr-wrap-->
        <?php
		$this->tmr_id++;
		}
		$output_string=ob_get_contents();
		ob_end_clean();
		return $output_string;
	}
	function tmr_draw_star($point){
		for($i=1;$i<=5;$i++){
			$class='';
			if(round($point/20,1)<($i-0.5)){
				$class='-o';
			}elseif(round($point/20,1)<$i){
				$class='-half-o';
			}
			echo '<i class="fa fa-star'.$class.'"></i>';
		}
	}
	/*
	 * Get all plugin options
	 */
	function tmr_get_all_option(){
		$tmr_options = get_option('tmr_options_group');
		$tmr_options['tmr_criteria'] = isset($tmr_options['tmr_criteria'])?$tmr_options['tmr_criteria']:'';
		$tmr_options['tmr_position'] = isset($tmr_options['tmr_position'])?$tmr_options['tmr_position']:'bottom';
		$tmr_options['tmr_float'] = isset($tmr_options['tmr_float'])?$tmr_options['tmr_float']:'block';
		$tmr_options['tmr_fontawesome'] = isset($tmr_options['tmr_fontawesome'])?$tmr_options['tmr_fontawesome']:0;
		$tmr_options['tmr_title']= isset($tmr_options['tmr_title'])?$tmr_options['tmr_title']:'';
		return $tmr_options;
	}
	/*
	 * Load js and css
	 */
	function tmr_frontend_scripts(){
		//wp_enqueue_script('ecs-js', tmr_PATH.'js/main.js',array('jquery'),1,true);
		wp_enqueue_style('truemag-rating', TMR_PATH.'style.css');
		$tmr_options = $this->tmr_get_all_option();
		if($tmr_options['tmr_fontawesome']==0){
			wp_enqueue_style('font-awesome-2', TMR_PATH.'font-awesome/css/font-awesome.min.css');
		}
	}
	
	//review save
	function tm_review_save_post($post_ID){
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return;
		if ( ! current_user_can( 'edit_post', $post_ID ) )
			return;
	
		$review_total = 0;
		$review_count = 0;
		$tmr_options = $this->tmr_get_all_option();
		$tmr_criteria = $tmr_options['tmr_criteria']?explode(",", $tmr_options['tmr_criteria']):'';
		if($tmr_criteria){
			foreach($tmr_criteria as $criteria){
				if($_POST['review_'.sanitize_title($criteria)]){
					$review_total += $_POST['review_'.sanitize_title($criteria)];
					$review_count++;
				}
			}
		}
		if(isset($_POST['custom_review'])){
			foreach($_POST['custom_review'] as $review){
				if($review['review_point']){
					$review_total += $review['review_point'];
					$review_count++;
				}
			}
		}
		if($review_count){
			update_post_meta( $post_ID, 'taq_review_score', round($review_total/$review_count,10));
		}
	}
	//the_content filter
	function tmr_the_content_filter($content){
		if ( is_single() ){
			$tmr_options = $this->tmr_get_all_option();
			if($tmr_options['tmr_position']=='top'){
				$content = '[tmreview /]'.$content;
			}elseif($tmr_options['tmr_position']=='bottom'){
				$content .= '[tmreview /]';
			}
		}
		// Returns the content.
		return do_shortcode($content);
	}
	function tmr_post_meta(){
		//option tree
		  $meta_box_review = array(
			'id'        => 'meta_box_review',
			'title'     => 'Review',
			'desc'      => '',
			'pages'     => array( 'post' ),
			'context'   => 'normal',
			'fields'    => array(
				array(
					'label'       => 'Review Title',
					'id'          => 'review_title',
					'type'        => 'text',
					'class'       => '',
					'desc'        => 'Review title for this post',
					'choices'     => array(),
					'settings'    => array()
			   )
		  	)
		  );
		  $tmr_options = $this->tmr_get_all_option();
		  $tmr_criteria = $tmr_options['tmr_criteria']?explode(",", $tmr_options['tmr_criteria']):'';
		  if($tmr_criteria){
			  foreach($tmr_criteria as $criteria){
				  $meta_box_review['fields'][] = array(
					  'id'          => 'review_'.sanitize_title($criteria),
					  'label'       => $criteria,
					  'desc'        => 'Point (Ex: 95)',
					  'std'         => '',
					  'type'        => 'text',
					  'class'       => '',
					  'choices'     => array()
				  );
			  }
		  }
		  $meta_box_review['fields'][] = array(
				'label'       => 'Custom Review Criterias',
				'id'          => 'custom_review',
				'type'        => 'list-item',
				'class'       => '',
				'desc'        => 'Add custom reviews',
				'choices'     => array(),
				'settings'    => array(
					 array(
						'label'       => 'Point',
						'id'          => 'review_point',
						'type'        => 'text',
						'desc'        => '',
						'std'         => '',
						'rows'        => '',
						'post_type'   => '',
						'taxonomy'    => ''
					 ),
				)
		  );
		  $meta_box_review['fields'][] = array(
			  'id'          => 'final_review',
			  'label'       => 'Final Review Word',
			  'desc'        => 'Ex: Good, Bad...',
			  'std'         => '',
			  'type'        => 'text',
			  'class'       => '',
			  'choices'     => array()
		  );
		  $meta_box_review['fields'][] = array(
			  'id'          => 'final_summary',
			  'label'       => 'Final Review Summary',
			  'desc'        => 'Ex: This is must-watch movie of this year',
			  'std'         => '',
			  'type'        => 'textarea',
			  'class'       => '',
			  'choices'     => array()
		  );
		  if (function_exists('ot_register_meta_box')) {
			ot_register_meta_box( $meta_box_review );
		  }
	}
	function regbtns($buttons)
	{
		array_push($buttons, 'tm_rating');
		return $buttons;
	}
	
	function regplugins($plgs)
	{
		$plgs['tm_rating'] = TMR_PATH . 'js/button.js';
		return $plgs;
	}
}
$trueMagRating = new trueMagRating();
//convert hex 2 rgba
function tmr_hex2rgba($hex,$opacity) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $opacity = $opacity/100;
   $rgba = array($r, $g, $b, $opacity);
   return implode(",", $rgba); // returns the rgb values separated by commas
   //return $rgba; // returns an array with the rgb values
}