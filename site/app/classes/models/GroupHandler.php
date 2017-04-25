<?php

namespace BioLIMS\app\classes\models;

/**
 * Group Handler
 * This class is for handling processing of groups
 */

use \PDO;
use BioLIMS\app\lib;
use BioLIMS\app\classes\models;
 
class GroupHandler {

	private $db;
	private $searchHandler;

	public function __construct( ) {
		$this->db = new PDO( DB_CONNECT, DB_USER, DB_PASS );
		$this->db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$this->searchHandler = new models\SearchHandler( );
	}
	
	/**
	 * Create a new permission group
	 */
	 
	public function addGroup( $groupName, $groupDesc, $groupMembers ) {
		
		$stmt = $this->db->prepare( "SELECT group_id FROM " . DB_MAIN . ".groups WHERE group_name=? AND group_status='active' LIMIT 1" );
		$stmt->execute( array( $groupName ));
		
		if( $stmt->rowCount( ) > 0 ) {
			return false;
		}
		
		$row = $stmt->fetch( PDO::FETCH_OBJ );
		
		if( lib\Session::validateCredentials( lib\Session::getPermission( 'MANAGE GROUPS' ))) {
			$stmt = $this->db->prepare( "INSERT INTO " . DB_MAIN . ".groups VALUES( '0', ?, ?, NOW( ), 'active' )" );
			$stmt->execute( array( $groupName, $groupDesc ));
			
			// Fetch its new ID
			$groupID = $this->db->lastInsertId( );
			$this->updateGroupUsers( $groupID, $groupMembers );
			
			return true;
		}
	
		return false;
	}
	
	/**
	 * Edit a Permission Group
	 */
	 
	public function editGroup( $groupID, $groupName, $groupDesc, $groupMembers ) {
		
		$stmt = $this->db->prepare( "SELECT group_id FROM " . DB_MAIN . ".groups WHERE group_name=? AND group_id != ? AND group_status='active' LIMIT 1" );
		$stmt->execute( array( $groupName, $groupID ));
		
		if( $stmt->rowCount( ) > 0 ) {
			return false;
		}
		
		$row = $stmt->fetch( PDO::FETCH_OBJ );
		
		if( lib\Session::validateCredentials( lib\Session::getPermission( 'MANAGE GROUPS' ))) {
			$stmt = $this->db->prepare( "UPDATE " . DB_MAIN . ".groups SET group_name=?, group_desc=?, group_status='active' WHERE group_id=?" );
			$stmt->execute( array( $groupName, $groupDesc, $groupID ));
			
			// Fetch its new ID
			$this->updateGroupUsers( $groupID, $groupMembers );
			
			return true;
		}
	
		return false;
	}
	
	/**
	 * Update the users associated with a specific
	 * permission group
	 */
	 
	public function updateGroupUsers( $groupID, $groupMembers ) {
		
		$stmt = $this->db->prepare( "UPDATE " . DB_MAIN . ".group_users SET group_user_status='inactive' WHERE group_id=?" );
		$stmt->execute( array( $groupID ));
		
		foreach( $groupMembers as $userID ) {
			$stmt = $this->db->prepare( "SELECT group_user_id FROM " . DB_MAIN . ".group_users WHERE user_id=? AND group_id=? LIMIT 1" );
			$stmt->execute( array( $userID, $groupID ));
			
			if( $stmt->rowCount( ) > 0 ) {
				// Already Exists, Re-Activate It
				$row = $stmt->fetch( PDO::FETCH_OBJ );
				$stmt = $this->db->prepare( "UPDATE " . DB_MAIN . ".group_users SET group_user_status='active' WHERE group_user_id=?" );
				$stmt->execute( array( $row->group_user_id ));
			} else {
				// Doesn't Exist, Add New
				$stmt = $this->db->prepare( "INSERT INTO " . DB_MAIN . ".group_users VALUES( '0',?, ?, NOW( ), 'active' )" );
				$stmt->execute( array( $groupID, $userID ));
			}
			
		}
		
	}
	
	/**
	 * Fetch information about a group based on the passed in
	 * group ID, return false if non-existant
	 */
	
	public function fetchGroup( $groupID ) {
		
		$stmt = $this->db->prepare( "SELECT * FROM " . DB_MAIN . ".groups WHERE group_id=? LIMIT 1" );
		$stmt->execute( array( $groupID ) );
		
		if( $row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
			return $row;
		} 
		
		return false;
		
	}
	
		/**
	 * Build a set of column header definitions for the manage permissions table
	 */
	 
	public function fetchColumnDefinitions( ) {
		
		$columns = array( );
		$columns[0] = array( "title" => "Name", "data" => 0, "orderable" => true, "sortable" => true, "className" => "", "dbCol" => 'group_name', "searchable" => true, "searchType" => "Text", "searchName" => "Name", "searchCols" => array( "group_name" => "exact" ));
		$columns[1] = array( "title" => "Description", "data" => 1, "orderable" => true, "sortable" => true, "className" => "", "dbCol" => 'group_desc', "searchable" => true, "searchType" => "Text", "searchName" => "Description", "searchCols" => array( "group_desc" => "exact" ));
		$columns[2] = array( "title" => "Members", "data" => 2, "orderable" => false, "sortable" => false, "className" => "", "dbCol" => '', "searchable" => false );
		$columns[3] = array( "title" => "Group Settings", "data" => 3, "orderable" => false, "sortable" => false, "className" => "text-center", "dbCol" => '', "searchable" => false );
		
		return $columns;
		
	}
	
	/**
	 * Build a base query with search params
	 * for DataTable construction
	 */
	 
