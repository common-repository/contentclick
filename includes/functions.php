<?php

function cc_api_request( $api_args ){
	$cc_api_key = get_option( "cc_api_key", "false" );
	
	$kv = array();	 
	foreach ( $api_args as $key => $value ) {	 
		$kv[] = stripslashes( $key )."=".stripslashes( $value );
	}
	
	$query_string = join( "&", $kv );
	$query_string .= "&api_key=".$cc_api_key;
	
	$url = "https://api.contentclick.co.uk/wordpress/wordpress_api.php?";

	$query_string_post = $query_string."&api_post_type=POST";	
	$request = new WP_Http;
	$result = $request->request( $url.$query_string_post );

	if( is_wp_error($result) || ! isset($result['body']) ){
		$query_string .= "&api_post_type=GET";
	
		$get_file = $url.$query_string;
		
		$result = file_get_contents( $get_file );
		return $result;
	} else {
		return $result['body'];
	}
	
}

function create_widget_script($ad_code){
$ad = explode("-",$ad_code);
$ad_script =  '
<div id="contentclick'.$ad[1].'"></div>
<script type="text/javascript">
    (function() {
        var data =
        {
            pub_id: "'.$ad[0].'",w_id: "'.$ad[1].'",pw: "'.$ad[2].'", cbust: (new Date()).getTime()
        };
        var u="";
        for(var key in data){u+=key+"="+data[key]+"&"}
        u=u.substring(0,u.length-1);		
        var a = document.createElement("script");
        a.type= "text/javascript";
        a.src = "https://api.contentclick.co.uk/pub_serve.php?" + u;
        a.async = "tue";		
        document.getElementById("contentclick'.$ad[1].'").appendChild(a);
    })();		
</script>
';

return $ad_script;
}