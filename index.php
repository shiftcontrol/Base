<?php

#echo "<pre>\n\$_SERVER: ". print_r( $_SERVER, true ) ."</pre>\n\n";

define("VERSION",	0.3);
define("PUBDOCS",	'lib/pub');
define("ERROR404",	'system/error/404.txt');
define("VIEWS", 	'system/tpl/');
define("INDEX", 	'index');
define("EXT",		'.txt');
define("CACHELIFE",	0); // secs
define("CACHEPATH",	'system/cache/');
define("URI",		$_SERVER['REQUEST_URI']);

require 'system/Core.php';

Core::run();