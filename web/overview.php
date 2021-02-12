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

    <table class="table">
        <thead>
            <tr>
                <th scope="col">Question #</th>
                <th scope="col">Subject</th>
                <th scope="col">Question</th>
                <th scope="col">Answers</th>
            </tr>
        </thead>
        <tbody>
            <?php
            try {
                $query = 'SELECT * FROM Subject s
                INNER JOIN Question q ON q.fk_pk_subject_id = s.pk_subject_id 
                INNER JOIN Answer a ON a.fk_pk_question_id = q.pk_question_id
                WHERE a.correct = TRUE;';
                $statement = $db_connection->prepare($query);
                if ($statement->execute()) {
                    while ($row = $statement->fetch()) {
                        echo "<tr>";
                        echo "<th scope=\"row\">".$row['pk_question_id']."</th>";
                        echo "<td>".$row['subject']."</td>";
                        echo "<td>".$row['question']."</td>";
                        echo "<td>".$row['answer']."</td>";
                        echo "</tr>";
                    }
                }
            } catch (PDOException $error) {
                die('Verbindung fehlgeschlagen: ' . $error->getMessage());
            }
            ?>
        </tbody>
    </table>

    <script src="" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
</body>

</html>