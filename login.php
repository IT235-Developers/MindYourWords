<?php
require_once 'auth/controller/AuthController.php';
require_once __DIR__ . '/components/flash_message.php';

$auth = new AuthController($pdo);

if ($auth->isLoggedIn()) {
	setFlashMessage("warning", "You are already logged in.");

	if ($auth->checkIfAdmin()) {
        header("Location: admin_homepage.php");
        exit();
    } else {
		header("Location: user_homepage.php");
        exit();
	}
}

if (isset($_POST['btn_login'])) {
	$auth->login($_POST['txt_email'], $_POST['txt_password']);
}
?>

<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	   	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
	    <link rel="stylesheet" type="text/css" href="styles/admin_styles.css">
		<link rel="icon" type="image/svg+xml" href="images/myw_favicon.svg">
		<title>Log in Page</title>
	</head>
	<body class="bg-img-gif">
		<div class="container-fluid main_container d-flex justify-content-evenly flex-xxl-row flex-column">
			<div class="col">
				<img class="secondary_logo_auth p-3" src="images\myw-primary-logo.svg">
			</div>
			<div class="col-xxl-6">
				<h3 class="mb-4">Welcome, English Learner!</h3>
				<h6 class="mt-2">Sign in to your account.</h6>

				<?php displayFlashMessage() ?>

				<form action="" method="POST">
					<div class="form-group">
						<label for="txt_email"></label>
						<input type="text" id="txt_email" name="txt_email" placeholder="Email Address" class="form-control"/>
					</div>

					<div class="form-group">
						<label for="txt_password"></label>
						<div class="input-group">
							<input type="Password" id="txt_password" name="txt_password" placeholder="Password" class="form-control"/>
							<span class="input-group-text" id="togglePassword" style="cursor: pointer;">
								<i class="bi bi-eye"></i>
							</span>
						</div>
					</div>

					<input type="submit" id="btn_login" name="btn_login" class="btn w-100 btn-primary mt-2 p-2" value="Login">

					<div class="form-group">
						<p class="mt-3 text-center">Don't have an account? <a href="sign_up.php">Register Here</a></p>
					</div>

					<div class="d-flex flex-column justify-content-center" style="margin-top: 32px;">
						<p class="text-center text-secondary mb-0"><a href="terms_and_conditions.php">Terms and Conditions</a></p>
						<p class="text-center text-secondary mb-0"><a href="about_us.php">All About Us</a></p>
					</div>
				</form>
			</div>
		</div>

		<script src="auth/validation/login_script.js"></script>
	</body>
</html>
