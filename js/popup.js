jQuery(document).ready(function(){

	jQuery('.cc_popup').click( function(e) {
		e.preventDefault();
		var path = jQuery(this).attr('href');		
		var h = parseInt( jQuery(window).height() * 0.85 );
		
		jQuery("body").append('<div class="cc_popup_box"><div class="cc_popup_box_header"><div class="cc_popup_close">Close</div></div><iframe class="cc_iframe" src="'+path+'" height="'+h+'" width="1100" ></iframe><div><script>jQuery(document).ready(function(){ jQuery(".cc_popup_close").click(function(){	jQuery(".cc_popup_box").hide();	location.reload(true);}); });</script>');
	});
	jQuery('.cc_popup_close').click(function(){
		$j('.cc_popup_box').hide();
		location.reload(true);
	});

	
	jQuery('.editinline').click(function(e){
		e.preventDefault();
		var id=jQuery(this).attr('id');
		// show all shows
		jQuery('.main_row').show();
		// hide all edits
		jQuery('.inline_edit_row').hide();
		// hide show line
		jQuery('#show_'+id).hide();		
		// show edit line
		jQuery('#edit_'+id).show();		
	});		
	
	jQuery('.cancel').click(function(){
		// show all shows
		jQuery('.main_row').show();
		// hide all edits
		jQuery('.inline_edit_row').hide();	
	});	
	
	jQuery('.main_row').mouseenter(function(){
		var id = jQuery(this).attr('id');		
		jQuery('#options_'+id).show();	
	});	
	jQuery('.main_row').mouseleave(function(){
		var id = jQuery(this).attr('id');		
		jQuery('#options_'+id).hide();	
	});		
	
	// placement radio value
	jQuery('.placement_radio').click(function(){
		var val = jQuery(this).val();
		var name = jQuery(this).attr('name');
		jQuery('#area_'+name).val(val);
	});
	
	
	
	
});