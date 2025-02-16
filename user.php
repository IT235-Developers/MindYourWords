<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User</title>
</head>
<body>
    <h3>Question 1</h3>
    <table>
        <tr>
            <h4>Word:</h4>
            <?php
                include("connection.php");
                //$id = $_POST['txt_hID'];

                $sqlWord = "SELECT lq_word FROM level_q ";

                $resWord = $con->query($sqlWord);

                if($resWord->num_rows > 0){
                    while($row = $resWord->fetch_assoc()){
                        echo "<button onclick='textToSpeechWord(this)' data-word='" . $row['lq_word'] . "'>🔊 Word</button>";
                    }
                }else{
                    Print('<script>alert("IDK 2")</script>');
                }
            ?>
        </tr>
        <tr>
            <h4>Example:</h4>
                <?php
                    include("connection.php");
                    $sqlExample = "SELECT lq_ex FROM level_q";

                    $resExample = $con->query($sqlExample);

                    if($resExample->num_rows > 0){
                        while($row = $resExample->fetch_assoc()){
                            echo "<button onclick='textToSpeechExample(this)' data-word='" . $row['lq_ex'] . "'>🔊 Example</button>";
                        }
                    }else{
                        Print('<script>alert("IDK 2")</script>');
                    }
                ?>
        </tr>
        <tr>
            <h4>Description:</h4>
            <?php
                include("connection.php");
                $sqlDesc = "SELECT lq_desc FROM level_q ";

                $resDesc = $con->query($sqlDesc);

                if($resDesc->num_rows > 0){
                    while($row = $resDesc->fetch_assoc()){
                        echo "".$row['lq_desc']." <br>";
                    }
                }else{
                    Print('<script>alert("IDK 2")</script>');
                }
            ?>
        </tr>

        <script type="text/javascript">
            function textToSpeechWord(button) {
                let word = button.getAttribute('data-word');
                let speech = new SpeechSynthesisUtterance();
                speech.text = word;
                speech.volume = 1;
                speech.rate = 0.8;
                speech.pitch = 1;
                window.speechSynthesis.speak(speech);
            }
            function textToSpeechExample(button) {
                let example = button.getAttribute('data-word');
                let speech2 = new SpeechSynthesisUtterance();
                speech2.text = example;
                speech2.volume = 1;
                speech2.rate = 0.8;
                speech2.pitch = 1;
                window.speechSynthesis.speak(speech2);
            }
        </script>

        <form action="" method="POST">
            <input type="text" name="level" placeholder="Level" required>
            <button type="submit" class="btn_add" name="btn_addLevel">+ Add Level</button>
        </form>
    </table>
</body>
</html>

<?php
    include("connection.php");
    if(isset($_POST['btn_addLevel'])){
        $level = $_POST['level'];

        $sql = "SELECT * FROM level WHERE categoryID = '".$id."'
        ";

        $res = $con->query($sql);

        if($res->num_rows>0){
            if($row = $res->fetch_assoc()){
                Print '<script>alert("Level Already Exist")</script>';
            }
        }else{
            $sqlInsertLevel = "INSERT INTO level(categoryID, levelNum) VALUES('".$id."', '".$level."')";
            if($con->query($sqlInsertLevel) == TRUE){
                Print '<script>alert("Level Added Successfully")</script>';
                Print '<script>window.location.assign("admin_category.php")</script>';
            }
        }
    }
    
?>
