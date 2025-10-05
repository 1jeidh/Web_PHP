<?php
session_start();
include('layouts/header.php');
?>

<section class="my-5 py-5 text-center">
  <div class="container my-5 py-4">
    <h2 class="font-weight-bold text-success">Thank You!</h2>
    <p>Your payment was successful.</p>
    <a href="account.php" class="btn btn-primary mt-3">View Your Orders</a>
  </div>
</section>

<?php include('layouts/footer.php'); ?>
