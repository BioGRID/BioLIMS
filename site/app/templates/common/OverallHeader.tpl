<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="description" content="{{META_DESC}}">
	<meta name="keywords" content="{{META_KEYWORDS}}">
	<meta name="author" content="Mike Tyers (TyersLab.com)">
	<meta name="copyright" content="Copyright &copy; {{YEAR}}, Mike Tyers (TyersLab.com), All Rights Reserved.">
	<meta name="application-name" content="BioLIMS">
	<meta name="robots" content="INDEX,FOLLOW">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<base href="{{ WEB_URL }}/">
	
	<link rel="icon" type="image/png" href="{{IMG_URL}}/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
	
	{{CANONICAL|raw}}
	
	<!-- IMS Stylesheets -->
	<link rel="stylesheet" type="text/css" href="{{CSS_URL}}/font-awesome.min.css" />

	{% for STYLESHEET in ADDON_CSS %}
		<link rel="stylesheet" type="text/css" href="{{CSS_URL}}/{{STYLESHEET}}" />
	{% endfor %}
	
	<link rel="stylesheet" type="text/css" href="{{CSS_URL}}/styles.min.css" />
	
	<!-- IMS Favicon -->
	<link rel="icon" type="image/png" href="{{IMG_URL}}/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="{{IMG_URL}}/favicon-16x16.png" sizes="16x16">
	
	<title>{{TITLE}} | {{ABBR}}</title>
</head>
<body>
<div id='main'>