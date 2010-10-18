<?php defined('VERSION') or die('No direct script access.');

require 'system/inc/Media.php'; 
require 'system/inc/Header.php';

function addPercentages($n) {
	return "%".$n."%";
}   

final class Core {

	public static function run(){
		Core::setup();
		Core::route();
	}

	public static function setup(){

		# Disable Notices
		//error_reporting(~E_NOTICE);
		//error_reporting(E_ALL);

		# Enable additional Modules
		require 'system/Cache.php';

		# All text should be written with Markdown syntax.
		# See Dingus here: http://michelf.com/projects/php-markdown/dingus/
		# We're using Markdown Extra from http://michelf.com/projects/php-markdown/
		require 'system/inc/markdown.php';

		# Require Plugins
		
		Media::setup();
		Header::setup();

	}

	public static function route(){
		global $BASE_DIRECTORIES;  
		global $BASE_FILTERS;
		
     	$fields = array();
		//list($e, $cmd, $arg, $subarg) = explode("/", URI);
		list($e, $cmd, $arg, $subarg) = array_pad(explode("/", URI), 4, ""); 
		if( ($arg=="") || ($arg=="all") ) $arg = 'list'; // both "/{empty}" and "/all" equals "/list"
		$args  = explode(",", $arg);

    	# Check Cache
		//TODO(marcin): better cache name e.g. replace all '/' with '_'
		$cacheName = $cmd.$arg;
		Cache::read( $cacheName );

		# Intercept calls for special urls

		#TODO: Verify
		# Search- and list-able Document properties (same as the Template defaults (combine?))

		# list-able Directories
		/*
		$properties = array("author", "date", "mdate", "title", "teaser", "tags", "country", "client", "team", "body");
		if( in_array($cmd, $properties) ){
			//TODO(marcin): this has to be killed and moved
			//from /tags to /projects/tags
			# Document properties
			$compare = !($arg=='list');
			$found = Core::matchProp( $cmd, $args, $compare);
			$body  = print_r( $found, true );
			$fields["body"] = $body; #Markdown($body);

		} else
		*/       
		
		//echo $cmd.in_array($cmd, $BASE_DIRECTORIES);
		//echo $arg.in_array($arg, array_keys($BASE_FILTERS));
		
		if( in_array($cmd, $BASE_DIRECTORIES) && ($arg=='list') ){
			# Directory-list requests
			$file = PUBDOCS.URI.INDEX.EXT;
			if (file_exists($file)) {
				Core::respond( $file, $cacheName, $cmd.'_list.php');
			}
			else {
				$listController = VIEWS .'/'. $cmd.'_list.php';
				include( $listController );
				exit();
			} 
		
		}
		else if( in_array($cmd, $BASE_DIRECTORIES) && in_array($arg, array_keys($BASE_FILTERS)) ){
			$file = PUBDOCS."/$cmd/".INDEX.EXT;
			if (file_exists($file)) {    
				$filter = $arg;
				$filterArgs = str_replace("-"," ", $subarg);
				Core::respond( $file, $cacheName, $cmd.'_list.php', $filter, $filterArgs);
			}
		} 
		else if( $cmd == "docs" ){
			echo Markdown( file_get_contents("system/docs.txt") );
      		exit();
		} 
		else if( (file_exists(PUBDOCS.URI)) && (!is_dir(PUBDOCS.URI)) ){
			//$fi = new finfo(FILEINFO_MIME,'/usr/share/file/magic');
			//$mime_type = $fi->buffer(file_get_contents(PUBDOCS.URI));
			
			//PHP 5.3
			//$finfo = finfo_open(FILEINFO_MIME_TYPE);
			//$mime_type = finfo_file($finfo, PUBDOCS.URI);
			
			//PHP 5.2
			$mime_type = mime_content_type(PUBDOCS.URI);
			header("Content-Type:$mime_type");
			readfile(PUBDOCS.URI);
			exit("");
		} 
		else {
			# Normal requests
			$file = Core::getFile( URI );
			Core::respond( $file, $cacheName);
		}
	}

    public static function populate($view, $fileName) {
		Core::respond($fileName, NULL, $view);  
	}  
 
