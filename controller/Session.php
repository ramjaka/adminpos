<?php
require_once('model/config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
}

$user_id = $_SESSION['user_id'];

$query = "SELECT username, first_name, last_name, user_status FROM user WHERE user_id = '$user_id'";
$result = mysqli_query($mysqli, $query);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        $user_data = mysqli_fetch_assoc($result);

        if ($user_data['user_status'] === 'suspended') {
            session_unset();
            session_destroy();
            header('Location: signin.php?status=suspend');
            exit();
        } else {
            $username = $user_data['username'];
            $first_name = $user_data['first_name'];
            $last_name = $user_data['last_name'];
        }
    } else {
        // Pengguna tidak ditemukan
        echo "User not found.";
        header('Location: signin.php');
    }
} else {
    // Error dalam eksekusi query
    echo "Something went wrong. Please try again later.";
    exit();
}