<?php

class Header {
	
	private static $items = array();
	
	public static function setup(){}
	
	public static function Add( $type, $uri="", $media="screen" ){
		switch( $type ){
			case 'script'	: self::$items[] = '<script type="text/javascript" src="'. $uri .'"></script>';	break;
			case 'style'	: self::$items[] = '<link type="text/css" rel="stylesheet" href="'. $uri .'" media="'. $media .'" />';	break;
		}
	}
	
	public static function Get( $type="", $uri="" ){
		echo "<!-- Header.Get() : -->";
		foreach( self::$items as $item ){
			echo "\n\t". $item;
		}
	}
	
}