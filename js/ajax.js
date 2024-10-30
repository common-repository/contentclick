jQuery(document).ready(function(){				
	jQuery('.api_save').click(function(){	
		var cc_api_key = jQuery('input[name=cc_api_key]').val();	
		jQuery.ajax({
			type: "POST",
			url: CCAjax.cc_api_url,
			data: "cc_api_key="+cc_api_key+"&type=save_api_key",
				success: function (html){
					if(html == "true"){
						var src = CCAjax.plugin_dir+'/images/tick.png'
						jQuery('#api_key_result').attr('src', src);
					} else {
						var src = CCAjax.plugin_dir+'/images/cross.png'
						jQuery('#api_key_result').attr('src', src);						
					}
					// add api key to account href
					var href = 'https://admin.contentclick.co.uk/publisher/my-account.php?api_key='+cc_api_key+'&wp=1';
					jQuery('#cc_account_settings').attr('href',href);					
				}
		});
	});	
	
	jQuery('.edit_status').click(function(e){
		e.preventDefault();
		var cc_api_key = jQuery('input[name=cc_api_key]').val();
		var w_id=jQuery(this).attr('id');
		w_id=w_id.replace('status_','');
		if( jQuery(this).text() == "Set Active"){
			var new_status = 1;
		} else {
			var new_status = 0;
		}
		jQuery.ajax({
			type: "POST",
			url: CCAjax.cc_edit_status,
			data: "api_key="+cc_api_key+"&type=edit_status&new_status="+new_status+"&w_id="+w_id,
				success: function (html){
					if(new_status == 1){
						// active
						jQuery('#status_show_'+w_id).text('Active');
						jQuery('#status_'+w_id).text('Pause');
					} else {
						// paused
						jQuery('#status_show_'+w_id).text('Paused');
						jQuery('#status_'+w_id).text('Set Active');
					}
					
				}
		});	
	});		
	

	jQuery('.placement_save').click(function(){	

		// get placement
		var w_id = jQuery(this).attr('id');
		var placement = jQuery('#area_placement_'+w_id).val();
		
		// get excluded category ids
		var cats = [];
		var i = 0;
		jQuery('.category_exclusion_'+w_id).each(function(){
			if(jQuery(this).is(':checked')){
				var cat_id = jQuery(this).attr('id');
				cats[i] = cat_id;
				i=i+1;
			}	
		});
		cats = JSON.stringify(cats);
		
		// get ad code
		var ad_code = jQuery('#ad_code_'+w_id).val();
		
		// get w_name
		var w_name = jQuery('#w_name_'+w_id).val();
		var post_id = jQuery('#post_id_'+w_id).val();
	
		jQuery.ajax({
			type: "POST",
			url: CCAjax.cc_placement,
			data: "type=placement_save&w_id="+w_id+"&placement="+placement+"&excluded_categories="+cats+"&ad_code="+ad_code+"&w_name="+w_name+"&post_id="+post_id,
				success: function (html){
					// close placement edit row
					// show all shows
					jQuery('.main_row').show();
					// hide all edits
					jQuery('.inline_edit_row').hide();	

					var data_back = JSON.parse(html);
					
					// update post_id and area_placement
					jQuery('#post_id_'+w_id).val(data_back.new_post_id);
					jQuery('#area_placement_'+w_id).val(data_back.placement);
					
					// update widget table with placement
					jQuery('#table_placement_'+w_id).html(data_back.placement);
				}

		});
	});		
});