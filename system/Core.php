<?php defined('VERSION') or die('No direct script access.');

final class Core {
	
	public static function run(){
		Core::setup();
		Core::route();
	}
	
	public static function setup(){
		
		# Disable Notices
		error_reporting(~E_NOTICE);
		
		# Enable additional Modules
		require 'system/Cache.php';
		
		# All text should be written with Markdown syntax.
		# See Dingus here: http://michelf.com/projects/php-markdown/dingus/
		# We're using Markdown Extra from http://michelf.com/projects/php-markdown/
		require 'system/inc/markdown.php';

		# Require Plugins
		require 'system/inc/Media.php';
		Media::setup();

		require 'system/inc/Header.php';
		Header::setup();
		
	}
	
	public static function route(){
		#TODO: Document
		
		$fields = array();
		list($e, $cmd, $arg) = explode("/", URI);
		if( ($arg=="") || ($arg=="all") ) $arg = 'list'; // both "/{empty}" and "/all" equals "/list"
		$args  = explode(",", $arg);
		
		
		echo "cmd:$cmd, arg:$arg";
				
		# Check Cache
		$cacheName = $cmd.$arg;
		#Cache::read( $cacheName );

		# Intercept calls for special urls

		#TODO: Verify		
		# Search- and list-able Document properties (same as the Template defaults (combine?))
		$properties = array("author", "date", "mdate", "title", "teaser", "tags", "country", "client", "team", "body");
		
		# list-able Directories
		$directories = array("projects", "news", "team", "references");
		
		if( in_array($cmd, $properties) ){
			# Document properties
			$compare = !($arg=='list');			
			$found = Core::matchProp( $cmd, $args, $compare);
			$body  = print_r( $found, true );
			$fields["body"] = $body; #Markdown($body);

		}else if( in_array($cmd, $directories) && ($arg=='list') ){
			# Directory-list requests
			$listController = VIEWS .'/'. $cmd.'_list.php';
			
			#echo $listController;
			include( $listController );
			exit;
			
		}else if( $cmd == "docs" ){
			echo Markdown( file_get_contents("system/docs.txt") );
			
		}else{
			# Normal requests
			$file = Core::getFile( URI );
			$fields = Core::getFields( $file, true );
		}
	
		#Core::respond( $fields, $cacheName );
		Core::respond( $fields, $cacheName, $cmd );
	}
	
	##
	
	public static function Populate($tplName, $fileName){
		
		$tplName = VIEWS .'/'. $tplName;
		
		if( !file_exists($tplName) || !file_exists($fileName) ) return "";

		# Import $fields into local scope, overwriting the defaults above
		$fields = Core::getFields( $fileName, true );
		extract( $fields );
		
		list($pathToFolder, $permalink) = Core::getPathInfo( $fileName );
		#echo "pathToFolder: $pathToFolder, permalink:$permalink\n";

		# Populate Template
		$viewfn	 = $tplName;//VIEWS . $view;
		$search  = array('%author%',	'%date%',	'%mdate%', '%title%',	'%teaser%',	'%tags%',	'%country%',	'%client%',	'%team%',	'%mdate%',	'%body%', '%thumb%');
		$replace = array( $author,		 $date,		 $mdate,	$title, 	 $teaser,    $tags,		 $country,		 $client,	 $team,		 $mdate,	 $body,    $thumb);

		# Output buffering + include() allows php execution in the view files :)
		ob_start();
		include( $viewfn );
		$subject = ob_get_clean();

		# Replace template tags
		$html 	 = str_replace($search, $replace, $subject);

		# Write Cache
		#Cache::write($cacheName, $html);

		# Return
		return $html;	
	}
	
	##
	
	public static function respond( $fields, $cacheName, $view="projects" ){
		echo "COULD use view $view";
		
		#TODO: Externalize!!
		# Setup default template values
		# Note: Only $body allows line breaks
		$state	= 1;		// 1:public, 2:review, 3:draft, 4:private
		$author	= "Std. Author";
		$date	= "100814";
		$mdate	= "00";
		$title 	= "Std Title";
		$teaser = "Std Teaser";
		$tags	= array();
		$country= "ISO";
		$client = "Std. Client";
		$team	= "Std. Team";
		$body 	= "Std **body**";
		$view 	= $view .'.php';

		# Import $fields into local scope, overwriting the defaults above
		extract( $fields );

		# Populate Template
		$viewfn	 = VIEWS .'/'. $view;
		$search  = array('%author%',	'%date%',	'%mdate%', '%title%',	'%teaser%',	'%tags%',	'%country%',	'%client%',	'%team%',	'%mdate%',	'%body%', '%thumb%');
		$replace = array( $author,		 $date,		 $mdate,	$title, 	 $teaser,    $tags,		 $country,		 $client,	 $team,		 $mdate,	 $body,    $thumb);

		# Output buffering + include() allows php execution in the view files :)
		ob_start();
		include( $viewfn );
		$subject = ob_get_clean();

		# Replace template tags
		$html 	 = str_replace($search, $replace, $subject);

		# Write Cache
		Cache::write($cacheName, $html);

		# Print
		echo $html;
	}
	
