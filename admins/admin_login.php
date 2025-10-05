<?php include('header.php'); ?>

<?php
if(isset($_SESSION['admin_logged_in'])){
    header('location: index.php');
    exit;
}

if(isset($_POST['login_btn'])){
    $email = $_POST['email'];
    $password = md5($_POST['password']); // hashed password

    // Check users table for admins only
    $stmt = $conn->prepare("SELECT user_id, user_name, user_email 
                            FROM users 
                            WHERE user_email = ? AND user_password = ? AND user_role = 'admin' LIMIT 1");
    $stmt->bind_param('ss', $email, $password);

    if($stmt->execute()){
        $stmt->bind_result($user_id, $user_name, $user_email);
        $stmt->store_result();

        if($stmt->num_rows() == 1){
            $stmt->fetch();
            $_SESSION['admin_id'] = $user_id;
            $_SESSION['admin_name'] = $user_name;
            $_SESSION['admin_email'] = $user_email;
            $_SESSION['admin_logged_in'] = true;

            header('location: index.php?login_success=Logged in successfully');
            exit;
        } else {
            $error = "Invalid admin email or password";
        }
    } else {
        $error = "Something went wrong, please try again";
    }
}
?>

<div class="container" style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="col-md-6">
        <h2 class="text-center mb-4">Admin Login</h2>

        <?php if(isset($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST" action="admin_login.php">
            <div class="form-group mb-3">
                <label>Email</label>
                <input type="email" class="form-control" name="email" placeholder="Enter email" required>
            </div>
            <div class="form-group mb-3">
                <label>Password</label>
                <input type="password" class="form-control" name="password" placeholder="Enter password" required>
            </div>
            <div class="form-group text-center">
                <input type="submit" name="login_btn" class="btn btn-primary btn-block" value="Login">
            </div>
        </form>
    </div>
</div>
