<?php
// Start the session
session_start();
if (isset($_POST['subject'])) {
    $_SESSION['subject'] = $_POST['subject'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Quiz with PHP</title>
    <meta name="description" content="A quiz made with php">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
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

    <div>
        <form action='<?php echo $_SERVER['PHP_SELF']; ?>' method='POST'>
            <label for="subject">Select a Subject:</label>
            <?php
            try {
                $query = 'SELECT * FROM `Subject`;';
                $statement = $db_connection->prepare($query);
                echo '<select name="subject">';
                if ($statement->execute()) {
                    while ($row = $statement->fetch()) {
                        if ($_SESSION['subject'] == $row['subject']) {
                            echo "<option selected>" . $row['subject'] . "</option>";
                        } else {
                            echo "<option>" . $row['subject'] . "</option>";
                        }
                    }
                }
                echo "</select>";
            } catch (PDOException $error) {
                die('Verbindung fehlgeschlagen: ' . $error->getMessage());
            }
            ?>
            <br>
            <Input type='submit' name='send' value='Start the Quiz!' />
        </form>
    </div>

    <?php
    //Once subject was choosen
    //Show questions & answers
    if (isset($_POST['subject'])) {
        $selected_subject = $_SESSION['subject'];
        try {
            //start from
            echo "<div>";
            echo "<form action=\"";
            echo $_SERVER['PHP_SELF'];
            echo "\" method=\"POST\">";

            //get nr of questions of subject
            $query = 'SELECT COUNT(`pk_question_id`) AS "nrofquestions" FROM Question q 
            INNER JOIN Subject s ON q.fk_pk_subject_id = s.pk_subject_id
            WHERE s.subject = ?;';
            $statement = $db_connection->prepare($query);
            if ($statement->execute([$selected_subject])) {
                $nrofquestions = $statement->fetch()['nrofquestions'];
            }

            //get pk of questions of subject
            $query = 'SELECT `pk_question_id` FROM Question q 
            INNER JOIN Subject s ON q.fk_pk_subject_id = s.pk_subject_id
            WHERE s.subject = ?;';
            $statement = $db_connection->prepare($query);
            if ($statement->execute([$selected_subject])) {
                $nrrow = 0;
                while ($row = $statement->fetch()) {
                    $pks[$nrrow] = $row['pk_question_id'];
                    $nrrow++;
                }
            }

            //iterate through questions
            for ($i = 0; $i < $nrofquestions; $i++) {
                //print question
                $query = 'SELECT question FROM Question q 
                INNER JOIN Subject s ON q.fk_pk_subject_id = s.pk_subject_id
                WHERE s.subject = ? && q.pk_question_id = ?;';
                $statement = $db_connection->prepare($query);
                if ($statement->execute([$selected_subject, $pks[$i]])) {
                    $row = $statement->fetch();
                    echo $row['question'] . "<br>";
                }

                //print all answers for question
                $query = 'SELECT question, answer FROM Question q 
                INNER JOIN Subject s ON q.fk_pk_subject_id = s.pk_subject_id
                INNER JOIN Answer a ON a.fk_pk_question_id = q.pk_question_id 
                WHERE s.subject = ? && q.pk_question_id = ?;';
                $statement = $db_connection->prepare($query);
                if ($statement->execute([$selected_subject, $pks[$i]])) {
                    $rownr = 1;
                    while ($row = $statement->fetch()) {
                        echo "<input type=\"checkbox\" name=\"question$i&answer$rownr\" value=\"" . $row['answer'] . "\">";
                        echo "<label for=\"question$i&answer$rownr\">" . $row['answer'] . "</label><br>";
                        $rownr++;
                    }
                }
                //new line for next question
                echo "<br>";
            }

            //confirm button
            echo "<input type=\"checkbox\" name=\"confirmation\" value=\"I confirm\">";
            echo "<label for=\"confirmation\">" .
                "I compeleted this Quiz on my own without any help or materials." . "</label><br>";

            //reset and submit button
            echo "<input type=\"submit\" value=\"Submit\">";
            echo "<input type=\"reset\" value=\"Reset\">";

            //close form
            echo "</form>";
            echo "</div>";
        } catch (PDOException $error) {
            die('Verbindung fehlgeschlagen: ' . $error->getMessage());
        }
    }
    ?>

    <?php
    //Once answers were submited
    //Show result
    if (isset($_POST['confirmation'])) {
        $selected_subject = $_SESSION['subject'];

        //start div
        echo "<div>";

        $nruseranswers = 0;
        $nranswers = 0;

        try {
            //get nr of correct answers
            $query = 'SELECT COUNT(a.pk_answer_id) AS "nranswers" FROM Question q
            INNER JOIN Subject s ON q.fk_pk_subject_id = s.pk_subject_id
            INNER JOIN Answer a ON a.fk_pk_question_id = q.pk_question_id 
            WHERE s.subject = ?;';
            $statement = $db_connection->prepare($query);
            if ($statement->execute([$selected_subject])) {
                $nranswers = $statement->fetch()['nranswers'];
            }
  
            //get nr of questions of subject
            $query = 'SELECT COUNT(`pk_question_id`) AS "nrofquestions" FROM Question q 
            INNER JOIN Subject s ON q.fk_pk_subject_id = s.pk_subject_id
            WHERE s.subject = ?;';
            $statement = $db_connection->prepare($query);
            if ($statement->execute([$selected_subject])) {
                $nrofquestions = $statement->fetch()['nrofquestions'];
            }

            //get pk of questions of subject
            $query = 'SELECT `pk_question_id` FROM Question q 
            INNER JOIN Subject s ON q.fk_pk_subject_id = s.pk_subject_id
            WHERE s.subject = ?;';
            $statement = $db_connection->prepare($query);
            if ($statement->execute([$selected_subject])) {
                $nrrow = 0;
                while ($row = $statement->fetch()) {
                    $pks[$nrrow] = $row['pk_question_id'];
                    $nrrow++;
                }
            }

            //iterate through questions
            for ($i = 0; $i < $nrofquestions; $i++) {
                //print question
                $query = 'SELECT question FROM Question q 
                INNER JOIN Subject s ON q.fk_pk_subject_id = s.pk_subject_id
                WHERE s.subject = ? && q.pk_question_id = ?;';
                $statement = $db_connection->prepare($query);
                if ($statement->execute([$selected_subject, $pks[$i]])) {
                    $row = $statement->fetch();
                    echo $row['question'] . "<br>";
                }

                //print all answers for question
                $query = 'SELECT question, answer, correct FROM Question q 
                INNER JOIN Subject s ON q.fk_pk_subject_id = s.pk_subject_id
                INNER JOIN Answer a ON a.fk_pk_question_id = q.pk_question_id 
                WHERE s.subject = ? && q.pk_question_id = ?;';
                $statement = $db_connection->prepare($query);
                if ($statement->execute([$selected_subject, $pks[$i]])) {
                    $rownr = 1;
                    while ($row = $statement->fetch()) {
                        if (array_key_exists("question$i&answer$rownr", $_POST)) {
                            //user ticked and is correct -> good
                            if ($row['correct']) {
                                $nruseranswers++;
                                echo "<input disabled checked class=\"correct\" type=\"checkbox\" name=\"question$i.answer$rownr\" value=\"" . $row['answer'] . "\">";
                                echo "<label class=\"correct\" for=\"question$i.answer$rownr\">" . $row['answer'] . "</label><br>";
                            }
                            //user ticked but is wrong -> bad
                            else {
                                echo "<input disabled checked class=\"wrong\" type=\"checkbox\" name=\"question$i.answer$rownr\" value=\"" . $row['answer'] . "\">";
                                echo "<label class=\"wrong\" for=\"question$i.answer$rownr\">" . $row['answer'] . "</label><br>";
                            }
                        } else {
                            //user did not tick but is correct -> bad
                            if ($row['correct']) {
                                echo "<input disabled class=\"wrong\" type=\"checkbox\" name=\"question$i.answer$rownr\" value=\"" . $row['answer'] . "\">";
                                echo "<label class=\"wrong\" for=\"question$i.answer$rownr\">" . $row['answer'] . "</label><br>";
                            }
                            //user did not tick and is wrong -> good
                            else {
                                $nruseranswers++;
                                echo "<input disabled class=\"correct\" type=\"checkbox\" name=\"question$i.answer$rownr\" value=\"" . $row['answer'] . "\">";
                                echo "<label class=\"correct\" for=\"question$i.answer$rownr\">" . $row['answer'] . "</label><br>";
                            }
                        }
                        $rownr++;
                    }
                }
                //new line for next question
                echo "<br>";
            }
            echo "You got $nruseranswers from $nranswers answers correct! Good job!";

            //end div
            echo "</div>";
        } catch (PDOException $error) {
            die('Verbindung fehlgeschlagen: ' . $error->getMessage());
        }
    }
    ?>

    <script src="" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
</body>

</html>