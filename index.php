<?php                        

define("VERSION",	0.3);
define("PUBDOCS",	'content/pub');
define("ERROR404",	'system/error/404.txt');
define("VIEWS", 	'views');
define("INDEX", 	'index');
define("EXT",		'.txt');
define("CACHELIFE",	5); // secs
define("CACHEPATH",	'cache');
define("URI",		$_SERVER['REQUEST_URI']);  

$BASE_DIRECTORIES = array("projects", "dev", "blog", "news", "team", "references");  

$BASE_FIELDS = array(    
	"state" => 4, // 1:public, 2:review, 3:draft, 4:private   
	"author"=> "Std. Author",
	"date"	=> "100814",	//TODO(marcin): should default to something more smart
	"mdate"	=> "00",
	"title" => "",    
	"thumb" => "../../static/img/thumb.png",
	"tags"	=> array(),
	"categories"	=> array(),
	"tags"	=> array(),
	"country"=> "ISO",
	"client" => "Std. Client",
	"team"	=> "Std. Team",
	"body" 	=> "Std **body**"	
);    

$BASE_FILTERS = array(
	"category" => "categories",
	"tag" => "tags",
	"technology" => "technologies",
	"date" => "date"
);
    
require 'system/Core.php';   
Core::run();