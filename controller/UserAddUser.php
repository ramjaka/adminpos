<?php
include "../model/config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $username = $_POST["username"];
    $username_creator = $_POST["username_creator"];
    $phone = $_POST["phone"];
    $access = isset($_POST["access"]) ? $_POST["access"] : [];
    $user_password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // user_phone logic
    $phone = preg_replace("/[^0-9]/", "", $_POST["phone"]);

    // Firt name validation
    if (empty($first_name)) {
        $errors['firstNameValidation'] = "First name is required.";
    }

    // Usrename validation
    if (empty($username)) {
        $errors['usernameValidation'] = "Username is required.";
    } elseif (strlen($username) < 8) {
        $errors['usernameValidation'] = "Username must be at least 8 characters long.";
    } else {
        $username_check_query = "SELECT * FROM `user` WHERE username = '$username'";
        $username_check_result = mysqli_query($mysqli, $username_check_query);
        if (mysqli_num_rows($username_check_result) > 0) {
            $errors['usernameValidation'] = "Username has already been taken.";
        }
    }

    // Phone number validation
    if (empty($phone)) {
        $errors['phoneValidation'] = "Phone number is required.";
    } elseif (strlen($phone) < 8) {
        $errors['phoneValidation'] = "Phone number must be at least 8 characters long.";
    } else {
        $phone_check_query = "SELECT * FROM `user` WHERE phone = '$phone'";
        $phone_check_result = mysqli_query($mysqli, $phone_check_query);
        if (mysqli_num_rows($phone_check_result) > 0) {
            $errors['phoneValidation'] = "Phone number has already been taken.";
        }
    }

    // Access validation
    if (empty($access)) {
        $errors['accessValidation'] = "Access is required.";
    }

    // conversion access value
    $access_str = implode(",", $access);

    if (empty($errors)) {
        // user_id logic
        $user_id_prefix = strtoupper(substr($first_name, 0, 1));
        $user_id_date_suffix = date('dmY');
        $count_query = "SELECT COUNT(*) AS total FROM `user` WHERE user_created_at >= CURDATE()";
        $count_result = mysqli_query($mysqli, $count_query);
        $count_row = mysqli_fetch_assoc($count_result);
        $count = $count_row['total'];
        $count_formatted = sprintf("%03d", $count + 1);
        $user_id = $user_id_prefix . $user_id_date_suffix . $count_formatted . '_USER';

        // Insert data into the user
        $query = "INSERT INTO `user` (user_id, first_name, last_name, username, phone, access, user_password, user_status, user_updated_by, user_created_by, user_updated_at, user_created_at)
        VALUES ('$user_id', '$first_name', '$last_name', '$username', '$phone', '$access_str', '$user_password', 'active', '', '$username_creator', '', NOW())";

        $result = mysqli_query($mysqli, $query);
        
        $response = "$first_name $last_name profile successfully added";
        header("Location: ../userOverview.php?successMessage=" . urlencode($response));
    } else {
        $queryString = http_build_query($errors);
        header("Location: ../userAddUser.php?$queryString");
        exit;
    }
}