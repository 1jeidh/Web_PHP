<?php
session_start();
include('layouts/header.php');

if (isset($_POST['order_pay_btn'])) {
    $order_status = $_POST['order_status'];
    $order_total_price = $_POST['order_total_price'];
}

// Determine amount
$amount = 0.00;
if (!empty($_SESSION['total'])) {
    $amount = floatval($_SESSION['total']);
} elseif (!empty($_POST['order_total_price'])) {
    $amount = floatval($_POST['order_total_price']);
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
            <p>Total payment: $<?php echo htmlspecialchars($amount_formatted); ?></p>
            <div id="paypal-button-container"></div>
        <?php } else { ?>
            <p>You don't have an order.</p>
        <?php } ?>
    </div>
</section>

<script src="https://www.paypal.com/sdk/js?client-id=AWoE0aD_YIHD4fEWKjr_NSwjxIq3J7pQcloErraIRnNrtywL-EOft1xe3Mw3si0XnUsCGQ1be01ovzlj&currency=USD"></script>

<script>
const amount = "<?php echo $amount_formatted; ?>";
console.log("Initializing PayPal with amount:", amount);

paypal.Buttons({
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                amount: { value: amount }
            }]
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
            .then(serverResponse => {
                console.log('Server response:', serverResponse);
                if (serverResponse.includes("paid")) {
                    window.location.href = 'thank_you.php';
                } else {
                    alert("Payment processed but update failed:\n" + serverResponse);
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                alert("Error communicating with server.");
            });
        });
    },

    onError: function(err) {
        console.error("PayPal error:", err);
        alert("Something went wrong — check the console for details.");
    }
}).render('#paypal-button-container');
</script>

<?php include('layouts/footer.php'); ?>
