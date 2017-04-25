
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
		
		fieldVals['currentPassword'] = {
			validators: {
				notEmpty: {
					message: 'You Must Enter Your Current Password'
				}
			}
		};
		
		fieldVals['newPassword'] = {
			validators: {
				notEmpty: {
					message: 'You Must Enter a New Password'
				},
				stringLength: {
					message: 'Your New Password Must be at Least 10 Characters Long',
					min: 10
				}
			}
		};
		
		fieldVals['newPasswordRepeat'] = {
			validators: {
				notEmpty: {
					message: 'You Must Enter Your New Password a Second Time'
				},
				identical: {
					field: 'newPassword',
					message: 'This password must match the one entered above exactly'
				}
			}
		};
			
		$("#changePasswordForm").formValidation({
			framework: 'bootstrap',
			fields: fieldVals
		}).on( 'success.form.fv', function( e ) {
			e.preventDefault( );
			
			var $form = $(e.target),
				fv = $(e.target).data( 'formValidation' );
			
			submitChangePassword( );
				
		});
	}
	
	function submitChangePassword( ) {
		
		var formData = $("#changePasswordForm").serializeArray( );
		var submitSet = { };
		
		// Get main form data
		$.each( formData, function( ) {
			submitSet[this.name] = this.value;
		});
		
		// Add type of tool
		submitSet['adminTool'] = "changePassword";
				
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
			if( data["STATUS"] == "error" ) {
				alertType = "danger";
				alertIcon = "fa-warning";
				$("#changePasswordForm").formValidation( 'disableSubmitButtons', false );
			} 
			
			$("#messages").html( '<div class="alert alert-' + alertType + '" role="alert"><i class="fa ' + alertIcon + ' fa-lg"></i> ' + data['MESSAGE'] + '</div></div>' );
			
		}).fail( function( jqXHR, textStatus, errorThrown ) {
			console.log( jqXHR );
			console.log( textStatus );
			$("#changePasswordForm").formValidation( 'disableSubmitButtons', false );
		});
		
	}

}));