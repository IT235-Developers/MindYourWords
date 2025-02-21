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
            <div class="row">
                <img src="images/myw-secondary-logo.svg" class="secondary_logo">
                <h3 class="welcome_header">
                    <?php
                        session_start();
                        echo "Welcome, " . $_SESSION['user']['username'] . "!";
                    ?>
                </h3>

                <!-- Display Categories -->
                <div class="row">
                    <?php
                        include("connection.php");

                        $sqlDisplayCategories = "SELECT * FROM category";
                        $resDisplayCategories = $con->query($sqlDisplayCategories);

                        if ($resDisplayCategories->num_rows > 0) {
                            while ($row = $resDisplayCategories->fetch_assoc()) {
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
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>
