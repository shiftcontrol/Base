<?php               

date_default_timezone_set("Europe/Copenhagen");         

define("VERSION",	0.3);
define("PUBDOCS",	'content/pub');
define("ERROR404",	'system/error/404.txt');
define("VIEWS", 	'views');
define("INDEX", 	'index');
define("EXT",		'.txt');
define("CACHELIFE",	5*60); // secs
define("CACHEPATH",	'cache');
define("URI",		$_SERVER['REQUEST_URI']);      
define("PAGE_WIDTH", 950);
define("MAX_MEDIA_HEIGHT", 550);
define("ADMIN_EMAIL", "admin@yourdomain.com");

$FLICK_API_KEY = "12345678901234567890";

$BASE_DIRECTORIES = array("blog", "experiments", "projects", "dev", "news", "team", "references");  

$BASE_FIELDS = array(    
	"state" => 1, // 1:public, 2:review, 3:draft, 4:private   
	"author"=> "Std. Author",
	"date"	=> "100814",	//TODO(marcin): should default to something more smart
	"mdate"	=> "00",
	"title" => "",    
	"thumb" => "../../static/img/thumb.png",
	"tags"	=> "",
	"categories"	=> "",
	"country"=> "ISO",
	"client" => "Std. Client",
	"team"	=> "Std. Team",
	"body" 	=> "",
	"teaser" => " "  
);    

$BASE_FILTERS = array(
	"category" => "categories",
	"tag" => "tags",
	"technology" => "technologies",
	"date" => "date"
);
    
require 'system/Core.php';   
Core::run();