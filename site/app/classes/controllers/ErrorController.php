<?php


namespace BioLIMS\app\classes\controllers;

/**
 * Error Controller
 * This controller handles the processing of error messages.
 */
 
use BioLIMS\app\lib;
use BioLIMS\app\classes\models;

class ErrorController extends lib\Controller {
	
	public function __construct( $twig ) {
		parent::__construct( $twig );
	}
	
	/**
	 * Index
	 * Default layout for when no specific error is passed, simple returns a 404 error
	 * page with generic description.
	 */
	
	public function Index( ) {
		
		header( "HTTP/1.0 404 Not Found" );
				
		$params = array( 
			"IMG_URL" => IMG_URL,
			"WEB_URL" => WEB_URL
		);
		
		$this->headerParams->set( "CANONICAL", "<link rel='canonical' href='" . WEB_URL . "/Error/' />" );
		$this->headerParams->set( "TITLE", "ERROR 404 Page Not Found" );
		
		$this->renderView( "error" . DS . "Error404.tpl", $params, false );
		
	}
	
	/**
	 * Permission Denied
	 * A static view presented when a user does not have permission to
	 * view a certain page
	 */
	
	public function PermissionDenied( ) {
		
		header( "HTTP/1.0 403 Forbidden" );
				
		$params = array( 
			"IMG_URL" => IMG_URL,
			"WEB_URL" => WEB_URL
		);
		
		$this->headerParams->set( "CANONICAL", "<link rel='canonical' href='" . WEB_URL . "/Error/' />" );
		$this->headerParams->set( "TITLE", "ERROR 403 Forbidden" );
		
		$this->renderView( "error" . DS . "Error403.tpl", $params, false );
		
	}

}

?>