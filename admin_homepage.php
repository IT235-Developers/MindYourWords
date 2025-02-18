<?php
session_start(); // Start session at the top of the script
include("connection.php");

function setFlashMessage($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function displayFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type'];
        $message = $_SESSION['flash']['message'];
        echo "<div class='alert alert-$type alert-dismissible fade show' role='alert'>
                $message
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
        unset($_SESSION['flash']); // Clear message after displaying
    }
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
    </head>
    <body>

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
        </div>

        <script src="js/shared-script.js"></script>
    </body>
</html>
