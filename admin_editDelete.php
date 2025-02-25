<?php
session_start();
include("auth/auth.php");
include("connection.php");
include("conn2.php");
include("functions.php");

// Flash message functions
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
        unset($_SESSION['flash']);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Edit Delete</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="styles/admin_styles.css">
</head>
<body class="bg-img">
    <div class="container-fluid secondary_container">
        <div class="row">
            <img src="images/myw-secondary-logo.svg" class="secondary_logo">

            <!-- Display Flash Messages -->
            <?php displayFlashMessage(); ?>

            <h3 class="welcome_header">Edit this question:</h3>
            <?php
            if (isset($_POST['btn_editQuestion'])) {
                $questionID = $_POST['txt_questionHID'];

                $sqlDisplayQuestions = "SELECT * FROM questions WHERE questionID = '$questionID'";
                $resDisplayQuestions = $con->query($sqlDisplayQuestions);

                if ($resDisplayQuestions->num_rows > 0) {
                    $row = $resDisplayQuestions->fetch_assoc();
                    ?>
                    <table class='table_display'>
                        <form method='POST'>
                            <tr hidden>
                                <td>
                                    <input type='text' name='txt_editQuestionID' value='<?= $row['questionID'] ?>'>
                                </td>
                            </tr>
                            <tr>
                                <td><label for='txt_editWord'>Word:</label></td>
                                <td colspan='5' width='100%'>
                                    <input type='text' name='txt_editWord' value='<?= htmlspecialchars($row['word'], ENT_QUOTES, 'UTF-8') ?>' style='width:100%'>
                                </td>
                            </tr>
                            <tr>
                                <td><label for='txt_editExample'>Example:</label></td>
                                <td colspan='5' width='100%'>
                                    <input type='text' name='txt_editExample' value='<?= htmlspecialchars($row['sampleSentence'], ENT_QUOTES, 'UTF-8') ?>' style='width:100%'>
                                </td>
                            </tr>
                            <tr>
                                <td><label for='txt_editDescription'>Definition:</label></td>
                                <td colspan='5' width='100%'>
                                    <input type='text' name='txt_editDescription' value='<?= htmlspecialchars($row['definition'], ENT_QUOTES, 'UTF-8') ?>' style='width:100%'>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <input type='submit' name='btn_updateWord' value='Update' class='btn update float-end'>
                                    <a href='admin_level.php' class='btn back float-end me-2'>Back</a>
                                </td>
                            </tr>
                        </form>
                    </table>
                    <?php
                } else {
                    setFlashMessage('danger', 'Question not found.');
                    header("Location: admin_level.php");
                    exit();
                }
            } elseif (isset($_POST['btn_deleteQuestion'])) {
                $levelID = $_POST['txt_levelHID'];
                $questionID = $_POST['txt_questionHID'];
                $categoryID = $_SESSION['categoryID'];

                archiveCategoryIfNotExist($categoryID, $con, $con2);
                archiveLevels($categoryID, $con, $con2);
                archiveQuestion($questionID, $con, $con2);
                deleteQuestionFromMainDb($questionID, $con);
                
                header("Location: admin_level.php");
                exit();
            }
            ?>
        </div>
    </div>
</body>
</html>

<?php
if (isset($_POST['btn_updateWord'])) {
    $questionID = $_POST['txt_editQuestionID'];
    $word = mysqli_real_escape_string($con, trim($_POST['txt_editWord']));
    $sampleSentence = mysqli_real_escape_string($con, trim($_POST['txt_editExample']));
    $definition = mysqli_real_escape_string($con, trim($_POST['txt_editDescription']));
  
    $sqlUpdate = "UPDATE questions SET word = '$word', sampleSentence = '$sampleSentence', definition = '$definition' WHERE questionID = '$questionID'";

    if ($con->query($sqlUpdate) === TRUE) {
        setFlashMessage('success', 'Question updated successfully.');
    } else {
        setFlashMessage('danger', 'Failed to update question.');
    }
    header("Location: admin_level.php?questionID=$questionID");
    exit();
}
?>
