<?php
// Start the session
session_start();
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
}
if (isset($_POST['uname']) && isset($_POST['psw'])) {
    $_SESSION['uname'] = $_POST['uname'];
    $_SESSION['psw'] = $_POST['psw'];
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

    <?php
    if (!isset($_SESSION['uname']) && !isset($_SESSION['psw'])) {
        print_r($_SESSION);
        echo '<div>';
        echo '<form action=';
        echo $_SERVER['PHP_SELF'];
        echo ' method="POST" class="login">';

        echo '<div class="imgcontainer">';
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

                echo '<img src="https://eu.ui-avatars.com/api/?background=random&name=' . $_SESSION['uname'] . '" alt="Avatar" class="smallavatar">';
                echo '</div>';

                echo "Welcome " . $row['firstname'];
            }
        }
    }
    ?>

    <script src="" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
</body>

</html>