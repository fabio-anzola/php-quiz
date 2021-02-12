<?php
// Start the session
session_start();
include "../database.php";
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
}
if (isset($_POST['uname']) && isset($_POST['psw'])) {
    $_SESSION['uname'] = $_POST['uname'];
    $_SESSION['psw'] = $_POST['psw'];
}
if (isset($_POST['choosesubject'])) {
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
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../navigation.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
</head>

<body>

    <?php
    include "../header.php";
    ?>

    <?php
    if (isset($_POST['removesubject'])) {
        $query = 'DELETE FROM Subject WHERE `subject` = ?;';
                $statement = $db_connection->prepare($query);
                if ($statement->execute([$_POST['subject']])) {
                    
                }
    }
    if (isset($_POST['addsubject'])) {
        $query = 'SELECT `pk_subject_id` FROM Subject WHERE `subject` = ?;';
                $statement = $db_connection->prepare($query);
                if ($statement->execute([$_POST['parent']])) {
                    $row = $statement->fetch();
                    $pk_subject_id = $row['pk_subject_id'];
                }
        $query = 'INSERT INTO Subject (`subject`, `fk_pk_subject_id`) VALUES (?, ?);';
                $statement = $db_connection->prepare($query);
                if ($statement->execute([$_POST['subject'], $pk_subject_id])) {
                    
                }
    }
    if (isset($_POST['editsubject'])) {
        $query = 'UPDATE Subject SET `subject` = ? WHERE `subject` = ?;';
                $statement = $db_connection->prepare($query);
                if ($statement->execute([$_POST['new_subject'], $_POST['old_subject']])) {
                    
                }
    }
    if (isset($_POST['removequestion'])) {
        $query = 'DELETE FROM Question WHERE `question` = ?;';
                $statement = $db_connection->prepare($query);
                if ($statement->execute([$_POST['question']])) {
                    
                }
    }
    if (isset($_POST['addquestion'])) {
        $query = 'SELECT `pk_subject_id` FROM Subject WHERE `subject` = ?;';
                $statement = $db_connection->prepare($query);
                if ($statement->execute([$_SESSION['subject']])) {
                    $row = $statement->fetch();
                    $pk_subject_id = $row['pk_subject_id'];
                }
        $query = 'INSERT INTO Question (`question`, `fk_pk_subject_id`) VALUES (?, ?);';
                $statement = $db_connection->prepare($query);
                if ($statement->execute([$_POST['question'], $pk_subject_id])) {
                    
                }
        $query = 'SELECT `pk_question_id` FROM Question WHERE `question` = ?;';
                $statement = $db_connection->prepare($query);
                if ($statement->execute([$_POST['question']])) {
                    $row = $statement->fetch();
                    $pk_question_id = $row['pk_question_id'];
                }
        $query = 'INSERT INTO Answer (`answer`, `correct`, `fk_pk_question_id`) VALUES (?, ?, ?), (?, ?, ?), (?, ?, ?), (?, ?, ?);';
                $statement = $db_connection->prepare($query);
                if ($statement->execute([
                    $_POST['answer1'], (isset($_POST['ans1']) ? 1 : 0), $pk_question_id,
                    $_POST['answer2'], (isset($_POST['ans2']) ? 1 : 0), $pk_question_id,
                    $_POST['answer3'], (isset($_POST['ans3']) ? 1 : 0), $pk_question_id,
                    $_POST['answer4'], (isset($_POST['ans4']) ? 1 : 0), $pk_question_id])) {

                }
    }
    if (isset($_POST['movequestion'])) {
        $query = 'SELECT `pk_subject_id` FROM Subject WHERE `subject` = ?;';
                $statement = $db_connection->prepare($query);
                if ($statement->execute([$_POST['new_subject']])) {
                    $row = $statement->fetch();
                    $new_pk_subject_id = $row['pk_subject_id'];
                }
        $query = 'SELECT `pk_question_id` FROM Question WHERE `question` = ?;';
                $statement = $db_connection->prepare($query);
                if ($statement->execute([$_POST['question']])) {
                    $row = $statement->fetch();
                    $pk_question_id = $row['pk_question_id'];
                }
        $query = 'UPDATE Question SET `fk_pk_subject_id` = ? WHERE `pk_question_id` = ?;';
                $statement = $db_connection->prepare($query);
                if ($statement->execute([$new_pk_subject_id, $pk_question_id])) {
                    
                }
    }
    ?>

    <?php
    if (!isset($_SESSION['uname']) && !isset($_SESSION['psw'])) {
        echo '<div>';
        echo '<form action=';
        echo $_SERVER['PHP_SELF'];
        echo ' method="POST" class="login">';

        echo '<div class="imgcontainer">';
        // https://eu.ui-avatars.com
        echo '<img src="https://eu.ui-avatars.com/api/?name=?" alt="Avatar" class="avatar">';
        echo '</div>';

        echo '<div class="container">';
        echo '<label for="uname"><b>Username</b></label>';
        echo '<input type="text" placeholder="Enter Username" name="uname" required>';

        echo '<label for="psw"><b>Password</b></label>';
        echo '<input type="password" placeholder="Enter Password" name="psw" required>';

        if (isset($_SESSION['wrongcreds'])) {
            echo '<p style="color:red">Wrong credentials!</p>';
        }

        echo '<button type="submit">Login</button>';

        // echo '<label>';
        // echo '<input type="checkbox" checked="checked" name="remember"> Remember me';
        // echo '</label>';

        echo '</div>';

        echo '<div class="container" style="background-color:#f1f1f1">';
        echo '<button type="reset" class="cancelbtn">Cancel</button>';
        // echo '<span class="psw">Forgot <a href="#">password?</a></span>';
        echo '</div>';

        echo '</form>';
        echo '</div>';
    }
    ?>

    <?php
    if (isset($_SESSION['uname']) && isset($_SESSION['psw'])) {
        $query = 'SELECT * FROM `User` u 
        WHERE u.username = ? 
        AND u.passphrase = MD5(?);';
        $statement = $db_connection->prepare($query);
        if ($statement->execute([$_SESSION['uname'], $_SESSION['psw']])) {
            $row = $statement->fetch();
            //
            if (!isset($row['pk_user_id'])) {
                session_unset();
                $_SESSION['wrongcreds'] = TRUE;
                echo '<script>parent.window.location.reload(true);</script>';
            } else {
                echo '<div class="navbar">';
                echo '<form action=';
                echo $_SERVER['PHP_SELF'];
                echo ' method="POST">';
                echo '<button type="submit" name="logout" value="logmeout">Logout</button>';
                echo '</form>';
                echo "<h1><a href=\"#\">"."Adminpanel"."</a></h1>";
                echo '<img src="https://eu.ui-avatars.com/api/?background=random&name=' . $_SESSION['uname'] . '" alt="Avatar" class="smallavatar">';
                echo '</div>';

                //Subject Div
                echo "<div>";
                echo "Subjects: <br>";

                //Remove subject
                echo "<div>";
                echo "<form action=\"";
                echo $_SERVER['PHP_SELF'];
                echo "\" method=\"POST\">";
                try {
                    $query = 'SELECT * FROM `Subject`;';
                    $statement = $db_connection->prepare($query);
                    echo '<select name="subject">';
                    if ($statement->execute()) {
                        while ($row = $statement->fetch()) {
                            echo "<option>" . $row['subject'] . "</option>";
                        }
                    }
                    echo "</select>";
                } catch (PDOException $error) {
                    die('Verbindung fehlgeschlagen: ' . $error->getMessage());
                }
                echo "<input type=\"submit\" name=\"removesubject\" value=\"Remove\">";
                echo "</form>";
                echo "</div>";

                //Add subject
                echo "<div>";
                echo "<form action=\"";
                echo $_SERVER['PHP_SELF'];
                echo "\" method=\"POST\">";
                try {
                    $query = 'SELECT * FROM `Subject`;';
                    $statement = $db_connection->prepare($query);
                    echo '<select name="parent">';
                    echo '<option> No parent (NULL) </option>';
                    if ($statement->execute()) {
                        while ($row = $statement->fetch()) {
                            echo "<option>" . $row['subject'] . "</option>";
                        }
                    }
                    echo "</select>";
                } catch (PDOException $error) {
                    die('Verbindung fehlgeschlagen: ' . $error->getMessage());
                }
                echo '<input type="text" placeholder="Enter Subject name" name="subject">';
                echo "<input type=\"submit\" name=\"addsubject\" value=\"Add\">";
                echo "</form>";
                echo "</div>";

                //Rename subject
                echo "<div>";
                echo "<form action=\"";
                echo $_SERVER['PHP_SELF'];
                echo "\" method=\"POST\">";
                try {
                    $query = 'SELECT * FROM `Subject`;';
                    $statement = $db_connection->prepare($query);
                    echo '<select name="old_subject">';
                    if ($statement->execute()) {
                        while ($row = $statement->fetch()) {
                            echo "<option>" . $row['subject'] . "</option>";
                        }
                    }
                    echo "</select>";
                } catch (PDOException $error) {
                    die('Verbindung fehlgeschlagen: ' . $error->getMessage());
                }    
                echo '<input type="text" placeholder="Enter the new name" name="new_subject">';
                echo "<input type=\"submit\" name=\"editsubject\" value=\"Rename\">";
                echo "</form>";
                echo "</div>";

                //Close subject
                echo "</div>";

                //Questions Answers Section
                echo "<div>";
                echo "Questions & Answers: <br>";

                //choose subject
                echo "<div>";
                echo "<form action=\"";
                echo $_SERVER['PHP_SELF'];
                echo "\" method=\"POST\">";
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
                echo "<input type=\"submit\" name=\"choosesubject\" value=\"Select\">";
                echo "</form>";
                echo "</div>";

                //subject is choosen
                if (isset($_SESSION['subject'])) {

                    //delete question
                    echo "<div>";
                    echo "<form action=\"";
                    echo $_SERVER['PHP_SELF'];
                    echo "\" method=\"POST\">";
                    try {
                        $query = 'SELECT * FROM Subject s 
                        INNER JOIN Question q ON q.fk_pk_subject_id = s.pk_subject_id
                        WHERE `subject` = ?;';
                        $statement = $db_connection->prepare($query);
                        echo '<select name="question">';
                        if ($statement->execute([$_SESSION['subject']])) {
                            while ($row = $statement->fetch()) {
                                echo "<option>" . $row['question'] . "</option>";
                            }
                        }
                        echo "</select>";
                        echo "<input type=\"submit\" name=\"removequestion\" value=\"Remove\">";
                    } catch (PDOException $error) {
                        die('Verbindung fehlgeschlagen: ' . $error->getMessage());
                    }
                    echo "</form>";
                    echo "<div>";

                    //create question
                    echo "<div>";
                    echo "<form action=\"";
                    echo $_SERVER['PHP_SELF'];
                    echo "\" method=\"POST\">";
                    echo '<input type="text" placeholder="Enter question here" name="question">';
                    echo "<br>";
                    echo '<input type="text" placeholder="Enter answer here" name="answer1">';
                    echo "<input type=\"checkbox\" name=\"ans1\" value=\"correct\">";
                    echo "<br>";
                    echo '<input type="text" placeholder="Enter answer here" name="answer2">';
                    echo "<input type=\"checkbox\" name=\"ans2\" value=\"correct\">";
                    echo "<br>";
                    echo '<input type="text" placeholder="Enter answer here" name="answer3">';
                    echo "<input type=\"checkbox\" name=\"ans3\" value=\"correct\">";
                    echo "<br>";
                    echo '<input type="text" placeholder="Enter answer here" name="answer4">';
                    echo "<input type=\"checkbox\" name=\"ans4\" value=\"correct\">";
                    echo "<br>";
                    echo "<input type=\"submit\" name=\"addquestion\" value=\"Add\">";
                    echo "</form>";
                    echo "</div>";

                    //move question
                    //delete question
                    echo "<div>";
                    echo "<form action=\"";
                    echo $_SERVER['PHP_SELF'];
                    echo "\" method=\"POST\">";
                    try {
                        $query = 'SELECT * FROM Subject s 
                        INNER JOIN Question q ON q.fk_pk_subject_id = s.pk_subject_id
                        WHERE `subject` = ?;';
                        $statement = $db_connection->prepare($query);
                        echo '<select name="question">';
                        if ($statement->execute([$_SESSION['subject']])) {
                            while ($row = $statement->fetch()) {
                                echo "<option>" . $row['question'] . "</option>";
                            }
                        }
                        echo "</select>";

                        $query = 'SELECT * FROM `Subject`;';
                        $statement = $db_connection->prepare($query);
                        echo '<select name="new_subject">';
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

                        echo "<input type=\"submit\" name=\"movequestion\" value=\"Move\">";
                    } catch (PDOException $error) {
                        die('Verbindung fehlgeschlagen: ' . $error->getMessage());
                    }
                    echo "</form>";
                    echo "<div>";


                    echo "</div>";


                }

                //Close Questions Answers
                echo "</div>";
            }
        }
    }
    ?>

    <script src="" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
</body>

</html>