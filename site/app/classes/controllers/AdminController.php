<?php


namespace BioLIMS\app\classes\controllers;

/**
 * Admin Controller
 * This controller handles the processing of several different admin tools and options.
 */
 
use BioLIMS\app\lib;
use BioLIMS\app\classes\models;

class AdminController extends lib\Controller {
	
	public function __construct( $twig ) {
		parent::__construct( $twig );
		
		$addonJS = array( );
		
		$addonCSS = array( );
		
		$this->headerParams->set( 'ADDON_CSS', $addonCSS );
		$this->footerParams->set( 'ADDON_JS', $addonJS );
	}
	
	/**
	 * Index
	 * Default layout for the main admin page, called when no other actions
	 * are requested via the URL.
	 */
	
	public function Index( ) {
		
		lib\Session::canAccess( lib\Session::getPermission( 'VIEW ADMIN TOOLS' ));
				
		$params = array(
			"WEB_URL" => WEB_URL,
			"IMG_URL" => IMG_URL
		);
		
		$this->headerParams->set( "CANONICAL", "<link rel='canonical' href='" . WEB_URL . "/Admin' />" );
		$this->headerParams->set( "TITLE", "Admin Tools" );
		
		$this->renderView( "admin" . DS . "AdminIndex.tpl", $params, false );
				
	}
	
	/**
	 * Change Password
	 * A tool for changing your own password but also the changing of anyone's password
	 * when the user possesses the correct permissions
	 */
	
	public function ChangePassword( ) {
		
		lib\Session::canAccess( lib\Session::getPermission( 'CHANGE PASSWORD' ));
		
		// Add some Change Password Specific JS
		$addonJS = $this->footerParams->get( 'ADDON_JS' );
		$addonJS[] = "formValidation/formValidation.min.js";
		$addonJS[] = "formValidation/bootstrap.min.js";
		$addonJS[] = "admin/admin-changePassword.js";
		
		// Add some Change Password Specific CSS
		$addonCSS = $this->headerParams->get( 'ADDON_CSS' );
		$addonCSS[] = "formValidation/formValidation.min.css";
		
		$this->headerParams->set( 'ADDON_CSS', $addonCSS );
		$this->footerParams->set( 'ADDON_JS', $addonJS );
		
		$userList = array( );
		if( lib\Session::validateCredentials( lib\Session::getPermission( 'CHANGE PASSWORD ALL' ))) {
			$userHandler = new models\UserHandler( );
			$userList = $userHandler->buildUserList( );
		}
				
		$params = array(
			"WEB_URL" => WEB_URL,
			"IMG_URL" => IMG_URL,
			"USER_LIST" => $userList
		);
		
		$this->headerParams->set( "CANONICAL", "<link rel='canonical' href='" . WEB_URL . "/Admin/ChangePassword' />" );
		$this->headerParams->set( "TITLE", "Change Password" );
		
		$this->renderView( "admin" . DS . "AdminChangePassword.tpl", $params, false );
				
	}
	
	/**
	 * Manage Users
	 * A tool for changing permissions and status levels of different users
	 * of the system.
	 */
	
	public function ManageUsers( ) {
		
		lib\Session::canAccess( lib\Session::getPermission( 'MANAGE USERS' ));
		
		// Add some Manage Users Specific JS
		$addonJS = $this->footerParams->get( 'ADDON_JS' );
		$addonJS[] = "jquery.qtip.min.js";
		$addonJS[] = "jquery.dataTables.js";
		$addonJS[] = "dataTables.bootstrap.js";
		$addonJS[] = "alertify.min.js";
		$addonJS[] = "blocks/biolims-dataTableBlock.js";
		$addonJS[] = "admin/admin-manageUsers.js";

		// Add some Manage Users Specific CSS
		$addonCSS = $this->headerParams->get( 'ADDON_CSS' );
		$addonCSS[] = "jquery.qtip.min.css";
		$addonCSS[] = "dataTables.bootstrap.css";
		$addonCSS[] = "alertify.min.css";
		$addonCSS[] = "alertify-bootstrap.min.css";
		
		$this->headerParams->set( 'ADDON_CSS', $addonCSS );
		$this->footerParams->set( 'ADDON_JS', $addonJS );
		
		$userHandler = new models\UserHandler( );
		$userCount = $userHandler->fetchUserCount( );
				
		$params = array(
			"WEB_URL" => WEB_URL,
			"IMG_URL" => IMG_URL,
			"TABLE_TITLE" => "Current Users",
			"ROW_COUNT" => $userCount
		);
		
		$this->headerParams->set( "CANONICAL", "<link rel='canonical' href='" . WEB_URL . "/Admin/ManagerUsers' />" );
		$this->headerParams->set( "TITLE", "Manage Users" );
		
		$this->renderView( "admin" . DS . "AdminManageUsers.tpl", $params, false );
				
	}
	
