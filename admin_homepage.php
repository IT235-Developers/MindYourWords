<?php
require_once 'auth/controller/AuthController.php';
include("auth/auth.php");
include("connection.php");

$auth = new AuthController($pdo);
if (!$auth->checkIfAdmin()) {
    setFlashMessage("danger", "Users are not allowed to access admin-only pages.");
    header("Location: user_homepage.php");
    exit();
}

if (isset($_POST['btn_addCategory'])) {
    $category = mysqli_real_escape_string($con, $_POST['txt_category']);

    $sqlSelectCategories = "SELECT * FROM category WHERE categoryName = '$category'";
    $resSelectCategories = $con->query($sqlSelectCategories);

    if ($resSelectCategories->num_rows > 0) {
        $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Category Already Exists!'];
    } else {
        $sqlInsertCategory = "INSERT INTO category(categoryName) VALUES('$category')";
        if ($con->query($sqlInsertCategory) === TRUE) {
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Category Added Successfully!'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Failed to Add Category!'];
        }
    }

    // Redirect to avoid form resubmission
    header("Location: admin_homepage.php");
    exit;
}
?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MindYourWords Admin Home</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <link rel="stylesheet" type="text/css" href="styles/admin_styles.css">
        <link rel="icon" type="image/svg+xml" href="images/myw_favicon.svg">
    </head>
    <body class="bg-img-gif">
        <div class="modal" id="logout_modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 10px;">
                    <div class="modal-header">
                        <h5 class="modal-title">Logout</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to logout?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="auth/logout.php" method="POST">
                            <button type="submit" class="btn btn-danger">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
      
        <div id="overlay"></div>
        <div class="container-fluid main_container">
            <div class="row">
                <img src="images/myw-secondary-logo.svg" class="secondary_logo">
                <h3 class="welcome_header">Welcome, Admin!</h3>

                <!-- Add New Category Form -->
                <div class="container bg-light p-3" id="myForm">
                    <h5 class="mb-2">Add New Category</h5>
                    <form action="" method="POST" class="myForm">
                        <input type="text" name="txt_category" placeholder="Category Name" class="inputFieldAdd" required>
                        <button type="submit" class="btn btn_add update float-end" name="btn_addCategory">Update</button>
                        <button type="button" class="btn btn_add cancel float-end" id="btn_cancel">Cancel</button>
                    </form>
                </div>
                <button id="btn_add" class="btn_add"> + Add Category</button>

                <!-- Display flash message when deleting category -->
                <?php displayFlashMessage(); ?>

                <!-- Display flash message when deleting category -->
                <?php displayFlashMessage(); ?>

                <!-- Flash Message -->
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['flash_message']['type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['flash_message']['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['flash_message']); // Clear the flash message ?>
                <?php endif; ?>

                <!-- Display Categories -->
                <div class="row">
                    <?php
                    $sqlDisplayCategories = "SELECT * FROM category";
                    $resDisplayCategories = $con->query($sqlDisplayCategories);

                    if ($resDisplayCategories->num_rows > 0) {
                        while ($row = $resDisplayCategories->fetch_assoc()) {
                            echo "
                                <div class='col-12 col-lg-6 g-3'>
                                    <form action='admin_category.php' method='POST'>
                                        <input type='text' name='txt_categoryHID' value='" . $row['categoryID'] . "' hidden>
                                        <button type='submit' class='category_level_container rounded text-center bg-white'>
                                            <p>" . $row['categoryName'] . "</p>
                                        </button>
                                    </form>
                                </div>
                            ";
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="d-flex flex-row mt-3 justify-content-end">
                <button type="submit" class="icon_button mt-3" data-bs-toggle="modal" style="background-color: #C33131" data-bs-target="#logout_modal">
                    <img src="resources/logout_24dp_E8EAED_FILL0_wght400_GRAD0_opsz24.svg" alt="Logout icon">
                </button>
            </div>
        </div>

        <script src="js/shared-script.js"></script>
    </body>
</html>
