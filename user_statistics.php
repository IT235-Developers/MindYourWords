<?php
    require_once 'auth/controller/AuthController.php';
    include("auth/auth.php");
    include("connection.php");
    include("functions.php");

    $auth = new AuthController($pdo);
    if ($auth->checkIfAdmin()) {
        setFlashMessage("danger", "Admins are not allowed to access user-only pages.");
        header("Location: admin_homepage.php");
        exit();
    }

    $userID = $_SESSION['user']['userID'];

    //If this method detects a user containing 0 level_history record, delete its stats
    removeStats($con, $userID);

    $username = $_SESSION['user']['username'];

    $sqlGetUserStats = "SELECT averageScore, highestScore, totalGamesPlayed, winningRate
        FROM user_stats WHERE userID = '$userID';";

    $resGetUserStats = $con->query($sqlGetUserStats);

    if($resGetUserStats){
        if($resGetUserStats->num_rows > 0){
            $userStats = $resGetUserStats->fetch_assoc();
            $averageScore = $userStats['averageScore'];
            $highestScore =  $userStats['highestScore'];
            $totalGamesPlayed = $userStats['totalGamesPlayed'];
            $winningRate = $userStats['winningRate'];
        }

        else{
            $averageScore = 0;
            $highestScore =  0;
            $totalGamesPlayed = 0;
            $winningRate = 0;
        }
    }

    else{
        echo "Query for getting the users stats failed to execute";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="styles/admin_styles.css">
    <link rel="stylesheet" type="text/css" href="user_statistics_styles.css">
    <link rel="icon" type="image/svg+xml" href="images/myw_favicon.svg">
    <title>MindYourWords Statistics</title>
</head>
<body class="bg-img-gif">

    <div class="container-fluid main_container">
        <div class="row">
            <img src="images/myw-secondary-logo.svg" class="secondary_logo">

            <h1 class="fs-2 text-center">Hello, <?= $username ?></h1>

            <!-- Outer flex container with wrapping -->
            <div class="d-flex flex-wrap outer-container mt-4">
                <!-- average score -->
                <div class="d-flex flex-column cell">
                    <div class="p-2 custom-text-color fs-3 fw-bolder text-center"><?= $averageScore ?></div>
                    <div class="p-2 custom-text-color fs-3 text-center">Average Score</div>
                </div>
                <!-- total games played -->
                <div class="d-flex flex-column cell">
                    <div class="p-2 custom-text-color fs-3 fw-bolder text-center"><?= $totalGamesPlayed ?></div>
                    <div class="p-2 custom-text-color fs-3 text-center">Total Games Played</div>
                </div>
                <!-- highest score -->
                <div class="d-flex flex-column cell">
                    <div class="p-2 custom-text-color fs-3 fw-bolder text-center"><?= $highestScore ?></div>
                    <div class="p-2 custom-text-color fs-3 text-center">Highest Score</div>
                </div>
                <!-- winning rate -->
                <div class="d-flex flex-column cell">
                    <div class="p-2 custom-text-color fs-3 fw-bolder text-center"><?= $winningRate ?>%</div>
                    <div class="p-2 custom-text-color fs-3 text-center">Winning Rate</div>
                </div>
            </div>

        </div>
        <a class="btn back float-end mt-3 me-2" href="user_homepage.php">Back</a>
    </div>
    
</body>
</html>