<?php
include('server/connection.php');

// 1. Get current page number
$page_no = isset($_GET['page_no']) && $_GET['page_no'] != "" ? (int)$_GET['page_no'] : 1;

// 2. Pagination settings
$total_records_per_page = 9;
$offset = ($page_no - 1) * $total_records_per_page;

// 3. Get filter values from GET
$category = isset($_GET['category']) ? $_GET['category'] : '';
$price    = isset($_GET['price']) ? (int)$_GET['price'] : 1000; // default max price

// 4. Count total records with/without filter
if($category != '' || $price != 1000){
    $stmt1 = $conn->prepare("SELECT COUNT(*) As total_records 
                             FROM products 
                             WHERE product_category LIKE ? AND product_price <= ?");
    $cat_like = "%$category%";
    $stmt1->bind_param("si", $cat_like, $price);
} else {
    $stmt1 = $conn->prepare("SELECT COUNT(*) As total_records FROM products");
}

$stmt1->execute();
$stmt1->bind_result($total_records);
$stmt1->fetch();
$stmt1->close();

// 5. Calculate total pages
$total_no_of_pages = ceil($total_records / $total_records_per_page);

// 6. Fetch products with/without filter
if($category != '' || $price != 1000){
    $stmt2 = $conn->prepare("SELECT * FROM products 
                             WHERE product_category LIKE ? AND product_price <= ? 
                             LIMIT ?, ?");
    $stmt2->bind_param("siii", $cat_like, $price, $offset, $total_records_per_page);
} else {
    $stmt2 = $conn->prepare("SELECT * FROM products LIMIT ?, ?");
    $stmt2->bind_param("ii", $offset, $total_records_per_page);
}

$stmt2->execute();
$products = $stmt2->get_result();

// 7. Build query string for pagination (to keep filters in URL)
$queryString = "";
if(isset($_GET['category'])) {
    $queryString .= "&category=" . urlencode($_GET['category']);
}
if(isset($_GET['price'])) {
    $queryString .= "&price=" . urlencode($_GET['price']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
        <!--Nav bar-->
        <nav class="navbar navbar-expand-lg navbar-light bg-light py-3 fixed-top">
            <div class="container-fluid">
                <img class="logos" src="assets/imgs/logo1.jpg"/>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse nav-buttons" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">               
                        <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.html">Contact Us</a></li>
                        <li class="nav-item">
                            <a href="cart.html"><i class="fa-solid fa-bag-shopping"></i></a>
                            <a href="account.html"><i class="fa-solid fa-user"></i></a>
                        </li>                            
                    </ul>
                </div>
            </div>	
        </nav>

        <section id="shop" class="my-5 py-5">
            <div class="container mt-5 py-5">
                <div class="row">

                <!-- Sidebar (Search) -->
                <div class="col-lg-3 col-md-4 col-sm-12">
                    <p>Search Products</p>
                    <hr>
                    <form action="shop.php" method="GET">
                        <p>Category</p>
                        <div class="form-check">
                            <input class="form-check-input" value="" type="radio" name="category"
                            <?php if(!isset($_GET['category']) || $_GET['category']==''){echo 'checked';}?>>
                            <label class="form-check-label">All</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" value="shoes" type="radio" name="category"
                            <?php if(isset($_GET['category']) && $_GET['category']=='shoes'){echo 'checked';}?>>
                            <label class="form-check-label">Shoes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" value="coats" type="radio" name="category"
                            <?php if(isset($_GET['category']) && $_GET['category']=='coats'){echo 'checked';}?>>
                            <label class="form-check-label">Coats</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" value="bags" type="radio" name="category"
                            <?php if(isset($_GET['category']) && $_GET['category']=='bags'){echo 'checked';}?>>
                            <label class="form-check-label">Bags</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" value="watches" type="radio" name="category"
                            <?php if(isset($_GET['category']) && $_GET['category']=='watches'){echo 'checked';}?>>
                            <label class="form-check-label">Watches</label>
                        </div>

                        <div class="mt-4">
                            <p>Price</p>
                            <input type="range" class="form-range w-100" name="price"
                            value="<?php echo isset($_GET['price']) ? $_GET['price'] : 1000; ?>"
                            min="1" max="1000" oninput="this.nextElementSibling.innerText = 'Up to $' + this.value">
                            <span>Up to $<?php echo isset($_GET['price']) ? $_GET['price'] : 1000; ?></span>
                        </div>

                        <input type="submit" value="Search" class="btn btn-primary w-100 mt-3">
                    </form>

                </div>

                <!-- Products -->
                <div class="col-lg-9 col-md-8 col-sm-12">
                    <h3>Our Products</h3>
                    <hr>
                    <p>Here you can check out our products</p>

                    <div class="row">
                    <?php while($row = $products->fetch_assoc()) { ?>
                        <div onclick="window.location.href='single_product.php?product_id=<?php echo $row['product_id']; ?>';"
                            class="product text-center col-lg-4 col-md-6 col-sm-12">
                            <img class="img-fluid mb-3 shop-img" src="assets/imgs/<?php echo $row['product_image']; ?>"/>
                            <div class="star">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i>
                                <i class="fas fa-star"></i><i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
                            <h4 class="p-price">$<?php echo $row['product_price'];?></h4>
                            <a href="single_product.php?product_id=<?php echo $row['product_id'];?>"><button class="buy-btn">Buy now</button></a>
                        </div>
                    <?php } ?>
                    </div>

                    <!-- Pagination -->
                    <div class="row">
                        <div class="col text-center">
                            <nav aria-label="Page navigation example">
                                <ul class="pagination justify-content-center mt-5">
                                    
                                    <!-- Previous -->
                                    <li class="page-item <?php if($page_no<=1){echo 'disabled';}?>">
                                        <a class="page-link" href="<?php if($page_no <= 1){echo '#';}else{ echo "?page_no=".($page_no-1).$queryString;} ?>">Previous</a>
                                    </li>

                                    <!-- Page Numbers -->
                                    <?php for($i=1; $i<=$total_no_of_pages; $i++){ ?>
                                        <li class="page-item <?php if($page_no==$i){echo 'active';}?>">
                                            <a class="page-link" href="?page_no=<?php echo $i . $queryString; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php } ?>

                                    <!-- Next -->
                                    <li class="page-item <?php if($page_no >= $total_no_of_pages){echo 'disabled';}?>">
                                        <a class="page-link" href="<?php if($page_no >= $total_no_of_pages){echo '#';} else{ echo "?page_no=".($page_no+1).$queryString;}?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>

                </div>
            </div>
        </section>

    <!--Footer-->
    <footer class="mt-5 py-5">
        <div class="row container mx-auto pt-5">
            <div class="footer-one col-lg-3 col-md-6 col-sm-12">
                <img class="logos" src="assets/imgs/logo2.jpg"/>
                <p class="pt-3">We provide the best products for the most affordable prices</p>
            </div>
            <div class="footer-one col-lg-3 col-md-6 col-sm-12">
                <h5 class="pb-2">Featured</h5>
                <ul class="text-uppercase">
                    <li><a href="#">men</a></li><li><a href="#">women</a></li>
                    <li><a href="#">boys</a></li><li><a href="#">girls</a></li>
                    <li><a href="#">new arrivals</a></li><li><a href="#">clothes</a></li>
                </ul>
            </div>
            <div class="footer-one col-lg-3 col-md-6 col-sm-12">
                <h5 class="pb-2">Contact Us</h5>
                <div><h6 class="text-uppercase">Address</h6><p>1234 Street Name, City</p></div>
                <div><h6 class="text-uppercase">Phone</h6><p>123 456 7890</p></div>
                <div><h6 class="text-uppercase">Email</h6><p>info@gmail.com</p></div>
            </div>
            <div class="footer-one col-lg-3 col-md-6 col-sm-12">
                <h5 class="pb-2">Instagram</h5>
                <div class="row">
                    <img src="assets/imgs/fe1.jpg" class="img-fluid w-25 h-100 m-2"/>
                    <img src="assets/imgs/fe2.jpg" class="img-fluid w-25 h-100 m-2"/>
                    <img src="assets/imgs/fe3.jpg" class="img-fluid w-25 h-100 m-2"/>
                    <img src="assets/imgs/fe4.jpg" class="img-fluid w-25 h-100 m-2"/>
                    <img src="assets/imgs/clothes1.jpg" class="img-fluid w-25 h-100 m-2"/>
                </div>
            </div>
        </div>
        <div class="copyright mt-5">
            <div class="row container mx-auto">
                <div class="col-lg-3 col-md-5 col-sm-12 mb-4">
                    <img src="assets/imgs/payment.jpg"/>
                </div>
                <div class="col-lg-3 col-md-5 col-sm-12 mb-4 text-nowrap mb-2">
                    <p>eComerce @ 2025 All Right Reserved</p>
                </div>
                <div class="icons col-lg-3 col-md-5 col-sm-12 mb-4">
                    <a href="#"><i class="fa-brands fa-facebook fa-lg"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram fa-lg"></i></a>
                    <a href="#"><i class="fa-brands fa-x-twitter fa-lg"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>