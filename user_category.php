<?php
    require_once 'auth/controller/AuthController.php';
    include("auth/auth.php");
    include("connection.php");

    $auth = new AuthController($pdo);
    if ($auth->checkIfAdmin()) {
        setFlashMessage("danger", "Admins are not allowed to access user-only pages.");
        header("Location: admin_homepage.php");
        exit();
    }

    if(isset($_POST['btn_cancel']) || isset($_POST['exit_window'])){
        $levelHistoryID = $_SESSION['levelHistoryID'];

        $sqlDeleteLevelHistory = "DELETE FROM level_history WHERE levelHistoryID = '$levelHistoryID'";

        if(!$con->query($sqlDeleteLevelHistory)){
            echo "Level history record deletion failed";
        }

        if(isset($_POST['exit_window'])){
            exit;
        }
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

    function isQuestionsAvailable($con,$row ) {
        $levelID = $row['levelID'];
        $sqlCheckQuestions = "SELECT COUNT(*) as questionCount FROM questions WHERE levelID = '$levelID'";
        $resCheckQuestions = $con->query($sqlCheckQuestions);
        $questionData = $resCheckQuestions->fetch_assoc();

        if ($questionData['questionCount'] > 0) {
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
    <title>MindYourWords User Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="styles/admin_styles.css">
    <link rel="icon" type="image/svg+xml" href="images/myw_favicon.svg">
</head>
<body class="bg-img-gif">
    <div class="container-fluid main_container">
        <div class="row">
            <img src="images/myw-secondary-logo.svg" class="secondary_logo">

            <?php
            include("connection.php");

            $sqlCategoryName = "SELECT * FROM category WHERE categoryID = $categoryHID";
            $resCategoryName = $con->query($sqlCategoryName);

            if ($resCategoryName->num_rows > 0) {
                $row = $resCategoryName->fetch_assoc();
            ?>
                <h3 class="category_header"><?= $row['categoryName'] ?></h3>
            <?php } 
                displayFlashMessage();
            ?>

            <div class="row">
                <?php
                $sqlDisplayLevel = "SELECT * FROM level WHERE categoryID = '$categoryHID'";
                $resDisplayLevel = $con->query($sqlDisplayLevel);

                if ($resDisplayLevel->num_rows > 0) {
                    while ($row = $resDisplayLevel->fetch_assoc()) {
                ?>
                        <div class="col-12 col-md-6 g-3">
                            <form action="user_level.php" method="POST">
                                <input type="hidden" name="txt_categoryHID" value="<?= $row['categoryID'] ?>">
                                <input type="hidden" name="txt_levelHID" value="<?= $row['levelID'] ?>">
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
            <a class="btn back float-end mt-3 me-2" href="user_homepage.php">Back</a>
        </form>
    </div>
</body>
</html>
