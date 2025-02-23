    include("connection.php");
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="user_statistics_styles.css">
    <title>MindYourWords Statistics</title>
</head>
<body>

    <div class="container-fluid main_container">
        <div class="row">
            <img src="images/myw-secondary-logo.svg" class="secondary_logo">

            <h1 class="fs-2 text-center">Hello, Username!</h1>

            <!-- Outer flex container with wrapping -->
            <div class="d-flex flex-wrap outer-container mt-4">
                <!-- average score -->
                <div class="d-flex flex-column cell">
                    <div class="p-2 custom-text-color fs-3 fw-bolder text-center">9</div>
                    <div class="p-2 custom-text-color fs-3 text-center">Average Score</div>
                </div>
                <!-- total games played -->
                <div class="d-flex flex-column cell">
                    <div class="p-2 custom-text-color fs-3 fw-bolder text-center">100</div>
                    <div class="p-2 custom-text-color fs-3 text-center">Total Games Played</div>
                </div>
                <!-- highest score -->
                <div class="d-flex flex-column cell">
                    <div class="p-2 custom-text-color fs-3 fw-bolder text-center">18</div>
                    <div class="p-2 custom-text-color fs-3 text-center">Highest Score</div>
                </div>
                <!-- winning rate -->
                <div class="d-flex flex-column cell">
                    <div class="p-2 custom-text-color fs-3 fw-bolder text-center">83%</div>
                    <div class="p-2 custom-text-color fs-3 text-center">Winning Rate</div>
                </div>
            </div>

        </div>
    </div>
    
</body>
</html>