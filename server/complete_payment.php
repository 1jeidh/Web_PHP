<?php
session_start();
include('connection.php'); // or 'server/connection.php' depending on your folder structure

// Get JSON from PayPal fetch request
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

$orderID = $data['orderID'] ?? null;
$details = $data['paymentDetails'] ?? [];
$amount = $details['purchase_units'][0]['amount']['value'] ?? 0;
$payment_method = "PayPal";

// Check user login
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "User not logged in";
    exit;
}
$user_id = $_SESSION['user_id'];

// Get the latest unpaid order for this user
$stmt = $conn->prepare("SELECT order_id FROM orders WHERE user_id=? AND order_status='not paid' ORDER BY order_date DESC LIMIT 1");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
    $order_id = $order['order_id'];

    //Update the order to "paid"
    $update = $conn->prepare("UPDATE orders SET order_status='paid' WHERE order_id=?");
    $update->bind_param('i', $order_id);
    $update_success = $update->execute();
    $update->close();

    //Insert into payments table
    $insert = $conn->prepare("INSERT INTO payments (order_id, user_id, transaction_id) 
                          VALUES (?, ?, ?)");
    $insert->bind_param('iis', $order_id, $user_id, $orderID);
    $insert_success = $insert->execute();


    if ($update_success && $insert_success) {
        echo "Order #$order_id marked as paid and payment recorded (Transaction: $orderID)";
    } else {
        echo "Error updating or inserting payment: " . $conn->error;
    }

} else {
    echo "No unpaid order found for this user.";
}

$stmt->close();
$conn->close();
?>
