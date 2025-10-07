<?php
include('../server/connection.php');

if (isset($_POST['create_product'])) {

    $product_name = $_POST['name'];
    $product_description = $_POST['description'];
    $product_price = $_POST['price'];
    $product_special_offer = $_POST['offer'];
    $product_category = $_POST['category'];
    $product_color = $_POST['color'];

    // Create safe and short file name base
    $base_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $product_name));
    $upload_dir = "../assets/imgs/";

    // Helper: Generate unique filename and upload
    function uploadImage($fileArray, $baseName, $suffix, $uploadDir)
    {
        if (!isset($fileArray) || $fileArray['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $ext = pathinfo($fileArray['name'], PATHINFO_EXTENSION);
        $ext = strtolower($ext ?: 'jpeg'); // Default if no ext

        // Create unique filename
        $unique_name = $baseName . "_" . uniqid() . "_$suffix." . $ext;
        $target = $uploadDir . $unique_name;

        // Move uploaded file
        if (move_uploaded_file($fileArray['tmp_name'], $target)) {
            return $unique_name;
        }
        return null;
    }

    // Upload images safely
    $image_name1 = uploadImage($_FILES['image1'], $base_name, '1', $upload_dir);
    $image_name2 = uploadImage($_FILES['image2'], $base_name, '2', $upload_dir);
    $image_name3 = uploadImage($_FILES['image3'], $base_name, '3', $upload_dir);
    $image_name4 = uploadImage($_FILES['image4'], $base_name, '4', $upload_dir);

    // Insert new product record
    $stmt = $conn->prepare("INSERT INTO products (
        product_name, product_description, product_price, product_special_offer,
        product_image, product_image2, product_image3, product_image4,
        product_category, product_color
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        'ssssssssss',
        $product_name,
        $product_description,
        $product_price,
        $product_special_offer,
        $image_name1,
        $image_name2,
        $image_name3,
        $image_name4,
        $product_category,
        $product_color
    );

    if ($stmt->execute()) {
        header('location: products.php?product_created=Product has been created successfully');
        exit;
    } else {
        header('location: products.php?product_failed=Error occurred, try again');
        exit;
    }
}
?>