	private function buildDataTableQuery( $params, $columns, $countOnly = false ) {
		
		if( $countOnly ) {
			$query = "SELECT count(*) as rowCount FROM " . DB_MAIN . ".groups";
		} else {
			$query = "SELECT group_id, group_name, group_desc FROM " . DB_MAIN . ".groups";
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
		
		$query .= " WHERE group_status='active'";
		
		// Check for actual entries here
		// so we only add AND component if necessary
		if( sizeof( $queryEntries ) > 0 ) {
			$query .= " AND " . implode( " AND ", $queryEntries );
		}
		
		return array( "QUERY" => $query, "OPTIONS" => $options );
		
	}
	
	/**
	 * Build a set of group data based on passed in parameters for searching
	 * and sorting of the results returned
	 */
	 
	public function buildCustomizedGroupList( $params ) {
		
		$columns = $this->fetchColumnDefinitions( );
		
		$groups = array( );
		
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
			$groups[$row->group_id] = $row;
		}
		
		return $groups;
		
	}
	
	/**
	 * Build a count of groups data based on passed in parameters for searching
	 * and sorting of the results returned
	 */
	 
	public function getUnfilteredGroupCount( $params ) {
		
		$columns = $this->fetchColumnDefinitions( );
		
		$groups = array( );
		
		$queryInfo = $this->buildDataTableQuery( $params, $columns, true );
		$query = $queryInfo['QUERY'];
		$options = $queryInfo['OPTIONS'];
		
		$stmt = $this->db->prepare( $query );
		$stmt->execute( $options );
		
		$row = $stmt->fetch( PDO::FETCH_OBJ );
		
		return $row->rowCount;
		
	}
	
	/**
	 * Build out the options for the Groups Manager Table Field
	 */
	 
	private function buildManageGroupOptions( $groupInfo ) {
		
		$options = array( );
		
		$options[] = '<a href="' . WEB_URL . '/Admin/EditGroup?groupID=' . $groupInfo->group_id . '"><i class="optionIcon fa fa-pencil-square-o fa-lg popoverData fileView text-success editGroup" data-title="Edit Group" data-content="Edit this Permission Group to Add or Remove new Members"></i></a>';
			
		$options[] = '<i class="optionIcon fa fa-times fa-lg popoverData fileView text-danger deleteGroup" data-groupid="' . $groupInfo->group_id . '" data-title="Delete Group" data-content="Remove this Permission Group from the Site"></i>';
		
		return implode( " ", $options );
		
	}
	
	/**
	 * Build a set of rows for the groups manager
	 */
	 
	public function buildManageGroupRows( $params ) {
		
		$groupList = $this->buildCustomizedGroupList( $params );
		$rows = array( );
		foreach( $groupList as $groupID => $groupInfo ) {
			$column = array( );
			
			// $checkedBoxes = array( );
			// if( isset( $params['checkedBoxes'] )) {
				// $checkedBoxes = $params['checkedBoxes'];
			// }
			
			// if( isset( $checkedBoxes[$groupID] ) && $checkedBoxes[$groupID] ) {
				// $column[] = "<input type='checkbox' class='biolimsDataTableRowCheck' value='" . $groupID . "' checked />";
			// } else {
				// $column[] = "<input type='checkbox' class='biolimsDataTableRowCheck' value='" . $groupID . "' />";
			// }
			
			$column[] = $groupInfo->group_name;
			$column[] = $groupInfo->group_desc;
			
			$memberList = $this->fetchGroupUsers( $groupID );
			$userList = array( );
			foreach( $memberList as $memberInfo ) {
				$userList[] = $memberInfo->user_firstname . " " . $memberInfo->user_lastname;
			}
			
			$column[] = implode( ", ", $userList );
			
			$column[] = $this->buildManageGroupOptions( $groupInfo );
			$rows[] = $column;
		}
		
		return $rows;
		
	}
	
	/**
	 * Fetch a list of members associated with this group
	 */
	 
	public function fetchGroupUsers( $groupID ) {
		
		$stmt = $this->db->prepare( "SELECT g.user_id, u.user_firstname, u.user_lastname FROM " . DB_MAIN . ".group_users g LEFT JOIN " . DB_MAIN . ".users u ON (g.user_id=u.user_id) WHERE g.group_user_status='active' AND g.group_id=? ORDER BY u.user_firstname ASC" );
		$stmt->execute( array( $groupID ));
		
		$users = array( );
		
		while( $row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
			$users[$row->user_id] = $row;
		}
		
		return $users;
		
	}
	
	/**
	 * Get a count of all groups available
	 */
	 
	public function fetchGroupCount( ) {
		
		$stmt = $this->db->prepare( "SELECT COUNT(*) as groupCount FROM " . DB_MAIN . ".groups" );
		$stmt->execute( );
		
		$row = $stmt->fetch( PDO::FETCH_OBJ );
		
		return $row->groupCount;
		
	}
	
	/**
	 * Get a count of all groups available
	 */
	 
	public function fetchGroups( ) {
		
		$stmt = $this->db->prepare( "SELECT * FROM " . DB_MAIN . ".groups WHERE group_status='active' ORDER BY group_name ASC" );
		$stmt->execute( );
		
		$groups = array( );
		while( $row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
			$groups[$row->group_id] = $row;
		}
		
		return $groups;
		
	}
	
	/**
	 * Change a group from active to inactive or vice versa
	 */
	 
	public function changeGroupStatus( $groupID, $status ) {
		$stmt = $this->db->prepare( "UPDATE " . DB_MAIN . ".groups SET group_status=? WHERE group_id=?" );
		$stmt->execute( array( $status, $groupID ) );
		return true;
	}
	
}

?>