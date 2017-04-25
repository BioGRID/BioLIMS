<?php

namespace BioLIMS\app\classes\models;

/**
 * Search Handler
 * This class is for handling processing of data
 * for both global and advanced search queries
 */

use \PDO;
use BioLIMS\app\lib;
use BioLIMS\app\classes\models;
 
class SearchHandler {

	private $db;
	private $twig;

	public function __construct( ) {
		$this->db = new PDO( DB_CONNECT, DB_USER, DB_PASS );
		$this->db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		
		$loader = new \Twig_Loader_Filesystem( TEMPLATE_PATH );
		$this->twig = new \Twig_Environment( $loader );
	}
	
	/**
	 * Fetch a set of fields to be used for creating an advanced search
	 * output to display
	 */
	 
	public function buildAdvancedSearchFields( $columns ) {
		
		$searchFields = array( );
		
		foreach( $columns as $columnIndex => $columnDef ) {
			if( $columnDef['searchable'] ) {
				$view = "advancedSearch" . DS . "AdvancedSearch" . $columnDef['searchType'] . ".tpl";
		
				$field = $this->twig->render( $view, array(
					"TITLE" => $columnDef['searchName'],
					"COLUMN" => $columnIndex
				));
				
				$searchFields[] = $field;
			}
		}
		
		return $searchFields;
		
	}
	
	/**
	 * Build out the global search section of a query
	 * based on the passed in parameters
	 */
	 
	public function buildGlobalSearch( $params, $columns ) {
		
		$options = array( );
		$queryBits = array( );
		
		// Only add global search params if the main search passed in
		// from datatables contains a value
		if( isset( $params['search'] ) && strlen($params['search']['value']) > 0 ) {
			
			// For OR searches
			$searchValues = explode( "|", $params['search']['value'] );
			
			foreach( $columns as $columnIndex => $columnInfo ) {
				
				// Only allow it if the columns is set to "SEARCHABLE"
				$components = array( );
				if( $columnInfo['searchable'] ) {
					foreach( $columnInfo['searchCols'] as $colName => $searchType ) {
						$this->buildSearchComponent( $searchType, $searchValues, $colName, false, $components, $options );
					}
					
					if( sizeof( $components ) > 0 ) {
						$queryBits[] = "(" . implode( " OR ", $components ) . ")";
					}
					
				}
				
			}
		
		}
		
		return array( "QUERY" => $queryBits, "OPTIONS" => $options );
		
	}
	
	/**
	 * Build out the advanced search section of a query
	 * based on the passed in parameters
	 */
	 
	public function buildAdvancedSearch( $params, $columns ) {
		
		$options = array( );
		$queryBits = array( );
		
		foreach( $params['columns'] as $tableColIndex => $tableColInfo ) {
			
			// Only add advanced search params if there is a value for this column
			if( isset( $tableColInfo['search'] ) && strlen($tableColInfo['search']['value']) > 0  ) {
				
				// Decode the array of possible values
				$searchValues = json_decode( $tableColInfo['search']['value'], true );
				
				// Fetch the column from the columnDefs
				$columnInfo = $columns[$tableColIndex];
				
				$components = array( );
				foreach( $columnInfo['searchCols'] as $colName => $searchType ) {
					$this->buildSearchComponent( $searchType, $searchValues, $colName, true, $components, $options );
				}
					
				if( sizeof( $components ) > 0 ) {
					$queryBits[] = "(" . implode( " OR ", $components ) . ")";
				}
					
			}
				
		}
		
		return array( "QUERY" => $queryBits, "OPTIONS" => $options );
		
	}
	
	/**
	 * Build individual column query bits based on passed 
	 * in parameters
	 */
	 
