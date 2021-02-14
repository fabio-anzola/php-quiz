<?php
// Start the session
session_start();
include "database.php";
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
    <link rel="stylesheet" href="navigation.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
</head>

<body>
    <h1> Anzola - Quiz </h1>

    <?php
    include "header.php";
    ?>

    <div>
        <form action='<?php echo $_SERVER['PHP_SELF']; ?>' method='POST'>
            <label class="nomargin" for="subject">Select a Subject:</label>
            <?php
            try {
                $query = 'SELECT * FROM `Subject`;';
                $statement = $db_connection->prepare($query);
                echo '<select name="subject">';
                echo "<option>---Please select---</option>";
                if ($statement->execute()) {
                    while ($row = $statement->fetch()) {
                        if (!isset($row['fk_pk_subject_id'])) {
                            if ($_SESSION['subject'] == $row['subject']) {
                                echo "<option selected value=\"" . $row['subject'] . "\">" . $row['subject'] . "</option>";
                            } else {
                                echo "<option value=\"" . $row['subject'] . "\">" . $row['subject'] . "</option>";
                            }
                        }

                        $subquery = 'SELECT * FROM `Subject` WHERE `fk_pk_subject_id` = ?;';
                        $substatement = $db_connection->prepare($subquery);
                        if ($substatement->execute([$row['pk_subject_id']])) {
                            while ($subrow = $substatement->fetch()) {
                                if ($_SESSION['subject'] == ($subrow['subject'])) {
                                    echo "<option selected value=\"" . $subrow['subject'] . "\">" . "&nbsp;&nbsp;&nbsp;&nbsp;" . $subrow['subject'] . "</option>";
                                } else {
                                    echo "<option value=\"" . $subrow['subject'] . "\">" . "&nbsp;&nbsp;&nbsp;&nbsp;" . $subrow['subject'] . "</option>";
                                }
                            }
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
                        echo "<input type=\"checkbox\" name=\"question$i&answer$rownr\" id=\"question$i&answer$rownr\" value=\"" . $row['answer'] . "\">";
                        echo "<label for=\"question$i&answer$rownr\">" . $row['answer'] . "</label><br>";
                        $rownr++;
                    }
                }
                //new line for next question
                echo "<br>";
            }

            //reset and submit button
            echo "<input type=\"submit\" name=\"submit\" value=\"Submit\">";
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
    if (isset($_POST['submit'])) {
        $selected_subject = $_SESSION['subject'];

        //start div
        echo "<div>";

        $nrofincorrect = 0;

        try {
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

                $incorrect = false;

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
                                echo "<input disabled checked class=\"correct\" type=\"checkbox\" name=\"question$i.answer$rownr\" value=\"" . $row['answer'] . "\">";
                                echo "<label class=\"correct\" for=\"question$i.answer$rownr\">" . $row['answer'] . "</label><br>";
                            }
                            //user ticked but is wrong -> bad
                            else {
                                $incorrect = true;
                                echo "<input disabled checked class=\"wrong\" type=\"checkbox\" name=\"question$i.answer$rownr\" value=\"" . $row['answer'] . "\">";
                                echo "<label class=\"wrong\" for=\"question$i.answer$rownr\">" . $row['answer'] . "</label><br>";
                            }
                        } else {
                            //user did not tick but is correct -> bad
                            if ($row['correct']) {
                                $incorrect = true;
                                echo "<input disabled class=\"correct\" type=\"checkbox\" name=\"question$i.answer$rownr\" value=\"" . $row['answer'] . "\">";
                                echo "<label class=\"correct\" for=\"question$i.answer$rownr\">" . $row['answer'] . "</label><br>";
                            }
                            //user did not tick and is wrong -> good
                            else {
                                echo "<input disabled class=\"wrong\" type=\"checkbox\" name=\"question$i.answer$rownr\" value=\"" . $row['answer'] . "\">";
                                echo "<label class=\"wrong\" for=\"question$i.answer$rownr\">" . $row['answer'] . "</label><br>";
                            }
                        }
                        $rownr++;
                    }
                }
                //new line for next question
                echo "<br>";
                if ($incorrect == true) {
                    $nrofincorrect++;
                }
            }
            //calculate
            echo "You got " . ($nrofquestions - $nrofincorrect) . " of $nrofquestions questions correct! Good job! <br>";

            if (((($nrofquestions - $nrofincorrect) / $nrofquestions) * 100) < 0) {
                $score = 0;
            } else {
                $score = ((($nrofquestions - $nrofincorrect) / $nrofquestions) * 100);
            }
            echo "You got a score of $score%";
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