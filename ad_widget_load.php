<?php

// ----------WIDGET IN POST-------------- //
add_filter('the_content', 'cc_add_widget', 100);
function cc_add_widget($content) {	

	if( is_front_page() == 1 OR is_home() == 1){
		return $content;
	}
	
	if( !is_single() ){
		return $content;
	}
		
// get post ids of ads to display 	
	// BEFORE
	$args = array(
		  'post_type'   => 'contentclick_ad',
		  'meta_query'  => array(
				array(
				  'value' => 'before_post_content'
				)
		  )
	);
	$cc_before = new WP_Query( $args );	
	if( $cc_before->have_posts() ) {
			while( $cc_before->have_posts() ) {
				$cc_before->the_post();		
			} 
	} 
	wp_reset_postdata();	
	
	// AFTER
	$args = array(
		  'post_type'   => 'contentclick_ad',
		  'meta_query'  => array(
				array(
				  'value' => 'after_post_content'
				)
		  )
	);
	$cc_after = new WP_Query( $args );	
	if( $cc_after->have_posts() ) {
			while( $cc_after->have_posts() ) {
				$cc_after->the_post();		
			} 
	} 
	wp_reset_postdata();	

	
// add widgets to front of content if not an excluded category

	// get post categories array
	$post_categories = array();
	$post_categories = wp_get_post_categories( $GLOBALS['post']->ID );

	$before_ads = "";
	foreach($cc_before->posts as $post){

		// get excluded categories array	
		$excluded_categories = get_post_meta( $post->ID, "excluded_categories", "" );
		$excluded_categories = json_decode($excluded_categories[0]);
		
		// if the post categories matches an excludede category, don't display ad
		$check = true;
		foreach($post_categories as $post_cat){
			if( in_array( $post_cat, $excluded_categories , true) ) {
				$check = false;
			}
		}

		if($check){
			$ad_widget = get_post( $post->ID, "ARRAY_A", "" );
			$ad_script = create_widget_script($ad_widget['post_title']);
			$before_ads .= $ad_script;
		}	
	}

// add widgets to end of content if not an excluded category
	$after_ads = "";
	foreach($cc_after->posts as $post){
	
		// get excluded categories array	
		$excluded_categories = get_post_meta( $post->ID, "excluded_categories", "" );
		$excluded_categories = json_decode($excluded_categories[0]);
			
		// if the post categories matches an excludede category, don't display ad
		$check = true;
		foreach($post_categories as $post_cat){
			if( in_array( $post_cat, $excluded_categories , true ) ) {
				$check = false;
			}
		}	

		if($check){
			$ad_widget = get_post( $post->ID, "ARRAY_A", "" );
			$ad_script = create_widget_script($ad_widget['post_title']);
			$after_ads .= $ad_script;
		}	
	}		
	
	$content = $before_ads.$content.$after_ads;	
	return $content;	
}
//. ----------WIDGET IN POST-------------- //




// --------- WIDGET IN SIDEBAR -----------//
add_action('widgets_init', 'register_cc_widget');
function register_cc_widget() {
	register_widget('cc_sidebar_widget');
}

class cc_sidebar_widget extends WP_Widget {
	
	function cc_sidebar_widget()	{
		$widget_ops = array('classname' => 'cc', 'description' => 'Widget for positioning ContentClick ads in the sidebar or footer');
		$control_ops = array('id_base' => 'cc_sidebar_widget');
		$this->WP_Widget('cc_sidebar_widget', 'ContentClick', $widget_ops, $control_ops);
	}
	
	function widget($args, $instance)	{		
		
	// get post ids of ads to display 	
		// sidebar
		$args = array(
			  'post_type'   => 'contentclick_ad',
			  'meta_query'  => array(
					array(
					  'value' => 'in_widget_sidebar'
					)
			  )
		);
		$cc_sidebar = new WP_Query( $args );	
		if( $cc_sidebar->have_posts() ) {
				while( $cc_sidebar->have_posts() ) {
					$cc_sidebar->the_post();		
				} 
		} 
		
		// get post categories array
		global $wp_query;
		$thePostID = $wp_query->post->ID;
		
		$post_categories = array();
		$post_categories = wp_get_post_categories( $thePostID );

		$sidebar_ads = "";
		foreach($cc_sidebar->posts as $post){

			// get excluded categories array	
			$excluded_categories = get_post_meta( $post->ID, "excluded_categories", "" );
			$excluded_categories = json_decode($excluded_categories[0]);
			
			// if the post categories matches an excludede category, don't display ad
			$check = true;
			foreach($post_categories as $post_cat){
				if( in_array( $post_cat, $excluded_categories, true ) ) {
					$check = false;
				}
			}

			if($check){
				$ad_widget = get_post( $post->ID, "ARRAY_A", "" );
				$ad_script = create_widget_script($ad_widget['post_title']);
				$sidebar_ads .= $ad_script;
			}	
		}
		
		echo $before_widget.$sidebar_ads.$after_widget;
	}	
}


//. --------- WIDGET IN SIDEBAR -----------//