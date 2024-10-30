<?php
function cc_widgets_function(){
	global $cc_api_key;
	global $cc_domain;
	global $cc_root;
	
	// get widget list for api_key and blog domain
	$site_domain = get_site_url();
	$args = array(
		"type"=>"widget_table",
		"domain"=>$site_domain,
	);
	$widget_list = cc_api_request( $args );
	$widget_list = json_decode( $widget_list );
	
	// create html
	
	// categories
	$args = array(
		'orderby' => 'name',
		'order'  => 'ASC',
		'parent' => 0,	
    );
    $categories = get_categories($args);
	

echo "<div class='cc_wrap'>
	<div class='cc_title'>ContentClick</div>
	<h3>Create and manage your widgets</h3>
	<div class='' >1. Add New - Click 'Add New Ad Widget' to set up an ad widget</div>
	<br />	
	<div class='' >2. Place the widget - Click on 'placement' underneath the Ad widget name below to select where you want the widget to appear. If you want to place the ad widget within a sidebar, use the ContentClick widget managed within Appearance>>Widgets</div>
	<br />		
	<a class='cc_popup' href='{$cc_domain}{$cc_root}publisher/manage-widget.php?api_key={$cc_api_key}&wp=1'><div class='button button-primary button-large' style='display:inline-block;' >Add New Ad Widget</div></a><br /><br />";
	echo "<input type='text' style='display:none;' name='cc_api_key' value='{$cc_api_key}'>";
	$table = "<table class='wp-list-table widefat fixed posts'>
		<thead>
			<tr>
				<th>Widget Name</th>
				<th>Placement</th>
				<th>Reports</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>";
	if( $widget_list ){
		foreach( $widget_list as $value ){
		
			$cc_post_id = get_page_by_title( $value->ad_code, "ARRAY_A", "contentclick_ad" );
			// category selected
			if($cc_post_id){
				$excluded_categories = get_post_meta( $cc_post_id['ID'], "excluded_categories", "" );
				$excluded_categories = json_decode($excluded_categories[0]);
			}	else {
				$excluded_categories = array();
			}			
			// radio selected
			$place_sel = array();
			if($cc_post_id){
				$selected_placement = get_post_meta( $cc_post_id['ID'], "placement", "" );
				if($selected_placement[0] == "before_post_content"){$place_sel[0] = "checked";}
				if($selected_placement[0] == "after_post_content"){$place_sel[1] = "checked";}
				if($selected_placement[0] == "in_widget_sidebar"){$place_sel[2] = "checked";}	
				if($selected_placement[0] == ""){unset($place_sel);}
			} else {
				unset($place_sel);
				unset($selected_placement[0]);
			}
			
			$cat_box ="";
			foreach($categories as $category)  {
				if(in_array($category->term_id, $excluded_categories)){ $checked = "checked";} else {$checked = "";}
				 $cat_box .= '<input class="category_exclusion_'.$value->w_id.'" type="checkbox" id="'.$category->term_id.'" name="'.$category->slug.'" value="1" '.$checked.'><label class="cc_parent_cat"> ' . $category->name . '</label><br />';
			} 	
			$cat_box = '<div class="cat_box">'.$cat_box.'</div>';
			
			if($value->widget_status == 1){
				$status = "Active";
				$status_edit = "Pause";
			} else {
				$status = "Paused";
				$status_edit = "Set Active";
			}
			
			
			$table .= "
				<tr id='show_{$value->w_id}' class='main_row'>
					<td>{$value->w_name}<div class='cc_options' id='options_show_{$value->w_id}'><a class='cc_popup' href='{$value->edit}&api_key={$cc_api_key}'>Edit</a> | <a class='editinline' href='#' id={$value->w_id}>Placement</a> | <a class='edit_status' id='status_{$value->w_id}' href='#'>{$status_edit}</a></div></td>
					<td id='table_placement_{$value->w_id}'>{$selected_placement[0]}</td>					
					<td><a class='cc_popup' href='{$value->reports}&api_key={$cc_api_key}&wp=1'>View</a></td>
					<td id='status_show_{$value->w_id}'>{$status}</td>
				</tr>
				<tr id='edit_{$value->w_id}' class='inline_edit_row'>
					<td colspan='4'>

						<div class='cc_place'>
							<div class='cc_place_title'>Select where the Widget should appear:</div>
							<input class='placement_radio' {$place_sel[0]} id='before_post_content' type='radio' name='placement_{$value->w_id}' value='before_post_content'></input><label> Before Post Content</label><br />
							<input class='placement_radio' {$place_sel[1]} id='after_post_content' type='radio' name='placement_{$value->w_id}' value='after_post_content'></input><label> After Post Content</label><br />
							<input class='placement_radio' {$place_sel[2]} id='in_widget_sidebar' type='radio' name='placement_{$value->w_id}' value='in_widget_sidebar'></input><label> In the ContentClick Widget (eg sidebar or footer)</label><br />
							<input type='text' value='{$selected_placement[0]}' id='area_placement_{$value->w_id}' style='display:none'>
							<input type='text' value='{$value->ad_code}' id='ad_code_{$value->w_id}' style='display:none'>
							<input type='text' value='{$value->w_name}' id='w_name_{$value->w_id}' style='display:none'>
							<input type='text' value='{$cc_post_id['ID']}' id='post_id_{$value->w_id}' style='display:none'>
						</div>

						<div class='cc_cats'>
							<div class='cc_place_title'>Exclude Categories:</div>
							{$cat_box}
						</div>
						
						<div class='clear'></div>
						<div class='buttons'>
							<div class='button-secondary cancel alignleft'>Cancel</div>
							<div id='{$value->w_id}' class='button-primary placement_save alignright'>Update</div>
						</div>	
						<br />
			
					</td>

						
				</tr>
				";
			
		}
	} else {
		$table .= "<tr><td colspan='4'>No Widgets Found on this domain</td></tr>";
	}
		$table .=" </tbody>
		<tfoot>
			<tr>
				<th>Widget Name</th>
				<th>Placement</th>
				<th>Reports</th>
				<th>Status</th>
			</tr>		
		</tfoot>
	</table>";
	
	echo $table;
	
	echo "</div>";	
}

