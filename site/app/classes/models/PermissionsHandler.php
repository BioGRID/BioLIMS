<?php

namespace BioLIMS\app\classes\models;

/**
 * Permissions Handler
 * This class is for handling processing of permissions
 */

use \PDO;
use BioLIMS\app\lib;
use BioLIMS\app\classes\models;
 
class PermissionsHandler {

	private $db;
	private $permissions;
	private $searchHandler;

	public function __construct( ) {
		$this->db = new PDO( DB_CONNECT, DB_USER, DB_PASS );
		$this->db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$this->permissions = array( "public", "observer", "curator", "poweruser", "admin" );
		$this->searchHandler = new models\SearchHandler( );
	}
	
	
	/**
	 * Build a set of column header definitions for the manage permissions table
	 */
	 
	public function fetchColumnDefinitions( ) {
		
		$columns = array( );
		$columns[0] = array( "title" => "ID", "data" => 0, "orderable" => true, "sortable" => true, "className" => "text-center", "dbCol" => 'permission_id', "searchable" => false );
		$columns[1] = array( "title" => "Name", "data" => 1, "orderable" => true, "sortable" => true, "className" => "", "dbCol" => 'permission_name', "searchable" => true, "searchType" => "Text", "searchName" => "Name", "searchCols" => array( "permission_name" => "exact" ));
		$columns[2] = array( "title" => "Description", "data" => 2, "orderable" => true, "sortable" => true, "className" => "", "dbCol" => 'permission_desc', "searchable" => true, "searchType" => "Text", "searchName" => "Description", "searchCols" => array( "permission_desc" => "exact" ));
		$columns[3] = array( "title" => "Category", "data" => 3, "orderable" => true, "sortable" => true, "className" => "text-center", "dbCol" => 'permission_category', "searchable" => true, "searchType" => "Text", "searchName" => "Category", "searchCols" => array( "permission_category" => "exact" ));
		$columns[4] = array( "title" => "Permission Setting", "data" => 4, "orderable" => false, "sortable" => false, "className" => "text-center", "dbCol" => '', "searchable" => false );
		
		return $columns;
		
	}
	
	/**
	 * Build a base query with search params
	 * for DataTable construction
	 */
	 
	private function buildDataTableQuery( $params, $columns, $countOnly = false ) {
		
		if( $countOnly ) {
			$query = "SELECT count(*) as rowCount FROM " . DB_MAIN . ".permissions";
		} else {
			$query = "SELECT permission_id, permission_name, permission_desc, permission_level, permission_category FROM " . DB_MAIN . ".permissions";
		}
		
		$options = array( );
		
		// Main storage for Query Components
		$queryEntries = array( );
		
		// Add in global search filter terms
		$globalQuery = $this->searchHandler->buildGlobalSearch( $params, $columns );
		if( sizeof( $globalQuery['QUERY'] ) > 0 ) {
			$queryEntries[] = "(" . implode( " OR ", $globalQuery['QUERY'] ) . ")";
			$options = array_merge( $options, $globalQuery['OPTIONS'] );
		}
		
		// Add in advanced search filter terms
		$advancedQuery = $this->searchHandler->buildAdvancedSearch( $params, $columns );
		if( sizeof( $advancedQuery['QUERY'] ) > 0 ) {
			$queryEntries[] = "(" . implode( " AND ", $advancedQuery['QUERY'] ) . ")";
			$options = array_merge( $options, $advancedQuery['OPTIONS'] );
		}
		
		// Check for actual entries here
		// so we only add WHERE component if necessary
		if( sizeof( $queryEntries ) > 0 ) {
			$query .= " WHERE " . implode( " AND ", $queryEntries );
		}
		
		return array( "QUERY" => $query, "OPTIONS" => $options );
		
	}
	
	/**
	 * Build a set of permission data based on passed in parameters for searching
	 * and sorting of the results returned
	 */
	 
	public function buildCustomizedPermissionsList( $params ) {
		
		$columns = $this->fetchColumnDefinitions( );
		
		$users = array( );
		$queryInfo = $this->buildDataTableQuery( $params, $columns, false );
		$query = $queryInfo['QUERY'];
		$options = $queryInfo['OPTIONS'];
		
		$orderBy = $this->searchHandler->buildOrderBy( $params, $columns );
		if( $orderBy ) {
			$query .= $orderBy;
		}
		
		$query .= $this->searchHandler->buildLimit( $params );
		
		$stmt = $this->db->prepare( $query );
		$stmt->execute( $options );
		
		while( $row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
			$users[$row->permission_id] = $row;
		}
		
		return $users;
		
	}
	
