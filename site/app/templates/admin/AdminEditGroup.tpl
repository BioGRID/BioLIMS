<div class='primaryContent'>
	<div class='container-fluid'>
		<h2>Edit Group <i class='fa fa-lg fa-users primaryIcon'></i> </h2>
		<div class='subheadLarge'>Use the following form to make adjustments to this group. <strong>Note:</strong> Users will need to <strong><a href='{{WEB_URL}}/Home/Logout' title='Logout'>logout</a></strong> and then log back in again for changes to be reflected in their group membership.</div>
	</div>
</div>

<div id='addGroupWrap' class='greyBG marginTopSm paddingLg marginBotSm'>
	<div class='container-fluid'>
		<h3>Edit Group</h3>
		<span id='addNewGroupSubhead' class='subheadSmall'>Use this form to edit an existing permission group in the database</span>
		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12 marginTopSm clearfix'>
			<form id='addGroupForm'>
				<div class='form-group col-lg-12 col-md-12'>
					<label for='groupName' class='control-label'>Group Name</label>
					<input type='text' class='form-control' id='groupName' name='groupName' placeholder='Group Name' value='{{ GROUP_NAME }}' />
				</div>
				<div class='form-group col-lg-12 col-md-12'>
					<label for='groupDesc' class='control-label'>Group Desc</label>
					<input type='text' class='form-control' id='groupDesc' name='groupDesc' placeholder='Group Desc' value='{{ GROUP_DESC }}' />
				</div>
				<div class='form-group col-lg-6 col-md-6'>
					<label for='groupMembers' class='control-label'>Select Group Members</label>
					<select class='form-control' id='groupMembers' name='groupMembers' multiple>
						{% for userID, userInfo in USERS %}
							<option value='{{userID}}'
								{% if userID in SELECTED_USERS|keys %}
									selected='selected'
								{% endif %}
								>{{userInfo.user_firstname}} {{userInfo.user_lastname}}
							</option>
						{% endfor %}
					</select>
				</div>
				<input type='hidden' name='groupID' id='groupID' value='{{ GROUP_ID }}' />
				<div class='col-lg-12 col-md-12'>
					<button class='btn btn-primary btn-lg' id='addGroupSubmit' type='submit'><strong>Submit Group Changes</strong> <i class='fa fa-check'></i></button>
				</div>
			</form>
		</div>
	</div>
	<div id='messages' class='container-fluid marginTopSm'></div>
</div>