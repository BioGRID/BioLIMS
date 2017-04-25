{% if BTN_LINK %}
	<a class='btn {{ BTN_CLASS }} btn-sm' href='{{ BTN_LINK }}' id='{{ BTN_ID }}'><i class='fa {{ BTN_ICON }}'></i> {{ BTN_TEXT }}</a>
{% else %}
	<a class='btn {{ BTN_CLASS }} btn-sm' id='{{ BTN_ID }}'><i class='fa {{ BTN_ICON }}'></i> {{ BTN_TEXT }}</a>
{% endif %}