<?php
/*
 * Plugin Name: Content Click
 * Plugin URI: http://www.contentclick.com
 * Description: Generate a new revenue stream from your blog with our free sponsored content WordPress plugin.
 * Version:  1.2.1
 * Author: Contentclick
 * Author URI: http://www.contentclick.com
 * Developer: Contentclick
 * Developer URI: http://www.contentclick.com
 * Text Domain: Contentclick
 * License: GPLv2
 *
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License, version 2, as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
 
// plugin file includes
include('includes/functions.php'); 
include('admin_pages.php'); 
include('ad_widget_load.php'); 
include('post/posts.php'); 
//. plugin file includes

// set variables
	// set api_key
	$cc_api_key = get_option( "cc_api_key", "false" );
	// check api key
	$site_domain = get_site_url();
	$args = array(
		"type"=>"check_key",
		"domain"=>$site_domain,
	);
	$api_check = cc_api_request( $args );	
	// set cc domain
	$cc_domain = "https://admin.contentclick.co.uk";
	// set cc root
	$cc_root = "/";
// .set variables

add_action( 'admin_init', 'contentclick_admin_init' );
function contentclick_admin_init() {
	// adds admin css
	wp_enqueue_style( 'cc-style',  plugin_dir_url( __FILE__ ).'css/admin.css' );
	// js files
	wp_enqueue_script( 'cc-ajax-request', plugin_dir_url( __FILE__ ) . 'js/ajax.js', array( 'jquery' ) );	
	wp_enqueue_script( 'cc-javascript', plugin_dir_url( __FILE__ ) . 'js/popup.js', array( 'jquery' ) );	
	// declare AJAX varaibles 
	$site_domain = get_site_url();
	$ajax_variables = array(
		'cc_api_url' =>  $site_domain  . '/?cc_contentclick_plugin_trigger=1',
		'cc_placement' =>  $site_domain  . '/?cc_contentclick_plugin_trigger=1',
		'cc_edit_status' =>  $site_domain  . '/?cc_contentclick_plugin_trigger=1',
		'plugin_dir' =>  plugin_dir_url( __FILE__ ),
	);
	wp_localize_script( 'cc-ajax-request', 'CCAjax', $ajax_variables );	
}	


// add contentclick manager menu
add_action("admin_menu", "addMenu");
function addMenu() {
	add_menu_page('ContentClick', 'ContentClick', '', 'cc_admin_menu', 'my_magic_function');
	add_submenu_page( 'cc_admin_menu', 'Account Settings', 'Account Settings', 'manage_options', 'cc_account', 'cc_account_function');
	add_submenu_page( 'cc_admin_menu', 'Ad Widgets', 'Ad Widgets', 'manage_options', 'cc_widgets', 'cc_widgets_function');		
}