	/**
	 * Build a count of permission data based on passed in parameters for searching
	 * and sorting of the results returned
	 */
	 
	public function getUnfilteredPermissionsCount( $params ) {
		
		$users = array( );
		$columns = $this->fetchColumnDefinitions( );
		
		$queryInfo = $this->buildDataTableQuery( $params, $columns, true );
		$query = $queryInfo['QUERY'];
		$options = $queryInfo['OPTIONS'];
		
		$stmt = $this->db->prepare( $query );
		$stmt->execute( $options );
		
		$row = $stmt->fetch( PDO::FETCH_OBJ );
		
		return $row->rowCount;
		
	}
	
	/**
	 * Build out the options for the Permissions Manager Table Field
	 */
	 
	private function buildManagePermissionsOptions( $permInfo ) {
		
		$options = array( );

		foreach( $this->permissions as $permission ) {
			$optionName = "permissionOption" . $permInfo->permission_id;
			$options[] = "<label class='radio-inline'><input class='permissionChange' type='radio' name='" . $optionName . "' id='" . $optionName . "' data-permission='" . $permInfo->permission_id . "' value='" . $permission . "'" . (($permission==$permInfo->permission_level)? " checked='checked'" : "") . " />" . $permission . "</label>";
		}
		
		return implode( " ", $options );
		
	}
	
	/**
	 * Build a set of rows for the permissions manager
	 */
	 
	public function buildManagePermissionsRows( $params ) {
		
		$permList = $this->buildCustomizedPermissionsList( $params );
		$rows = array( );
		foreach( $permList as $permID => $permInfo ) {
			$column = array( );
			$column[] = $permID;
			$column[] = $permInfo->permission_name;
			$column[] = $permInfo->permission_desc;
			$column[] = $permInfo->permission_category;
			$column[] = $this->buildManagePermissionsOptions( $permInfo );
			$rows[] = $column;
		}
		
		return $rows;
		
	}
	
	/**
	 * Change permission level for a given permission
	 */
	 
	public function changePermissionLevel( $permissionID, $newLevel ) {
		
		if( lib\Session::validateCredentials( lib\Session::getPermission( 'MANAGE PERMISSIONS' ))) {
			$stmt = $this->db->prepare( "UPDATE " . DB_MAIN . ".permissions SET permission_level=? WHERE permission_id=?" );
			$stmt->execute( array( $newLevel, $permissionID ));
			return true;
		}
		
		return false;
		
	}
	
	/**
	 * Get a count of all permissions available
	 */
	 
	public function fetchPermissionCount( ) {
		
		$stmt = $this->db->prepare( "SELECT COUNT(*) as permCount FROM " . DB_MAIN . ".permissions" );
		$stmt->execute( );
		
		$row = $stmt->fetch( PDO::FETCH_OBJ );
		
		return $row->permCount;
		
	}
	
	/**
	 * Get the list of permissions
	 */
	 
	public function getPermissionList( ) {
		return $this->permissions;
	}
	
	/**
	 * Add a new permission to the database with a default
	 * setting for the permission level
	 */
	 
	public function addPermission( $permissionName, $permissionDesc, $permissionLevel, $permissionCategory ) {
		
		$permissionName = strtoupper( trim($permissionName) );
		$permissionCategory = strtoupper( trim($permissionCategory) );
		
		$stmt = $this->db->prepare( "SELECT permission_id FROM " . DB_MAIN . ".permissions WHERE permission_name=? AND permission_category=? LIMIT 1" );
		$stmt->execute( array( $permissionName, $permissionCategory ));
		
		if( $stmt->rowCount( ) > 0 ) {
			return false;
		}
		
		$row = $stmt->fetch( PDO::FETCH_OBJ );
		
		if( lib\Session::validateCredentials( lib\Session::getPermission( 'MANAGE PERMISSIONS' ))) {
			$stmt = $this->db->prepare( "INSERT INTO " . DB_MAIN . ".permissions VALUES( '0', ?, ?, ?, ? )" );
			$stmt->execute( array( $permissionName, $permissionDesc, $permissionCategory, $permissionLevel  ));
			return true;
		}
	
		return false;
		
	}
	
}

?>