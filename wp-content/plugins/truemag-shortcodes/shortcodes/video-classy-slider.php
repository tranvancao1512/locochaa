<?php

function tm_sc_parse_post_classy_slider($atts, $content){
	$condition = isset($atts['condition']) ? $atts['condition'] : 'lastest';
	$ids = isset($atts['ids']) ? $atts['ids'] : '';
	$categories = isset($atts['categories']) ? $atts['categories'] : '';
	$tags = isset($atts['tags']) ? $atts['tags'] : '';
	$sort_by = isset($atts['sort_by']) ? $atts['sort_by'] : 'DESC';
	$count = isset($atts['count']) ? $atts['count'] : 12;
	$timerange = isset($atts['timerange']) ? $atts['timerange'] : '';
	$paged = isset($atts['paged']) ? $atts['paged'] : '';
	$postformats = isset($atts['postformats']) ? $atts['postformats'] : '';
	
	$auto = isset($atts['auto']) ? $atts['auto'] : 0;
	$auto_timeout = isset($atts['auto_timeout']) ? $atts['auto_timeout'] : '';
	$auto_duration = isset($atts['auto_duration']) ? $atts['auto_duration'] : '';
	$player = isset($atts['player']) ? $atts['player'] : 1;
	
	$themes_pur = '';
	if(function_exists('ot_get_option')){ $themes_pur = ot_get_option('theme_purpose');}

	$content_helper = new CT_ContentHelper;	
	$header_query = $content_helper->tm_get_popular_posts($condition, $tags, $count, $ids, $sort_by, $categories, $args = array(), $themes_pur, $postformats, $timerange, $paged);
	
	$first_video_id = isset($_GET['start']) ? $_GET['start'] : get_the_ID(); // ID of first active video
    $click_action_in_series = function_exists('ot_get_option') ? ot_get_option('click_action_in_series','default') : 'default';
?>
<div id="classy-carousel">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="is-carousel" id="stage-carousel" <?php echo !$auto ? 'data-notauto=1' : '';
				echo $auto_timeout ? ' data-auto_timeout=' . $auto_timeout : '';
				echo $auto_duration ? ' data-auto_duration='. $auto_duration : '';?>>
                    <div class="classy-carousel-content">
                    <?php if($header_query->have_posts()){
						$item_count = 0;
						while($header_query->have_posts()): $header_query->the_post();
							
							$item_count++;
							
							if ($click_action_in_series == 'link' && get_the_ID() != $first_video_id) {
								continue;
							}
							
							
							$format = get_post_format(get_the_ID());
					?>
                        <div class="video-item <?php echo (get_the_ID() == $first_video_id) ? 'start' : '';?>">
                            <div class="item-thumbnail">
                            <?php if($player && get_post_format(get_the_ID()) == 'video'){
								tm_video(get_the_ID(), get_the_ID() == $first_video_id ? (ot_get_option('auto_play_video', false) == 1 ? true : false) : false);
							} else { ?>
                                <a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" >
                                <?php
									if(has_post_thumbnail()){
										global $_device_;
										global $_is_retina_;
										if($_device_== 'mobile' && !$_is_retina_){
											$thumb = 'thumb_520x293';
										}else{
											$thumb = 'thumb_748x421';
										}
										$thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id(), $thumb, true);
									}else{
										$thumbnail[0] = function_exists('tm_get_default_image') ? tm_get_default_image() : '';
										$thumbnail[1] = 748;
										$thumbnail[2] = 421;
									}
									?>
									<img src="<?php echo $thumbnail[0] ?>" width="<?php echo $thumbnail[1] ?>" height="<?php echo $thumbnail[2] ?>" alt="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>">
									<?php if($format == '' || $format == 'standard'  || $format == 'gallery'){ ?>
                                    <div class="link-overlay fa fa-search"></div>
                                    <?php }else {?>
                                    <div class="link-overlay fa fa-play"></div>
                                    <?php }  ?>

                                </a>
                                <div class="item-head">
                                    <h3><a href="<?php the_permalink() ?>"  title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
                                    <span><?php the_time(get_option('date_format')); ?></span>
                                </div>
                            <?php } ?>
                            </div>
                        </div><!--/video-item-->
                    <?php
						endwhile;
						wp_reset_postdata();
					}?>
                    </div><!--/carousel-content-->
                    <div class="clearfix"></div>
                </div><!--stage-->
            </div><!--col8-->
            <div class="col-md-4">
                <div class="is-carousel" id="control-stage-carousel">
                    <a class="control-up"><i class="fa fa-angle-up"></i></a>
                    <div class="classy-carousel-content">
                    <?php if($header_query->have_posts()){
						$item_count = 0;
						while($header_query->have_posts()): $header_query->the_post();
						$item_count++;
					?>
                        <div <?php if ($click_action_in_series == 'link') { echo 'data-href="'.get_the_permalink().'"'; } ?> class="video-item <?php echo (get_the_ID() == $first_video_id) ? 'start' : '';?>">
                            <div class="item-thumbnail">
                            <?php
								if(has_post_thumbnail()){
									$thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id(),'thumb_72x72', true);
								}else{
									$thumbnail[0] = function_exists('tm_get_default_image') ? tm_get_default_image() : '';
									$thumbnail[1] = 72;
									$thumbnail[2] = 72;
								}
								?>
								<img src="<?php echo $thumbnail[0] ?>" width="<?php echo $thumbnail[1] ?>" height="<?php echo $thumbnail[2] ?>" alt="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>" width="72">
                            </div>
                            <div class="item-head">
                                <h3><?php if ($click_action_in_series == 'link') {?><a href="<?php echo get_the_permalink();?>"><?php } ?><?php the_title(); ?><?php if ($click_action_in_series == 'link') {?></a><?php } ?></h3>
                                <span><?php the_time(get_option('date_format')); ?></span>
                            </div>
                            <div class="clearfix"></div>
                        </div><!--/video-item-->
                    <?php
						endwhile;
						wp_reset_postdata();
					}?>
                    </div><!--/carousel-content-->
                    <a class="control-down"><i class="fa fa-angle-down"></i></a>
                </div><!--control-stage-->
            </div>
        </div><!--/row-->
    </div><!--/container-->
</div><!--classy-->

<?php 
}
add_shortcode( 'classy', 'tm_sc_parse_post_classy_slider' );