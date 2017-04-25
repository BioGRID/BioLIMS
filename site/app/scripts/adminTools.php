<?php

/**
 * Execute a process used in the handling of data files
 * and process the results
 */

session_start( );

require_once __DIR__ . '/../../app/lib/Bootstrap.php';

use BioLIMS\app\lib;
use BioLIMS\app\classes\models;

$postData = json_decode( $_POST['expData'], true );

if( isset( $postData['adminTool'] ) ) {	
	
	switch( $postData['adminTool'] ) {
		
		// Perform a change password operation
		// for a single user 
		case 'changePassword' :
			
			$results = array( );
			if( lib\Session::isLoggedIn( ) && isset( $postData['newPassword'] ) && isset( $postData['currentPassword'] )) {
				
				$userID = $_SESSION[SESSION_NAME]['ID'];
				$userName = $_SESSION[SESSION_NAME]['NAME'];
				
				// If changing for the user making the request
				// verify their existing password first, before
				// performing change
				$user = new lib\User( );
				if( $user->verifyPassword( $userName, $postData['currentPassword'] )) {
					$user->changePassword( $userID, $postData['newPassword'] );
					$results = array( "STATUS" => "success", "MESSAGE" => "Password Successfully Changed!" );
					lib\Session::logout( );
				} else {
					$results = array( "STATUS" => "error", "MESSAGE" => "The password you entered for 'current password' does not match your current password..." );
				}
				
			} else if( lib\Session::validateCredentials( lib\Session::getPermission( 'CHANGE PASSWORD ALL' )) && isset( $postData['newPassword'] ) && isset( $postData['userID'] )) {
				
				// If permission level is high enough, this tool allows
				// for changing of anyones password. Does not require
				// original password verification
				$user = new lib\User( );
				$user->changePassword( $postData['userID'], $postData['newPassword'] );
				$results = array( "STATUS" => "success", "MESSAGE" => "Password Successfully Changed!" );
				
				if( $postData['userID'] == $_SESSION[SESSION_NAME]['ID'] ) {
					lib\Session::logout( );
				}
				
			} else {
				$results = array( "STATUS" => "error", "MESSAGE" => "Unable to change password at this time, please try again later!" );
			}
			
			echo json_encode( $results );
			break;
		
		// Change the User Class Up or Down
		// for promoting/demoting users
		case 'userClassChange' :
		
			$results = array( );
			if( lib\Session::validateCredentials( lib\Session::getPermission( 'MANAGE USERS' )) && isset( $postData['userID'] ) && isset( $postData['direction'] )) {
				$user = new lib\User( );
				$newClass = $user->changeUserLevel( $postData['userID'], $postData['direction'] );
				if( $newClass ) {
					$results = array( "STATUS" => "SUCCESS", "MESSAGE" => "Successfully Changed User Class", "NEWVAL" => $newClass );
				} else {
					$results = array( "STATUS" => "ERROR", "MESSAGE" => "You do not have High Enough Permissions to Perform this Action" );
				}
			} else {
				$results = array( "STATUS" => "ERROR", "MESSAGE" => "You do not have Valid Permission to Perform this Action" );
			}
			
			echo json_encode( $results );
			break;
			
		// Change the User Status between active
		// and inactive based on the current setting
		case 'userStatusChange' :
		
			$results = array( );
			if( lib\Session::validateCredentials( lib\Session::getPermission( 'MANAGE USERS' )) && isset( $postData['userID'] ) && isset( $postData['status'] )) {
				$user = new lib\User( );
				$newStatus = $user->changeUserStatus( $postData['userID'], $postData['status'] );
				if( $newStatus ) {
					$results = array( "STATUS" => "SUCCESS", "MESSAGE" => "Successfully Changed User Status", "NEWVAL" => $newStatus );
				} else {
					$results = array( "STATUS" => "ERROR", "MESSAGE" => "You do not have High Enough Permissions to Perform this Action" );
				}
			} else {
				$results = array( "STATUS" => "ERROR", "MESSAGE" => "You do not have Valid Permission to Perform this Action" );
			}
			
			echo json_encode( $results );
			break;
			
		// Change the User Status between active
		// and inactive based on the current setting
		case 'addNewUser' :
		
			$results = array( );
			if( lib\Session::validateCredentials( lib\Session::getPermission( 'ADD USER' )) && isset( $postData['userName'] ) && isset( $postData['userPassword'] ) && isset( $postData['userFirstName'] ) && isset( $postData['userLastName'] ) && isset( $postData['userEmail'] ) && isset( $postData['userClass'] )) {
				$user = new lib\User( );
				
				if( !$user->usernameExists( $postData['userName'] ) && !$user->emailExists( $postData['userEmail'] )) {
					$user->addUser( $postData['userName'], $postData['userPassword'], $postData['userFirstName'], $postData['userLastName'], $postData['userEmail'], $postData['userClass'] );
					$results = array( "STATUS" => "SUCCESS", "MESSAGE" => "Successfully Added New User" );
				} else {
					$results = array( "STATUS" => "ERROR", "MESSAGE" => "The Username or Email you Entered Already Belong to an Existing User" );
				}
				
			} else {
				$results = array( "STATUS" => "ERROR", "MESSAGE" => "Unable to add a new user at this time, please try again later!" );
			}
			
			echo json_encode( $results );
			break;
			
		// Change a permission level for a given permission
		// setting option
		case 'permissionLevelChange' :
		
			$results = array( );
			if( lib\Session::validateCredentials( lib\Session::getPermission( 'MANAGE PERMISSIONS' )) && isset( $postData['permission'] ) && isset( $postData['level'] )) {
				$permHandler = new models\PermissionsHandler( );
				$newPerm = $permHandler->changePermissionLevel( $postData['permission'], $postData['level'] );
				if( $newPerm ) {
					$results = array( "STATUS" => "SUCCESS", "MESSAGE" => "Successfully Changed Permission Level" );
				} else {
					$results = array( "STATUS" => "ERROR", "MESSAGE" => "You do not have High Enough Permissions to Perform this Action" );
				}
			} else {
				$results = array( "STATUS" => "ERROR", "MESSAGE" => "You do not have Valid Permission to Perform this Action" );
			}
			
			echo json_encode( $results );
			break;
			
		// Add a new permission to the permissions table
		case 'addPermission' :
			$results = array( );
			if( lib\Session::validateCredentials( lib\Session::getPermission( 'MANAGE PERMISSIONS' )) && isset( $postData['permissionName'] ) && isset( $postData['permissionDesc'] ) && isset( $postData['permissionLevel'] ) && isset( $postData['permissionCategory'] )) {
				$permHandler = new models\PermissionsHandler( );
				
				if( $permHandler->addPermission( $postData['permissionName'], $postData['permissionDesc'], $postData['permissionLevel'], $postData['permissionCategory'] )) {
					$results = array( "STATUS" => "SUCCESS", "MESSAGE" => "Successfully Added New Permission" );
				} else {
					$results = array( "STATUS" => "ERROR", "MESSAGE" => "The Permission Name/Category Name you Entered Already Exists" );
				}
				
			} else {
				$results = array( "STATUS" => "ERROR", "MESSAGE" => "Unable to add a new permission at this time, please try again later!" );
			}
			
			echo json_encode( $results );
			break;
			
		// Add a new group to the groups table
		case 'addGroup' :
			$results = array( );
			if( lib\Session::validateCredentials( lib\Session::getPermission( 'MANAGE GROUPS' )) && isset( $postData['groupName'] ) && isset( $postData['groupDesc'] ) &&isset( $postData['groupMembers'] )) {
				$groupHandler = new models\GroupHandler( );
				
				if( !isset( $postData['groupID'] )) {
					
					if( $groupHandler->addGroup( $postData['groupName'], $postData['groupDesc'], $postData['groupMembers'] )) {
						$results = array( "STATUS" => "SUCCESS", "MESSAGE" => "Successfully Added New Group" );
					} else {
						$results = array( "STATUS" => "ERROR", "MESSAGE" => "The Group Name you Entered Already Exists" );
					}
					
				} else {
					
					if( $groupHandler->editGroup( $postData['groupID'], $postData['groupName'], $postData['groupDesc'], $postData['groupMembers'] )) {
						$results = array( "STATUS" => "REDIRECT", "MESSAGE" => WEB_URL . "/Admin/ManageGroups" );
					} else {
						$results = array( "STATUS" => "ERROR", "MESSAGE" => "The Group Name you Entered Already Exists" );
					}
					
				}
				
			} else {
				$results = array( "STATUS" => "ERROR", "MESSAGE" => "Unable to add a new group at this time, please try again later!" );
			}
			
			echo json_encode( $results );
			break;
			
		// Change the Group Status between active
		// and inactive based on the current setting
		case 'groupDelete' :
		
			$results = array( );
			if( lib\Session::validateCredentials( lib\Session::getPermission( 'MANAGE GROUPS' )) && isset( $postData['groupID'] )) {
				$groupHandler = new models\GroupHandler( );
				if( $groupHandler->changeGroupStatus( $postData['groupID'], 'inactive' )) {
					$results = array( "STATUS" => "SUCCESS", "MESSAGE" => "Successfully Removed Group" );
				} else {
					$results = array( "STATUS" => "ERROR", "MESSAGE" => "You do not have High Enough Permissions to Perform this Action" );
				}
			} else {
				$results = array( "STATUS" => "ERROR", "MESSAGE" => "You do not have Valid Permission to Perform this Action" );
			}
			
			echo json_encode( $results );
			break;
	}
}

exit( );
 
?>