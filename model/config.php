<?php
// Connection server
$databaseHost = 'localhost';
$databaseName = 'ramjaka';
$databaseUsername = 'YouAreJ4k$';
$databasePassword = '7E2RpxkVk0Yt';

// timezone
date_default_timezone_set("Asia/Jakarta");

$mysqli = mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName);