	private function buildSearchComponent( $searchType, $searchValues, $colName, $isAdvanced = false, &$components, &$options ) {
		
		// Ranged Searches are Different
		// When coming from Advanced Search
		if( strtoupper($searchType) == "RANGE" && $isAdvanced ) {
			$searchType = "RANGE_ADVANCED";
		} 
		
		switch( strtoupper( $searchType ) ) {
						
			case "EXACT" :
			
				// Use an exact match, unless we have a wildcard
				// term, in which casey ou have to use a LIKE with
				// % only at the end
				foreach( $searchValues as $searchValue ) {
					
					if( $isAdvanced ) {
						$searchValue = $searchValue['query'];
					}
					
					$searchInfo = $this->convertWildcardSearchValue( $searchValue );
					if( $searchInfo['TYPE'] == "wildcard" ) {
						$components[] = $colName . " LIKE ?";
						$options[] = $searchInfo['VALUE'] . '%';
					} else {
						$components[] = $colName . "=?";
						$options[] = $searchInfo['VALUE'];
					}
					
				}
				break;
				
			case "RANGE" :
			
				// Use a range that's very close to where things are likely 
				// to be rounded off at
				foreach( $searchValues as $searchValue ) {
					$searchInfo = $this->convertWildcardSearchValue( $searchValue );
					$searchInfo['VALUE'] = $this->removeCommasAndSpaces( $searchInfo['VALUE'] );
					if( is_numeric( $searchInfo['VALUE'] ) ) {
						$components[] = $colName . " BETWEEN ? AND ?";
						$options[] = $searchInfo['VALUE'] - 0.000005;
						$options[] = $searchInfo['VALUE'] + 0.000005;
						
					}
				}
				break;
				
			case "RANGE_ADVANCED" :
			
				// Determine if we have both a Min and Max Value
				$rangeSet = array( "MAX" => "", "MIN" => "" );
				foreach( $searchValues as $searchValue ) {
					$searchTerm = $searchValue['query'];
					$searchInfo = $this->convertWildcardSearchValue( $searchTerm );
					$searchInfo['VALUE'] = $this->removeCommasAndSpaces( $searchInfo['VALUE'] );
					if( is_numeric( $searchInfo['VALUE'] )) {
						
						if( $searchValue['range'] == "MIN" ) {
							$rangeSet['MIN'] = $searchInfo['VALUE'];
						} else if( $searchValue['range'] == "MAX" ) {
							$rangeSet['MAX'] = $searchInfo['VALUE'];
						}					
					}
				}
				
				// Change up the Query based on whether or not we have
				// a full range or just a MIN or MAX value exclusively
				if( $rangeSet['MIN'] != "" || $rangeSet['MAX'] != "" ) {
					if( $rangeSet['MIN'] != "" && $rangeSet['MAX'] != "" ) {
						$components[] = $colName . " BETWEEN ? AND ?";
						$options[] = $rangeSet['MIN'];
						$options[] = $rangeSet['MAX'];
					} else if( $rangeSet['MIN'] != "" ) {
						$components[] = $colName . " >= ?";
						$options[] = $rangeSet['MIN'];
					} else if( $rangeSet['MAX'] != "" ) {
						$components[] = $colName . " <= ?";
						$options[] = $rangeSet['MAX'];
					}
				}
				break;
				
			case "DATE" :
			
				// Dates have two components and evaluation
				// and a date value
				foreach( $searchValues as $searchValue ) {
					
					if( $isAdvanced ) {
						$components[] = $colName . " " . $searchValue['eval'] . "?";
						$options[] = $searchValue['query'];
					} else {
						$components[] = $colName . "=?";
						$options[] = $searchValue;
					}
					
				}
				break;
				
			case "LIKE" :
			
				// Use a LIKE with % on both Sides
				foreach( $searchValues as $searchValue ) {
					
					if( $isAdvanced ) {
						$searchValue = $searchValue['query'];
					}
					
					$searchInfo = $this->convertWildcardSearchValue( $searchValue );
					$components[] = $colName . " LIKE ?";
					$options[] = '%' . $searchInfo['VALUE'] . '%';
				}
				break;
				
			case "EXACT_QUOTES" :
			
				// Use a LIKE with % on both Sides and quotes as part of the LIKE
				// this is most useful for searching through a json stores string
				// like gene aliases when wanting an exact match
				foreach( $searchValues as $searchValue ) {
					
					if( $isAdvanced ) {
						$searchValue = $searchValue['query'];
					}
					
					$searchInfo = $this->convertWildcardSearchValue( $searchValue );
					$components[] = $colName . " LIKE ?";
					if( $searchInfo['TYPE'] == "wildcard" ) {
						$options[] = '%\"' . $searchInfo['VALUE'] . '%';
					} else {
						$options[] = '%\"' . $searchInfo['VALUE'] . '\"%';
					}
				}
				break;
		}
		
	}
	
	/**
	 * Convert a search value that is numeric by removing commas
	 * and spaces
	 */
	 
	private function removeCommasAndSpaces( $value ) {
		$value = trim( $value );
		$value = str_replace( array( ",", " " ), "", $value );
		return $value;
	}
	
	/**
	 * Convert a search value into a wildcard if it
	 * contains a * at the end of it
	 */
	 
	private function convertWildcardSearchValue( $value ) {
		
		$value = trim( $value );
		$type = "normal";
		if( substr( $value, -1 ) === "*" ) {
			$value = rtrim( $value, "*" );
			$type = "wildcard";
		}
			
		return array( "VALUE" => $value, "TYPE" => $type );
		
	}
	
	/**
	 * Build the ORDER BY component of the query
	 * based on passed in parameters
	 */
	 
	public function buildOrderBy( $params, $columns ) {
		
		$orderByEntries = array( );
		if( isset( $params['order'] ) && sizeof( $params['order'] ) > 0 ) {
			
			$orderByEntries = array( );
			foreach( $params['order'] as $orderIndex => $orderInfo ) {
				$orderByEntries[] = $columns[$orderInfo['column']]['dbCol'] . " " . $orderInfo['dir'];
			}
			
		}
		
		if( sizeof( $orderByEntries ) > 0 ) {
			return " ORDER BY " . implode( ",", $orderByEntries );
		}
		
		return false;
		
	}
	
	/**
	 * Build the LIMIT component of the query
	 * based on passed in parameters
	 */
	 
	public function buildLimit( $params ) {
		return " LIMIT " . $params['start'] . "," . $params['length'];
	}
}