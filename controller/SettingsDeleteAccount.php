<?php
// ======== Update User Status Hold ======== //
include "../model/config.php";

// ======== Update User Password ======== //
if (isset($_POST["btn-suspend-user"])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];

    $query = "UPDATE `user` SET user_status = 'suspend', user_updated_by = '$username', user_updated_at = NOW() WHERE user_id = '$user_id'";
    
    $result = mysqli_query($mysqli, $query);

    $response = "password successfully suspend";
    header("Location: Signout.php");
    exit;
}