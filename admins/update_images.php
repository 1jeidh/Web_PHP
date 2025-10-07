<?php
include('../server/connection.php');
session_start();

// Check login
if (!isset($_SESSION['admin_logged_in'])) {
    header('location: login.php');
    exit;
}

if (isset($_POST['update_images'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];

    // Ensure uploads folder exists
    $upload_dir = "../assets/imgs/";

    $image_names = [];
    for ($i = 1; $i <= 4; $i++) {
        if (!isset($_FILES["image$i"]) || $_FILES["image$i"]['error'] != 0) {
            header("location: edit_images.php?product_id=$product_id&product_name=$product_name&error=Missing+file+$i");
            exit;
        }

        $tmp_name = $_FILES["image$i"]['tmp_name'];
        $image_name = "{$product_name}{$i}.jpeg";

        // Move uploaded file
        if (!move_uploaded_file($tmp_name, $upload_dir . $image_name)) {
            header("location: edit_images.php?product_id=$product_id&product_name=$product_name&error=Failed+to+upload+$image_name");
            exit;
        }

        $image_names[] = $image_name;
    }

    // Update database
    $stmt = $conn->prepare("UPDATE products 
        SET product_image=?, product_image2=?, product_image3=?, product_image4=? 
        WHERE product_id=?");

    $stmt->bind_param("ssssi", 
        $image_names[0], 
        $image_names[1], 
        $image_names[2], 
        $image_names[3], 
        $product_id
    );

    if ($stmt->execute()) {
        header('location: products.php?images_updated=Images+updated+successfully');
        exit;
    } else {
        header('location: products.php?images_failed=Database+update+failed');
        exit;
    }
} else {
    header('location: products.php');
    exit;
}
?>
