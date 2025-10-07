<?php 
include('header.php'); 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../server/connection.php');

// Check admin login
if (!isset($_SESSION['admin_logged_in'])) {
    header('location: login.php');
    exit;
}

// Validate parameters
if (!isset($_GET['product_id']) || !isset($_GET['product_name'])) {
    header('location: products.php');
    exit;
}

$product_id = $_GET['product_id'];
$product_name = $_GET['product_name'];
?>

<div class="container-fluid">
  <div class="row" style="min-height: 100vh;">
    <?php include('sidemenu.php'); ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
      </div>

      <h2>Update Product Images</h2>

      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger text-center">
          <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
      <?php endif; ?>

      <div class="card shadow-sm rounded-4 mt-4">
        <div class="card-body">
          <form id="edit-image-form" enctype="multipart/form-data" method="POST" action="update_images.php">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>"/>
            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product_name); ?>"/>

            <div class="row g-3">
              <?php for ($i = 1; $i <= 4; $i++): ?>
                <div class="col-md-6">
                  <label class="form-label">Image <?php echo $i; ?></label>
                  <input type="file" class="form-control" name="image<?php echo $i; ?>" required>
                </div>
              <?php endfor; ?>
            </div>

            <div class="mt-4 text-center">
              <button type="submit" name="update_images" class="btn btn-primary px-5">Update</button>
              <a href="products.php" class="btn btn-secondary px-4 ms-2">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</div>

<?php include('footer.php'); ?>
