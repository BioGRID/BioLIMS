<?php

namespace BioLIMS\app\lib;

/**
 * Loader
 * This class handles the routing of incoming traffic and determines the
 * correct view to associate with the requesting url. It also handles other
 * global tasks that are centralized and common to the majority of the site.
 */
 
use BioLIMS\app\classes\controllers;
 
class Loader {
	
	private $controller;
	private $action;
	private $option;
	private $urlValues;
	
	/**
	* Determine if we have an action set and correctly
	* organize the URL to determine the correct routing 
	* for this specific url request.
	*/
	
	public function __construct( $urlValues ) {
	
		$this->urlValues = $urlValues;
		$this->controller = "/";
		$this->action = "/";
		$this->option = "";
		
		// URL structure of the format:
		// http://mysite.com/<controller>/<action>/<option>
	
		if( isset( $this->urlValues['path'] ) ) {
			
			if( CONFIG['WEB']['WEB_SUBFOLDER'] != "" ) {
				// When in a subfolder, the path contains the subfolder name
				// which breaks the structure required for url matching
				$this->urlValues['path'] = preg_replace( "/^\/" . CONFIG['WEB']['WEB_SUBFOLDER'] . "/iUs", "", $this->urlValues['path'] );
			} 
			
			$this->urlValues['path'] = trim( $this->urlValues['path'], "/" );
			$splitPath = explode( "/", $this->urlValues['path'] );
			
			if( sizeof( $splitPath ) >= 1 ) {
				$this->controller = array_shift( $splitPath );
				
				if( sizeof( $splitPath ) >= 1 ) { 
					$this->action = array_shift( $splitPath );
					
					// If we have a controller and an action, the rest becomes
					// options to be used within the controller/model.
					if( sizeof( $splitPath ) > 0 ) {
						$this->option = implode( "/", $splitPath );
					}
				}
				
			} else {
				$this->controller = "Home";
			}
		
		} else {
			$this->controller = "Home";
		}
		
		// Default to Homepage if no controller is passed
		if( $this->controller == "" || $this->controller == "/" ) {
			$this->controller = "Home";
		}
		
		// Default to Index action if no action is passed
		if( $this->action == "" || $this->action == "/" ) {
			$this->action = "Index";
		}
		
	}
	
	/**
	* Generate Controller and Execute Action based on parameters 
	* established via the constructor. This uses a switch because
	* the SplAutoloader was having trouble dealing with dynamically
	* created classes, so now the switch loads the class in advance
	* before it is initialized later.
	*/
 
	public function createController( ) {
		
		$loader = new \Twig_Loader_Filesystem( TEMPLATE_PATH );
		$twig = new \Twig_Environment( $loader );
		
		$controllerName = ucwords( $this->controller );
		$controllerClass = "\\BioLIMS\\app\\classes\\controllers\\" . $controllerName . "Controller";
		$actionName = ucwords( $this->action );
		
		if( class_exists( $controllerClass ) ) {
			$controller = new $controllerClass( $twig );
			
			if( method_exists( $controller, $actionName ) ) {
				$controller->setParams( $actionName, $this->option, $this->urlValues );
				return $controller;
			} else {
				header( "Location: " . WEB_URL . "/Error/" );
			}
			
		} else {
			header( "Location: " . WEB_URL . "/Error/" );
		}
		
		return false;
		
	}
	
}

?>