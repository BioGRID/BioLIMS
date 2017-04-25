<?php

namespace BioLIMS\app\inc;

/**
 * Permission Options
 * Below are a set of permission options used throughout the web application. These options are 100% pulled from the database
 * so no modifications should be made directly to this file.
 */

use \PDO;

/*
 * Parse permissions out of the database and store them in an array
 * that can be referenced throughout the site
 */
 
$permissions = array( );
if( !isset( $_SESSION['PERMISSIONS'] ) || (strtotime( "-30 minutes" ) > $_SESSION['PERMISSIONS']['LAST_UPDATE']) ) {
	
	$db = new PDO( DB_CONNECT, DB_USER, DB_PASS );
	$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	
	$stmt = $db->prepare( "SELECT permission_name, permission_level FROM " . DB_MAIN . ".permissions" );
	$stmt->execute( );
	
	while( $row = $stmt->fetch( PDO::FETCH_OBJ )) {
		$permissions[strtoupper($row->permission_name)] = $row->permission_level;
	}
	
	$permissions['LAST_UPDATE'] = strtotime( "now" );
	$_SESSION['PERMISSIONS'] = $permissions;
	
} else {
	$permissions = $_SESSION['PERMISSIONS'];
}

define( 'PERMISSIONS', $permissions );

?>