<?php 

namespace BioLIMS\app\lib;

/**
 * User
 * This class handles authentication and validation of user login credentials
 * and setting up of session variables.
 */
 
use \PDO;
use BioLIMS\app\lib\Session;
	
class User {
	
	private $db;
	private $MAX_RAND = 1000000;
	
	/**
	 * Initialize DB Connection
	 * Connection Variables are in inc/Config.php
	 */
	 
	function __construct( ) {
		$this->db = new PDO( DB_CONNECT, DB_USER, DB_PASS );
		$this->db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	}
	
	/**
	 * Attempt to validate a user and if it fails
	 * see if cookie is available. In case of either being a success
	 * setup the user session details. Otherwise, do nothing but leave
	 * it blank.
	 */
	
	public function validate( ) {
		
		if( isset( $_SESSION[SESSION_NAME] ) ) {
			
			$userData = $this->fetchUserDetails( $_SESSION[SESSION_NAME]['ID'], true );
			
			// If user was deactivated since last visit to the site
			// forcefully log them out, and invalidate their session
			
			if( $userData['STATUS'] == 'inactive' ) {
				Session::logout( );
				header( 'Location: ' . WEB_NAME . '/' );
			}
			
			// Otherwise, setup the fetched userData as the
			// IMS_USER session
			
			$_SESSION[SESSION_NAME] = $userData;
			return true;
		
		} else if( isset( $_COOKIE[COOKIE_NAME] ) ) {
			
			// Test to see if a COOKIE has been set allowing for a persistant
			// Login experience on repeat visits after closing the browser
			
			$cookie = json_decode( $_COOKIE[COOKIE_NAME], true );
			
			if( $cookie != NULL ) {
				if( $this->validateByCookie( $cookie['UUID'], $cookie['ID'] ) ) {
					return true;
				}
			}
			
		}
		
		Session::logout( );
		return false;
		
	}
	
	/**
	 * Take passed in Login credentials and validate that it is
	 * a legitimate login.
	 */
	 
	public function validateByLogin( $username, $password, $isPersistent ) {
		
		if( $userID = $this->verifyPassword( $username, $password ) ) {
			
			$_SESSION[SESSION_NAME] = $this->fetchUserDetails( $userID );
			$this->updatePersistent( $userID, $isPersistent );
			return true;
			
		} 
		
		return false;
	}
	
	/** 
	 * Update the random and uuid fields of the user table
	 * to check for cookie based logins
	 */
	 
	private function updatePersistent( $userID, $setCookie = false ) {
		
		$cookie = array( 
			"UUID" => "-",
			"ID" => 0
		);
		
		if( $setCookie ) {
			
			$cookie['UUID'] = $this->generateUUID( $userID );
			$cookie['ID'] = rand( 1, $this->MAX_RAND );
		
			$cookieJson = json_encode( $cookie );
			setcookie( COOKIE_NAME, $cookieJson, time( ) + PERSISTENT_TIMEOUT, "/" );
			
		} 
			
		$stmt = $this->db->prepare( "UPDATE " . DB_MAIN . ".users SET user_uuid=?, user_random=?, user_lastlogin=NOW( ) WHERE user_id=?" );
		$stmt->execute( array( $cookie['UUID'], $cookie['ID'], $userID ) );
		
	}
	
	/**
	 * Validate that a user is logged in via COOKIE
	 * rather than by username/password combo
	 */
	 
	public function validateByCookie( $uuid, $random ) {
		
		if( $userID = $this->verifyPersistent( $uuid, $random ) ) {
			
			$_SESSION[SESSION_NAME] = $this->fetchUserDetails( $userID, false );
			$this->updatePersistent( $userID, true );
			return true;
			
		} 
		
		return false;
	}
	
	/**
	 * Validate that the uuid and random are active in combination
	 * within the user table, if not, return false
	 */
	 
	private function verifyPersistent( $uuid, $random ) {
	
		$stmt = $this->db->prepare( "SELECT user_id FROM " . DB_MAIN . ".users WHERE user_uuid=? AND user_random=? AND user_status='active'" );
		$stmt->execute( array( $uuid, $random ) );
		
		if( $stmt->rowCount( ) <= 0 ) {
			return false;
		} 
		
		$row = $stmt->fetch( PDO::FETCH_OBJ );
		return $row->user_id;
	
	}
	
	/**
	 * Grab information about the user from the user id
	 * and return a formatted array containing it
	 */
	 
