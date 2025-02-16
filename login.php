<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
	    <link rel="stylesheet" type="text/css" href="styles/admin_styles.css">
		<title>Log in Page</title>
	</head>
	<body>
		<div class="container-fluid main_container">
			<img class="secondary_logo" src="images\myw-secondary-logo.svg">
			<h3>Welcome, English Learner!</h3>
			<h6 class="mt-2">Sign in to your account.</h6>
			<form action="" method="POST">
				<div class="form-group">
					<label for="txt_email"></label>
					<input type="text" id="txt_email" placeholder="Email Address" class="form-control" required/>
				</div>

				<div class="form-group">
					<label for="txt_password"></label>
					<input type="Password" id="txt_password" placeholder="Password" class="form-control" required/>
				</div>

				<input type="submit" id="btn_login" class="btn w-100 btn-primary mt-2 p-2" value="Login">

				<div class="form-group">
					<p class="mt-3">Don't have an account? <a href="sign_up.php">Register Here</p>
				</div>
			</form>
		</div>
	</body>
</html>