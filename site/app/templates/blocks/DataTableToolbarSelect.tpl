<div class='form-horizontal {{ SELECT_CLASS }}'>
	<div>
		<label class='control-label col-sm-4' style='padding-top: 6px; text-align: right; padding-right: 0px;'>{{ SELECT_LABEL }}</label>
		<div class='col-sm-8'>
			<select class='biolimsToolbarSelect form-control input-sm'>
				{% for OPT_ID, OPT_INFO in OPTIONS %}
					<option value='{{ OPT_ID }}' {{ OPT_INFO.SELECTED }}>{{ OPT_INFO.NAME }}</option>
				{% endfor %}
			</select>
		</div>
	</div>
</div>