	public function fetchUserDetails( $userID ) {
		
		$stmt = $this->db->prepare( "SELECT user_id, user_name, user_firstname, user_lastname, user_email, user_class, user_status, user_lastgroup, user_passwordreset FROM " . DB_MAIN . ".users WHERE user_id=? LIMIT 1" );
		$stmt->execute( array( $userID ) );
		
		$row = $stmt->fetch( PDO::FETCH_OBJ );
		
		$userInfo = array( 
			"ID" => $row->user_id, 
			"NAME" => $row->user_name, 
			"FIRSTNAME" => $row->user_firstname, 
			"LASTNAME" => $row->user_lastname, 
			"EMAIL" => $row->user_email, 
			"CLASS" => $row->user_class, 
			"STATUS" => $row->user_status,
			"RESET_PASS" => $row->user_passwordreset,
			"GROUPS" => $this->fetchUserGroups( $row->user_id )
		);
		
		return $userInfo;
		
	}
	
	/**
	 * Grab all of the groups the user has access to so we
	 * can display them as selectable within the site.
	 */
	 
	private function fetchUserGroups( $userID ) {
	 
		$stmt = $this->db->prepare( "SELECT group_id, group_name FROM " . DB_MAIN . ".groups WHERE group_status='active' AND group_id IN ( SELECT group_id FROM " . DB_MAIN . ".group_users WHERE group_user_status='active' AND user_id=?)" );
		$stmt->execute( array( $userID ) );

		$groups = array( );
		while( $row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
			$group = array( "ID" => $row->group_id, "NAME" => $row->group_name ); 
			$groups[$group["ID"]] = $group;
		}

		ksort( $groups );
		return $groups;
	 
	}
	
	/**
	 * Validate the password and return the USER ID if it
	 * is successful
	 */
	 
	public function verifyPassword( $username, $password ) {
		
		$stmt = $this->db->prepare( "SELECT user_id, user_password FROM " . DB_MAIN . ".users WHERE user_name=? AND user_status='active'" );
		$stmt->execute( array( $username ) );
		
		if( $stmt->rowCount( ) <= 0 ) {
			return false;
		}
		
		$row = $stmt->fetch( PDO::FETCH_OBJ );
		if( password_verify( $password, $row->user_password ) ) {
			return $row->user_id;
		} 
		
		return false;
	}
	
	/**
	 * Generate a unique ID and set it to the database
	 * so it can be referenced later
	 */
	 
	private function generateUUID( $userID ) {
		
		$salt = mcrypt_create_iv( 32, MCRYPT_DEV_URANDOM );
		$uuid = hash( "sha256", rand( 1, 500550212 ) . $salt );
		
		return $uuid;
		
	}
	
	/**
	 * Deactivate a user preventing them from being able to login
	 */
	 
	public function delUser( $userID ) {
		
		$stmt = $this->db->prepare( "UPDATE " . DB_MAIN . ".users SET user_status='inactive', user_uuid='-', user_random='0' WHERE user_id=?" );
		$stmt->execute( array( $userID ) );
		$stmt->close( );
		
	}
	
	/**
	 * Creates a password using our current password creation methods
	 */
	 
	private function generatePasswordHash( $password ) {
		$hash = password_hash( $password, PASSWORD_BCRYPT, array( "cost" => 12 ) );
		return $hash;
	}
	
	/**
	 * Alter the login password for an existing user
	 * and then invalidate their cookie credentials
	 */
	 
	public function changePassword( $userID, $password ) {
		
		$passwordHash = $this->generatePasswordHash( $password );
		$stmt = $this->db->prepare( "UPDATE " . DB_MAIN . ".users SET user_password=?, user_uuid='-', user_random='0', user_passwordreset='0' WHERE user_id=?" );
		
		if( $userID == $_SESSION[SESSION_NAME]['ID'] ) {
			$_SESSION[SESSION_NAME]['RESET_PASS'] = 0;
		}
		
		$stmt->execute( array( $passwordHash, $userID ) );
		
	}
	
	/** 
	 * Add a new user to the users table
	 */
	 
	public function addUser( $userName, $password, $firstname, $lastname, $email, $class ) {
		
		$stmt = $this->db->prepare( "SELECT user_id FROM " . DB_MAIN . ".users WHERE user_name=? OR user_email=?" );
		$stmt->execute( array( $userName, $email ) );
		
		if( $stmt->rowCount( ) > 0 ) {
			return false;
		}
		
		$passwordHash = $this->generatePasswordHash( $password );
		$stmt = $this->db->prepare( "INSERT INTO " . DB_MAIN . ".users VALUES( 0,?,?,?,?,?,'0','-','0000-00-00 00:00:00',?,'active','1', '1' )" );
		
		$stmt->execute( array( $userName, $passwordHash, $firstname, $lastname, $email, $class ) );
		
		return true;
		
	}
	
