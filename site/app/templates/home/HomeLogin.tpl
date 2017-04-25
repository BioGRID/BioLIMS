<div class='container primaryContent'>
	<div class='row'>
		<div class='col-lg-6 col-md-6 col-sm-6 col-lg-offset-3 col-sm-offset-3 col-md-offset-3'>  
			<form class="form-signin" role="form" method="POST" action="{{ WEB_URL }}/Home/Login">
				<h1 class="marginBotSm"><i class='fa fa-lock'></i> Login to {{WEB_NAME_ABBR}} </h1>
				<input type="text" name='username' class="form-control marginBotSm" placeholder="Username" value="{{ USERNAME }}" required autofocus>
				<input type="password" name='password' class="form-control" placeholder="Password" required>
				<div class="checkbox">
					<label>
					  <input type="checkbox" name='remember' value="true" checked> Remember me
					</label>
				</div>
				<button class="btn btn-lg btn-success" type="submit">Sign in <i class='fa fa-check'></i></button>
			</form><br />
			<div id='loginError' class="alert alert-danger {{SHOW_ERROR}}"><i class='fa fa-lg fa-exclamation-triangle'></i> {{ERROR}}</div>
		</div>
	</div>
</div>

