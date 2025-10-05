<?php include('header.php'); ?>

<?php
// Ensure admin logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('location: admin_login.php');
    exit;
}

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order_result = $stmt->get_result();

    // If no record found
    if ($order_result->num_rows === 0) {
        header('location: index.php?order_failed=Order not found');
        exit;
    }

    $order = $order_result->fetch_assoc();
}

else if (isset($_POST['edit_order'])) {
    $order_status = $_POST['order_status'];
    $order_id = intval($_POST['order_id']);

    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    $stmt->bind_param('si', $order_status, $order_id);

    if ($stmt->execute()) {
        header('location: index.php?order_updated=Order updated successfully');
        exit;
    } else {
        header('location: index.php?order_failed=Error updating order');
        exit;
    }
}

else {
    header('location: index.php');
    exit;
}
?>

<div class="container-fluid">
  <div class="row" style="min-height: 100vh;">

    <?php include('sidemenu.php'); ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Order</h1>
      </div>

      <div class="card shadow p-4">
        <form method="POST" action="edit_order.php">
          <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">

          <div class="mb-3">
            <label class="form-label"><strong>Order ID:</strong></label>
            <p><?php echo $order['order_id']; ?></p>
          </div>

          <div class="mb-3">
            <label class="form-label"><strong>Total Cost:</strong></label>
            <p><?php echo number_format($order['order_cost'], 2); ?> USD</p>
          </div>

          <div class="mb-3">
            <label class="form-label"><strong>Order Date:</strong></label>
            <p><?php echo $order['order_date']; ?></p>
          </div>

          <div class="mb-3">
            <label class="form-label"><strong>Order Status:</strong></label>
            <select class="form-select" required name="order_status">
              <option value="not paid" <?php if($order['order_status']=='not paid') echo 'selected'; ?>>Not Paid</option>
              <option value="paid" <?php if($order['order_status']=='paid') echo 'selected'; ?>>Paid</option>
              <option value="shipped" <?php if($order['order_status']=='shipped') echo 'selected'; ?>>Shipped</option>
              <option value="delivered" <?php if($order['order_status']=='delivered') echo 'selected'; ?>>Delivered</option>
            </select>
          </div>

          <div class="mt-4">
            <button type="submit" name="edit_order" class="btn btn-primary">Save Changes</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </main>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script>
    feather.replace()
</script>

