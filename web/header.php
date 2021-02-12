<?php
//print_r($_SERVER);

$BASE = 'http://' . $_SERVER['HTTP_HOST'] . '/web/';
$PATH_OVERVIEW = $BASE . "overview.php";
$PATH_INDEX = $BASE . "index.php";
$PATH_ADMIN = $BASE . "admin/index.php";


echo "<nav>";
echo "<a href=\"$PATH_OVERVIEW\">"."Question Overview"."</a>";
echo "<a href=\"$PATH_INDEX\">"."Home"."</a>";
echo "<a href=\"$PATH_ADMIN\">"."Admin Panel"."</a>";
echo "</nav>";
?>