	/**
	 * Add User
	 * A tool for adding a new user to the system that can
	 * then login to the site successfully
	 */
	
	public function AddUser( ) {
		
		lib\Session::canAccess( lib\Session::getPermission( 'ADD USER' ));
		
		// Add some Change Password Specific JS
		$addonJS = $this->footerParams->get( 'ADDON_JS' );
		$addonJS[] = "formValidation/formValidation.min.js";
		$addonJS[] = "formValidation/bootstrap.min.js";
		$addonJS[] = "admin/admin-addUser.js";
		
		// Add some Change Password Specific CSS
		$addonCSS = $this->headerParams->get( 'ADDON_CSS' );
		$addonCSS[] = "formValidation/formValidation.min.css";
		
		$this->headerParams->set( 'ADDON_CSS', $addonCSS );
		$this->footerParams->set( 'ADDON_JS', $addonJS );
				
		$userHandler = new models\UserHandler( );
		$userClasses = $userHandler->fetchUserClasses( );
				
		$params = array(
			"WEB_URL" => WEB_URL,
			"IMG_URL" => IMG_URL,
			"USER_CLASSES" => $userClasses
		);
		
		$this->headerParams->set( "CANONICAL", "<link rel='canonical' href='" . WEB_URL . "/Admin/AddUser' />" );
		$this->headerParams->set( "TITLE", "Add User" );
		
		$this->renderView( "admin" . DS . "AdminAddUser.tpl", $params, false );
				
	}
	
	/**
	 * Manage Groups
	 * A tool for adding and viewing groups for
	 * permission settings 
	 */
	
	public function ManageGroups( ) {
		
		lib\Session::canAccess( lib\Session::getPermission( 'MANAGE GROUPS' ));
		
		// Add some Manage Permissions Specific JS
		$addonJS = $this->footerParams->get( 'ADDON_JS' );
		$addonJS[] = "jquery.qtip.min.js";
		$addonJS[] = "formValidation/formValidation.min.js";
		$addonJS[] = "formValidation/bootstrap.min.js";
		$addonJS[] = "jquery.dataTables.js";
		$addonJS[] = "dataTables.bootstrap.js";
		$addonJS[] = "alertify.min.js";
		$addonJS[] = "blocks/biolims-dataTableBlock.js";
		$addonJS[] = "admin/admin-manageGroups.js";
		
		// Add some Manager Permissions Specific CSS
		$addonCSS = $this->headerParams->get( 'ADDON_CSS' );
		$addonCSS[] = "jquery.qtip.min.css";
		$addonCSS[] = "formValidation/formValidation.min.css";
		$addonCSS[] = "dataTables.bootstrap.css";
		$addonCSS[] = "alertify.min.css";
		$addonCSS[] = "alertify-bootstrap.min.css";
		
		$this->headerParams->set( 'ADDON_CSS', $addonCSS );
		$this->footerParams->set( 'ADDON_JS', $addonJS );
		
		$userHandler = new models\UserHandler( );
		$userList = $userHandler->buildUserList( );
		
		$groupHandler = new models\GroupHandler( );
		$groupCount = $groupHandler->fetchGroupCount( );
				
		$params = array(
			"WEB_URL" => WEB_URL,
			"IMG_URL" => IMG_URL,
			"TABLE_TITLE" => "Current Groups",
			"ROW_COUNT" => $groupCount,
			"USERS" => $userList
		);
		
		$this->headerParams->set( "CANONICAL", "<link rel='canonical' href='" . WEB_URL . "/Admin/ManageGroups' />" );
		$this->headerParams->set( "TITLE", "Manage Groups" );
		
		$this->renderView( "admin" . DS . "AdminManageGroups.tpl", $params, false );
				
	}
	
	/**
	 * Edit Group
	 * A tool for editing groups for
	 * permission settings 
	 */
	
