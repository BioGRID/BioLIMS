<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12 biolimsDataTableAdvancedSearch marginTopSm marginBotSm' style='display: none'>
	<div class='pull-right'><a class='biolimsDataTableAdvancedToggle'><i class='fa fa-times text-danger fa-lg'></i></a></div>
	<h3>Advanced Filters</h3>
	<span class='subheadSmall'>Enter text in the corresponding fields to limit results in the table below to only those that match for the a specific column. For help with correct search syntax, visit the <strong><a href='{{WIKI_URL}}/Advanced-Searching-Filtering' target='_BLANK'>wiki</a></strong>.</span>
	<h4 class='marginTopSm'>Global Filter</h4>
	<div class='marginTopSm greyBG clearfix biolimsAdvancedSearchFields'>
		<input type="text" class="form-control biolimsDataTableGlobal" placeholder="Globally Applies to All Columns" value="" autofocus>
	</div>
	<h4 class='marginTopSm'>Column Specific Filters</h4>
	<div class='marginTopSm greyBG clearfix biolimsAdvancedSearchFields'>
		{{ ADVANCED_FIELDS | raw }}
	</div>
	<div class='marginTopSm col-lg-12 col-md-12'>
		<button class='btn btn-success btn-lg submitAdvancedSearchBtn' id='submitAdvancedSearchBtn' type='submit'><strong>Submit Filter</strong> <i class='fa fa-check'></i></button>
	</div>
</div>