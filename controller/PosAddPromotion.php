<?php
include "../model/config.php";

if (isset($_POST["btn-save-promotion"])) {
    $username_creator = $_POST["username_creator"];
    $promotion_name = $_POST["promotion_name"];
    $promotion_value_rupiah = $_POST["promotion_value_rupiah"];
    $promotion_value_percentage = $_POST["promotion_value_percentage"];

    // promotion rupiah logic
    $promotion_value_rupiah = preg_replace("/[^0-9]/", "", $_POST["promotion_value_rupiah"]);

    $promotion_name_check_query = "SELECT * FROM `promotion` WHERE promotion_name = '$promotion_name'";
    $promotion_name_check_result = mysqli_query($mysqli, $promotion_name_check_query);
    if (mysqli_num_rows($promotion_name_check_result) > 0) {
        $response = "Promotion name $promotion_name already exists";
        header("Location: ../posPromotion.php?errorMessage=" . urlencode($response));
        exit;
    }

    $query = "INSERT INTO `promotion` (promotion_name, promotion_value_rupiah, promotion_value_percentage, promotion_status, promotion_updated_by, promotion_created_by, promotion_updated_at, promotion_created_at)
        VALUES ('$promotion_name', '$promotion_value_rupiah', '$promotion_value_percentage', 'active', '', '$username_creator', '', NOW())";

    $result = mysqli_query($mysqli, $query);
    $response = "Promotion $promotion_name successfully added";
    header("Location: ../posPromotion.php?successMessage=" . urlencode($response));
}