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
        <div class="container-fluid main_container">
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
            </div>
            <form action="auth/logout.php" method="POST">
                <button type="submit" class="btn delete mt-3 float-end">Logout</button>
            </form>
            <form action="user_statistics.php" method="POST">
                <button type="submit" class="btn btn-primary me-2 mt-3 float-end">Stats</button>
            </form>
        </div>
    </body>
</html>
