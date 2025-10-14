<?php
session_start();
include('layouts/header.php');

// Ưu tiên lấy từ POST (nếu bấm "Pay now" ở order_details.php)
$amount = 0.00;
$order_id = null;

if (isset($_POST['order_pay_btn'])) {
    $amount = floatval($_POST['order_total_price'] ?? 0);
    $order_id = intval($_POST['order_id'] ?? 0);

    // Lưu tạm vào session để complete_payment.php biết order nào cần update
    $_SESSION['recent_order_total'] = $amount;
    $_SESSION['recent_order_id'] = $order_id;
} elseif (!empty($_SESSION['recent_order_total'])) {
    // Nếu vừa mới checkout xong
    $amount = floatval($_SESSION['recent_order_total']);
}

$amount_formatted = number_format($amount, 2, '.', '');
?>

<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="font-weight-bold">Payment</h2>
        <hr class="mx-auto">
    </div>

    <div class="mx-auto container text-center">
        <?php if ($amount > 0) { ?>
            <p>Total payment: <strong>$<?php echo htmlspecialchars($amount_formatted); ?></strong></p>
            <div id="paypal-button-container" class="mt-4"></div>
        <?php } else { ?>
            <p>You don't have an order to pay.</p>
        <?php } ?>
    </div>
</section>

<!-- PayPal SDK -->
<script src="https://www.sandbox.paypal.com/sdk/js?client-id=AWoE0aD_YIHD4fEWKjr_NSwjxIq3J7pQcloErraIRnNrtywL-EOft1xe3Mw3si0XnUsCGQ1be01ovzlj&currency=USD&intent=capture"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const amount = "<?php echo $amount_formatted; ?>";
    console.log("PayPal initialized with amount:", amount);

    const container = document.getElementById("paypal-button-container");
    if (!container) {
        console.error("paypal-button-container not found");
        return;
    }

    paypal.Buttons({
        style: {
            layout: 'vertical',
            color: 'gold',
            shape: 'rect',
            label: 'paypal'
        },
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{ amount: { value: amount } }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                alert('Transaction completed by ' + details.payer.name.given_name);

                return fetch('server/complete_payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include',
                    body: JSON.stringify({
                        orderID: data.orderID,
                        payerID: data.payerID,
                        paymentDetails: details
                    })
                })
                .then(res => res.text())
                .then(res => {
                    console.log('Server response:', res);
                    if (res.includes("paid")) {
                        window.location.href = 'thank_you.php';
                    } else {
                        alert("Payment processed but update failed:\n" + res);
                    }
                })
                .catch(err => {
                    console.error("Fetch error:", err);
                    alert("Error sending payment info to server.");
                });
            });
        },
        onError: function(err) {
            console.error("PayPal error:", err);
            alert("Something went wrong — check the console for details.");
        }
    }).render('#paypal-button-container');
});
</script>

<?php include('layouts/footer.php'); ?>
