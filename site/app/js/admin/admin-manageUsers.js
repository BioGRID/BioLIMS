
/**
 * Javascript Bindings that apply to changing of passwords
 * in the admin tools
 */
 
(function( yourcode ) {

	yourcode( window.jQuery, window, document );

} (function( $, window, document ) {
	
	var baseURL = $("head base").attr( "href" );

	$(function( ) {
		$(".datatableBlock").biolimsDataTableBlock({ 
			sortCol: 1, 
			sortDir: "ASC", 
			pageLength: 100,
			colTool: "manageUsersHeader", 
			rowTool: "manageUsersRows", 
			optionsCallback: function( datatable ) {
				initializeClassChangeOptions( datatable );
				initializeStatusChangeOptions( datatable );
				initializeOptionPopups( );
			}
		});
	});
	
	/**
	 * Setup the functionality of the class change icons
	 */
	 
	function initializeClassChangeOptions( datatable ) {
		
		$(".datatableBlock").on( "click", ".classChange", function( ) {
			
			var currentClick = $(this);
			var submitSet = { };
			
			submitSet['userID'] = $(this).attr( "data-userid" );
			submitSet['direction'] = $(this).attr( "data-direction" );
			submitSet['adminTool'] = "userClassChange";
			
			//Convert to JSON
			submitSet = JSON.stringify( submitSet );
		
			$.ajax({
				url: baseURL + '/scripts/adminTools.php',
				type: 'POST',
				data: { 'expData': submitSet}, 
				dataType: 'json'
			}).done( function( results ) {
				
				if( results['STATUS'] == "SUCCESS" ) {
					alertify.success( results['MESSAGE'] );
					var cellIndex = datatable.cell( currentClick.closest( 'tr' ).find('.userClass') ).index( );
					datatable.cell( cellIndex ).data( results['NEWVAL'] );
					datatable.draw( false );
				} else {
					alertify.error( results['MESSAGE'] );
				}
			});
		});
		
	}
	
	/**
	 * Setup the functionality of the status change icons
	 */
	 
	function initializeStatusChangeOptions( datatable ) {
		
		$(".datatableBlock").on( "click", ".statusChange", function( ) {
			
			var currentClick = $(this);
			var submitSet = { };
			
			submitSet['userID'] = $(this).attr( "data-userid" );
			submitSet['status'] = $(this).attr( "data-status" );
			submitSet['adminTool'] = "userStatusChange";
			
			//Convert to JSON
			submitSet = JSON.stringify( submitSet );
		
			$.ajax({
				url: baseURL + 'scripts/adminTools.php',
				type: 'POST',
				data: { 'expData': submitSet}, 
				dataType: 'json'
			}).done( function( results ) {
				
				if( results['STATUS'] == "SUCCESS" ) {
					alertify.success( results['MESSAGE'] );
					var cellIndex = datatable.cell( currentClick.closest( 'tr' ).find('.userStatus') ).index( );
					datatable.cell( cellIndex ).data( results['NEWVAL'] );
					datatable.draw( false );
				} else {
					alertify.error( results['MESSAGE'] );
				}
			});
		});
		
	}
	
	/**
	 * Setup tooltips for the options in the options column
	 */
	 
	 function initializeOptionPopups( ) {
		 
		$("#datatableBlock").on( 'mouseover', '.popoverData', function( event ) {
	 
			var optionPopup = $(this).qtip({
				overwrite: false,
				content: {
					text: $(this).data( "content" )
				},
				style: {
					classes: 'qtip-bootstrap',
					width: '250px'
				},
				position: {
					my: 'bottom right',
					at: 'top left'
				},
				show: {
					event: event.type,
					ready: true,
					solo: true
				},
				hide: {
					delay: 1000,
					fixed: true,
					event: 'mouseleave'
				}
			}, event);
			
		});
		
	 }
	
}));