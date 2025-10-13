<?php
session_start();

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if (!isset($_SESSION['total'])) $_SESSION['total'] = 0;

// Add to cart
if (isset($_POST['add_to_cart'])) {
    if (isset($_SESSION['cart'])) {
        $product_array_ids = array_column($_SESSION['cart'], "product_id");
        if (!in_array($_POST['product_id'], $product_array_ids)) {
            $product_id = $_POST['product_id'];
            $product_array = array(
                'product_id' => $_POST['product_id'],
                'product_name' => $_POST['product_name'],
                'product_price' => $_POST['product_price'],
                'product_image' => $_POST['product_image'],
                'product_quantity' => $_POST['product_quantity']
            );
            $_SESSION['cart'][$product_id] = $product_array;
        } else {
            echo '<script>alert("Product was already in the cart")</script>';
        }
    } else {
        $product_id = $_POST['product_id'];
        $product_array = array(
            'product_id' => $product_id,
            'product_name' => $_POST['product_name'],
            'product_price' => $_POST['product_price'],
            'product_image' => $_POST['product_image'],
            'product_quantity' => $_POST['product_quantity']
        );
        $_SESSION['cart'][$product_id] = $product_array;
    }
    calculateTotalCart();

// Remove product
} else if (isset($_POST['remove_product'])) {
    foreach ($_SESSION['cart'] as $key => $value) {
        if ($value['product_id'] == $_POST['product_id']) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    calculateTotalCart();

// Edit quantity
} else if (isset($_POST['edit_quantity'])) {
    $product_id = $_POST['product_id'];
    $product_quantity = (int)$_POST['product_quantity'];

    if ($product_quantity <= 0) {
        unset($_SESSION['cart'][$product_id]);
    } else {
        $_SESSION['cart'][$product_id]['product_quantity'] = $product_quantity;
    }
    calculateTotalCart();

// Checkout button clicked
} else if (isset($_POST['checkout'])) {
    // Save cart & total into a temporary checkout session
    $_SESSION['checkout_data'] = [
        'cart' => $_SESSION['cart'],
        'total' => $_SESSION['total']
    ];

    // Clear the cart immediately
    unset($_SESSION['cart']);
    unset($_SESSION['total']);

    // Redirect to checkout page
    header('Location: checkout.php');
    exit;
}

function calculateTotalCart() {
    $total = 0;
    foreach ($_SESSION['cart'] as $key => $value) {
        $product = $_SESSION['cart'][$key];
        $price = $product['product_price'];
        $quantity = $product['product_quantity'];
        $total += ($price * $quantity);
    }
    $_SESSION['total'] = $total;
}
?>

<?php include('layouts/header.php'); ?>

<!--Cart-->
<section class="cart container my-5 py-5">
    <div class="container mt-5">
        <h2 class="font-weight-bold">Your Cart</h2>
        <hr>
    </div>

    <table class="mt-5 pt-5">
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>

        <?php if (empty($_SESSION['cart'])) { ?>
            <tr>
                <td colspan="3" class="text-center py-4">Your cart is empty.</td>
            </tr>
        <?php } else { ?>
            <?php foreach ($_SESSION['cart'] as $key => $value) { ?>
                <tr>
                    <td>
                        <div class="product-info">
                            <img src="assets/imgs/<?php echo $value['product_image']; ?>" />
                            <div>
                                <p><?php echo $value['product_name']; ?></p>
                                <small><span>$</span><?php echo $value['product_price']; ?></small>
                                <form method="POST" action="cart.php">
                                    <input type="hidden" name="product_id" value="<?php echo $value['product_id']; ?>" />
                                    <input type="submit" name="remove_product" class="remove-btn" value="remove" />
                                </form>
                            </div>
                        </div>
                    </td>
                    <td>
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="product_id" value="<?php echo $value['product_id']; ?>" />
                            <input type="number" name="product_quantity" value="<?php echo $value['product_quantity']; ?>" />
                            <input type="submit" class="edit-btn" value="edit" name="edit_quantity" />
                        </form>
                    </td>
                    <td>$<?php echo $value['product_quantity'] * $value['product_price']; ?></td>
                </tr>
            <?php } ?>
        <?php } ?>
    </table>

    <div class="cart-total">
        <table>
            <tr>
                <td>Total</td>
                <td>$ <?php echo $_SESSION['total']; ?></td>
            </tr>
        </table>
    </div>

    <div class="checkout-container">
        <form method="POST" action="cart.php">
            <input type="submit" class="btn checkout-btn" value="Checkout" name="checkout" />
        </form>
    </div>
</section>

<?php include('layouts/footer.php'); ?>
