<?php
// Connection server
$databaseHost = 'localhost';
$databaseName = 'ramjaka';
$databaseUsername = 'root';
$databasePassword = '231298';

// timezone
date_default_timezone_set("Asia/Jakarta");

$mysqli = mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName);