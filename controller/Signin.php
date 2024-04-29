<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once('../model/config.php');

    $username = mysqli_real_escape_string($mysqli, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM user WHERE username = '$username'";
    $result = mysqli_query($mysqli, $query);

    if ($result) {
        $user = mysqli_fetch_assoc($result);

        // Verifikasi password
        if ($user && password_verify($password, $user['user_password'])) {
            if ($user['user_status'] === 'suspend') {
                $response = "Your account is no longer active";
                header("Location: ../signin.php?errorMessage=" . urlencode($response));
            } else {
                $_SESSION['user_id'] = $user['user_id'];
                header('Location: ../index.php');
                exit();
            }
        } else {
            $response = "Your username or password is incorrect";
            header("Location: ../signin.php?errorMessage=" . urlencode($response));
        }
    } else {
        $response = "Something went wrong, please try again";
        header("Location: ../signin.php?errorMessage=" . urlencode($response));
    }
}