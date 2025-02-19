<?php
    session_start();
    include("connection.php");

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
<body>
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
            <?php } ?>

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
            <button type="submit" name="btn_deleteCategory" class="btn delete mt-3 float-end">Delete</button>
            <a class="btn back float-end mt-3 me-2" href="user_homepage.php">Back</a>
        </form>
    </div>
</body>
</html>
