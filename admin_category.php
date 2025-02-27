<?php
require_once 'auth/controller/AuthController.php';
include("auth/auth.php");
include("connection.php");
include("conn2.php");
include("functions.php");

$auth = new AuthController($pdo);
if (!$auth->checkIfAdmin()) {
    setFlashMessage("danger", "Users are not allowed to access admin-only pages.");
    header("Location: user_homepage.php");
    exit();
}

// Store categoryID in session when form is submitted
if (isset($_POST['txt_categoryHID'])) {
    $_SESSION['categoryID'] = $_POST['txt_categoryHID'];
}

// Retrieve categoryID from session
if (isset($_SESSION['categoryID'])) {
    $categoryHID = $_SESSION['categoryID'];
} else {
    setFlashMessage('danger', 'Category ID is missing.');
    header("Location: admin_homepage.php");
    exit();
}

// Add new level
if (isset($_POST['btn_addLevel'])) {
    $level = mysqli_real_escape_string($con, $_POST['txt_level']);
    $categoryHID = $_POST['txt_categoryHID'];

    $sqlSelectLevels = "SELECT * FROM level WHERE categoryID = '$categoryHID' AND levelName = '$level'";
    $resSelectLevels = $con->query($sqlSelectLevels);

    if ($resSelectLevels->num_rows > 0) {
        setFlashMessage('warning', 'Level already exists.');
    } else {
        $sqlInsertLevel = "INSERT INTO level(categoryID, levelName) VALUES('$categoryHID', '$level')";
        if ($con->query($sqlInsertLevel) === TRUE) {
            setFlashMessage('success', 'Level added successfully.');
        } else {
            setFlashMessage('danger', 'Failed to add level.');
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset(($_POST['btn_deleteCategory']))) {
    $categoryID = $_SESSION['categoryID'];
    $levelName = $_SESSION['levelName'];

    archiveCategoryIfNotExist($categoryID, $con, $con2);
    archiveLevels($categoryID, $con, $con2);
    archiveAllQuestions($categoryID, $con, $con2);
    deleteCategoryFromMainDb($categoryID, $con);

    header("Location: admin_homepage.php");
    exit();
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MindYourWords Admin Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="styles/admin_styles.css">
</head>
<body class="bg-img-gif">
    <div id="overlay"></div>
    <div class="container-fluid main_container">
        <div class="row">
            <img src="images/myw-secondary-logo.svg" class="secondary_logo">

            <?php
            $sqlCategoryName = "SELECT * FROM category WHERE categoryID = $categoryHID";
            $resCategoryName = $con->query($sqlCategoryName);

            if ($resCategoryName->num_rows > 0) {
                $row = $resCategoryName->fetch_assoc();
            ?>
                <h3 class="category_header"><?= $row['categoryName'] ?></h3>
            <?php } ?>

            <div class="container bg-light p-3 popUp" id="myForm">
                <h5 class="mb-2 mt-2">Add New Level</h5>
                <form action="" method="POST">
                    <input type="text" name="txt_level" placeholder="Level Name" class="inputFieldAdd" required>
                    <input type="hidden" name="txt_categoryHID" value="<?= $categoryHID ?>">
                    <button type="submit" class="btn btn_add update float-end" name="btn_addLevel">Update</button>
                    <button type="button" class="btn btn_add cancel float-end" id="btn_cancel">Cancel</button>
                </form>
            </div>
            <button id="btn_add" class="btn_add"> + Add Level</button>

            <!-- Display Flash Messages -->
            <?php displayFlashMessage(); ?>

            <div class="row">
                <?php
                $sqlDisplayLevel = "SELECT * FROM level WHERE categoryID = '$categoryHID'";
                $resDisplayLevel = $con->query($sqlDisplayLevel);

                if ($resDisplayLevel->num_rows > 0) {
                    while ($row = $resDisplayLevel->fetch_assoc()) {
                ?>
                        <div class="col-12 col-md-6 g-3">
                            <form action="admin_level.php" method="POST">
                                <input type="hidden" name="txt_categoryHID" value="<?= $row['categoryID'] ?>">
                                <input type="hidden" name="txt_levelHID" value="<?= $row['levelID'] ?>">
                                <input type="hidden" name="txt_levelName" value="<?= $row['levelName'] ?>">
                                <button type="submit" class="category_level_container rounded text-center bg-white">
                                    <p><?= $row['levelName'] ?></p>
                                </button>
                            </form>
                        </div>
                <?php
                    }
                }
                ?>
            </div>
        </div>
        <form method="POST">
            <input type="hidden" name="txt_categoryHID" value="<?= $categoryHID ?>">
            <button type="submit" name="btn_deleteCategory" class="btn delete mt-3 float-end">Delete</button>
            <a class="btn back float-end mt-3 me-2" href="admin_homepage.php">Back</a>
        </form>
    </div>
    
    <script src="js/shared-script.js"></script>
</body>
</html>
