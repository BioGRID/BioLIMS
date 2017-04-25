
/**
 * Javascript Bindings that apply to changing of passwords
 * in the admin tools
 */
 
(function( yourcode ) {

	yourcode( window.jQuery, window, document );

} (function( $, window, document ) {
	
	var baseURL = $("head base").attr( "href" );

	$(function( ) {
		initializeFormValidation( );
	});
	
	function initializeFormValidation( ) {
		
		var fieldVals = { };
		
		fieldVals['userName'] = {
			validators: {
				notEmpty: {
					message: 'You Must Enter A Unique User Name'
				}
			}
		};
		
		fieldVals['userPassword'] = {
			validators: {
				notEmpty: {
					message: 'You Must Enter a New Password'
				},
				stringLength: {
					message: 'Your New Password Must be at Least 8 Characters Long',
					min: 8
				}
			}
		};
		
		fieldVals['userPasswordRepeat'] = {
			validators: {
				notEmpty: {
					message: 'You Must Enter Your New Password a Second Time'
				},
				identical: {
					field: 'userPassword',
					message: 'This password must match the one entered above exactly'
				}
			}
		};
		
		fieldVals['userFirstName'] = {
			validators: {
				notEmpty: {
					message: 'You Must Enter the Users First Name'
				}
			}
		};
		
		fieldVals['userLastName'] = {
			validators: {
				notEmpty: {
					message: 'You Must Enter the Users Last Name'
				}
			}
		};
		
		fieldVals['userEmail'] = {
			validators: {
				notEmpty: {
					message: 'You Must Enter Your Current Password'
				},
				emailAddress: {
					message: 'You Must Enter a Valid Email Address'
				}
			}
		};
			
		$("#addNewUserForm").formValidation({
			framework: 'bootstrap',
			fields: fieldVals
		}).on( 'success.form.fv', function( e ) {
			e.preventDefault( );
			
			var $form = $(e.target),
				fv = $(e.target).data( 'formValidation' );
			
			submitNewUser( );
				
		});
	}
	
	function submitNewUser( ) {
		
		var formData = $("#addNewUserForm").serializeArray( );
		var submitSet = { };
		
		// Get main form data
		$.each( formData, function( ) {
			submitSet[this.name] = this.value;
		});
		
		// Add type of tool
		submitSet['adminTool'] = "addNewUser";
				
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
			if( data["STATUS"] == "ERROR" ) {
				alertType = "danger";
				alertIcon = "fa-warning";
				$("#addNewUserForm").formValidation( 'disableSubmitButtons', false );
			} else if( data["STATUS"] == "SUCCESS" ) {
				$("#addNewUserForm").trigger( "reset" );
				$("#addNewUserForm").data('formValidation').resetForm( );
			}
			
			$("#messages").html( '<div class="alert alert-' + alertType + '" role="alert"><i class="fa ' + alertIcon + ' fa-lg"></i> ' + data['MESSAGE'] + '</div></div>' );
			
		}).fail( function( jqXHR, textStatus, errorThrown ) {
			console.log( jqXHR );
			console.log( textStatus );
			$("#addNewUserForm").formValidation( 'disableSubmitButtons', false );
		});
		
	}

}));