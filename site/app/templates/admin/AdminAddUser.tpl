<div class='primaryContent'>
	<div class='container-fluid'>
		<h2>Add New User <i class='fa fa-lg fa-user primaryIcon'></i> </h2>
		<div class='subheadLarge'>Fill in the following fields to add a new user to the database.</div>
	</div>
</div>

<div class='greyBG marginTopSm paddingLg marginBotSm'>
	<div class='container-fluid'>
		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
			<form id='addNewUserForm'>
				<div class='form-group col-lg-12 col-md-12'>
					<label for='userName' class='control-label'>Username</label>
					<input type='text' class='form-control' id='userName' name='userName' placeholder='Enter a Unique User Name' />
				</div>
				<div class='form-group col-lg-12 col-md-12'>
					<label for='userPassword' class='control-label'>New Password</label>
					<input type='password' class='form-control' id='userPassword' name='userPassword' placeholder='Enter a Password' />
				</div>
				<div class='form-group col-lg-12 col-md-12'>
					<label for='userPasswordRepeat' class='control-label'>New Password Repeated</label>
					<input type='password' class='form-control' id='userPasswordRepeat' name='userPasswordRepeat' placeholder='Enter the same Password Again' />
				</div>
				<div class='form-group col-lg-12 col-md-12'>
					<label for='userFirstName' class='control-label'>First Name</label>
					<input type='text' class='form-control' id='userFirstName' name='userFirstName' placeholder='Enter First Name of User' />
				</div>
				<div class='form-group col-lg-12 col-md-12'>
					<label for='userLastName' class='control-label'>Last Name</label>
					<input type='text' class='form-control' id='userLastName' name='userLastName' placeholder='Enter Last Name of User' />
				</div>
				<div class='form-group col-lg-12 col-md-12'>
					<label for='userEmail' class='control-label'>Email Address</label>
					<input type='text' class='form-control' id='userEmail' name='userEmail' placeholder='Enter a Unique Email Address for the User' />
				</div>
				<div class='form-group col-lg-12 col-md-12'>
					<label for='userClass' class='control-label'>Select a User Class</label>
					<select class='form-control' id='userClass' name='userClass'>
						{% for userClassName in USER_CLASSES %}
							<option value='{{userClassName}}'>{{userClassName}}</option>
						{% endfor %}
					</select>
				</div>
				<div class='marginTopSm marginBotSm col-lg-12 col-md-12'>
					<button class='btn btn-success btn-lg' id='submitNewUser' type='submit'><strong>Submit New User</strong> <i class='fa fa-check'></i></button>
				</div>
			</form>
		</div>
	</div>
	<div id='messages' class='container-fluid'></div>
</div>

