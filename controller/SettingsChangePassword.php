<?php
// ======== Update User Status Hold ======== //
include "../model/config.php";

// ======== Update User Password ======== //
if (isset($_POST["btn-password-user"])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $current_password = $_POST['currentPassword'];
    $new_password = $_POST['newPassword'];
    $confirm_password = $_POST['confirmPassword'];

    if ($new_password != $confirm_password) {
        $response = "Password does not match";
        header("Location: ../settings.php?errorMessage=" . urlencode($response));
    } else {
        $query = "SELECT * FROM user WHERE user_id = '$user_id'";
        $result = mysqli_query($mysqli, $query);

        if ($result) {
            $user = mysqli_fetch_assoc($result);

            if ($user && password_verify($current_password, $user['user_password']) == true) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                $query = "UPDATE `user` SET user_password = '$hashed_password', user_updated_by = '$username', user_updated_at = NOW() WHERE user_id = '$user_id'";

                $result = mysqli_query($mysqli, $query);

                $response = "password successfully updated";
                header("Location: ../settings.php?successMessage=" . urlencode($response));
                exit;
            } else {
                $response = "Current password is incorrect";
                header("Location: ../settings.php?errorMessage=" . urlencode($response));
            }
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
}
