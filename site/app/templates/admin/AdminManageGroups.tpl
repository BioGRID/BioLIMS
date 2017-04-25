<div class='primaryContent'>
	<div class='container-fluid'>
		<h2>Manage Groups <i class='fa fa-lg fa-users primaryIcon'></i> </h2>
		<div class='subheadLarge'>Use the following table to make adjustments to groups used by the system. <strong>Note:</strong> Users will need to <strong><a href='{{WEB_URL}}/Home/Logout' title='Logout'>logout</a></strong> and then log back in again for changes to be reflected in their group membership.</div>
	</div>
</div>

<div id='addGroupWrap' class='greyBG marginTopSm paddingLg marginBotSm'>
	<div class='container-fluid'>
		<h3>Add New Group</h3>
		<span id='addNewGroupSubhead' class='subheadSmall'>Use this form to add a new permission group to the database</span>
		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12 marginTopSm clearfix'>
			<form id='addGroupForm'>
				<div class='form-group col-lg-12 col-md-12'>
					<label for='groupName' class='control-label'>Group Name</label>
					<input type='text' class='form-control' id='groupName' name='groupName' placeholder='Group Name' />
				</div>
				<div class='form-group col-lg-12 col-md-12'>
					<label for='groupDesc' class='control-label'>Group Desc</label>
					<input type='text' class='form-control' id='groupDesc' name='groupDesc' placeholder='Group Desc' />
				</div>
				<div class='form-group col-lg-6 col-md-6'>
					<label for='groupMembers' class='control-label'>Select Group Members</label>
					<select class='form-control' id='groupMembers' name='groupMembers' multiple>
						{% for userID, userInfo in USERS %}
							<option value='{{userID}}'>{{userInfo.user_firstname}} {{userInfo.user_lastname}}</option>
						{% endfor %}
					</select>
				</div>
				<div class='col-lg-12 col-md-12'>
					<button class='btn btn-success btn-lg' id='addGroupSubmit' type='submit'><strong>Add Group</strong> <i class='fa fa-check'></i></button>
				</div>
			</form>
		</div>
	</div>
	<div id='messages' class='container-fluid marginTopSm'></div>
</div>

{% include 'blocks/DataTableBlock.tpl' %}