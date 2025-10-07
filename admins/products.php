<?php include('header.php'); ?>

<?php
// 1. Check admin login
if (!isset($_SESSION['admin_logged_in'])) {
    header('location: admin_login.php');
    exit();
}

// 2. Pagination setup
$total_records_per_page = 5;
$page_no = isset($_GET['page_no']) && is_numeric($_GET['page_no']) ? (int)$_GET['page_no'] : 1;
$offset = ($page_no - 1) * $total_records_per_page;

// 3. Count total products
$stmt1 = $conn->prepare("SELECT COUNT(*) AS total_records FROM products");
$stmt1->execute();
$stmt1->bind_result($total_records);
$stmt1->store_result();
$stmt1->fetch();
$total_no_of_pages = ceil($total_records / $total_records_per_page);

// 4. Fetch product data
$stmt2 = $conn->prepare("SELECT * FROM products LIMIT ?, ?");
$stmt2->bind_param("ii", $offset, $total_records_per_page);
$stmt2->execute();
$products = $stmt2->get_result();
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

      <h2>Products</h2>

      <!-- Success / Error Messages -->
      <?php 
      $alerts = [
        'edit_success_message' => 'success',
        'edit_failure_message' => 'danger',
        'deleted_successfully' => 'success',
        'deleted_failure' => 'danger',
        'product_created' => 'success',
        'product_failed' => 'danger',
        'images_updated' => 'success',
        'images_failed' => 'danger'
      ];
      foreach ($alerts as $key => $type):
        if (isset($_GET[$key])): ?>
          <div class="alert alert-<?php echo $type; ?> text-center">
            <?php echo htmlspecialchars($_GET[$key]); ?>
          </div>
      <?php endif; endforeach; ?>

      <!-- Table -->
      <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table align-middle table-hover table-striped">
              <thead class="table-light">
                <tr>
                  <th>Product Id</th>
                  <th>Image</th>
                  <th>Name</th>
                  <th>Price</th>
                  <th>Offer</th>
                  <th>Category</th>
                  <th>Color</th>
                  <th>Edit Images</th>
                  <th>Edit</th>
                  <th>Delete</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($product = $products->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo $product['product_id']; ?></td>
                    <td><img src="<?php echo "../assets/imgs/" . htmlspecialchars($product['product_image']); ?>" 
                             alt="Product" style="width: 70px; height: 70px; border-radius: 8px;"></td>
                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                    <td>$<?php echo number_format($product['product_price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($product['product_special_offer']); ?>%</td>
                    <td><?php echo htmlspecialchars($product['product_category']); ?></td>
                    <td><?php echo htmlspecialchars($product['product_color']); ?></td>
                    <td>
                      <a class="btn btn-warning btn-sm" 
                         href="edit_images.php?product_id=<?php echo $product['product_id']; ?>&product_name=<?php echo urlencode($product['product_name']); ?>">
                         Edit Images
                      </a>
                    </td>
                    <td>
                      <a class="btn btn-primary btn-sm" 
                         href="edit_product.php?product_id=<?php echo $product['product_id']; ?>">
                         Edit
                      </a>
                    </td>
                    <td>
                      <a class="btn btn-danger btn-sm" 
                         href="delete_product.php?product_id=<?php echo $product['product_id']; ?>"
                         onclick="return confirm('Are you sure you want to delete this product?');">
                         Delete
                      </a>
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
