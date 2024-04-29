<?php
include "../model/config.php";

if (isset($_POST["btn-edit-member"])) {
    $username_creator = $_POST["username_creator"];
    $member_email_old = $_POST["member_email_old"];
    $member_phone_old = $_POST["member_phone_old"];
    $member_id = $_POST["member_id"];
    $member_first_name = $_POST["member_first_name"];
    $member_last_name = $_POST["member_last_name"];
    $member_phone = $_POST["member_phone"];
    $member_email = $_POST["member_email"];
    $member_address = $_POST["member_address"];

    // user_phone logic
    $member_phone = preg_replace("/[^0-9]/", "", $_POST["member_phone"]);

    // Member email validation
    $new_member_email = mysqli_real_escape_string($mysqli, $member_email);
    $member_email_check_query = "SELECT * FROM `member` WHERE member_email = '$new_member_email' AND member_email != '$member_email_old'";
    $member_email_check_result = mysqli_query($mysqli, $member_email_check_query);
    if (mysqli_num_rows($member_email_check_result) > 0) {
        $errors['memberEmailValidation'] = "Member email has already been taken.";
    }

    // Member phone validation
    $new_member_phone = mysqli_real_escape_string($mysqli, $member_phone);
    $member_phone_check_query = "SELECT * FROM `member` WHERE member_phone = '$new_member_phone' AND member_phone != '$member_phone_old'";
    $member_phone_check_result = mysqli_query($mysqli, $member_phone_check_query);
    if (mysqli_num_rows($member_phone_check_result) > 0) {
        $errors['memberPhoneValidation'] = "Member phone has already been taken.";
    }

    if (empty($errors)) {
        $query = "UPDATE `member` SET member_first_name = '$member_first_name', member_last_name = '$member_last_name', member_phone = '$member_phone', member_email = '$member_email', member_address = '$member_address', member_updated_by = '$username_creator', member_updated_at = NOW() WHERE member_id = '$member_id'";

        $result = mysqli_query($mysqli, $query);
        
        $response = "Member $member_first_name $member_last_name successfully updated";
        header("Location: ../customerProfile.php?member_id=$member_id&successMessage=" . urlencode($response));
    } else {
        $queryString = http_build_query($errors);
        header("Location: ../customerProfile.php?member_id=$member_id&$queryString");
        exit;
    }
}

// active
if (isset($_POST["btn-active"])) {
    $member_id = $_POST["member_id"];
    $member_first_name = $_POST["member_first_name"];
    $member_last_name = $_POST["member_last_name"];
    $username_creator = $_POST["username_creator"];

    $query = "UPDATE `member` SET member_status = 'active', member_updated_by = '$username_creator', member_updated_at = NOW() WHERE member_id = '$member_id'";

    $result = mysqli_query($mysqli, $query);

    $response = "Member $member_first_name $member_last_name successfully active";
    header("Location: ../customerProfile.php?member_id=$member_id&successMessage=" . urlencode($response));
}

// hold
if (isset($_POST["btn-hold"])) {
    $member_id = $_POST["member_id"];
    $member_first_name = $_POST["member_first_name"];
    $member_last_name = $_POST["member_last_name"];
    $username_creator = $_POST["username_creator"];

    $query = "UPDATE `member` SET member_status = 'hold', member_updated_by = '$username_creator', member_updated_at = NOW() WHERE member_id = '$member_id'";

    $result = mysqli_query($mysqli, $query);

    $response = "Member $member_first_name $member_last_name successfully hold";
    header("Location: ../customerProfile.php?member_id=$member_id&successMessage=" . urlencode($response));
}