 	public static function respond( $fileName, $cacheName, $view = "", $filter="", $filterArgs=""){
		global $BASE_FIELDS;
		
        $fileFields = Core::getFields( $fileName, true, true ); 

		list($pathToFolder, $permalink, $path) = Core::getPathInfo( $fileName );

		$localFields = array(
			"permalink" => $permalink
		);                      
		  
		if ($view == "") {
			$view = $path[0].".php";
		}		

		//echo $fields['view'] . "###";
		if (isset($fileFields['view'])) {
			$view = $fileFields['view'];
		}
		$viewfn	 = VIEWS .'/'. $view; 
		
		//echo "Core:respond fileName:$fileName\n";
  		//echo $viewfn . "\n";  
		
		if (!file_exists($viewfn) || is_dir($viewfn)) {
			error_log("Core:respond empty template for fileName:$fileName\n");
			return;
		}        
		
		$fields =  $localFields + $fileFields + $BASE_FIELDS;
	   
        # Populate Template  
		

		# Output buffering + include() allows php execution in the view files :)
		ob_start();
		include( $viewfn );
		$subject = ob_get_clean();

		# Replace template tags       
		$keys = array_map("addPercentages", array_keys($fields)); 		
		//$keys = array_keys($fields); 
		$values = array_values($fields);		
		   	
		//$html 	 = str_replace($keys, $values, $subject);   
		//$html 	 = str_replace(array("a", "b"), array("A","B"), $subject); 
		                                         
		//error_log("Core:respond fileName:$fileName\n");
		/*
		echo "Core:respond fileName:$fileName\n";
		echo "Core:respond keys\n";
		print_r($keys);             
		echo "Core:respond values\n";
		print_r($values);        
		/**/
		$html 	 = str_replace($keys, $values, $subject);   
		//$html = $subject;
		
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

		if( !file_exists($file) ) {
			error_log("Core::getFile doesn't exist : $file");
			$file = ERROR404;
		}

		#echo "FILE: $file, PATH: $path\n";
		return $file;
	}

	public static function getPathInfo( $file ){
		$a = explode(PUBDOCS, trim($file, "/"));
		$a = end($a);          
		$a = explode('/', $a);
		$b = array_pop($a);       
		$path = explode(PUBDOCS."/", $file);
		$path = end($path);         
		$path = explode("/", $path);  
		
		$pathToFolder = '/'. PUBDOCS . implode('/', $a) .'/';
		$permalink = implode('/', $a) . "/";		

		return array($pathToFolder, $permalink, $path);
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
					//$fileArray = ;
					//if ($fileArray) {
						foreach(glob($folder ."/*.txt")  as $filename) {
							if( !in_array( $filename, $collection ) ){
								if( !in_array( $file, $exclude) && !in_array( $filename, $exclude)){
									$collection[] = $filename;
								}
							}
						}
					//}
				}
			}
		}
		closedir($handle);
		return ( $collection );
	}

	public static function getFilesExt($folder, $sortBy = NULL, $reverse = false, $filter="", $filterArgs="") {
	 	global $BASE_FILTERS;
		$files = Core::getFiles($folder, array("$folder/index.txt"));
		if ($sortBy) {
			$files2 = array();
			foreach($files as $file) {		  
				$fields = Core::getFields($file); 
				if ($filter) {                                 
					if (strpos($fields[$BASE_FILTERS[$filter]], $filterArgs) === FALSE) {
						continue;
					}
				}
				$files2["$file"] = $fields[$sortBy];
			}
			asort($files2);
			$files = array_keys($files2);
		}
		                    
		if ($reverse) {
			$files = array_reverse($files);	
		}       
		
		//$files = array_slice($files, 0, 1); //TEMP
		
		return $files;     
	}

	public static function getFields($file, $doProcessBody=true, $debug=false){
		//if ($debug) { 
		//	echo "Core:getFields:$file\n";
		//	readfile($file);
		//}
		$inf = explode("/", $file);
		$fil = array_pop($inf);
		$fld = implode("/", $inf) ."/";

		$arr = array();
		$c = file_get_contents( $file );
		$lines = explode("\n", $c);    
		//if ($debug) { 
		//	echo "Core:getFields:$file\n";
		//	print_r($lines);
		//}		
		$i = 0;
		foreach($lines as $line){
			if( strpos($line, '@') === 0 ){
				list($key, $value) = explode(":", $line, 2);		# split key:value pairs
				$key = substr($key, 1);								# remove starting '@'
				//$value = trim(current(explode("//", $value, 2)));	# remove comments "// comment" on this line
				$value = explode("//", $value, 2);
				$value = current($value);
				$value = trim($value);	# remove comments "// comment" on this line
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
						$value = Media::Process($file, $fld, $value);
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