	/**
	 * Get a set of users that can be iterated over
	 */
	 
	public function fetchUsersList( $fetchInactive = false ) {
		
		$stmt = $this->db->prepare( "SELECT user_id, user_name, user_firstname, user_lastname, user_email, user_lastlogin, user_class, user_status FROM " . DB_MAIN . ".users WHERE user_id != '1' ORDER BY user_firstname ASC" );
		$stmt->execute( );

		$users = array( );
		while( $row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
			$users[$row->user_id] = array( "ID" => $row->user_id, "USERNAME" => $row->user_name, "FIRSTNAME" => $row->user_firstname, "LASTNAME" => $row->user_lastname, "EMAIL" => $row->user_email, "LASTLOGIN" => $row->user_lastlogin, "CLASS" => $row->user_class, "STATUS" => $row->user_status );
		}
		
		return $users;
		
	}
	
	/**
	 * Check to see if a username already exists in the database
	 */
	 
	public function usernameExists( $username ) {
		
		$stmt = $this->db->prepare( "SELECT user_id FROM " . DB_MAIN . ".users WHERE user_name=? LIMIT 1" );
		$stmt->execute( array( $username ) );
		
		if( $stmt->rowCount( ) <= 0 ) {
			return false;
		}
		
		return true;
		
	}
	
	/**
	 * Check to see if an email already exists in the database
	 */
	 
	public function emailExists( $email ) {
		
		$stmt = $this->db->prepare( "SELECT user_id FROM " . DB_MAIN . ".users WHERE user_email=? LIMIT 1" );
		$stmt->execute( array( $email ) );
		
		if( $stmt->rowCount( ) <= 0 ) {
			return false;
		}
		
		return true;
		
	}
	
	/**
	 * Change a users class by moving upward or downward
	 */
	 
	public function changeUserLevel( $userID, $levelDirection ) {
		
		$stmt = $this->db->prepare( "SELECT user_class FROM " . DB_MAIN . ".users WHERE user_id=? LIMIT 1" );
		$stmt->execute( array( $userID ) );
		
		if( $stmt->rowCount( ) <= 0 ) {
			return false;
		}
		
		$row = $stmt->fetch( PDO::FETCH_OBJ );
		$userClass = $row->user_class;
		
		$newClass = "observer";
		$testClass = $newClass;
		if( $levelDirection == "promote" ) {
			if( $userClass == "observer" ) {
				$newClass = "curator";
			} else if( $userClass == "curator" ) {
				$newClass = "poweruser";
			} else if( $userClass == "poweruser" ) {
				$newClass = "admin";
			} else if( $userClass == "admin" ) {
				$newClass = "admin";
			}
			
			$testClass = $newClass;
			
		} else {
			if( $userClass == "observer" ) {
				$newClass = "observer";
			} else if( $userClass == "curator" ) {
				$newClass = "observer";
			} else if( $userClass == "poweruser" ) {
				$newClass = "curator";
			} else if( $userClass == "admin" ) {
				$newClass = "poweruser";
			}
			
			$testClass = $userClass;
		}
		
		if( Session::validateCredentials( $testClass ) ) {
			$stmt = $this->db->prepare( "UPDATE " . DB_MAIN . ".users SET user_class=?, user_uuid='-', user_random='0' WHERE user_id=?" );
			$stmt->execute( array( $newClass, $userID ) );
			return $newClass;
		}
		
		return false;
		
	}
	
	/**
	 * Change a user from active to inactive or vice versa
	 */
	 
	public function changeUserStatus( $userID, $status ) {
		
		$stmt = $this->db->prepare( "SELECT user_class FROM " . DB_MAIN . ".users WHERE user_id=? LIMIT 1" );
		$stmt->execute( array( $userID ) );
		
		if( $stmt->rowCount( ) <= 0 ) {
			return false;
		}
		
		$row = $stmt->fetch( PDO::FETCH_OBJ );
		$userClass = $row->user_class;
		
		if( Session::validateCredentials( $userClass ) ) {
			$stmt = $this->db->prepare( "UPDATE " . DB_MAIN . ".users SET user_status=?, user_uuid='-', user_random='0' WHERE user_id=?" );
			$stmt->execute( array( $status, $userID ) );
			return $status;
		}
		
		return false;
		
	}
	
	function __destruct( ) {
		$this->db = null;
	}
	
}

?>