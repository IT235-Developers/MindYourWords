<?php
session_start();
require_once __DIR__ . '/components/flash_message.php';
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
    <div class="container-fluid main_container d-flex justify-content-evenly flex-xxl-row flex-column">
      <div class="col">
        <img class="secondary_logo_auth p-3" src="images\myw-primary-logo.svg">
      </div>
      <div class="col-xxl-6"> 
        <h3 class="mb-4">Create an Account!</h3>
        <h6 class="mt-2">Enter your details to register.</h6>

        <?php displayFlashMessage() ?>

        <form id="signupForm" action="auth/sign_up.php" method="POST">
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
            <div class="input-group">
              <input type="password" id="txt_password" name="txt_password" placeholder="Password" class="form-control" required/>
              <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                <i class="bi bi-eye"></i>
              </span>
            </div>
          </div>

          <div class="form-group">
            <label for="txt_cpassword"></label>
            <div class="input-group">
              <input type="password" id="txt_cpassword" name="txt_cpassword" placeholder="Confirm Password" class="form-control" required/>
              <span class="input-group-text" id="toggleCPassword" style="cursor: pointer;">
                <i class="bi bi-eye"></i>
              </span>
            </div>
          </div>

          <div id="error-message" class="text-danger"></div>

          <input type="submit" class="btn w-100 btn-primary mt-2 p-2" value="Sign Up">
          
          <p class="mt-3 text-center">Already have an account? <a href="login.php">Login here</a></p>
        </form>
      </div>
    
      
    </div>

    <script src="auth/validation/sign_up_script.js"></script>
  </body>
</html>