	public static function getUrlToFile( $file ){
		#
		# Tranform a filesystem path to a url
		# lib/pub/projects/markup/index.txt		-> /projects/markup
		# lib/pub/projects/markdown/markdown.txt	-> /projects/markdown/markdown
		#
		$a = explode(PUBDOCS, $file);
		$b = explode("/", end($a));
		if( end($b) == INDEX.EXT ){
			# capture "index.txt"
			array_pop($b);
			$path = implode('/',  $b);
		}else{
			# capture filename.txt
			$b[]  = current( explode(EXT, array_pop($b)));
			$path = implode('/',  $b);
		}
		return $path;
	}

	public static function matchProp( $property, $search, $compare=true ){
		#
		# Search all $property tags, in all files, for a match on $search
		#TODO: Add Cache
		
		if( !is_array($search) ) $search = explode(",", $search);

		#echo "<pre>";

		$files = Core::getFiles();
		$found = array();
		$filter = array();
		foreach( $files as $file ){

			$props  = Core::getFields($file, false);
			$fields = explode(",", $props[$property]); 

			foreach( $fields as $field ){
				if( $field == "" ) continue;

				foreach( $search as $val ){
					#echo "\nchecking field '$field' against value '$val'";
					if( $compare ){
						if( $field != $val ) continue;
					}
					
					#echo " - Found";
					
					if( in_array($file, $filter) ) continue;
					
					$found[] = array(
						"file"		=> $file,
						$property	=> $props[$property],
						"uri"		=> Core::getUrlToFile( $file ),
					);
					$filter[] = $file;
				}
			}
		}
		
		
		
		$results = array(
			"key"	=> $property,
			"search"=> implode(", ", $search),
			"view"	=> "match",
			"data"	=> $found
		);
		
		return $results;
	}

	public static function getFile( $folder ){
		# Allow both /path/to/folder/file(without file-extension)
		# and /path/to/folder (where we will load the INDEX.EXT file)
		$path = PUBDOCS . $folder;
		if( $path == PUBDOCS ) $path = PUBDOCS .'/index';

		$file = $path . EXT;
		if( is_dir($path) )	$file = $path .'/'. INDEX . EXT;

		if( !file_exists($file) ) $file = ERROR404;
		
		#echo "FILE: $file, PATH: $path\n";	
		return $file;
	}
	
	public static function getPathInfo( $file ){
		$a = end( explode(PUBDOCS, $file));
		$a = explode('/', $a);
		$b = array_pop($a);
		
		$pathToFolder = '/'. PUBDOCS . implode('/', $a) .'/';
		$permalink = implode('/', $a) .'/';

		return array($pathToFolder, $permalink);
	}

	public static function getFiles( $folder=NULL, $exclude=NULL, $collection=NULL){

		if( $folder === NULL )		$folder = PUBDOCS;
		if( $exclude === NULL )		$exclude = array();
		if( $collection === NULL )	$collection = array();

		$handle = opendir( $folder );
		while (($file = readdir($handle))!==false) {
			if ( substr($file,0,1)!="." && substr($file,0,2)!="..") {
				$key = $folder.'/'.$file;
				if( is_dir( $key ) ){
					$collection = Core::getFiles( $key, $exclude, $collection );	
				}else{
					foreach( glob($folder ."/*.txt") as $filename) {
						if( !in_array( $filename, $collection ) ){
							if( !in_array( $file, $exclude) ){
								$collection[] = $filename;
							}
						}
					}
				}
			}
		}
		closedir($handle); 
		return ( $collection );
	}

	public static function getFields($file, $doProcessBody=true){
		
		$inf = explode("/", $file);
		$fil = array_pop($inf);
		$fld = implode("/", $inf) ."/";

		$arr = array();
		$c = file_get_contents( $file );
		$lines = explode("\n", $c);
		$i = 0;
		foreach($lines as $line){
			if( strpos($line, '@') === 0 ){
				list($key, $value) = explode(":", $line, 2);		# split key:value pairs
				$key = substr($key, 1);								# remove starting '@'
				$value = trim(current(explode("//", $value, 2)));	# remove comments "// comment" on this line
				$arr["".$key] = trim($value);
				$i++;
				
				# Check state
				#if( $state > 1 ) exit("Not Public");

				# Custom key parsing
				if( $key == 'body' ){
					$value = implode("\n", array_slice($lines, $i, sizeof($lines) ) );

					if( $doProcessBody ){
						# Run the $value through active plugins
						$value = Markdown( $value );
						
						# unfinnished
						if( class_exists( Media ) ) $value = Media::Process($file, $fld, $value);
						//if( defined(UNITY3D) ){
						//	$value = Media::unity3d($file, $fld, $value);
						//}
					}

					# Prefix path to images
					$value = str_replace('<img src="', '<img src="/'.$fld, $value);

					$arr["".$key] = trim($value);
				}
			}
		}

		# Add Modified date
		$arr["mdate"] = date ("ymd@H:i", filemtime($file));

		return $arr;
	}
	
}