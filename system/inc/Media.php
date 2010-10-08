<?php

final class Media {
	
	public static function setup(){}
	
	public static function Process($file, $fld, $value){
		$value = Media::unity3d($file, $fld, $value);
		$value = Media::swf($file, $fld, $value);
		$value = Media::image($file, $fld, $value);
		
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
	
	public static function image($file, $fld, $value){
		
		if( stristr( $value, '!IMG') === FALSE ) return $value;

		/*
		Trigger syntax:
		!IMG,src
		*/
		
		$olines = array();
		$vlines = explode("\n", $value);
		foreach($vlines as $line){
			if( stristr( trim($line), '!IMG') !== FALSE ){
				$cleanline = strip_tags($line);
				list($tag,$src) = explode(",", trim( $cleanline ));				
				$line = "<img src='$src' title=''/>";
			}
			$olines[] = $line;
		}
		return implode("\n", $olines);
		return implode("\n", $olines);
	}


	public static function swf($file, $fld, $value){
		return $value;
	}
	
	
	public static function quicktime($file, $fld, $value){}
	
	
}	