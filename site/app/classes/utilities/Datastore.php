<?php

namespace BioLIMS\app\classes\utilities;

/**
 * Datastore
 * This is a simple class for storing data records in an array
 * with the goal of eventually passing the stored array to a view
 * for rendering.
 */
 
class Datastore {

	private $datastore;
	
	public function __construct( ) {
		$this->datastore = array( );
	}
	
	/**
	 * Get a single record out of the datastore
	 */
	
	public function get( $name ) {
		return $this->datastore[$name];
	}
	
	/**
	 * Sets a single record in the datastore
     */
	
	public function set( $name, $value ) {
		$this->datastore[$name] = $value;
	}
	
	/**
	 * Removes a single record from the datastore
	 */
	
	public function remove( $name ) {
		unset( $this->datastore[$name] );
	}
	
	/**
	 * Set the datastore by passing in an entire array
	 * of keys and values all at once.
	 */
	 
	public function setList( $list ) {
		foreach( $list as $key => $value ) {
			$this->datastore[$key] = $value;
		}
	}
	
	/**
	 * Wraps an array of values in $before and $after and then
	 * sets the newly wrapped values inside the datastore
	 */
	
	public function wrapSet( $name, $values, $before, $after ) {
		
		if( is_array( $values ) ) {
			$processedSet = array( );
			foreach( $values as $value ) {
				$processedSet[] = $before . $value . $after;
			}
			$this->datastore[$name] = $processedSet;
		} else {
			$this->datastore[$name] = $before . $values . $after;
		}
		
	}
	
	/**
	 * Returns the entire datastore
	 */
	
	public function getList( ) {
		return $this->datastore;
	}
	
}

?>