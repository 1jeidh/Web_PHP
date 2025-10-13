<?php
session_start();
include('server/connection.php');

if (!isset($_SESSION['logged_in'])) {
    header('location: login.php');
    exit;
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('location: login.php');
    exit;
}

// Handle password change
if (isset($_POST['change_password'])) {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $user_email = $_SESSION['user_email'];

    if ($password !== $confirmPassword) {
        header('location: account.php?error=Password don\'t match');
        exit;
    } elseif (strlen($password) < 6) {
        header('location: account.php?error=Password must be at least 6 characters');
        exit;
    } else {
        $stmt = $conn->prepare("UPDATE users SET user_password=? WHERE user_email=?");
        $hashed_password = md5($password);
        $stmt->bind_param('ss', $hashed_password, $user_email);

        if ($stmt->execute()) {
            header('location: account.php?message=Password has been updated successfully');
        } else {
            header('location: account.php?error=Couldn\'t update password');
        }
        exit;
    }
}

// Orders with pagination
if (isset($_SESSION['logged_in'])) {
    $user_id = $_SESSION['user_id'];

    // Pagination setup
    $limit = 5; // orders per page
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Count total orders
    $stmt_count = $conn->prepare("SELECT COUNT(*) AS total FROM orders WHERE user_id=?");
    $stmt_count->bind_param('i', $user_id);
    $stmt_count->execute();
    $total_result = $stmt_count->get_result();
    $total_orders = $total_result->fetch_assoc()['total'];
    $total_pages = ceil($total_orders / $limit);

    // Fetch paginated orders
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY order_date DESC LIMIT ? OFFSET ?");
    $stmt->bind_param('iii', $user_id, $limit, $offset);
    $stmt->execute();
    $orders = $stmt->get_result();
}
?>

<?php include('layouts/header.php'); ?>

<!--Account-->
<section class="my-5 py-5">
    <div class="row container mx-auto">
        <div class="text-center mt-3 pt-5 col-lg-6 col-md-12 col-sm-12">
            <p class="text-center text-success"><?php if (isset($_GET['register_success'])) echo $_GET['register_success']; ?></p>
            <p class="text-center text-success"><?php if (isset($_GET['login_success'])) echo $_GET['login_success']; ?></p>
            <h3 class="font-weight-bold">Account info</h3>
            <hr class="mx-auto">
            <div class="account-info">
                <p>Name <span><?php echo $_SESSION['user_name'] ?? ''; ?></span></p>
                <p>Email <span><?php echo $_SESSION['user_email'] ?? ''; ?></span></p>
                <p><a href="#orders" id="orders-btn">Your orders</a></p>
                <p><a href="account.php?logout=1" id="logout-btn">Logout</a></p>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12">
            <form id="account-form" method="POST" action="account.php">
                <p class="text-center text-danger"><?php if (isset($_GET['error'])) echo $_GET['error']; ?></p>
                <p class="text-center text-success"><?php if (isset($_GET['message'])) echo $_GET['message']; ?></p>
                <h3>Change Password</h3>
                <hr class="mx-auto">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" class="form-control" name="confirmPassword" placeholder="Confirm Password" required>
                </div>
                <div class="form-group">
                    <input type="submit" value="Change Password" name="change_password" class="btn btn-primary" id="change-pass-btn">
                </div>
            </form>
        </div>
    </div>
</section>

<!--Orders-->
<section id="orders" class="orders container my-5">
    <div class="container mt-2">
        <h2 class="font-weight-bold text-center">Your Orders</h2>
        <hr class="mx-auto">
    </div>

    <table class="mt-5 pt-5 mx-auto text-center">
        <tr>
            <th>Order Id</th>
            <th>Order Cost</th>
            <th>Order Status</th>
            <th>Order Date</th>
            <th>Order Details</th>
        </tr>

        <?php if (isset($orders) && $orders->num_rows > 0): ?>
            <?php while ($row = $orders->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['order_id']; ?></td>
                    <td>$<?php echo $row['order_cost']; ?></td>
                    <td><?php echo ucfirst($row['order_status']); ?></td>
                    <td><?php echo $row['order_date']; ?></td>
                    <td>
                        <form method="GET" action="order_details.php">
                            <input type="hidden" name="order_status" value="<?php echo $row['order_status']; ?>">
                            <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                            <input class="btn btn-outline-primary" name="order_details_btn" type="submit" value="Details">
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center py-4">You have no orders yet.</td>
            </tr>
        <?php endif; ?>
    </table>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="d-flex justify-content-center align-items-center mt-4 gap-2 flex-wrap">
            <?php if ($page > 1): ?>
                <a class="btn btn-outline-primary" href="?page=<?php echo $page - 1; ?>">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a class="btn <?php echo $i == $page ? 'btn-primary' : 'btn-outline-primary'; ?>" href="?page=<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a class="btn btn-outline-primary" href="?page=<?php echo $page + 1; ?>">Next</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</section>

<?php include('layouts/footer.php'); ?>
