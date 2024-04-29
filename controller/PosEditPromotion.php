<?php
include "../model/config.php";

// active
if (isset($_POST["btn-active"])) {
    $promotion_id = $_POST["promotion_id"];
    $promotion_name = $_POST["promotion_name"];
    $username_creator = $_POST["username_creator"];

    $query = "UPDATE `promotion` SET promotion_status = 'active', promotion_updated_by = '$username_creator', promotion_updated_at = NOW() WHERE promotion_id = '$promotion_id'";

    $result = mysqli_query($mysqli, $query);

    $response = "Promotion $promotion_name successfully active";
    header("Location: ../posPromotion.php?promotion_id=$promotion_id&successMessage=" . urlencode($response));
}

// hold
if (isset($_POST["btn-hold"])) {
    $promotion_id = $_POST["promotion_id"];
    $promotion_name = $_POST["promotion_name"];
    $username_creator = $_POST["username_creator"];

    $query = "UPDATE `promotion` SET promotion_status = 'hold', promotion_updated_by = '$username_creator', promotion_updated_at = NOW() WHERE promotion_id = '$promotion_id'";

    $result = mysqli_query($mysqli, $query);

    $response = "Promotion $promotion_name successfully hold";
    header("Location: ../posPromotion.php?promotion_id=$promotion_id&successMessage=" . urlencode($response));
}

// delete
if (isset($_POST["btn-delete"])) {
    $promotion_id = $_POST["promotion_id"];
    $promotion_name = $_POST["promotion_name"];

    $query = "DELETE FROM `promotion` WHERE promotion_id = '$promotion_id'";

    $result = mysqli_query($mysqli, $query);

    $response = "Promotion $promotion_name successfully deleted";
    header("Location: ../posPromotion.php?promotion_id=$promotion_id&successMessage=" . urlencode($response));
}