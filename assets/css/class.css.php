<?php header('Content-type: text/css'); ?>
<?php
class CSS_Object {
	
	function CSS_Object() {
		return true;
	}
	
	function border_radius($attrs) {
		echo "border-radius: {$attrs}; -moz-border-radius: {$attrs}; -webkit-border-radius: {$attrs}; ";
	}
	
	function box_shadow($attrs) {
		echo "box-shadow: {$attrs}; -moz-box-shadow: {$attrs}; -webkit-box-shadow: {$attrs}; "; 
	}
	
	function gradient($start_color=false,$end_color=false,$image='') {
		
		$image_url      = "";
		$image_position = "";
		$image_repeat   = "";
			
		if ( is_array($image) ) :
			$image_url      = "url('" . $image[0] . "'), ";
			$image_position = " background-position: " . $image[1] . "; ";
			$image_repeat   = " background-repeat: " . $image[2] . "; ";
		endif;
		
		echo "background: {$start_color}; /* Old browsers */ "
			. "background: {$image_url} -moz-linear-gradient(top, {$start_color} 0%, {$end_color} 100%); /* FF3.6+ */ "
			. "background: {$image_url} -webkit-gradient(linear, left top, left bottom, color-stop(0%,{$start_color}), color-stop(100%,{$end_color})); /* Chrome,Safari4+ */ "
			. "background: {$image_url}{$image_repeat}{$image_position}, -webkit-linear-gradient(top, {$start_color} 0%,{$end_color} 100%); /* Chrome10+,Safari5.1+ */ "
			. "background-image: {$image_url} -ms-linear-gradient(top, {$start_color} 0%,{$end_color} 100%); /* IE10+ */ "
			. "background: -o-linear-gradient(top, {$start_color} 0%,{$end_color} 100%); /* Opera11.10+ */ "
			. "filter    : progid:DXImageTransform.Microsoft.gradient( startColorstr='" . strtoupper($start_color) . "', endColorstr='" . strtoupper($end_color) . "',GradientType=0 ); /* IE6-9 */ "
			. "background: linear-gradient(top, {$start_color} 0%,{$end_color} 100%); /* W3C */ {$image_position}{$image_repeat}";
	}
	
	function inline_block() {
		echo " display: inline-block; *display: inline; *zoom: 1; ";
	}
	
	
	function background_opacity($amount) {
		echo  "background:transparent; "
			. "filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#{$amount}000000,endColorstr=#{$amount}000000); "
			. "zoom: 1; ";
	}
	
	function register_definitions( $sent_arr=false) {
		
		$arr = (!$sent_arr) ? $this->config : $sent_arr;
		
		foreach ( $arr as $key=>$val ) :
			
			# upper case the $key
			$key = strtoupper($key);
			
			# if the object $def_key exists, append to it
			if ( !isset($this->def_key) ) : 
				$this->def_key = 'CSS_'.$key;
			
			else :
				if ( @is_array($val) ) :
					$this->def_key .= '_' . $key;
				endif;
			endif;
			
			# if the $val is an array, nest this method to break down its values
			if ( is_array($val) ) :
				$this->register_definitions( $val );
				$this->def_key = str_replace('_'.$key,'',$this->def_key);
			
			else :
				
				$this->def_val = $val;
				echo "\t" . $this->def_key . '_' . $key . " : " . $this->def_val."\n";
				define($this->def_key . '_' . $key, $this->def_val);
				
			endif;	
					
			if ( !$sent_arr ) :
				unset($this->def_key,$this->def_val);
			endif;
			
		endforeach;
		
	}
	
	
}

$css = new CSS_Object();
$css->config = array();
include(dirname(__FILE__).'/css.config');
echo "/* These are the predefined attributes specified in the config file \n";
$css->register_definitions();
echo "*/ \n\n";
?>