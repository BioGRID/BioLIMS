<div class='primaryContent'>
	<div class='container-fluid'>
		<h2>Change Password <i class='fa fa-lg fa-lock primaryIcon'></i> </h2>
		<div class='subheadLarge'>Use the following form to modify passwords.</div>
	</div>
</div>

<div class='greyBG marginTopSm paddingLg marginBotSm'>
	<div class='container-fluid'>
		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
			<form id='changePasswordForm'>
				<div class='form-group col-lg-12 col-md-12'>
					{% if not USER_LIST %}
						<label for='currentPassword' class='control-label'>Current Password</label>
						<input type='password' class='form-control' id='currentPassword' name='currentPassword' placeholder='Enter Your Current Password' />
					{% else %}
						<label for='userID' class='control-label'>Select a User</label>
						<select class='form-control' id='userID' name='userID'>
							{% for userID, userInfo in USER_LIST %}
								<option value='{{userInfo.user_id}}'>{{userInfo.user_firstname}} {{userInfo.user_lastname}} ({{userInfo.user_name}})</option>
							{% endfor %}
						</select>
					{% endif %}
				</div>
				<div class='form-group col-lg-12 col-md-12'>
					<label for='newPassword' class='control-label'>New Password</label>
					<input type='password' class='form-control' id='newPassword' name='newPassword' placeholder='Enter Your New Password' />
				</div>
				<div class='form-group col-lg-12 col-md-12'>
					<label for='newPasswordRepeat' class='control-label'>New Password Repeated</label>
					<input type='password' class='form-control' id='newPasswordRepeat' name='newPasswordRepeat' placeholder='Enter Your New Password Again' />
				</div>
				<div class='marginTopSm col-lg-12 col-md-12'>
					<button class='btn btn-success btn-lg' id='submitPasswordChange' type='submit'><strong>Submit Password Change</strong> <i class='fa fa-check'></i></button>
				</div>
			</form>
		</div>
	</div>
	<div id='messages' class='container-fluid'></div>
</div>

