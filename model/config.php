<?php
// Connection server
$databaseHost = 'localhost';
$databaseName = 'brix8356_pos';
$databaseUsername = 'brix8356';
$databasePassword = 'Brinara.id!123';

// timezone
date_default_timezone_set("Asia/Jakarta");

$mysqli = mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName);