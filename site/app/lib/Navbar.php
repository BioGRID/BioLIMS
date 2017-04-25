<?php

namespace BioLIMS\app\lib;

/**
 * NAVBAR
 * This class contains a static array that can be customized to modify
 * the site Navbar.
 *
 * - Status can be "public", "observer", "curator", "poweruser", "admin"
 * - public - Seen by all, even non-logged in users
 * - observer - Lowest Class, sees Observer and Public links
 * - curator - General Class, sees Curator, Observer, and Public Links
 * - poweruser - Slightly Elevated User, sees PowerUser, Curator, Observer, and Public Links
 * - admin - Highest User Class, sees All Links
 */
 
use BioLIMS\app\lib;
 
class Navbar {

	public static $leftNav;
	public static $rightNav;
	
	public static function init( ) {
			
		self::$leftNav = array( );
		self::$rightNav = array( );
		
		// LEFT SIDE OF NAVBAR
		self::$leftNav['Home'] = array( "URL" => WEB_URL, "TITLE" => 'Return to Homepage', "STATUS" => 'public' );
		
		// RIGHT SIDE OF NAVBAR
		self::$rightNav['Admin'] = array( "URL" => "#", "TITLE" => 'Administration Utilities', "STATUS" => 'observer', "DROPDOWN" => array( ) );
		self::$rightNav['Admin']['DROPDOWN']['Add User'] = array( "URL" => WEB_URL . "/Admin/AddUser", "TITLE" => 'Add New User', "STATUS" => 'poweruser'  );
		self::$rightNav['Admin']['DROPDOWN']['Change Password'] = array( "URL" => WEB_URL . "/Admin/ChangePassword", "TITLE" => 'Change Password', "STATUS" => 'observer'  );
		self::$rightNav['Admin']['DROPDOWN']['Manage Users'] = array( "URL" => WEB_URL . "/Admin/ManageUsers", "TITLE" => 'Manage Users and Status', "STATUS" => 'poweruser' );
		self::$rightNav['Admin']['DROPDOWN']['Manage Permissions'] = array( "URL" => WEB_URL . "/Admin/ManagePermissions", "TITLE" => 'Manage Page Access Permissions', "STATUS" => 'admin' );
		self::$rightNav['Admin']['DROPDOWN']['Manage Groups'] = array( "URL" => WEB_URL . "/Admin/ManageGroups", "TITLE" => 'Manage Groups', "STATUS" => 'poweruser' );
		self::$rightNav['Logout'] = array( "URL" => WEB_URL . "/Home/Logout", "TITLE" => 'Logout from Site', "STATUS" => 'observer' );
	
	}
	
}