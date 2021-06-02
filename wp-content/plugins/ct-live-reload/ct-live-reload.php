<?php
/*
Plugin Name: CT livereload
Plugin URI: http://dev2.teeallover.com/premium/wp-content/uploads/livereload
Description: Css livereload
Author: Lunartheme
Version: 1.0
Author URI: http://lunartheme.com
*/

add_action( 'wp_footer', 'livereload_script' );
function livereload_script() {

	?>
	<script>
		document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] +
			':35729/livereload.js?snipver=1"></' + 'script>')
	</script>
	<?php
}

add_action('wp_head', 'show_template');
function show_template() {
    global $template;
    // fw_print( is_admin() );
    if ( function_exists('fw_print') && isset($_GET['debug']) && $_GET['debug'] ) {
      fw_print($template);
    }
}

// debug mode 
if ( ! class_exists( 'Kint' ) ) {
  include  'kint/Kint.class.php';
}

add_action('admin_footer', 'ct_live_reload');
function ct_live_reload() {
    ?>
  <script>
    document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] +
      ':35729/livereload.js?snipver=1"></' + 'script>')
  </script>
  <?php
}


function tee_insert_attachment_file () {
   $dir = wp_upload_dir();
        
    $folder = $dir['basedir'] . '/psd';
    
    $files = scandir( $folder );
    foreach( $files as $key => $file ) {
        if ( strlen( $file ) < 5 ) continue;
        lf( $file );
        
        $file_path = $folder . '/' . $file;
        
        $filetype = wp_check_filetype( basename( $file_path ), null );
        
        $attachment = array(
          'guid'           => $wp_upload_dir['baseurl'] . '/psd/' . basename( $file_path ), 
          'post_mime_type' => $filetype['type'],
          'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_path ) ),
          'post_content'   => '',
          'post_status'    => 'inherit'
        );
        
        $attach_id = wp_insert_attachment( $attachment, $file_path );
        lf( $attach_id );
    }
}
