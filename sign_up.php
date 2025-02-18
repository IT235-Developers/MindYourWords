<?php
require_once 'auth/controller/AuthController.php';
require_once __DIR__ . '/components/flash_message.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthController($pdo);
    $auth->signup($_POST['txt_username'], $_POST['txt_email'], $_POST['txt_password']);
}
?>

<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="styles/admin_styles.css">
      <title>Sign Up Page</title>
  </head>
  <body>
    <div class="container-fluid main_container">
      <img class="secondary_logo" src="images\myw-secondary-logo.svg">
      <h3>Create an Account!</h3>
      <h6 class="mt-2">Enter your details to register.</h6>

      <?php displayFlashMessage() ?>

      <form id="signupForm" action="sign_up.php" method="POST">
        <div class="form-group">
          <label for="txt_username"></label>
          <input type="text" id="txt_username" name="txt_username" placeholder="Username" class="form-control" required/>
        </div>

        <div class="form-group">
          <label for="txt_email"></label>
          <input type="email" id="txt_email" name="txt_email" placeholder="Email Address" class="form-control" required/>
        </div>

        <div class="form-group">
          <label for="txt_password"></label>
          <input type="Password" id="txt_password" name="txt_password" placeholder="Password" class="form-control" required/>
        </div>

        <div class="form-group">
          <label for="txt_cpassword"></label>
          <input type="Password" id="txt_cpassword" name="txt_cpassword" placeholder="Confirm Password" class="form-control" required/>
        </div>

        <div id="error-message" class="text-danger"></div>

        <input type="submit" id="btn_signUp" class="btn w-100 btn-primary mt-2 p-2" value="Sign Up">

        <p class="mt-3">Already have an account? <a href="login.php">Login here</a></p>
      </form>
    </div>

    <script src="auth/validation/sign_up_script.js"></script>
  </body>
</html>
