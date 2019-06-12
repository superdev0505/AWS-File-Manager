

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login AWS</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="/images/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/css/login/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/css/login/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/css/login/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/css/login/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="/css/login/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/css/login/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/css/login/util.css">
	<link rel="stylesheet" type="text/css" href="/css/login/main.css">
<!--===============================================================================================-->
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100 p-l-50 p-r-50 p-t-77 p-b-30">
				<form class="login100-form validate-form" action="{{url('/login')}}" method="POST" enctype="multipart/form-data">
					{{ csrf_field() }}
					<span class="login100-form-title p-b-55">
						<b>Log in</b>
					</span>

					@if($errors->any())
					<script>				
					      alert("Email or Password is incorrect!   Try again please.");
					</script>
					@endif
					

					<div class="wrap-input100 validate-input m-b-16" data-validate = "Valid email is required: ex@abc.xyz">
						<input class="input100 form-control" type="text" id="email" name="email" placeholder="Email">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<span class="lnr lnr-envelope"></span>
						</span>
					</div>

					<div class="wrap-input100 validate-input m-b-16" data-validate = "Password is required">
						<input class="input100 form-control" type="password" id="password" name="password" placeholder="Password">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<span class="lnr lnr-lock"></span>
						</span>
					</div>
					<label>
					<input type="checkbox"  name="remember"> Remember me
					</label>
					
					<div class="container-login100-form-btn p-t-25">
						<button type="submit" class="login100-form-btn login-btn">
							Log in
						</button>
					</div>

					<div class="text-center w-full p-t-42 p-b-22">
						<img class="m_img" src="/images/s3.png" alt="s3_img" style="width:75%">
					</div>

				</form>
			</div>
		</div>
	</div>
	
<!--===============================================================================================-->	
	<script src="/js/login/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="/js/login/popper.js"></script>
	<script src="/js/login/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="/js/login/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="/js/login/main.js"></script>

</body>
</html>
