<?php
// this file contains all the admin ajax post handler functions

add_filter('query_vars','plugin_add_trigger');
function plugin_add_trigger($vars) {
    $vars[] = 'cc_contentclick_plugin_trigger';
    return $vars;
}
 

 
add_action('template_redirect', 'plugin_trigger_check');
function plugin_trigger_check() {
    if(intval(get_query_var('cc_contentclick_plugin_trigger')) == 1) {
	
		// save and remeber api key 
		if( $_POST['type'] == "save_api_key" ){
			$cc_api_key = $_POST['cc_api_key'];
			$result = update_option( "cc_api_key", $cc_api_key );
			
			// check api key
			$site_domain = get_site_url();
			$args = array(
				"type"=>"check_key",
				"domain"=>$site_domain,
			);
			$check = cc_api_request( $args );	
			echo $check;
			exit();
		}
		
		
		// edits the status of a widget
		if ( $_POST['type'] == "edit_status" ) {	 
			$kv = array();	 
			foreach ( $_POST as $key => $value ) {	 
				$kv[] = stripslashes( $key )."=".stripslashes( $value );
			}
			
			$query_string = join( "&", $kv );
			$url = "https://api.contentclick.co.uk/wordpress/wordpress_api.php?";	
			$query_string_post = $query_string."&api_post_type=POST";		
			$request = new WP_Http;
			$result = $request->request( $url.$query_string_post );	
			
			if( is_wp_error($result) || ! isset($result['body']) ){
				$query_string .= "&api_post_type=GET";
			
				$get_file = $url.$query_string;
				
				$result = file_get_contents( $get_file );
				echo $result;
			} else {
				echo $result['body'];
			}
			
			exit();
		}
		
		
		if( $_POST['type'] == "placement_save" ){
			// save post
			$post = array(
				'ID' => $_POST['post_id'],
				'menu_order'     => 0,
				'comment_status' => 'closed' ,
				'ping_status'    => 'closed',
				'post_type'      => 'contentclick_ad',
				'post_status' => 'publish',				
				'post_title' => $_POST['ad_code'],
				'post_name' => $_POST['w_name'],
				
			);	
			$id = wp_insert_post( $post, $wp_error );
			
			// save post_meta
			update_post_meta($id, "excluded_categories", $_POST['excluded_categories'], "");
			update_post_meta($id, "placement", $_POST['placement'], "");
			
			$return['new_post_id'] = $id;
			$return['placement'] = $_POST['placement'];
			
			echo json_encode($return);
			exit();
		}		
						
		
    exit;
    }
}



