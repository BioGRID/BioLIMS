<?php

namespace BioLIMS\app\lib;

/**
 * Bootstrap
 * Setup autoload functionality and configure any other globally accessible
 * functionality that will be available to subsequent sections of the site.
 * This is called prior to initialization of any Controller which will 
 * determine the correct routing for the incoming traffic.
 *
 * AUTOLOAD Heavily Copied from with Slight Modifications:
 * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md
 */
 
define( "DS", DIRECTORY_SEPARATOR );

require_once __DIR__ . '/../inc/Config.php';
require_once __DIR__ . '/../inc/Permission.php';
require_once __DIR__ . '/../vendor/autoload.php';


spl_autoload_register( function( $className ) {
	
	// Base Namespace
	$prefix = "BioLIMS\\app\\";
	
	// Base Path
	$basePath = APP_PATH;
	
	// Check to see if class starts with Base Namespace
	$len = strlen( $prefix );
	if( strncmp( $prefix, $className, $len ) !== 0 ) {
		// Get out of here because it's not in our namespace
		return;
	}
	
	$baseClass = substr( $className, $len );
	$file = $basePath . "/" . str_replace( '\\', DS, $baseClass ) . '.php';
	
	if( file_exists( $file ) ) {
		require_once $file;
	}
	
});

?>