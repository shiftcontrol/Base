<?php


class Cache {

	public static function read( $cacheName ){
		if( CACHELIFE <= 0 ) return;
		
		$cacheFile = CACHEPATH . '/' . $cacheName;

		if( file_exists($cacheFile) ){
			
			$now		 = time();
			$cacheAge	 = filemtime( $cacheFile ) + CACHELIFE;
			$sourceAge	 = filemtime( Core::getFile(URI) ) + CACHELIFE;
			$cacheDelta  = ($cacheAge - $now);
			$sourceDelta = ($cacheAge - $sourceAge);
			
			echo "Cache:: sourceDelta: $sourceDelta, cacheDelta: $cacheDelta \n";
			
			if( ($cacheDelta > 0) && ($sourceDelta > 0) ){
				// cache-file is newer than CACHELIFE seconds
				// and source-file (.txt) is older than the cache-file -> use cache
				echo ": Using cache";
				echo file_get_contents( $cacheFile );
				exit;
			}else{
				// file is older than CACHELIFE -> clear cache
				echo ": Refreshing cache";
				unlink( $cacheFile );
			}
		}

	}
	
	public static function write( $cacheName, $content ){
		
		$cacheFile = CACHEPATH .'/'. $cacheName;
		
		file_put_contents($cacheFile, $content);
	}
	
}