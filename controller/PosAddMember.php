<?php
include "../model/config.php";

if (isset($_POST["btn-save-member"])) {
    $username_creator = $_POST["username_creator"];
    $member_first_name = $_POST["member_first_name"];
    $member_last_name = $_POST["member_last_name"];
    $member_phone = $_POST["member_phone"];
    $member_email = $_POST["member_email"];
    $member_address = $_POST["member_address"];

    // user_phone logic
    $member_phone = preg_replace("/[^0-9]/", "", $_POST["member_phone"]);

    $member_email_check_query = "SELECT * FROM `member` WHERE member_email = '$member_email'";
    $member_email_check_result = mysqli_query($mysqli, $member_email_check_query);
    if (mysqli_num_rows($member_email_check_result) > 0) {
        $response = "Member email $member_email already exists";
        header("Location: ../PosCRM.php?errorMessage=" . urlencode($response));
        exit;
    }

    $member_phone_check_query = "SELECT * FROM `member` WHERE member_phone = '$member_phone'";
    $member_phone_check_result = mysqli_query($mysqli, $member_phone_check_query);
    if (mysqli_num_rows($member_phone_check_result) > 0) {
        $response = "Member phone $member_phone already exists";
        header("Location: ../PosCRM.php?errorMessage=" . urlencode($response));
        exit;
    }

    // member_id logic
    $member_id_prefix = strtoupper(substr($member_first_name, 0, 1));
    $member_id_date_suffix = date('dmY');
    $count_query = "SELECT COUNT(*) AS total FROM `member` WHERE member_created_at >= CURDATE()";
    $count_result = mysqli_query($mysqli, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);
    $count = $count_row['total'];
    $count_formatted = sprintf("%03d", $count + 1);
    $member_id = $member_id_prefix . $member_id_date_suffix . $count_formatted . '_MEMBER';

    $query = "INSERT INTO `member` (member_id, member_first_name, member_last_name, member_phone, member_email, member_address, member_status, member_updated_by, member_created_by, member_updated_at, member_created_at)
        VALUES ('$member_id', '$member_first_name', '$member_last_name', '$member_phone', '$member_email', '$member_address', 'active', '', '$username_creator', '', NOW())";

    $result = mysqli_query($mysqli, $query);
    $response = "Member $member_first_name $member_last_name successfully added";
    header("Location: ../posCRM.php?successMessage=" . urlencode($response));
}