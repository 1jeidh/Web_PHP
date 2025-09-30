<?php 

include('server/connection.php');

if(isset($_GET['product_id'])){
    $product_id = $_GET['product_id'];
        $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->bind_param("i",$product_id);
        $stmt->execute();
        $product = $stmt->get_result();
}else{
    header('location: index.php');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Single Product</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
        <!--Nav bar-->
        <nav class="navbar navbar-expand-lg navbar-light bg-light py-3 fixed-top">
            <div class="container-fluid">
                <img class="logos" src="assets/imgs/logo1.jpg"/>
                
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse nav-buttons" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">               
                        <li class="nav-item">
                            <a class="nav-link" href="index.html">Home</a>
                        </li>
                        <li class="nav-item" >
                            <a class="nav-link" href="shop.html">Shop</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Blog</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.html">Contact Us</a>
                        </li>
                        <li class="nav-item">
                            <a href="cart.html"><i class="fa-solid fa-bag-shopping"></i></a>
                            <a href="account.html"><i class="fa-solid fa-user"></i></a>
                        </li>                            
                    </ul>
                </div>
            </div>	
        </nav>

        <!--Single product-->
        <section class="container-fluid single-product my-5 pt-5">
            <div class="row mt-5">
                <?php while($row = $product->fetch_assoc()){?>
                    <div class="col-lg-5 col-md-6 col-sm-12">
                        <img class="img-fluid w-100 pb-1" src="assets/imgs/<?php echo $row['product_image']; ?>" id="mainImg">
                        <div class="small-img-group">
                            <div class="small-img-col">
                                <img src="assets/imgs/<?php echo $row['product_image']; ?>" width="100%" class="small-img"/>
                            </div>
                            <div class="small-img-col">
                                <img src="assets/imgs/<?php echo $row['product_image2']; ?>" width="100%" class="small-img"/>
                            </div>
                            <div class="small-img-col">
                                <img src="assets/imgs/<?php echo $row['product_image3']; ?>" width="100%" class="small-img"/>
                            </div>
                            <div class="small-img-col">
                                <img src="assets/imgs/<?php echo $row['product_image4']; ?>" width="100%" class="small-img"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <h6>Men/Shoes</h6>
                        <h3 class="py-4"><?php echo $row['product_name']; ?></h3>
                        <h2><?php echo $row['product_price']; ?></h2>
                        
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="product_image" value="<?php echo $row['product_image']; ?>"/>
                            <input type="hidden" name="product_name" value="<?php echo $row['product_name']; ?>">
                            <input type="hidden" name="product_price" value="<?php echo $row['product_price']; ?>">
                            <input type="number" name="product_quantity" value="1">
                            <button class="buy-btn" type="submit" name="add_to_cart">Add To Cart</button>
                        </form>
                        
                        <h4 class="mt-5 mb-5">Product details</h4>
                        <span>
                            <?php echo $row['product_description']; ?>
                        </span>
                    </div>
                <?php }?>
            </div>
        </section>

        <!--Related product-->
        <section id="related-products" class="my-5 pb-5">
            <div class="container text-center mt-5 py-5">
                <h3>Related Products</h3>
                <hr class="mx-auto">
            </div>
            <div class="row mx-auto container-fluid">
                <?php include('server/get_featured_products.php');?>
                <?php while($row = $featured_products->fetch_assoc()){?>
                    <div class="product text-center col-lg-3 col-md-4 col-sm-12">
                        <img class="img-fluid mb-3" src="assets/imgs/<?php echo $row['product_image']; ?>">
                        <div class="star">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
                        <h4 class="p-price">$<?php echo $row['product_price']; ?></h4>
                        <a href="single_product.php?product_id=<?php echo $row['product_id'];?>"><button class="buy-btn">Buy now</button></a>
                    </div>
                <?php }?>
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
                    <li><a href="#">men</a></li>
                    <li><a href="#">women</a></li>
                    <li><a href="#">boys</a></li>
                    <li><a href="#">girls</a></li>
                    <li><a href="#">new arrivals</a></li>
                    <li><a href="#">clothes</a></li>
                </ul>
            </div>
            
            <div class="footer-one col-lg-3 col-md-6 col-sm-12">
                <h5 class="pb-2">Contact Us</h5>
                <div>
                    <h6 class="text-uppercase">Address</h6>
                    <p>1234 Street Name, City</p>
                </div>
                <div>
                    <h6 class="text-uppercase">Phone</h6>
                    <p>123 456 7890</p>
                </div>
                <div>
                    <h6 class="text-uppercase">Email</h6>
                    <p>info@gmail.com</p>
                </div>
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
   
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <script>
        var mainImg = document.getElementById("mainImg");
        var smallImg = document.getElementsByClassName("small-img");
        for (let i = 0; i < smallImg.length; i++) {
            smallImg[i].onclick = function() {
                mainImg.src = this.src;
            }
        }
    </script>
</body>
</html>