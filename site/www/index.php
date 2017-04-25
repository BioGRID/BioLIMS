<?php

/**
 * Index
 * Manage access to the site. Load files needed to direct traffic to the
 * correct location within the application structure
 */
 
error_reporting( E_ALL );
ini_set( 'display_errors', 'On' );
 
session_start( );
header( "Cache-control: private" );

require_once __DIR__ . '/../app/lib/Bootstrap.php';

use BioLIMS\app\lib\Loader;
use BioLIMS\app\lib\User;

$loader = new Loader( $_GET );
$controller = $loader->createController( );
$controller->executeAction( );

?>