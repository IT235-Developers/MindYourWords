<?php
session_start();
include("connection.php");
include("functions.php");

$userID = $_SESSION['user']['userID'];

//If this method detects a user containing 0 level_history record, delete its stats
removeStats($con, $userID);

function isLevelsAvailable($con, $row) {
    $categoryID = $row['categoryID'];
    $sqlCheckLevels = "SELECT COUNT(*) as levelCount FROM level WHERE categoryID = '$categoryID'";
    $resCheckLevels = $con->query($sqlCheckLevels);
    $levelData = $resCheckLevels->fetch_assoc();

    if ($levelData['levelCount'] > 0) {
        return true;
    } else {
        return false;
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MindYourWords User Home</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <link rel="stylesheet" type="text/css" href="styles/admin_styles.css">
    </head>
    <body>
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
        <div class="container-fluid main_container" style="padding-top: 30px;">
            <div class="row mb-3">
                <img src="images/myw-secondary-logo.svg" class="secondary_logo">
                <h3 class="welcome_header text-center mb-4">
                    <?php 
                    $username = $_SESSION['user']['username'];
                    
                    echo "Welcome, {$username}!"; 
                    ?>
                </h3>

                <!-- Display Categories -->
                <div class="row">
                    <?php
                        $sqlDisplayCategories = "SELECT * FROM category";
                        $resDisplayCategories = $con->query($sqlDisplayCategories);

                        if ($resDisplayCategories->num_rows > 0) {
                            while ($row = $resDisplayCategories->fetch_assoc()) {
                                if (isLevelsAvailable($con,$row)) {
                                    echo "
                                    <div class='col-12 col-lg-6 g-3'>
                                        <form action='user_category.php' method='POST'>
                                            <input type='text' name='txt_categoryHID' value='" . $row['categoryID'] . "' hidden>
                                            <button type='submit' class='category_level_container rounded text-center bg-white'>
                                                <p>" . $row['categoryName'] . "</p>
                                            </button>
                                        </form>
                                    </div>
                                ";
                                }
                            }
                        }
                    ?>
                </div>
                <div class="d-flex flex-row mt-3 justify-content-end gap-1">
                    <form action="user_statistics.php" method="POST">
                        <button type="submit" class="icon_button me-2 mt-3" style="background-color: #46A33A"><img src="resources/monitoring_24dp_E8EAED_FILL0_wght400_GRAD0_opsz24.svg" alt=""></button>
                    </form>
                    <button type="submit" class="icon_button mt-3" data-bs-toggle="modal" style="background-color: #C33131" data-bs-target="#logout_modal"><img src="resources/logout_24dp_E8EAED_FILL0_wght400_GRAD0_opsz24.svg" alt="Logout icon"></button>
                </div>
            </div>

        </div>
    </body>
</html>