function cc_account_function(){
	
global $cc_api_key;
global $api_check;
global $cc_domain;
global $cc_root;

$site_domain = urlencode( get_site_url() );

if( $api_check == "true" ){
	$img = "tick";
} else {
	$img = "cross";
}

echo "<div class='cc_wrap'>
	<div class='cc_title'>ContentClick</div>
	<h3>Welcome to the Wordpress ContentClick Plugin.</h3><br />
	<div class='' ><span style='font-weight:bold;font-size:15px;'>1.</span> Do you have a ContentClick account? If not, please first <a href='http://www.contentclick.co.uk/signup.php?wp=1&domain={$site_domain}' target='_blank'>create one</a> for free.</div>
	<br />
	<div class=''><span style='font-weight:bold;font-size:15px;'>2.</span> Connect your ContentClick unique API code that you received upon sign up. Access your API code <a href='http://www.contentclick.co.uk/login.php?wp=1' target='_blank'>here</a> under MyAccount</div>
	<br />
	<div class=''>Input Your ContentClick API Key and click save:</div>
	<input type='text' style='width:250px;' name='cc_api_key' value='".$cc_api_key."' /><img id='api_key_result' src='".plugin_dir_url( __FILE__ )."/images/".$img.".png'>
	<br /><br />
	<div class='button-primary api_save' style='display:inline-block;'>Save and Remember ContentClick API Key</div>
	<br /><br />
	<div class=''><span style='font-weight:bold;font-size:15px;'>3.</span> Create Your <a href='admin.php?page=cc_widgets'>ContentClick widgets</a></div>
	<br /><br />
	<h3>Manage your account and billing details.</h3><br />
	<div class=''>Click <a href='https://admin.contentclick.co.uk/login.php' target='_blank'>MyAccount</a> to manage your account settings</div>	
	<br />
	<div class=''>In order to receive payment, please add your payment details into the MyAccount section.</div>
	<br />	
	</div>";
}