	public function EditGroup( ) {
		
		lib\Session::canAccess( lib\Session::getPermission( 'MANAGE GROUPS' ));
		
		// If we're not passed a numeric values and a set of group ids, show 404
		if( !isset( $_GET['groupID'] ) || !is_numeric( $_GET['groupID'] )) {
			lib\Session::sendPageNotFound( );
		}
		
		$groupHandler = new models\GroupHandler( );
		$groupInfo = $groupHandler->fetchGroup( $_GET['groupID'] );
		
		// Group Doesn't Exist
		if( !$groupInfo ) {
			lib\Session::sendPageNotFound( );
		}
		
		// Add some Manage Permissions Specific JS
		$addonJS = $this->footerParams->get( 'ADDON_JS' );
		$addonJS[] = "formValidation/formValidation.min.js";
		$addonJS[] = "formValidation/bootstrap.min.js";
		$addonJS[] = "alertify.min.js";
		$addonJS[] = "admin/admin-manageGroups.js";
		
		// Add some Manager Permissions Specific CSS
		$addonCSS = $this->headerParams->get( 'ADDON_CSS' );
		$addonCSS[] = "formValidation/formValidation.min.css";
		$addonCSS[] = "alertify.min.css";
		$addonCSS[] = "alertify-bootstrap.min.css";
		
		$this->headerParams->set( 'ADDON_CSS', $addonCSS );
		$this->footerParams->set( 'ADDON_JS', $addonJS );
		
		$userHandler = new models\UserHandler( );
		$userList = $userHandler->buildUserList( );
		
		$groupUsers = $groupHandler->fetchGroupUsers( $_GET['groupID'] );
				
		$params = array(
			"WEB_URL" => WEB_URL,
			"IMG_URL" => IMG_URL,
			"GROUP_ID" => $groupInfo->group_id,
			"GROUP_NAME" => $groupInfo->group_name,
			"GROUP_DESC" => $groupInfo->group_desc,
			"SELECTED_USERS" => $groupUsers,
			"USERS" => $userList
		);
		
		$this->headerParams->set( "CANONICAL", "<link rel='canonical' href='" . WEB_URL . "/Admin/EditGroup' />" );
		$this->headerParams->set( "TITLE", "Edit Group" );
		
		$this->renderView( "admin" . DS . "AdminEditGroup.tpl", $params, false );
				
	}
	
	/**
	 * Manage Permissions
	 * A tool for adding and managing permission values and the settings 
	 * each one is configured to
	 */

	 public function ManagePermissions( ) {
		
		lib\Session::canAccess( lib\Session::getPermission( 'MANAGE PERMISSIONS' ));
		
		// Add some Manage Permissions Specific JS
		$addonJS = $this->footerParams->get( 'ADDON_JS' );
		$addonJS[] = "formValidation/formValidation.min.js";
		$addonJS[] = "formValidation/bootstrap.min.js";
		$addonJS[] = "jquery.dataTables.js";
		$addonJS[] = "dataTables.bootstrap.js";
		$addonJS[] = "alertify.min.js";
		$addonJS[] = "blocks/biolims-dataTableBlock.js";
		$addonJS[] = "admin/admin-managePermissions.js";
		
		// Add some Manager Permissions Specific CSS
		$addonCSS = $this->headerParams->get( 'ADDON_CSS' );
		$addonCSS[] = "formValidation/formValidation.min.css";
		$addonCSS[] = "dataTables.bootstrap.css";
		$addonCSS[] = "alertify.min.css";
		$addonCSS[] = "alertify-bootstrap.min.css";
		
		$this->headerParams->set( 'ADDON_CSS', $addonCSS );
		$this->footerParams->set( 'ADDON_JS', $addonJS );
		
		$permHandler = new models\PermissionsHandler( );
		$permCount = $permHandler->fetchPermissionCount( );
		$permissionList=  $permHandler->getPermissionList( );
				
		$params = array(
			"WEB_URL" => WEB_URL,
			"IMG_URL" => IMG_URL,
			"TABLE_TITLE" => "Current Permissions",
			"ROW_COUNT" => $permCount,
			"PERMISSION_LIST" => $permissionList
		);
		
		$this->headerParams->set( "CANONICAL", "<link rel='canonical' href='" . WEB_URL . "/Admin/ManagePermissions' />" );
		$this->headerParams->set( "TITLE", "Manage Permissions" );
		
		$this->renderView( "admin" . DS . "AdminManagePermissions.tpl", $params, false );
				
	}
	 
}

?>