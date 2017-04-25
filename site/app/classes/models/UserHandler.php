<?php

namespace BioLIMS\app\classes\models;

/**
 * User Handler
 * This class is for handling processing of users
 */

use \PDO;
use BioLIMS\app\lib;
use BioLIMS\app\classes\models;
 
class UserHandler {

	private $db;
	private $searchHandler;

	public function __construct( ) {
		$this->db = new PDO( DB_CONNECT, DB_USER, DB_PASS );
		$this->db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$this->searchHandler = new models\SearchHandler( );
	}
	
	/**
	 * Build a list of all user ids, user names, emails, and first and last names, last login, class, status
	 */
	 
	public function fetchUserClasses( ) {
		
		$userClasses = array( "observer" );
		
		if( lib\Session::validateCredentials( "admin" ) ) {
			$userClasses[] = "curator";
			$userClasses[] = "poweruser";
			$userClasses[] = "admin";
		} else if( lib\Session::validateCredentials( "poweruser" ) ) {
			$userClasses[] = "curator";
			$userClasses[] = "poweruser";
		} else if( lib\Session::validateCredentials( "admin" ) ) {
			$userClasses[] = "curator";
		}
		
		return $userClasses;
		
	}
	
	/**
	 * Build a list of all user ids, user names, emails, and first and last names, last login, class, status
	 */
	 
	public function buildUserList( ) {
		
		$users = array( );
		
		$stmt = $this->db->prepare( "SELECT user_id, user_name, user_firstname, user_lastname, user_email, user_lastlogin, user_class, user_status FROM " . DB_MAIN . ".users ORDER BY user_firstname ASC" );
		$stmt->execute( );
		
		while( $row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
			$users[$row->user_id] = $row;
		}
		
		return $users;
	}
	
	/**
	 * Build a set of column header definitions for the manage users table
	 */
	 
	public function fetchColumnDefinitions( ) {
		
		$columns = array( );
		$columns[0] = array( "title" => "ID", "data" => 0, "orderable" => true, "sortable" => true, "className" => "text-center", "dbCol" => 'user_id', "searchable" => false );
		$columns[1] = array( "title" => "Name", "data" => 1, "orderable" => true, "sortable" => true, "className" => "", "dbCol" => 'user_name', "searchable" => true, "searchType" => "Text", "searchName" => "Name", "searchCols" => array( "user_name" => "exact" ));
		$columns[2] = array( "title" => "First Name", "data" => 2, "orderable" => true, "sortable" => true, "className" => "", "dbCol" => 'user_firstname', "searchable" => true, "searchType" => "Text", "searchName" => "First Name", "searchCols" => array( "user_firstname" => "exact" ));
		$columns[3] = array( "title" => "Last Name", "data" => 3, "orderable" => true, "sortable" => true, "className" => "", "dbCol" => 'user_lastname', "searchable" => true, "searchType" => "Text", "searchName" => "Last Name", "searchCols" => array( "user_lastname" => "exact" ));
		$columns[4] = array( "title" => "Email", "data" => 4, "orderable" => true, "sortable" => true, "className" => "", "dbCol" => 'user_email', "searchable" => true, "searchType" => "Text", "searchName" => "Email", "searchCols" => array( "user_email" => "exact" ));
		$columns[5] = array( "title" => "Last Login", "data" => 5, "orderable" => true, "sortable" => true, "className" => "", "dbCol" => 'user_lastlogin', "searchable" => true, "searchType" => "Text", "searchName" => "Last Login", "searchCols" => array( "user_lastlogin" => "date" ));
		$columns[6] = array( "title" => "Class", "data" => 6, "orderable" => true, "sortable" => true, "className" => "text-center userClass", "dbCol" => 'user_class', "searchable" => true, "searchType" => "Text", "searchName" => "Class", "searchCols" => array( "user_class" => "exact" ));
		$columns[7] = array( "title" => "Status", "data" => 7, "orderable" => true, "sortable" => true, "className" => "text-center userStatus", "dbCol" => 'user_status', "searchable" => true, "searchType" => "Text", "searchName" => "Status", "searchCols" => array( "user_status" => "exact" ));
		$columns[8] = array( "title" => "Options", "data" => 8, "orderable" => false, "sortable" => false, "className" => "text-center", "dbCol" => '', "searchable" => false );
		
		return $columns;
		
	}
	
