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