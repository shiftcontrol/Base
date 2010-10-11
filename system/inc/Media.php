<?php

final class Media {
	
	public static function setup(){}
	
	public static function Process($file, $fld, $value){
		$value = Media::unity3d($file, $fld, $value);
		$value = Media::swf($file, $fld, $value);
		$value = Media::image($file, $fld, $value);
		$value = Media::vimeo($file, $fld, $value);
		
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
				list($tag,$src,$title) = explode(",", trim( $cleanline ));				
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
				list($tag,$id,$width,$height) = explode(",", trim( $cleanline ));				
				$line = "<p class='centercontent'><iframe src=\"http://player.vimeo.com/video/$id\" width=\"$width\" height=\"$height\" frameborder=\"0\"></iframe></p>";
			}
			$olines[] = $line;
		}
		return implode("\n", $olines);
	}
	
}	