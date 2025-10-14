<?php
session_start();
include('connection.php');

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

$orderID = $data['orderID'] ?? null;
$details = $data['paymentDetails'] ?? [];

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "User not logged in";
    exit;
}

$user_id = $_SESSION['user_id'];
$local_order_id = $_SESSION['recent_order_id'] ?? null;

if (!$local_order_id) {
    echo "No pending order found in session.";
    exit;
}


$update = $conn->prepare("UPDATE orders SET order_status='paid' WHERE order_id=? AND user_id=?");
$update->bind_param('ii', $local_order_id, $user_id);
$update_success = $update->execute();
$update->close();


$insert = $conn->prepare("INSERT INTO payments (order_id, user_id, transaction_id)
                          VALUES (?, ?, ?)");
$insert->bind_param('iis', $local_order_id, $user_id, $orderID);
$insert_success = $insert->execute();
$insert->close();

if ($update_success && $insert_success) {
    echo "paid";
    unset($_SESSION['recent_order_id']);
    unset($_SESSION['recent_order_total']);
} else {
    echo "Error updating payment: " . $conn->error;
}

$conn->close();
?>
