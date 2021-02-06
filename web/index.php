<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]>      <html class="no-js"> <![endif]-->
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Quiz with PHP</title>
    <meta name="description" content="A quiz made with php">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1> Anzola - Quiz </h1>

    <?php
    # Load variables
    $db_host = isset($_ENV["db_host"]) ? $_ENV["db_host"] : "127.0.0.1";
    $db_user = isset($_ENV["db_user"]) ? $_ENV["db_user"] : "root";
    $db_pass = isset($_ENV["db_pass"]) ? $_ENV["db_pass"] : "";
    $db_name = "quiz";
    $db_connStr = "mysql:host=" . $db_host . ";dbname=" . $db_name;
    $db_options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    //Define PDO Object
    try {
        $db_connection = new PDO($db_connStr, $db_user, $db_pass, $db_options);
    } catch (PDOException $error) {
        die('Connection failed: ' . $error->getMessage());
    }
    ?>

    <form action='<?php echo $_SERVER['PHP_SELF']; ?>' method='POST'>    
        <label for="subject">Select a Subject:</label>
        <?php
        try {
            $query = 'SELECT * FROM `Subject`';
            $statement = $db_connection->prepare($query);
            echo '<select name="subject">';
            if ($statement->execute()) {
                while ($row = $statement->fetch()) echo "<option>".$row['subject']."</option>";
            }
            echo "</select>";
        } catch (PDOException $error) {
            die('Verbindung fehlgeschlagen: ' . $error->getMessage());
        }
        ?>
        <br>
        <Input type='submit' name='send' value='Start the Quiz!' />
    </form>

    <!--Once subject was choosen-->
    <!--Show questions & answers-->
    <?php
    if (!isset($_POST['subject'])) {
        return;
    }
    $selected_subject = $_POST['subject'];
    try {
        //start from
        echo "<form action=\"index.php\" method=\"post\">";

        //get nr of questions of subject
        $query = 'SELECT COUNT(`pk_question_id`) AS "nrofquestions" FROM Question q 
        INNER JOIN Subject s ON q.fk_pk_subject_id = s.pk_subject_id
        WHERE s.subject = ?';
        $statement = $db_connection->prepare($query);
        if ($statement->execute([$selected_subject])) {
            $nrofquestions = $statement->fetch()['nrofquestions'];
        }

        //iterate through questions
        for ($i=1; $i <= $nrofquestions; $i++) { 
            //print question
            $query = 'SELECT question FROM Question q 
            INNER JOIN Subject s ON q.fk_pk_subject_id = s.pk_subject_id
            WHERE s.subject = ? && q.pk_question_id = ?';
            $statement = $db_connection->prepare($query);
            if ($statement->execute([$selected_subject, $i])) {
                $row = $statement->fetch();
                echo $row['question'] ."<br>";
            }

            //print all answers for question
            $query = 'SELECT question, answer FROM Question q 
            INNER JOIN Subject s ON q.fk_pk_subject_id = s.pk_subject_id
            INNER JOIN Answer a ON a.fk_pk_question_id = q.pk_question_id 
            WHERE s.subject = ? && q.pk_question_id = ?;';
            $statement = $db_connection->prepare($query);
            if ($statement->execute([$selected_subject, $i])) {
                $rownr = 1;
                while ($row = $statement->fetch()) {
                    echo "<input type=\"checkbox\" name=\"question$i.answer$rownr\" value=\"".$row['answer']."\">";
                    echo "<label for=\"question$i.answer$rownr\">".$row['answer']."</label><br>";
                    $rownr++;
                }
            }
            //new line for next question
            echo "<br>";
        }

        //reset and submit button
        echo "<input type=\"submit\" value=\"Submit\">";
        echo "<input type=\"reset\" value=\"Reset\">";

        //close form
        echo "</form>";

    } catch (PDOException $error) {
        die('Verbindung fehlgeschlagen: ' . $error->getMessage());
    }
    ?>

    <script src="" async defer></script>
</body>

</html>