<?php include('header.php'); ?>

<?php
// 1. Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('location: admin_login.php');
    exit;
}

// 2. Pagination setup
$total_records_per_page = 5;
$page_no = isset($_GET['page_no']) && is_numeric($_GET['page_no']) ? (int)$_GET['page_no'] : 1;
$offset = ($page_no - 1) * $total_records_per_page;

// 3. Count total orders
$stmt1 = $conn->prepare("SELECT COUNT(*) AS total_records FROM orders");
$stmt1->execute();
$stmt1->bind_result($total_records);
$stmt1->store_result();
$stmt1->fetch();
$total_no_of_pages = ceil($total_records / $total_records_per_page);

// 4. Fetch orders with limit
$stmt2 = $conn->prepare("SELECT * FROM orders ORDER BY order_date DESC LIMIT ?, ?");
$stmt2->bind_param("ii", $offset, $total_records_per_page);
$stmt2->execute();
$orders = $stmt2->get_result();
?>

<div class="container-fluid">
  <div class="row" style="min-height: 100vh;">
    <!-- Sidebar -->
    <?php include('sidemenu.php'); ?>

    <!-- Main content -->
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
      </div>
      <h2>Orders</h2>
      <?php if (isset($_GET['order_updated'])): ?>
        <div class="alert alert-success text-center"><?php echo $_GET['order_updated']; ?></div>
      <?php endif; ?>
      <?php if (isset($_GET['order_failed'])): ?>
        <div class="alert alert-danger text-center"><?php echo $_GET['order_failed']; ?></div>
      <?php endif; ?>

      <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table align-middle table-hover table-striped">
              <thead>
                <tr>
                  <th>Order Id</th>
                  <th>Status</th>
                  <th>User Id</th>
                  <th>Date</th>
                  <th>Phone</th>
                  <th>Address</th>
                  <th>Edit</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($order = $orders->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo $order['order_id']; ?></td>
                    <td>
                      <?php if ($order['order_status'] == 'paid'): ?>
                        <span class="badge bg-success">Paid</span>
                      <?php elseif ($order['order_status'] == 'shipped'): ?>
                        <span class="badge bg-primary">Shipped</span>
                      <?php else: ?>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($order['order_status']); ?></span>
                      <?php endif; ?>
                    </td>
                    <td><?php echo $order['user_id']; ?></td>
                    <td><?php echo $order['order_date']; ?></td>
                    <td><?php echo $order['user_phone']; ?></td>
                    <td><?php echo $order['user_address']; ?></td>
                    <td>
                      <a class="btn btn-primary btn-sm" href="edit_order.php?order_id=<?php echo $order['order_id']; ?>">Edit</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-3">
              <li class="page-item <?php if ($page_no <= 1) echo 'disabled'; ?>">
                <a class="page-link" href="<?php if ($page_no <= 1) echo '#'; else echo '?page_no='.($page_no - 1); ?>">Previous</a>
              </li>

              <?php for ($i = 1; $i <= $total_no_of_pages; $i++): ?>
                <li class="page-item <?php if ($page_no == $i) echo 'active'; ?>">
                  <a class="page-link" href="?page_no=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
              <?php endfor; ?>

              <li class="page-item <?php if ($page_no >= $total_no_of_pages) echo 'disabled'; ?>">
                <a class="page-link" href="<?php if ($page_no >= $total_no_of_pages) echo '#'; else echo '?page_no='.($page_no + 1); ?>">Next</a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </main>
  </div>
</div>

<?php include('footer.php'); ?>
