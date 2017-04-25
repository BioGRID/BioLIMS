<div class='primaryContent'>
	<div class='container-fluid'>
		<div class='pull-right'>
			<i class='fa fa-cloud-upload fa-4x primaryIcon'></i>
		</div>
		<h2>Welcome to {{ WEB_NAME_ABBR }}</h2>
		<div class='subheadLarge'>Thanks for logging in, <strong>{{FIRSTNAME}} {{LASTNAME}}</strong>. If this is not you, please <strong><a href='{{ WEB_URL }}/Home/Logout' title='Logout of your account'>Logout</a></strong> as soon as possible.</div>

		<hr class='marginTopSm marginBotSm' />
		<div class="alert alert-danger" role="alert" {% if not ALERT_MSG %}style='display:none'{% endif %}>{{ ALERT_MSG | raw }}</div>
		<div class='paddingSm'>
			<h3 class='paddingTopNone'>Getting Started</h3>
			<div class='subheadLarge'>Whether you're new to the site or a regular user, the following tools and categories can help you get started with <strong>{{ WEB_NAME_ABBR }}</strong>.
		</div>
	</div>
</div>

<div class='gettingStartedBox greyBG marginTopSm paddingLg marginBotSm'>
	<div class='container-fluid'>
	<section class="row">
			<div class="col-lg-6 col-md-6 col-sm-12">
				<div class="panel panel-warning">
					<div class="panel-heading"><strong>INFO #1</strong></div>
					<div class="pull-right"><i class="fa fa-4x fa-file-text paddingLg primaryIcon"></i></div>
					<div class="panel-body">
						INFO #1
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12">
				<div class="panel panel-primary">
					<div class="panel-heading"><strong>INFO #2</strong></div>
					<div class="pull-right"><i class="fa fa-4x fa-bar-chart paddingLg primaryIcon"></i></div>
					<div class="panel-body">
						INFO #2
					</div>
				</div>
			</div>
			
		</section>
		<section class="row">
			<div class="col-lg-6 col-md-6 col-sm-12">
				<div class="panel panel-info">
					<div class="panel-heading"><strong>INFO #3</strong></div>
					<div class="pull-right"><i class="fa fa-4x fa-cloud-upload paddingLg primaryIcon"></i></div>
					<div class="panel-body">
						INFO #3
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12">
				<div class="panel panel-success">
					<div class="panel-heading"><strong>INFO #4</strong></div>
					<div class="pull-right"><i class="fa fa-4x fa-book paddingLg primaryIcon"></i></div>
					<div class="panel-body">
						INFO #4  
					</div>
				</div>
			</div>
		 </section>
		 <section class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<div class="panel panel-danger">
					<div class="panel-heading"><strong>Administration Tools</strong></div>
					<div class="pull-right"><i class="fa fa-4x fa-gear paddingLg primaryIcon"></i></div>
					<div class="panel-body">
						In addition, your account has been granted admin status over one or more administrative tools due to your permission settings. This allows you to perform a few more tasks that may not be available to your average user. Currently, have permission to 
						{% for ADMIN_TOOL, ADMIN_URL in ADMIN_TOOLS %}
							<strong><a href='{{ ADMIN_URL }}' title='{{ ADMIN_TOOL }}'>{{ ADMIN_TOOL }}</a></strong>,
						{% endfor %}. 
						To view a full list of available <strong>ADMIN</strong> tools, simply click the link <strong>ADMIN</strong> in the top right corner of the navigation bar at the top of this page.
					</div>
				</div>
			</div>
		</section>
		<section class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<div class="panel panel-default" style="margin-bottom: 5px;">
					<div class="panel-body">
						<div class="pull-left" style="padding-right: 10px;"><i class="fa fa-lg fa-lock primaryIcon"></i></div>
						At any time, simply click on <strong><a href="{{ WEB_URL }}/Home/Logout" title="Logout of Your Account">Logout</a></strong> on here or any page of the site to securely logoff the <strong>{{ WEB_NAME_ABBR }}</strong> website.
					</div>
				</div>
			</div>
		</section>
		<section class="row marginBotXs">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<div class="panel panel-default" style="margin-bottom: 5px;">
					<div class="panel-body">
						<div class="pull-left" style="padding-right: 10px;"><i class="fa fa-lg fa-globe primaryIcon"></i></div>
						This site requires a modern <strong>HTML 5 compatible browser</strong>. Please use <a href="http://www.mozilla.org/en-US/firefox/new/" title="Get Firefox">Firefox 50+</a>, <a href="https://www.google.com/intl/en/chrome/browser/" title="Get Chrome">Chrome 50+</a>, <a href="https://www.microsoft.com/en-us/windows/microsoft-edge" title="Get Internet Explorer">Microsoft Edge</a>, or <a href="http://www.opera.com/" title="Get Opera">Opera 42+</a>. 
					</div>
				</div>
			</div>
		</section>
		<section class="row marginBotXs">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<div class="panel panel-default" style="margin-bottom: 5px;">
					<div class="panel-body">
						<div class="pull-left" style="padding-right: 10px;"><i class="fa fa-lg fa-handshake-o primaryIcon"></i></div>
						The {{ WEB_NAME_ABBR }} website and all associated tools are provided "as is" and without any warranty or support under the <strong><a href='https://opensource.org/licenses/MIT' title='MIT Open Source License'>MIT Open Source License</a></strong> and are archived at <a href='https://github.com/BioGRID' title='BioGRID GitHub'>GitHub</a>. This project is generously funded by grants from the <a href="http://www.nih.gov/" title="NIH">National Institutes of Health</a>, <a href="http://www.cihr-irsc.gc.ca/" title="CIHR">Canadian Institutes of Health Research</a>, and <a href='http://www.genomequebec.com/' title='Genome Quebec'>Genome Quebec</a> as part of the <a href='https://thebiogrid.org' title='The BioGRID'>BioGRID</a> family of bioinformatics tools.
					</div>
				</div>
			</div>
		</section>
	</div>
</div>