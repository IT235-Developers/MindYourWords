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

// Handle add question form submission
if (isset($_POST['btn_addQuestion'])) {
    $levelID = $_POST['txt_levelHID'];

    $word = mysqli_real_escape_string($con, trim($_POST['txt_addWord']));
    $sampleSentence = mysqli_real_escape_string($con, trim($_POST['txt_addExample']));
    $definition = mysqli_real_escape_string($con, trim($_POST['txt_addDescription']));

    $sqlSelect = "SELECT * FROM questions WHERE levelID = $levelID AND word = '$word' AND sampleSentence = '$sampleSentence' AND definition = '$definition'";
    $resSelect = $con->query($sqlSelect);

    if ($resSelect->num_rows > 0) {
        setFlashMessage('warning', 'Word already exists.');
    } else {
        $sqlInsert = "INSERT INTO questions (levelID, word, sampleSentence, definition) VALUES ($levelID, '$word', '$sampleSentence', '$definition')";
        if ($con->query($sqlInsert) === TRUE) {
            setFlashMessage('success', 'Word added successfully.');
        } else {
            setFlashMessage('danger', 'Failed to add word.');
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle the deletion of level
if (isset($_POST['btn_deleteLevel'])) {
    $categoryID = $_SESSION['categoryID'];
    $levelID = $_SESSION['levelID'];
    $levelName = $_SESSION['levelName'];

    archiveCategoryIfNotExist($categoryID, $con, $con2);
    archiveLevel($levelID, $con, $con2);
    archiveQuestionsByLevelId($levelID, $con, $con2);
    deleteLevelFromMainDb($levelID, $con);

    header("Location: admin_category.php");
    exit();
}


?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MindYourWords Admin Level</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="styles/admin_styles.css">
    <link rel="icon" type="image/svg+xml" href="images/myw_favicon.svg">
</head>
<body class="bg-img-gif">
    <div class="container-fluid secondary_container">
        <div class="row">
            <img src="images/myw-secondary-logo.svg" class="secondary_logo">

            <?php
            if (isset($_POST['txt_levelHID']) && isset($_POST['txt_categoryHID']) && isset($_POST['txt_levelName'])) {
                $_SESSION['levelID'] = $_POST['txt_levelHID'];
                $_SESSION['categoryID'] = $_POST['txt_categoryHID'];
                $_SESSION['levelName'] = $_POST['txt_levelName'];
            }

            if (isset($_SESSION['levelID']) && isset($_SESSION['categoryID'])) {
                $levelID = $_SESSION['levelID'];
                $categoryID = $_SESSION['categoryID'];
            } else {
                setFlashMessage('danger', 'Level ID or Category ID is missing.');
                header("Location: admin_category.php");
                exit();
            }

            $sqlCategoryLevelName = "SELECT c.categoryName, l.levelName FROM category c
                                    JOIN level l ON c.categoryID = l.categoryID 
                                    WHERE c.categoryID = $categoryID AND l.levelID = $levelID";

            $resCategoryLevelName = $con->query($sqlCategoryLevelName);

            if ($resCategoryLevelName->num_rows > 0) {
                $row = $resCategoryLevelName->fetch_assoc();
                echo "<h3 class='category_header'>" . $row['categoryName'] . " - " . $row['levelName'] . "</h3>";
            }
            ?>
            <button data-levelid="<?= $levelID; ?>" class="btn_add" id="btn_add">+ Add Question</button>

            <!-- Display Flash Messages -->
            <?php displayFlashMessage(); ?>

            <table class="table">
                <thead>
                    <tr>
                        <th>Word</th>
                        <th colspan="3">Sample Sentence</th>
                        <th colspan="3">Definition</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $getQuestions = "SELECT * FROM questions WHERE levelID = $levelID";
                    $resQuestions = $con->query($getQuestions);

                    if ($resQuestions->num_rows > 0) {
                        while ($row = $resQuestions->fetch_assoc()) {
                            echo "
                            <tr>
                                <td>{$row['word']}</td>
                                <td colspan='3'>{$row['sampleSentence']}</td>
                                <td colspan='3'>{$row['definition']}</td>
                                <td>
                                    <form action='admin_editDelete.php' method='POST'>
                                        <input type='hidden' name='txt_levelHID' value='{$row['levelID']}'>
                                        <input type='hidden' name='txt_questionHID' value='{$row['questionID']}'>
                                        <button type='submit' name='btn_editQuestion'><img src='images/edit.svg' class='btn_actions'></button>
                                        <button type='submit' name='btn_deleteQuestion'><img src='images/delete.svg'></button>
                                    </form>
                                </td>
                            </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <form method="POST" class="mb-2">
            <button type="submit" name="btn_deleteLevel" class="btn delete mt-3 float-end">Delete</button>
            <a class="btn back mt-3 me-2 float-end" href="admin_category.php">Back</a>
        </form>
    </div>

    <!-- Add Question Form -->
    <div class="container bg-light p-3 popUp" id="myForm" style="display: none;">
        <h5 class="mb-2">Add New Question</h5>
        <form action="" method="POST">
            <input type="text" name="txt_addWord" style="width: 100%" placeholder="Enter Word" class="mb-2" required>
            <input type="text" name="txt_addExample" style="width: 100%" placeholder="Enter Example" class="mb-2" required>
            <input type="text" name="txt_addDescription" style="width: 100%" placeholder="Enter Definition" class="mb-2" required>
            <input type="hidden" id="txt_levelHID" name="txt_levelHID">
            <button type="submit" class="btn btn_add update float-end" name="btn_addQuestion">Add Question</button>
            <button type="button" class="btn btn_add cancel float-end" id="btn_cancel">Cancel</button>
        </form>
    </div>
    <div id="overlay" style="display: none;"></div>

    <script src="js/shared-script.js"></script>
</body>
</html>
