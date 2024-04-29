<?php
include "../model/config.php";

if (isset($_POST["btn-edit-access"])) {
    $user_id = $_POST["user_id"];
    $username_creator = $_POST["username_creator"];
    $access = isset($_POST["access"]) ? $_POST["access"] : [];

    // conversion access value
    $access_str = implode(",", $access);

    if (empty($errors)) {
        $query = "UPDATE `user` SET access = '$access_str', user_updated_by = '$username_creator', user_updated_at = NOW() WHERE user_id = '$user_id'";

        $result = mysqli_query($mysqli, $query);

        
        $response = "profile successfully updated";
        header("Location: ../userOverview.php?successMessage=" . urlencode($response));
    } else {

    }
}

if (isset($_POST["active-button"])) {
    $user_id = $_POST["user_id"];
    $username_creator = $_POST["username_creator"];

    if (empty($errors)) {
        $query = "UPDATE `user` SET user_status = 'active', user_updated_by = '$username_creator', user_updated_at = NOW() WHERE user_id = '$user_id'";

        $result = mysqli_query($mysqli, $query);

        
        $response = "profile status successfully updated to active";
        header("Location: ../userOverview.php?successMessage=" . urlencode($response));
    } else {

    }
}

if (isset($_POST["hold-button"])) {
    $user_id = $_POST["user_id"];
    $username_creator = $_POST["username_creator"];

    if (empty($errors)) {
        $query = "UPDATE `user` SET user_status = 'hold', user_updated_by = '$username_creator', user_updated_at = NOW() WHERE user_id = '$user_id'";

        $result = mysqli_query($mysqli, $query);

        
        $response = "profile status successfully updated to hold";
        header("Location: ../userOverview.php?successMessage=" . urlencode($response));
    } else {

    }
}

if (isset($_POST["suspend-button"])) {
    $user_id = $_POST["user_id"];
    $username_creator = $_POST["username_creator"];

    if (empty($errors)) {
        $query = "UPDATE `user` SET user_status = 'suspend', user_updated_by = '$username_creator', user_updated_at = NOW() WHERE user_id = '$user_id'";

        $result = mysqli_query($mysqli, $query);

        
        $response = "profile status successfully updated to suspend";
        header("Location: ../userOverview.php?successMessage=" . urlencode($response));
    } else {

    }
}