<div class='primaryContent'>
	<div class='container-fluid'>
		<h2>Manage Permissions <i class='fa fa-lg fa-gavel primaryIcon'></i> </h2>
		<div class='subheadLarge'>Use the following table to make adjustments to the access permissions used by the system. This page is available only to admins. <strong>Note:</strong> Users will need to <strong><a href='{{WEB_URL}}/Home/Logout' title='Logout'>logout</a></strong> and then log back in again for changes to be reflected in their permissions.</div>
	</div>
</div>

<div id='addPermissionWrap' class='greyBG marginTopSm paddingLg marginBotSm'>
	<div class='container-fluid'>
		<h3>Add New Permission</h3>
		<span id='addNewPermissionSubhead' class='subheadSmall'>Use this form to add a new Permission Type to the Database</span>
		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12 marginTopSm clearfix'>
			<form id='addPermissionForm'>
				<div class='form-group col-lg-6 col-md-6'>
					<label for='permissionName' class='control-label'>Permission Name</label>
					<input type='text' class='form-control' id='permissionName' name='permissionName' placeholder='Permission Name' />
				</div>
				<div class='form-group col-lg-6 col-md-6'>
					<label for='permissionDesc' class='control-label'>Permission Description</label>
					<input type='text' class='form-control' id='permissionDesc' name='permissionDesc' placeholder='Permission Description'	/>
				</div>
				<div class='form-group col-lg-6 col-md-6'>
					<label for='permissionCategory' class='control-label'>Permission Category</label>
					<input type='text' class='form-control' id='permissionCategory' name='permissionCategory' placeholder='Permission Category'	/>
				</div>
				<div class='form-group col-lg-6 col-md-6'>
					<label for='permissionLevel' class='control-label'>Permission Level</label>
					<select class='form-control' id='permissionLevel' name='permissionLevel'>
						{% for permissionName in PERMISSION_LIST %}
							<option value='{{permissionName}}'>{{permissionName}}</option>
						{% endfor %}
					</select>
				</div>
				<div class='col-lg-12 col-md-12'>
					<button class='btn btn-success btn-lg' id='addPermissionSubmit' type='submit'><strong>Add Permission</strong> <i class='fa fa-check'></i></button>
				</div>
			</form>
		</div>
	</div>
	<div id='messages' class='container-fluid marginTopSm'></div>
</div>

{% include 'blocks/DataTableBlock.tpl' %}