	/**
	 * Build a base query with search params
	 * for DataTable construction
	 */
	 
	private function buildDataTableQuery( $params, $columns, $countOnly = false ) {
		
		if( $countOnly ) {
			$query = "SELECT count(*) as rowCount FROM " . DB_MAIN . ".users";
		} else {
			$query = "SELECT user_id, user_name, user_firstname, user_lastname, user_email, user_lastlogin, user_class, user_status FROM " . DB_MAIN . ".users";
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
	 * Build a set of user data based on passed in parameters for searching
	 * and sorting of the results returned
	 */
	 
	public function buildCustomizedUserList( $params ) {
		
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
			$users[$row->user_id] = $row;
		}
		
		return $users;
		
	}
	
	/**
	 * Build a count of user data based on passed in parameters for searching
	 * and sorting of the results returned
	 */
	 
	public function getUnfilteredUsersCount( $params ) {
		
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
	 * Build out the options for the User Manager Table Field
	 */
	 
	private function buildUserManagerOptions( $userInfo ) {
		
		$options = array( );

		$options[] = '<i class="optionIcon fa fa-arrow-up fa-lg popoverData classChange text-info" data-userid="' . $userInfo->user_id . '" title="Promote this Account" data-content="Click this to Increase user\'s access level" data-direction="promote"></i>';
		$options[] = '<i class="optionIcon fa fa-arrow-down fa-lg popoverData classChange text-primary" data-userid="' . $userInfo->user_id . '" title="Demote this Account" data-content="Click this to Decrease user\'s access level" data-direction="demote"></i>';
		
		if( $userInfo->user_status == "active" ) {
			$options[] = '<i class="optionIcon fa fa-times fa-lg popoverData text-danger statusChange" data-userid="' . $userInfo->user_id . '" title="Disable this Account" data-content="Click this to disable user\'s access to the ' . CONFIG['WEB']['WEB_NAME_ABBR'] . ' Website" data-status="inactive"></i>';
		} else {
			$options[] = '<i class="optionIcon fa fa-check fa-lg popoverData text-success statusChange" data-userid="' . $userInfo->user_id . '" title="Enable this Account" data-content="Click this to disable user\'s access to the ' . CONFIG['WEB']['WEB_NAME_ABBR'] . ' Website" data-status="active"></i>';
		}
		
		return implode( " ", $options );
		
	}
	
	/**
	 * Build a set of rows for the user manager
	 */
	 
	public function buildManageUserRows( $params ) {
		
		$userList = $this->buildCustomizedUserList( $params );
		$rows = array( );
		foreach( $userList as $userID => $userInfo ) {
			$column = array( );
			$column[] = $userID;
			$column[] = $userInfo->user_name;
			$column[] = $userInfo->user_firstname;
			$column[] = $userInfo->user_lastname;
			$column[] = $userInfo->user_email;
			$column[] = $userInfo->user_lastlogin;
			$column[] = $userInfo->user_class;
			$column[] = $userInfo->user_status;
			$column[] = $this->buildUserManagerOptions( $userInfo );
			$rows[] = $column;
		}
		
		return $rows;
		
	}
	
	/**
	 * Get a count of all users available
	 */
	 
	public function fetchUserCount( ) {
		
		$stmt = $this->db->prepare( "SELECT COUNT(*) as userCount FROM " . DB_MAIN . ".users" );
		$stmt->execute( );
		
		$row = $stmt->fetch( PDO::FETCH_OBJ );
		
		return $row->userCount;
		
	}
	
	/**
	 * Fetch information about a user based on the passed in
	 * user ID, return false if non-existant
	 */
	
	public function fetchUser( $userID ) {
		
		$stmt = $this->db->prepare( "SELECT * FROM " . DB_MAIN . ".users WHERE user_id=? LIMIT 1" );
		$stmt->execute( array( $userID ) );
		
		if( $row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
			return $row;
		} 
		
		return false;
		
	}
	
}

?>