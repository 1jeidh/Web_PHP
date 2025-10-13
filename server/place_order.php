<?php
session_start();
include('connection.php');

if (!isset($_SESSION['logged_in'])) {
    header('location: ../login.php?message=Please login/register to place an order');
    exit;
}

// Use checkout_data instead of cart
if (empty($_SESSION['checkout_data']['cart'])) {
    header('location: ../index.php?message=Your cart is empty');
    exit;
}

if (isset($_POST['place_order'])) {

    // get user info -> store in db
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $order_cost = $_SESSION['checkout_data']['total']; // ✅ use checkout_data total
    $order_status = "not paid";
    $user_id = $_SESSION['user_id'];
    $order_date = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO orders (order_cost, order_status, user_id, user_phone, user_city, user_address, order_date) 
        VALUE (?, ?, ?, ?, ?, ?, ?);");
    
    $stmt->bind_param('isiisss', $order_cost, $order_status, $user_id, $phone, $city, $address, $order_date); 

    $stmt_status = $stmt->execute();

    if (!$stmt_status) {
        header('location: ../index.php?message=Something went wrong');
        exit;
    }

    // create new order and store order info in db
    $order_id = $stmt->insert_id;

    // get products from checkout_data
    foreach ($_SESSION['checkout_data']['cart'] as $key => $product) {
        $product_id = $product['product_id'];
        $product_name = $product['product_name'];
        $product_image = $product['product_image'];
        $product_price = $product['product_price'];  
        $product_quantity = $product['product_quantity'];

        // store each item into order_items db
        $stmt1 = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_image, product_price, product_quantity, user_id, order_date)
            VALUE (?, ?, ?, ?, ?, ?, ?, ?);");
        
        $stmt1->bind_param('iissiiis', $order_id, $product_id, $product_name, $product_image, $product_price, $product_quantity, $user_id, $order_date);

        $stmt1->execute();
    }

    // ✅ remove checkout_data after order placed (prevent duplicate submission)
    unset($_SESSION['checkout_data']);

    // notify user
    header('location: ../payment.php?order_status=order placed successfully');
    exit;
}
?>
