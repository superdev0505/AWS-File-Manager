<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>

	<title>AWS File Manager</title>

	<meta name="csrf-token" content="{{ csrf_token() }}">

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link href="https://use.fontawesome.com/releases/v5.0.8/css/all.css" rel="stylesheet">
	<link rel="stylesheet" href="/css/application.css">
	<link rel="stylesheet" href="/css/theme/material-dashboard.min.css">
	
	<!--   Core JS Files   -->
	<script src="/js/theme/jquery.min.js" type="text/javascript"></script>
	<script src="/js/theme/popper.min.js" type="text/javascript"></script>
	<script src="/js/theme/bootstrap-material-design.min.js" type="text/javascript"></script>

	<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
	<script src="/js/theme/material-dashboard.min.js?v=2.1.0" type="text/javascript"></script>

	<!--  User scripts    -->
	<script src="/js/application.js" type="text/javascript"></script>


	<!-- jQuery Modal -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
	<link rel="stylesheet" href="css/jquery.modal.min.css" />
	
	<!-- jodit_editor -->
	<link rel="stylesheet" href="/css/jodit/jodit.min.css"/>
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,300i,400,400i,700,700i" rel="stylesheet">

	<script src="/js/jodit/jodit.min.js"></script>
	<script src="/js/jodit/prism.js"></script>
	<script src="/js/jodit/app.js"></script>

	<link rel="stylesheet" href="/css/custom.css"/>
	
</head>
<body>

<div class="wrapper ">

	<div class="sidebar" data-color="purple" data-background-color="white" data-image="/images/sidebar.jpg">

		<div class="logo">
			<a href="" class="simple-text logo-normal"><b>AWS HTML File Manager</b></a>
		</div>

		<div class="sidebar-wrapper">

			<ul class="nav">

				<li class="nav-item">
					<div class="nav-link font-weight-bold">
						<i class="material-icons">folder</i>
						<p>Directory</p>
					</div>
				</li>

				<li class="nav-item">
					<a id="make_directory_btn" class="nav-link" href="">
						<i class="material-icons">create_new_folder</i>
						<p>Create</p>
					</a>
				</li>

				<li class="nav-item">
					<div class="nav-link font-weight-bold">
						<i class="material-icons">attachment</i>
						<p>File</p>
					</div>
				</li>

				<li class="nav-item">
					<a id="create_new_file_btn" class="nav-link" href="">
						<i class="material-icons">add</i>
						<p>Create</p>
					</a>
				</li>

				<li class="nav-item">
					<a id="upload_file_btn" class="nav-link" href="">
						<i class="material-icons">publish</i>
						<p>Upload</p>
					</a>
				</li>

				<li class="nav-item">
					<div class="nav-link font-weight-bold">
						<i class="material-icons">cloud</i>
						<p>Content</p>
					</div>
				</li>

				<li class="nav-item">
					<a id="copy_btn" class="nav-link" href="">
						<i class="material-icons">done</i>
						<p>Copy</p>
					</a>
				</li>

				<li class="nav-item">
					<a id="cut_btn" class="nav-link" href="">
						<i class="material-icons">done_all</i>
						<p>Cut</p>
					</a>
				</li>

				<li class="nav-item">
					<a id="paste_btn" class="nav-link" href="">
						<i class="material-icons">all_out</i>
						<p>Paste</p>
					</a>
				</li>

				<li class="nav-item">
					<a id="remove_btn" class="nav-link" href="">
						<i class="material-icons">delete</i>
						<p>Remove</p>
					</a>
				</li>
				<li>
					<br>
				</li>
				<li class="nav-item">
					<div class="font-weight-bold">
						<a class="nav-link" href="{{url('/logout')}}">
							<i class="material-icons">exit_to_app</i>
							<p>Logout</p>
						</a>
					</div>
				</li>

			</ul>

		</div>

	</div>

	<div class="main-panel">

		<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top">

			<div class="container-fluid">

				<button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false"
				        aria-label="Toggle navigation">
					<span class="sr-only">Toggle navigation</span>
					<span class="navbar-toggler-icon icon-bar"></span>
					<span class="navbar-toggler-icon icon-bar"></span>
					<span class="navbar-toggler-icon icon-bar"></span>
				</button>

				<div class="collapse navbar-collapse justify-content-end">

					<form id="search_form" class="navbar-form">

						<div class="input-group no-border">

							<div id="clear_search" class="input-group-prepend d-none">
							      <span class="input-group-text close">
							          <i class="material-icons">close</i>
							      </span>
							</div>

							<input type="text" value="" class="form-control" placeholder="Search...">

							<button type="submit" class="btn btn-white btn-round btn-just-icon">
								<i class="material-icons">search</i>
								<div class="ripple-container"></div>
							</button>

						</div>

					</form>

					<ul class="navbar-nav"> </ul>
				</div>

			</div>

		</nav>

		<div class="content">

			<div class="container-fluid">

				@yield('content')

			</div>

		</div>


	</div>

</div>
</body>
</html>
