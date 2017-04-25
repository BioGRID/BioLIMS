<?php


namespace BioLIMS\app\classes\controllers;

/**
 * Home Controller
 * This controller handles the processing of the main homepage.
 */
 
use BioLIMS\app\lib;
use BioLIMS\app\classes\models;

class HomeController extends lib\Controller {
	
	private $RECENT_TO_SHOW = 5;
	
	public function __construct( $twig ) {
		parent::__construct( $twig );
		
		$addonJS = array( );
		$addonCSS = array( );
		
		$this->headerParams->set( 'ADDON_CSS', $addonCSS );
		$this->footerParams->set( 'ADDON_JS', $addonJS );
		
		$this->headerParams->set( "CANONICAL", "<link rel='canonical' href='" . WEB_URL . "' />" );
		$this->headerParams->set( "TITLE", CONFIG['WEB']['WEB_NAME'] );
	}
	
	/**
	 * Index
	 * Default layout for the main homepage of the site, called when no other actions
	 * are requested via the URL.
	 */
	
	public function Index( ) {
		$this->Member( );
	}
	
	/**
	 * Member
	 * Default Layout for a Logged in Member of the Site
	 */
	 
	 public function Member( ) {
		 
		lib\Session::canAccess( lib\Session::getPermission( 'VIEW DASHBOARD' ));
		
		// Add some JS
		$addonJS = $this->footerParams->get( 'ADDON_JS' );
		$addonJS[] = "jquery.qtip.min.js";
		
		// Add some CSS
		$addonCSS = $this->headerParams->get( 'ADDON_CSS' );
		$addonCSS[] = "jquery.qtip.min.css";
		
		// Set modified Params
		$this->headerParams->set( 'ADDON_CSS', $addonCSS );
		$this->footerParams->set( 'ADDON_JS', $addonJS );
		
		// Figure out what valid admin tools this person has access to...
		$adminTools = array( );
		if( lib\Session::validateCredentials( lib\Session::getPermission( 'VIEW ADMIN TOOLS' )) ) {
			if( lib\Session::validateCredentials( lib\Session::getPermission( 'CHANGE PASSWORD ALL' )) ) {
				$adminTools["Change User Passwords (including your own)"] = WEB_URL . "/Admin/ChangePassword";
			} 
			
			if( lib\Session::validateCredentials( lib\Session::getPermission( 'CHANGE PASSWORD' )) ) { 
				$adminTools["Change Your Password"] = WEB_URL . "/Admin/ChangePassword";
			}
			
			if( lib\Session::validateCredentials( lib\Session::getPermission( 'ADD USER' )) ) {
				$adminTools["Add a New User"] = WEB_URL . "/Admin/AddUser";
			}
			
			if( lib\Session::validateCredentials( lib\Session::getPermission( 'MANAGE USERS' )) ) {
				$adminTools["Manage Existing User Permissions"] = WEB_URL . "/Admin/ManageUsers";
			}
			
			if( lib\Session::validateCredentials( lib\Session::getPermission( 'MANAGE USERS' )) ) {
				$adminTools["Manage Existing User Permissions"] = WEB_URL . "/Admin/ManageUsers";
			}
			
			if( lib\Session::validateCredentials( lib\Session::getPermission( 'MANAGE PERMISSIONS' )) ) {
				$adminTools["Manage Site Permissions"] = WEB_URL . "/Admin/ManagePermissions";
			}
		}
		
		// Check to see if password needs to be reset
		$alertMsg = "";
		if( $_SESSION[SESSION_NAME]['RESET_PASS'] == "1" ) {
			$alertMsg = "<i class='fa fa-warning fa-lg dangerIcon'></i> You must change your password immediately. Click <a href='" . WEB_URL . "/Admin/ChangePassword' title='Change Your Password'>here</a> to do so...";
		}
		
		$params = array( 
			"WEB_NAME" => CONFIG['WEB']['WEB_NAME'],
			"WEB_NAME_ABBR" => CONFIG['WEB']['WEB_NAME_ABBR'],
			"WEB_DESC" => CONFIG['WEB']['WEB_DESC'],
			"WEB_URL" => WEB_URL,
			"VERSION" => CONFIG['WEB']['VERSION'],
			"FIRSTNAME" => $_SESSION[SESSION_NAME]['FIRSTNAME'],
			"LASTNAME" => $_SESSION[SESSION_NAME]['LASTNAME'],
			"IMG_URL" => IMG_URL,
			"ADMIN_TOOLS" => $adminTools,
			"ALERT_MSG" => $alertMsg
		);
		
		$this->renderView( "home" . DS . "HomeIndex.tpl", $params, false );
			
	}
	
    /**
	 * Login
	 * Layout for the Login page for the site, called when a user
	 * does not have adequate permissions to view the standard news page.
	 */
	 
	 public function Login( ) { 
		 
		if( lib\Session::isLoggedIn( ) ) {
			header( 'Location: ' . WEB_URL . "/" );
		} else {
		 
			$params = array(
				"WEB_NAME_ABBR" => CONFIG['WEB']['WEB_NAME_ABBR'],
				"SHOW_ERROR" => "hidden",
				"WEB_URL" => WEB_URL,
				"IMG_URL" => IMG_URL
			);
			
			// Check to see if User is attempting to
			// Login to the site
			
			if( isset( $_POST['username'] ) ) {
				
				$user = new lib\User( );
				
				if( $user->validateByLogin( $_POST['username'], $_POST['password'], $_POST['remember'] ) ) {
					header( 'Location: ' . WEB_URL . '/' );
				} else {
					$params['SHOW_ERROR'] = '';
					$params['ERROR'] = 'Your Login Credentials are Invalid. Please try again!';
				}
				
				$params["USERNAME"] = $_POST['username'];
			}

			$this->renderView( "home" . DS . "HomeLogin.tpl", $params, false );
			
		}
	 }
	 
	/**
	 * Logout
	 * Logout of the site by invalidating the session
	 * and removing cookies.
	*/
	  
	public function Logout( ) {
		lib\Session::logout( );
		header( 'Location: ' . WEB_URL . '/' );
	}	

}

?>