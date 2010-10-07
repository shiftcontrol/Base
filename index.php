<?php

#echo "<pre>\n\$_SERVER: ". print_r( $_SERVER, true ) ."</pre>\n\n";

define("VERSION",	0.3);
define("PUBDOCS",	'content/pub');
define("ERROR404",	'system/error/404.txt');
define("VIEWS", 	'views');
define("INDEX", 	'index');
define("EXT",		'.txt');
define("CACHELIFE",	0); // secs
define("CACHEPATH",	'cache');
define("URI",		$_SERVER['REQUEST_URI']);

require 'system/Core.php';

Core::run();