<html lang="en">
  <head>
  	<meta charset="UTF-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="styles/admin_styles.css">
  	<title>Sign Up Page</title>
  </head>
  <body>
    <div class="container-fluid main_container">
      <img class="secondary_logo" src="images\myw-secondary-logo.svg">
      <h3>Create an Account!</h3>
      <h6 class="mt-2">Enter your details to register.</h6>

      <form action="" method="POST">
        <div class="form-group">
          <label for="txt_username"></label>
          <input type="text" id="txt_username" placeholder="Username" class="form-control" required/>
        </div>

        <div class="form-group">
          <label for="txt_email"></label>
          <input type="text" id="txt_email" placeholder="Email Address" class="form-control" required/>
        </div>

        <div class="form-group">
          <label for="txt_password"></label>
          <input type="Password" id="txt_password" placeholder="Password" class="form-control" required/>
        </div>

        <div class="form-group">
          <label for="txt_cpassword"></label>
          <input type="Password" id="txt_cpassword" placeholder="Confirm Password" class="form-control" required/>
        </div>

        <input type="submit" id="btn_signUp" class="btn w-100 btn-primary mt-2 p-2" value="Login">

        <p class="mt-3">Already have an account? <a href="login.php">Login here</p>
      </form>
    </div>
  </body>
</html>