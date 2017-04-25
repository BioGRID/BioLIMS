<?php 

namespace BioLIMS\app\classes\utilities;

/**
 * NavbarGenerator
 * Creates a Bootstrap Compatible Navbar by loading the array stored in
 * app/lib/Navbar.php to create it.
 */
 
use BioLIMS\app\lib\Navbar;
use BioLIMS\app\lib\Session;

class NavbarBuilder {

	private $navbar;
	private $navbarFixed;
	private $navbarStyle;
	
	private $userLevel;
	private $isLoggedIn;

	function __construct( $isFixed, $style, $userLevel = "public", $isLoggedIn = false ) {
		$this->navbar = array( );
		$this->navbarFixed = $isFixed;
		$this->navbarStyle = $style;
		
		$this->userLevel = $userLevel;
		$this->isLoggedIn = $isLoggedIn;
		
		Navbar::init( );
	}
	
	/**
	* Returns a formatted navbar based on the passed in array
	* stored in the Navbar.php file.
	*/
	
	public function fetchNavbar( $isFluid = false ) {
		$this->generateNavbar( $isFluid );
		return $this->navbar;
	}
	
	/**
	* Construct the Navbar by stepping through the Navbar array
	* and building out the correct structure based on the users
	* permission level.
	*/
	
	private function generateNavbar( $isFluid = false ) {
	
		$leftNav = Navbar::$leftNav;
		$rightNav = Navbar::$rightNav;
		
		$currentURL = WEB_URL . $_SERVER['REQUEST_URI'];
	
		if( $this->navbarFixed ) {
			$this->navbar[] = '<div class="navbar ' . $this->navbarStyle . ' navbar-fixed-top" role="navigation">';
		} else {
			$this->navbar[] = '<div class="navbar ' . $this->navbarStyle . '" role="navigation">';
		}
		
		$this->navbar[] = '<div class="navbar-header"><button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">';
		$this->navbar[] = '<span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>';
		$this->navbar[] = '<a class="navbar-brand" title="' . CONFIG['WEB']['WEB_NAME'] . " (" . CONFIG['WEB']['WEB_NAME_ABBR'] . ')" href="' . WEB_URL . '"> <strong>' . CONFIG['WEB']['WEB_NAME_ABBR'] . '</strong> <sup>' . CONFIG['WEB']['VERSION'] . '</sup></a></div>';
		$this->navbar[] = '<div class="collapse navbar-collapse">';
		
		if( sizeof( $leftNav ) > 0 ) {
			$this->navbar[] = '<ul class="nav navbar-nav">';
				$this->processNavbarLinks( $leftNav, $currentURL );
			$this->navbar[] = '</ul>';
		}
		
		if( sizeof( $rightNav ) > 0 ) {
			$this->navbar[] = '<ul class="nav navbar-nav navbar-right">';
				$this->processNavbarLinks( $rightNav, $currentURL );
			$this->navbar[] = '</ul>';
		}
		
		$this->navbar[] = '</div></div>';
	}
	
	/**
	* Build out the actual links themselves by stepping through the DROPDOWNS if 
	* present and structuring the navbar correctly
	*/
	
	private function processNavbarLinks( $linkSet, $currentURL ) {
		
		foreach( $linkSet as $linkName => $linkInfo ) { 
		
			$showLink = false;
			if( $linkInfo['STATUS'] == "public" ) {
				$showLink = true;
			} else if( $linkInfo['STATUS'] == "public_only" && !$this->isLoggedIn ) {
				$showLink = true;
			} else if( $linkInfo['STATUS'] == "observer" && $this->isLoggedIn ) {
				$showLink = true;
			} else if( $linkInfo['STATUS'] == "curator" && $this->isLoggedIn && ($this->userLevel == "curator" || $this->userLevel == "poweruser" || $this->userLevel == "admin")) {
				$showLink = true;
			} else if( $linkInfo['STATUS'] == "poweruser" && $this->isLoggedIn && ($this->userLevel == "poweruser" || $this->userLevel == "admin") ) {
				$showLink = true;
			} else if( $linkInfo['STATUS'] == "admin" && $this->isLoggedIn && ($this->userLevel == "admin") ) {
				$showLink = true;
			}
			
			if( $showLink ) {
				if( !isset( $linkInfo['DROPDOWN'] ) ) {
					
					if( $linkInfo['URL'] == "HEADER" ) {
						$this->navbar[] = '<li class="dropdown-header">' . $linkInfo['TITLE'] . '</li>';
					} else if( $linkInfo['URL'] == "DIVIDER" ) {
						$this->navbar[] = '<li class="divider"></li>';
					} else if( $linkInfo['URL'] != $currentURL ) {	
						$this->navbar[] = "<li><a href='" . $linkInfo['URL'] . "' title='" . $linkInfo['TITLE'] . "'>" . $linkName . "</a></li>";
					} else {
						$this->navbar[] = "<li class='active'><a href='" . $linkInfo['URL'] . "' title='" . $linkInfo['TITLE'] . "'>" . $linkName . "</a></li>";
					}
					
				} else {
					
					$this->navbar[] = "<li class='dropdown'><a class='dropdown-toggle' data-toggle='dropdown' href='" . $linkInfo['URL'] . "' title='" . $linkInfo['TITLE'] . "'>" . $linkName . " <i class='fa fa-angle-down'></i></a>";
					$this->navbar[] = "<ul class='dropdown-menu'>";
					$this->processNavbarLinks( $linkInfo['DROPDOWN'], $currentURL );
					$this->navbar[] = "</ul></li>";
					
				}
			}
			
		}
	}

}

?>