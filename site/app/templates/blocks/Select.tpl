<select class='biolimsSelect form-control input-sm {{ SELECT_CLASS }}' style='max-width: 200px;'>
	{% for OPT_ID, OPT_INFO in OPTIONS %}
		<option value='{{ OPT_ID }}' {{ OPT_INFO.SELECTED }}>{{ OPT_INFO.NAME }}</option>
	{% endfor %}
</select>