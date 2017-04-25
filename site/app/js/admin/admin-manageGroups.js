
/**
 * Javascript Bindings that apply to management of permission groups
 * in the admin tools
 */
 
(function( yourcode ) {

	yourcode( window.jQuery, window, document );

} (function( $, window, document ) {
	
	var baseURL = $("head base").attr( "href" );

	$(function( ) {
		initializeFormValidation( );
		
		if( $(".datatableBlock").length ) {
			$(".datatableBlock").biolimsDataTableBlock({ 
				sortCol: 0, 
				sortDir: "ASC", 
				pageLength: 1000,
				colTool: "manageGroupHeader", 
				rowTool: "manageGroupRows", 
				optionsCallback: function( datatable ) {
					initializeGroupOptions( datatable );
					initializeOptionPopups( );
				}
			});
		}
		
	});

	
	/**
	 * Setup the functionality of the group icons
	 */
	 
	function initializeGroupOptions( datatable ) {
		
		$(".datatableBlock").on( "click", ".deleteGroup", function( ) {
			
			var currentClick = $(this);
			var submitSet = { };
			
			submitSet['groupID'] = $(this).attr( "data-groupid" );
			submitSet['adminTool'] = "groupDelete";
			
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
					datatable.draw( false );
				} else {
					alertify.error( results['MESSAGE'] );
				}
				
			});
		});
		
	}
	
	/**
	 * Setup the validation for the add new permission form
	 */
	
	function initializeFormValidation( ) {
		
		var fieldVals = { };
		
		fieldVals['groupName'] = {
			validators: {
				notEmpty: {
					message: 'You must enter a permission name'
				}
			}
		};
		
		fieldVals['groupDesc'] = {
			validators: {
				notEmpty: {
					message: 'You must enter a short description for the group'
				}
			}
		};
		
		fieldVals['groupMembers'] = {
			validators: {
				notEmpty: {
					message: 'You must select at least one member for the group'
				}
			}
		};
			
		$("#addGroupForm").formValidation({
			framework: 'bootstrap',
			fields: fieldVals
		}).on( 'success.form.fv', function( e ) {
			e.preventDefault( );
			
			var $form = $(e.target),
				fv = $(e.target).data( 'formValidation' );
			
			submitAddNewGroup( );
				
		});
	}
	
	function submitAddNewGroup( ) {
		
		var formData = $("#addGroupForm").serializeArray( );
		var submitSet = { };
		
		// Get main form data
		$.each( formData, function( ) {
			submitSet[this.name] = this.value;
		});
		
		// Add type of tool
		submitSet['adminTool'] = "addGroup";
		
		// Get multiuser select
		submitSet['groupMembers'] = [];
		$("#groupMembers option:selected").each( function( ) {
			submitSet['groupMembers'].push( $(this).val( ) );
		})
				
		// Convert to JSON
		submitSet = JSON.stringify( submitSet );
		
		// Send via AJAX for submission to
		// database and placement of files
		$.ajax({
			url: baseURL + "/scripts/adminTools.php",
			type: "POST",
			data: {"expData" : submitSet},
			dataType: 'json',
			beforeSend: function( ) {
				$("#messages").html( "" );
			}
		}).done( function( data, textStatus, jqXHR ) {
			
			var alertType = "success";
			var alertIcon = "fa-check";
			var showMsg = true;
			if( data["STATUS"] == "ERROR" ) {
				alertType = "danger";
				alertIcon = "fa-warning";
				$("#addGroupForm").formValidation( 'disableSubmitButtons', false );
			} else if( data["STATUS"] == "SUCCESS" ) {
				$("#addGroupForm").trigger( "reset" );
				$("#addGroupForm").data('formValidation').resetForm( );
				$(".biolimsDataTable").DataTable( ).draw( false );
			} else if( data["STATUS"] == "REDIRECT" ) {
				window.location = data["MESSAGE"];
				showMsg = false;
			}
			
			if( showMsg ) {
				$("#messages").html( '<div class="alert alert-' + alertType + '" role="alert"><i class="fa ' + alertIcon + ' fa-lg"></i> ' + data['MESSAGE'] + '</div></div>' );
			}
			
		}).fail( function( jqXHR, textStatus, errorThrown ) {
			console.log( jqXHR );
			console.log( textStatus );
			$("#addGroupForm").formValidation( 'disableSubmitButtons', false );
		});
		
	}
	
	/**
	 * Setup tooltips for the options in the options column
	 */
	 
	 function initializeOptionPopups( ) {
		 
		$(".datatableBlock").on( 'mouseover', '.popoverData', function( event ) {
	 
			var optionPopup = $(this).qtip({
				overwrite: false,
				content: {
					title: $(this).data( "title" ),
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