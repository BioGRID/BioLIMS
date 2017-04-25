<?php

namespace BioLIMS\app\lib;

/**
 * Controller
 * This is the base class upon which all other controllers are built. 
 * With this class as the foundation, we build additional classes for
 * actual control of different areas of the site.
 */
 
use BioLIMS\app\classes\utilities;
 
abstract class Controller {
	
	protected $action;
	protected $option;
	protected $urlValues;
	
	protected $headerParams;
	protected $footerParams;
	
	private $twig;
	
	public function __construct( $twig ) {
		$this->twig = $twig;
		$today = getdate( );
		
		$this->headerParams = new utilities\Datastore( );
		$this->headerParams->setList( array( 
			"META_DESC" => CONFIG['WEB']['WEB_DESC'],
			"META_KEYWORDS" => CONFIG['WEB']['WEB_KEYWORDS'],
			"CSS_URL" => CSS_URL,
			"IMG_URL" => IMG_URL,
			"YEAR" => $today['year'],
			"TITLE" => CONFIG['WEB']['WEB_NAME'],
			"ABBR" => CONFIG['WEB']['WEB_NAME_ABBR'],
			"WEB_URL" => WEB_URL
		));
		
		$this->footerParams = new utilities\Datastore( );
		$this->footerParams->setList( array( 
			"YEAR" => $today['year'],
			"COPYRIGHT_URL" => CONFIG['WEB']['OWNER_URL'],
			"COPYRIGHT_OWNER" => CONFIG['WEB']['OWNER_NAME'],
			"JS_URL" => JS_URL
		));
		
	}
	
	/**
	 * Initialize parameters for access by the other functions of the class
	 */
	
	public function setParams( $action, $option, $urlValues ) {
		$this->action = $action;
		$this->option = $option;
		$this->urlValues = $urlValues;
	}
	
	/**
	 * Execute the action that has been requested from the specific controller
	 * this allows us to have a generic access point while keeping the member
	 * functions of the child class protected.
	 */
	
	public function executeAction( ) {
		return $this->{$this->action}( );
	}
	
	/**
	 * Fetch the view for rendering to the user.
	 */
	
	protected function fetchView( $view, $params, $fluidNav = false ) {
		
		$finalDocument = "";
		$finalDocument .= $this->generateOverallHeader( false );
		$finalDocument .= $this->generateNavbar( false, $fluidNav );
		$finalDocument .= $this->processView( $view, $params, false );
		$finalDocument .= $this->generateOverallFooter( false );
		return $finalDocument;
	}
	
	/** 
	 * Render a view to the user.
	 */
	
	protected function renderView( $view, $params, $fluidNav = false ) {
		
		if( DEBUG ) {
			//print_r( $_COOKIE );
			//print_r( $_SESSION );
		}
	
		$this->generateOverallHeader( true );
		$this->generateNavbar( true, $fluidNav );
		$this->processView( $view, $params, true );
		$this->generateOverallFooter( true );
		
		return true;
	}
	
	/** 
	 * Process a view for the user.
	 */
	 
	protected function processView( $view, $params, $render = true ) {
	
		$view = $this->twig->render( $view, $params );
		
		if( $render ) {
			echo $view;
			return true;
		} 
		
		return $view;
	}
	
	/** 
	 * Generate overall header and either return it or render it.
	 */
	
	protected function generateOverallHeader( $render = true ) {
		$view = "common" . DS . "OverallHeader.tpl";
		return $this->processView( $view, $this->headerParams->getList( ), $render );
	}
	
	/** 
	 * Generate overall footer and either return it or render it.
	 */
	
	protected function generateOverallFooter( $render = true ) {
		$view = "common" . DS . "OverallFooter.tpl";
		return $this->processView( $view, $this->footerParams->getList( ), $render );
	}
	
	/**
	 * Generate the navbar for the header of the site.
	 */
	 
	protected function generateNavbar( $render = true, $isFluid = false ) {
		
		$class = "public";
		$isLoggedIn = false;
		
		if( isset( $_SESSION[SESSION_NAME] ) ) {
			$class = $_SESSION[SESSION_NAME]['CLASS'];
			$isLoggedIn = true;
		}
		
		$view = "common" . DS . "Navbar.tpl";
		$navbarGen = new utilities\NavbarBuilder( false, "navbar-inverse", $class, $isLoggedIn );
		$navbar = $navbarGen->fetchNavbar( true );
		
		return $this->processView( $view, array( "NAVBAR" => implode( "\n", $navbar )), $render );
		
	}
	
}

?>