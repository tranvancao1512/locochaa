<?php

/*
Plugin Name: BAW Addon - Optimizer
Description: Reduce number of records in Post Meta table created by BAW plugin and convert data from BAW to Top10 plugin
Author: CactusThemes
Author URI: http://cactusthemes.com
Version: 1.0.1
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('cactus_baw_optimizer')){
	class cactus_baw_optimizer{
		function __construct(){
			add_action( 'init', array($this,'init'), 0);
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array($this, 'admin_video_scripts_styles') );
			
			add_action( 'wp_ajax_cactus_baw_optimizer', array($this, 'do_import' ));
			add_action( 'wp_ajax_nopriv_cactus_baw_optimizer', array($this, 'do_import' ));
		}
		
		public function init(){
            //$this->do_import();
		}
		
		public function admin_menu(){
			add_options_page(
				esc_html__('BAW Optimizer','cactus'),
				esc_html__('BAW Optimizer','cactus'),
				'manage_options',
				'cactus_baw_optimizer',
				array(
					$this,
					'settings_page'
				)
			);
		}
		
		function admin_video_scripts_styles() {
			wp_enqueue_style('cactus-baw-addon-css',plugins_url('/css/admin.css', __FILE__));
			wp_enqueue_script( 'cactus-baw-addon-js',plugins_url('/js/admin.js', __FILE__) , array(), '20161405', true );
		}
		
		function do_import(){
            $work = $_POST['work'];
            
            if($work == 'delete'){
                $this->delete_old_data();
            } else {
                // convert to top-10
                $index = intval($_POST['index']);
                
                $message = $this->convert_posts($index);
                
                $total_posts = wp_count_posts('post');
                
                $progress = $index / $total_posts->publish * 100;
                
                $result = array(
							'progress' => $progress,
							'total' => $total_posts->publish,
                            'message' => $message);
                
                echo json_encode($result);
                die();
            }
		}
    
        /**
         * Delete BAW data
         */
        private function delete_old_data(){
            $date = $_POST['date'];
            $type = $_POST['dataType'];
            
            // get ID of the first found row
            $thedate = date_create_from_format('Ymd', $date);
            if($thedate){
                $found = false;
                
                global $wpdb;
                $first_meta_id = 0;
                
                while(!$found && $thedate->format('Ymd') > '20000101'){
                    
                    $sql = "SELECT max(meta_id) as id FROM {$wpdb->prefix}postmeta WHERE meta_key = %s";
                    
                    $suffix = '';
                    if($type == 'day'){
                        $suffix = $thedate->format('Ymd');
                        
                        $thedate = $thedate->sub(new DateInterval('P1D'));
                    } elseif($type == 'week'){
                        // get week of that date
                        $suffix = $thedate->format('YW');
                        
                        $thedate = $thedate->sub(new DateInterval('P7D'));
                        
                    } elseif($type == 'month'){
                        // get month of that date
                        $suffix = $thedate->format('Ym');;
                        
                        $thedate = $thedate->sub(new DateInterval('P1M'));
                    }

                    $results = $wpdb->get_results($wpdb->prepare($sql, "_count-views_{$type}-{$suffix}"));
                    
                    if($results && count($results) > 0){
                        $first_meta_id = $results[0]->id;
                        if($first_meta_id != ''){
                            $found = true;
                        }
                    }
                }
                
                if($found && $first_meta_id != 0){
                    
                    // now delete all data which is added prior
                    $sql = "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key LIKE %s AND meta_id <= %d";
                    $results = $wpdb->query($wpdb->prepare($sql, "_count-views_{$type}-%", $first_meta_id));
                    
                    echo json_encode(array('message' => sprintf('%d rows are deleted', $results), 'status' => 'success'));
                    die();
                }

                echo json_encode(array('status' => 'fail', 'message' => '0 row found'));
            } else {
                echo json_encode(array('status' => 'fail', 'message' => 'Incorrect date format'));
            }
			die();
        }
        
        /**
         * DO THE CONVERT HERE
         */
		private function convert_posts($index){
			 $wp_query = new WP_Query(array(
								'post_type' => 'post',
								'offset' => $index,
								'posts_per_page' => 1,
								'post_status' => 'publish'
							));
                            
            if($wp_query->have_posts()){
                $posts = $wp_query->posts;
            }
							
			if($posts && count($posts) > 0){
				$post = $posts[0];
				
				global $wpdb;
				
				/** CONVERT BAW VIEWS COUNT TO TOP-10 VIEWS **/
				
				$view_all = get_post_meta($post->ID, '_count-views_all', true);	
				//echo $view_all;exit;

				$sql = "SELECT * FROM {$wpdb->prefix}top_ten WHERE postnumber = %d AND blog_id = %d";
				$found = $wpdb->get_results($wpdb->prepare($sql, $post->ID, get_current_blog_id()));
				
				if(!$found || count($found) == 0){

					// insert into table
					$done = $wpdb->insert("{$wpdb->prefix}top_ten",array('postnumber' => $post->ID,
																		'cntaccess' => $view_all,
																		'blog_id' => get_current_blog_id()),
																array('%d','%d', '%d'));
																

				} else {
                    // update if exists
                    $done = $wpdb->update("{$wpdb->prefix}top_ten",array('cntaccess' => $view_all),
                                                                    array('postnumber' => $post->ID,
																		'blog_id' => get_current_blog_id()),
																array('%d'), array('%d', '%d') );
                }
				
				// daily views count
				$sql = "SELECT * FROM $wpdb->postmeta WHERE post_id = %d AND meta_key LIKE %s";
				$results = $wpdb->get_results($wpdb->prepare($sql, $post->ID, '_count-views_day-%'));
				
				if($results){
					foreach($results as $result){
						$day = substr($result->meta_key, 17); // cut meta_key string to get the date. format: YYYYMMDD
						$date = date_create_from_format('Ymd', $day);
						$day = $date->format('Y-m-d') . ' 00:00:00';
						
						// now move data to Top10 table
						$sql = "SELECT * FROM {$wpdb->prefix}top_ten_daily WHERE postnumber = %d AND dp_date = %s";
						$found = $wpdb->get_results($wpdb->prepare($sql, $post->ID, $day));
						
						if(!$found || count($found) == 0){

							// insert into table
							$done = $wpdb->insert("{$wpdb->prefix}top_ten_daily",array('postnumber' => $post->ID,
																				'cntaccess' => $result->meta_value, 
																				'dp_date' => $day,
																				'blog_id' => get_current_blog_id()),
																		array('%d','%d', '%s', '%d'));
																		

						} else {
                            // insert into table
							$done = $wpdb->update("{$wpdb->prefix}top_ten_daily", array('cntaccess' => $result->meta_value),
                                                                                
                                                                                array('postnumber' => $post->ID, 
																				'dp_date' => $day,
																				'blog_id' => get_current_blog_id()),
																		array('%d'), array('%d', '%s', '%d'));
                        }
					}
				}
				/** end VIEWS converting **/
                
                return 'Converting ' . $post->post_title;
			}
		}
		
		public function settings_page(){
			?>
			<div class="wrap">
				<h2>BAW Optimizer Tool</h2>
				<p>This tool is used to delete old records in Post Meta table created by BAW plugin. You can also convert data from BAW to Top10 plugin so you can switch</p>
                <p><select id="optimizer"><option value="delete">Delete Old Data</option><option value="converttotop10">Convert To Top-10 plugin data</option></select></p>
                <div id="opt-delete" class="opt-panel">
                <p style="color:#FF0000">Note: this process is irreversible!</p>
                <p>Delete BAW data before this date: <br/><label><input type="text" name="date" id="baw-date" value=""/> Format: YYYYMMDD (ex: 20160921)</label></p>
                <p>Check which type of data to delete:<br/>
                <label><select name="dataType" id="baw-type"><option value="day">Daily</option><option value="week">Weekly</option><option value="month">Monthly</option></select></label>
                </div>
                <div id="opt-converttotop10" class="hide opt-panel">
                <p>Convert BAW data to <a href="https://wordpress.org/plugins/top-10/" target="_blank">Top-10</a> data</p>
                </div>
				<div id="converter-button-wraper"><a href="javascript:void(0)" id="baw-optimizer-button">Start</a> <div class="progress-bar animate" id="import-progress-bar"><span class="inner" style="width:0%"><span><!-- --></span></span></div></div>
				<p id="converter-message"><!-- --></p>
			</div>
			<?php
		}
	}
}

$cactus_baw_optimizer = new cactus_baw_optimizer();