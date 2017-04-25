<?php 

namespace BioLIMS\app\lib;

/**
 * Session
 * This class handles ensurance that a user is logged in and 
 * has permissions to visit pages.
 */
 
use BioLIMS\app\lib\User;
	
class Session {
	
	/**
	 * Simple function to check and see if a person is currently logged
	 * in. Ideal for testing such status for correct routing on pages that
	 * may be open to the public.
	 */
	 
	public static function isLoggedIn( ) {
		if( isset( $_SESSION[SESSION_NAME] ) ) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Test credentials and redirect to invalid credentials page
	 * if user does not have access
	 */
	 
	public static function canAccess( $permissionLevel ) {
		
		// Users must be logged in to have access
		// and must also have valid credentials
		if( self::validateCredentials( $permissionLevel ) ) {
			return true;
		}
		
		if( self::isLoggedIn( ) ) {
			// Send them to a Permission Denied Page
			// if they are logged in, but permission was denied
			header( "Location: " . WEB_URL . "/Error/PermissionDenied" );
		} else {
			// Otherwise, send them to the login page
			header( "Location: " . WEB_URL . "/Home/Login" );
		}
		
	}
	
	/**
	 * Send a user to a Permission Denied Page
	 */
	 
	public static function sendPermissionDenied( ) {
		header( "Location: " . WEB_URL . "/Error/PermissionDenied" );
	}
	
	/**
	 * Send a user to a 404 Page
	 */
	 
	public static function sendPageNotFound( ) {
		header( "Location: " . WEB_URL . "/Error" );
	}
	
	/**
	 * Validate a users currect credentials and return correctly
	 * based on there current status.
	 */
	 
	public static function validateCredentials( $permissionLevel ) {
		
		if( !self::isLoggedIn( ) ) {
			$user = new User( );
			$user->validate( );
		}
		
		if( self::isLoggedIn( ) ) {
			return self::validatePermissions( $permissionLevel, $_SESSION[SESSION_NAME]["CLASS"] );
		}
		
		return false;
	}
	
	/**
	 * Test to see if a user permission level is okay for
	 * the user to view based on their own user level
	 */
	 
	private static function validatePermissions( $permissionLevel, $userPermission ) {
		
		if( $permissionLevel == "observer" && ($userPermission == "observer" || $userPermission == "curator" || $userPermission == "poweruser" || $userPermission == "admin") ) {
			return true;
		} else if( $permissionLevel == "curator" && ($userPermission == "curator" || $userPermission == "poweruser" || $userPermission == "admin") ) {
			return true;
		} else if( $permissionLevel == "poweruser" && ($userPermission == "poweruser" || $userPermission == "admin") ) {
			return true;
		} else if( $permissionLevel == "admin" && ($userPermission == "admin") ) {
			return true;
		}
		
		return false;
		
	}
	
	/**
	 * Update current group the user is accessing
	 * in both the session and then the database
	 * for their user ID
	 */
	 
	public static function updateGroup( $groupID ) {
		if( self::isLoggedIn( ) ) {
			
			if( $groupID != $_SESSION[SESSION_NAME]["GROUP"] ) {
				if( isset( $_SESSION[SESSION_NAME]["GROUPS"][$groupID] ) ) {
					$_SESSION[SESSION_NAME]["GROUP"] = $groupID;
					$user = new User( );
					$user->updateGroup( $groupID, $_SESSION[SESSION_NAME]["ID"] );
					return true;
				}
			}

		}
		
		return false;
	}
	
	/**
	 * Get Permission
	 * Fetch the permission value out of the permission array and
	 * if it doesn't exist, return a default value.
	 */
	 
	public static function getPermission( $permissionIndex ) {
		if( isset( PERMISSIONS[strtoupper($permissionIndex)] )) {
			return PERMISSIONS[strtoupper($permissionIndex)];
		}
		
		return "poweruser";
	}
	
	/**
	 * Invalidate the session and deprecate the cookie
	 * when the user logs out
	 */
	 
	public static function logout( ) {
	
		session_unset( );
		session_destroy( );
		
		setcookie( COOKIE_NAME, "", time( )-(60*60*24*10000), "/" );
		
	}
	
}