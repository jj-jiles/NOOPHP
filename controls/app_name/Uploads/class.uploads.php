<?php

class Upload_Image_System {
	
	
	var $image;
	
	
	# ###
	#
	# Tries a cURL request for the image
	# checks the header to makes sure it's valid
		function Upload_Image_System() {
			
			$image = array();
		}
	#
	#
	# END
	#
	# ###
	
	
	
	
	
	
	
	# ###
	#
	# validates the file extension of the file being submitted
		function valid_file_type($file_name) {
			$file_types = array('.png','.jpg','.jpeg','.gif','.bmp');
			foreach ($file_types as $file_type) :
				if ( stristr($file_name, $file_type) ) :
					$a = true;
					break;
				else :
					$a = false;
				endif;
			endforeach;
			return $a;
		}	
	#
	#
	# END
	#
	# ###
	
	
	
	
	
	
	
	# ###
	#
	# Retrieves the image from the URL given
		function pull_the_image(){
			global $form, $config;
			
			$this->headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
			$this->headers[] = 'Connection: Keep-Alive';
			$this->headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
			$this->user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)';
		
			//echo $rawdata;
			
			$ch = curl_init ($form->from_web_url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
			curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
			$rawdata=curl_exec($ch);
			curl_close ($ch);
			
			//print $rawdata;
			
			#
			#get the file extension
			$ext = explode('.', $this->image['base_name']);
			$cnt = count($ext);
			$cnt--;
			$ext = $ext[$cnt];
			
			$new_image = $config->path->cdn . '/' . $this->new_file_name;
			
			$this->image['upload_as']       = $config->path->cdn . '/' . $this->new_file_name;
			$this->image['upload_as_thumb'] = $config->path->cdn . '/thumbs/' . $this->new_file_name;
			
			
			if(file_exists($this->image['upload_as'])) :
				unlink($this->image['upload_as']);
			endif;
			
			$fp = fopen($this->image['upload_as'],'w');
			
			if ( @fwrite($fp, $rawdata) ) :
				chmod($this->image['upload_as'],0777);
				return true;
			endif;
			
			fclose($fp);
			return false;
		}
	#
	#
	# END
	#
	# ###
	
	
	
	
	
	
	
	# ###
	#
	# Uploads the image to the server
		function upload_the_image() {		
			global $config, $db;
			
			$upload_to_directory            = $config->path->assets . '/images/enterprise/logos';
			$this->image['upload_as']       = $config->path->assets . '/images/enterprise/logos/' . $this->new_file_name;
			$this->image['upload_as_thumb'] = $config->path->assets . '/images/enterprise/logos/thumbs/' . $this->new_file_name;
			
			// Make sure its an approved file type
			if ($this->valid_file_type($this->image['base_name'])) :
				// Upload the file
				
				if ( is_file($this->image['upload_as'])) :
					unlink($this->image['upload_as']);
				endif;
				if ( is_file($this->image['upload_as_thumb'])) :
					chmod($this->image['upload_as_thumb'], 0777);
					unlink($this->image['upload_as_thumb']);
				endif;
				
				if (move_uploaded_file($_FILES['incl_enterprise_logo']['tmp_name'], $this->image['upload_as'])) :
								
					chmod($this->image['upload_as'], 0777);
							
					//$image = new SimpleImage();
					//$image->load( $upload_to_directory . '/' . $this->new_file_name);
					
					//$image->resizeToHeight(145);
					//$image->save( $upload_to_directory . '/thumbs/' . $this->new_file_name);
					
					$resize = new Smart_Image_Resize();
					$resize->smart_resize_image( 
						$this->image['upload_as'], 
						$width              = 0, 
						$height             = 145, 
						$proportional       = true, 
						$output             = $this->image['upload_as_thumb'], 
						$delete_original    = false, 
						$use_linux_commands = false 
					);
					
					list($x, $y)   = getimagesize( $upload_to_directory . '/' . $this->new_file_name); 
					list($tx, $ty) = getimagesize( $upload_to_directory . '/thumbs/' . $this->new_file_name); 
					
					
							
				endif;
				
				return true;
			endif;
			
			return false;
		}
	#
	#
	# END
	#
	# ###
	
	
	
	function valid_request() {
		global $form;
		
		$has_upload = (bool) ( isset($_FILES['incl_enterprise_logo']) && !empty($_FILES['incl_enterprise_logo']) );	
				
		if ( isset($_FILES['incl_enterprise_logo']) ) :
			$this->image['base_name'] = basename($_FILES['incl_enterprise_logo']['name']);
		endif;
		$is_valid_type = $this->valid_file_type($this->image['base_name']);
		
		if ( @$has_upload && $is_valid_type ) :
			return true;
		else :
			return false;
		endif;
	}
	
	
	# ###
	#
	# Processes the entire image transaction. Uploads and then inserts into database
		function process_image() {
			global $config, $db, $app, $form, $session;
			
				if ( @$this->valid_request() ) :
				
						#
						#get the file extension
						$ext = explode('.', $this->image['base_name']);
						$cnt = count($ext);
						$cnt--;
						$ext = $ext[$cnt];
						
						# encode the ID for new file name
						$img_id = $app->encode_id($_SESSION['profile']->id);
						#
						# rename the file to the encoded ID
						$this->new_file_name = $img_id.'.'.$ext;
						
						
					
						if ( isset($_FILES['incl_enterprise_logo']) ) :
							$this->image_saved = $this->upload_the_image();
						endif;
						
						if ( @$this->image_saved ) :
						
							$sql = "UPDATE accounts SET enterprise_logo = '$this->new_file_name' WHERE id = " . $session->id();
							
							if ( !$db->query($sql) ) :
								return false;
							else :
								$_SESSION['profile']->enterprise_logo = $this->new_file_name;
								return true;
							endif;
						endif;
				
				else :
					$form->error .= '<li>A valid image file is required.</li>';
									
				endif;
				
				return false;
		}
	#
	#
	# END
	#
	# ###
	
	
}


include( dirname(__FILE__) . '/class.uploads.resize-image.php');
$upload = new Upload_Image_System();

?>