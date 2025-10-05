<?php
include('server/connection.php');

// Get current page number
$page_no = isset($_GET['page_no']) && $_GET['page_no'] != "" ? (int)$_GET['page_no'] : 1;

// Pagination settings
$total_records_per_page = 9;
$offset = ($page_no - 1) * $total_records_per_page;

// Get filter values from GET
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_like = "%" . $search . "%";
$category = isset($_GET['category']) ? $_GET['category'] : '';
$price    = isset($_GET['price']) ? (int)$_GET['price'] : 1000; // default max price
$cat_like = "%$category%";


// Count total records with/without filter
if($category != '' || $price != 1000 || $search != ''){
    $stmt1 = $conn->prepare("SELECT COUNT(*) As total_records 
                             FROM products 
                             WHERE product_category LIKE ? 
                               AND product_price <= ? 
                               AND product_name LIKE ?");
    $stmt1->bind_param("sis", $cat_like, $price, $search_like);
} else {
    $stmt1 = $conn->prepare("SELECT COUNT(*) As total_records FROM products");
}

$stmt1->execute();
$stmt1->bind_result($total_records);
$stmt1->fetch();
$stmt1->close();

// Calculate total pages
$total_no_of_pages = ceil($total_records / $total_records_per_page);

// Fetch products with/without filter
if($category != '' || $price != 1000 || $search != ''){
    $stmt2 = $conn->prepare("SELECT * FROM products 
                             WHERE product_category LIKE ? 
                               AND product_price <= ? 
                               AND product_name LIKE ? 
                             LIMIT ?, ?");
    $stmt2->bind_param("sisii", $cat_like, $price, $search_like, $offset, $total_records_per_page);
} else {
    $stmt2 = $conn->prepare("SELECT * FROM products LIMIT ?, ?");
    $stmt2->bind_param("ii", $offset, $total_records_per_page);
}

$stmt2->execute();
$products = $stmt2->get_result();

// Build query string for pagination (to keep filters in URL)
$queryString = "";
if(isset($_GET['category'])) {
    $queryString .= "&category=" . urlencode($_GET['category']);
}
if(isset($_GET['price'])) {
    $queryString .= "&price=" . urlencode($_GET['price']);
}
if(isset($_GET['search']))  {
    $queryString .= "&search=" . urlencode($_GET['search']);
}
?>

<?php include('layouts/header.php'); ?>

    <section id="shop" class="my-5 py-5">
        <div class="container mt-5 py-5">
            <div class="row">

            <!-- Sidebar (Search) -->
            <div class="col-lg-3 col-md-4 col-sm-12">
                <form action="shop.php" method="GET">
                    <p>Search Products</p>
                    <hr>
                    <input type="text" class="form-control mb-3" name="search" placeholder="Search by name..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <p>Category</p>
                    <hr>
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
                        <hr>
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

<?php include('layouts/footer.php'); ?>