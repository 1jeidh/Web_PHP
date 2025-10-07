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
    $product_name = trim(preg_replace('/\s+/', '_', $_POST['product_name'])); // sanitize spaces

    $upload_dir = "../assets/imgs/";

    // Ensure folder exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $query = $conn->prepare("SELECT product_image, product_image2, product_image3, product_image4 FROM products WHERE product_id=?");
    $query->bind_param("i", $product_id);
    $query->execute();
    $query->bind_result($old1, $old2, $old3, $old4);
    $query->fetch();
    $query->close();

    $old_images = [$old1, $old2, $old3, $old4];
    $new_images = [];

    for ($i = 1; $i <= 4; $i++) {
        if (isset($_FILES["image$i"]) && $_FILES["image$i"]['error'] == 0) {
            $tmp_name = $_FILES["image$i"]['tmp_name'];

            // If old file exists → overwrite it
            if (!empty($old_images[$i - 1]) && file_exists($upload_dir . $old_images[$i - 1])) {
                $target_file = $upload_dir . $old_images[$i - 1];
            } else {
                // fallback: generate new one only if no existing file
                $target_file = $upload_dir . "{$product_name}_{$product_id}_{$i}.jpeg";
            }

            // Move (overwrite)
            if (!move_uploaded_file($tmp_name, $target_file)) {
                header("location: edit_images.php?product_id=$product_id&product_name=$product_name&error=Failed+to+upload+image+$i");
                exit;
            }

            // Store only filename
            $new_images[] = basename($target_file);
        } else {
            // keep old image if not replaced
            $new_images[] = $old_images[$i - 1];
        }
    }

    $stmt = $conn->prepare("UPDATE products 
        SET product_image=?, product_image2=?, product_image3=?, product_image4=? 
        WHERE product_id=?");

    $stmt->bind_param(
        "ssssi",
        $new_images[0],
        $new_images[1],
        $new_images[2],
        $new_images[3],
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