<?php require_once("phpFlickr/phpFlickr.php"); ?>
<?php

class Media {
	
	public static function setup(){}
	
	public static function Process($file, $fld, $value){
		$value = Media::unity3d($file, $fld, $value);
		$value = Media::swf($file, $fld, $value);
		$value = Media::image($file, $fld, $value);
		$value = Media::vimeo($file, $fld, $value);
		$value = Media::flickrset($file, $fld, $value);
		return $value;
	}
	
	
	
	public static function unity3d($file, $fld, $value){
		
		if( stristr( $value, '!U3D') === FALSE ) return $value;

		/*
		Trigger syntax:
		!U3D,"preview build v3.1",300,500,test.unity3d,inline|click,click.png
		*/
		
		$olines = array();
		$vlines = explode("\n", $value);
		foreach($vlines as $line){
			if( stristr( trim($line), '!U3D') !== FALSE ){
				$cleanline = strip_tags($line);
				list($tag,$desc,$width,$height,$src,$type,$image,$crap) = explode(",", trim( $cleanline ));
				
				if( class_exists( Header ) ){
					Header::Add("script", "/static/js/unityobject.js");
					Header::Add("style", "/static/css/unity.css");
				}

				$line = "<code>--\nreplaced:$cleanline\n-> inserting unity3d plugin here:\ndesc:$desc\nwidth:$width\nheight:$height\nsrc:$fld$src\ntype:$type\nimage:$image\n--</code><br /><br />\n";// . $line;
			}
			$olines[] = $line;
		}
		return implode("\n", $olines);
	}
	                                                  
	/*
	Trigger syntax:
	!IMG,src
	*/
	public static function image($file, $fld, $value){		
		if( stristr( $value, '!IMG') === FALSE ) return $value;

		$olines = array();
		$vlines = explode("\n", $value);
		foreach($vlines as $line){
			if( stristr( trim($line), '!IMG') !== FALSE ){
				$cleanline = strip_tags($line);
				list($tag,$src,$title) = array_pad(explode(",", trim( $cleanline )), 3, "");				
				$line = "<img src='$src' title='$title'/>";
			}
			$olines[] = $line;
		}
		return implode("\n", $olines);
	}


	public static function swf($file, $fld, $value){
		return $value;
	}
	
	
	public static function quicktime($file, $fld, $value){}
	
	/*
	Trigger syntax:
	!VIMEO,id,width,height
	*/
	public static function vimeo($file, $fld, $value) {
		if( stristr( $value, '!VIMEO') === FALSE ) return $value;
		
		$olines = array();
		$vlines = explode("\n", $value);
		foreach($vlines as $line){
			if( stristr( trim($line), '!VIMEO') !== FALSE ){
				$cleanline = strip_tags($line);
				list($tag,$id,$width,$height,$bgcolor,$fullpage) = array_pad(explode(",", trim( $cleanline )),6,"");	
				$style = "";
				if ($bgcolor) {
					$style .= "background:#$bgcolor;padding-top:0.5em";
				}	 
				if (defined("PAGE_WIDTH") && $fullpage == "true") {     
					$height = PAGE_WIDTH * $height / $width;
					$width = PAGE_WIDTH;
					if (defined("MAX_MEDIA_HEIGHT") && $height > MAX_MEDIA_HEIGHT) {						
						$width = MAX_MEDIA_HEIGHT * $width / $height;
						$height = MAX_MEDIA_HEIGHT;
                    }										
				}   	
				$line = "<p class='centercontent vimeo' style='$style'><iframe src=\"http://player.vimeo.com/video/$id\" width=\"$width\" height=\"$height\" frameborder=\"0\"></iframe></p>";
			}
			$olines[] = $line;
		}
		return implode("\n", $olines);
	}      
	
	public static function flickrset($file, $fld, $value){ 
		if( stristr( $value, '!FLICKRSET') === FALSE ) return $value;
		
		$olines = array();
		$vlines = explode("\n", $value);
		foreach($vlines as $line){
			if( stristr( trim($line), '!FLICKRSET') !== FALSE ){
				$cleanline = strip_tags($line);
				list($tag,$id) = explode(",", trim( $cleanline ));				
				$line = Media::flickrsetbuild($id);
			}
			$olines[] = $line;
		}
		return implode("\n", $olines);
	}  

  	public static function flickrsetbuild($id) {  
		global $FLICK_API_KEY;
		
		$f = new phpFlickr("$FLICK_API_KEY");
		$i = 0;            
		$text = "";
		$username = "marcinignac";
		// Find the NSID of the username inputted via the form
	    $person = $f->people_findByUsername($username);   
	
		$personid = "47952774@N02";

	    // Get the friendly URL of the user's photos
	    $photos_url = $f->urls_getUserPhotos($personid);

	    // Get the user's first 36 public photos
	    //$photos = $f->people_getPublicPhotos($personid, NULL, NULL, 36);
        $photos = $f->photosets_getPhotos($id);	   
	    // Loop through the photos and output the html
		$text .= "<div class='span-24 flickrset'>";
	    foreach ((array)$photos['photoset']['photo'] as $photo) {
	        $text .= "<a href=$photos_url$photo[id]>";
	        $text .= "<img border='0' alt='$photo[title]' " . "src=" . $f->buildPhotoURL($photo, "Square") . ">";
	        $text .= "</a>";
	        $i++;
	        // If it reaches the sixth photo, insert a line break
	        //if ($i % 6 == 0) {
	        //    $text .= "<br>\n";
	        //}
	    }  
		$text .= "</div>";               
		
		return $text;
	}
}	