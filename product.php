<?php
// Include database configuration
require_once('_header.php');

// Fetch product details from the database
function fetchProductData($pdo, $categorySlug, $productSlug) {
    $stmt = $pdo->prepare(
        "SELECT p.*, c.tcat_id, c.tcat_name 
        FROM tbl_product p
        JOIN tbl_top_category c ON p.tcat_id = c.tcat_id
        WHERE c.tcat_slug = :tcat_slug AND p.p_slug = :p_slug LIMIT 1"
    );
    $stmt->execute(['tcat_slug' => $categorySlug, 'p_slug' => $productSlug]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Increment product view count
function incrementProductView($pdo, $productId) {
    $stmt = $pdo->prepare("UPDATE tbl_product SET p_total_view = p_total_view + 1 WHERE p_id = :p_id");
    $stmt->execute(['p_id' => $productId]);
}

// Fetch available sizes and colors for the product
function fetchProductVariants($pdo, $productId) {
    $stmt = $pdo->prepare(
        "SELECT s.size_name, c.color_name
        FROM tbl_product_variant pv
        JOIN tbl_size s ON pv.size_id = s.size_id
        JOIN tbl_color c ON pv.color_id = c.color_id
        WHERE pv.p_id = :p_id"
    );
    $stmt->execute(['p_id' => $productId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Calculate the average rating of a product
function calculateAverageRating($pdo, $productId) {
    $stmt = $pdo->prepare("SELECT rating FROM tbl_rating WHERE p_id = :p_id");
    $stmt->execute(['p_id' => $productId]);
    $ratings = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $totalRatings = count($ratings);
    $averageRating = $totalRatings > 0 ? array_sum($ratings) / $totalRatings : 0;
    return ['avg_rating' => $averageRating, 'total_ratings' => $totalRatings];
}

// Handle adding product to cart
function addToCart($product, $quantity) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check stock before adding
    if ($quantity > $product['p_qty']) {
        echo "<script>alert('Sorry! Only {$product['p_qty']} item(s) available in stock.');</script>";
    } else {
        $_SESSION['cart'][] = [
            'p_id' => $product['p_id'],
            'size_id' => $_POST['size_id'] ?? 0,
            'color_id' => $_POST['color_id'] ?? 0,
            'quantity' => $quantity,
            'price' => $product['p_current_price'],
            'name' => $product['p_name'],
            'photo' => $product['p_featured_photo']
        ];
        echo "<script>alert('Product added to cart successfully!');</script>";
    }
}

// Handle product review submission
function submitReview($pdo, $productId, $customerId, $comment, $rating) {
    $stmt = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id = :p_id AND cust_id = :cust_id");
    $stmt->execute(['p_id' => $productId, 'cust_id' => $customerId]);

    if ($stmt->rowCount() > 0) {
        echo "<script>alert('You have already rated this product.');</script>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO tbl_rating (p_id, cust_id, comment, rating) VALUES (:p_id, :cust_id, :comment, :rating)");
        $stmt->execute([
            'p_id' => $productId,
            'cust_id' => $customerId,
            'comment' => $comment,
            'rating' => $rating
        ]);
        echo "<script>alert('Thank you for your review!');</script>";
    }
}

// Main Code Execution
$categorySlug = $_GET['category'] ?? '';
$productSlug = $_GET['slug'] ?? '';

$product = null;
$ratingInfo = null;
$sizes = [];
$colors = [];

if ($categorySlug && $productSlug) {
    $product = fetchProductData($pdo, $categorySlug, $productSlug);

    if ($product) {
        incrementProductView($pdo, $product['p_id']);
        $variants = fetchProductVariants($pdo, $product['p_id']);
        if (!empty($variants)) {
            $sizes = array_values(array_unique(array_column($variants, 'size_name')));
            $colors = array_values(array_unique(array_column($variants, 'color_name')));
        }
        $ratingInfo = calculateAverageRating($pdo, $product['p_id']);

        // Handle add to cart submission
        if (isset($_POST['form_add_to_cart'])) {
            addToCart($product, $_POST['p_qty']);
        }

        // Handle review submission
        if (isset($_POST['form_review']) && isset($_SESSION['customer']['cust_id'])) {
            submitReview($pdo, $product['p_id'], $_SESSION['customer']['cust_id'], $_POST['comment'], $_POST['rating']);
        }
    }
}
?>
            <div id="page-content">   
                <!--Breadcrumbs-->
                <div class="breadcrumbs-wrapper text-uppercase">
                    <div class="container">
                        <div class="breadcrumbs"><a href="index.html" title="Back to the home page">Home</a><span>|</span><span class="fw-bold">Product Layout Style1</span></div>
                    </div>
                </div>
                <!--End Breadcrumbs-->

                <!--Main Content-->
                <div class="container">
                    <!--Product Content-->
                    <div class="product-single">
                        <div class="row">
                            <div class="col-lg-7 col-md-6 col-sm-12 col-12">
                                <div class="product-details-img thumb-left clearfix d-flex-wrap mb-3 mb-md-0">
                                    <div class="product-thumb">
                                        <div id="gallery" class="product-dec-slider-2 product-tab-left">
                                            <a class="slick-slide slick-cloned active">
                                                <img class="blur-up lazyload" data-src="assets/images/products/product-6-1.jpg" src="assets/images/products/product-6-1.jpg" alt="product" />
                                            </a>
                                            
                                        </div>
                                    </div>
                               
                                </div>
                            </div>

                            <div class="col-lg-5 col-md-6 col-sm-12 col-12">
                                <!-- Product Info -->
                                <div class="product-single__meta">
                                    <h1 class="product-single__title">Product Layout Style1</h1>
                                    <div class="product-single__subtitle">From Italy</div>
                                    <!-- Product Reviews -->
                                    <div class="product-review mb-2"><a class="reviewLink d-flex-center" href="#reviews"><i class="an an-star"></i><i class="an an-star mx-1"></i><i class="an an-star"></i><i class="an an-star mx-1"></i><i class="an an-star-o"></i><span class="spr-badge-caption ms-2">16 Reviews</span></a></div>
                                    <!-- End Product Reviews -->
                                    <!-- Product Info -->
                                    <div class="product-info">
                                        <p class="product-type">Vendor: <span>Bohemian France</span></p>  
                                        <p class="product-type">Product Type: <span>Floral Top</span></p>  
                                        <p class="product-sku">SKU: <span class="variant-sku">1416PT-1</span></p>
                                    </div>
                                    <!-- End Product Info -->
                                    <!-- Product Price -->
                                    <div class="product-single__price pb-1">
                                        <span class="visually-hidden">Regular price</span>
                                        <span class="product-price__sale--single">
                                            <span class="product-price-old-price">$200.00</span><span class="product-price__price product-price__sale">$225.00</span>   
                                            <span class="discount-badge"><span class="devider me-2">|</span><span>Save: </span><span class="product-single__save-amount"><span class="money">$99.00</span></span><span class="off ms-1">(<span>25</span>%)</span></span> 
                                        </span>
                                        <div class="product__policies fw-normal mt-1">Tax included.</div>
                                    </div>
                                    <!-- End Product Price -->
                                    <!-- Countdown -->
                                    <div class="countdown-text d-flex-wrap mb-3 pb-1">
                                        <label class="mb-2 mb-lg-0">Limited-Time Offer :</label>
                                        <div class="prcountdown d-flex" data-countdown="2024/10/01"></div>
                                    </div>
                                    <!-- End Countdown -->
                                    <!-- Product Sold -->
                                    <div class="orderMsg d-flex-center" data-user="23" data-time="24">
                                        <img src="assets/images/order-icon.jpg" alt="order" />
                                        <p class="m-0"><strong class="items">8</strong> Sold in last <strong class="time">14</strong> hours</p>
                                        <p id="quantity_message" class="ms-2 ps-2 border-start">Hurry! Only  <span class="items fw-bold">4</span>  left in stock.</p>
                                    </div>
                                    <!-- End Product Sold -->
                                </div>
                                <!-- End Product Info -->
                                <!-- Product Form -->
                                <form method="post" action="#" class="product-form hidedropdown">
                                    <!-- Swatches Color -->
                                    <div class="swatches-image swatch clearfix swatch-0 option1" data-option-index="0">
                                        <div class="product-form__item">
                                            <label class="label d-flex">Color:<span class="required d-none">*</span><span class="slVariant ms-1 fw-bold">Red</span></label>
                                            <ul class="swatches d-flex-wrap list-unstyled clearfix">
                                                <li data-value="Green" class="swatch-element color green available active">
                                                    <label class="swatchLbl rounded color xlarge green" title="Green"></label>
                                                    <span class="tooltip-label top">Green</span>
                                                </li>
                                                <li data-value="Peach" class="swatch-element color peach available">
                                                    <label class="swatchLbl rounded color xlarge peach" title="Peach"></label>
                                                    <span class="tooltip-label top">Peach</span>
                                                </li>
                                                <li data-value="White" class="swatch-element color white available">
                                                    <label class="swatchLbl rounded color xlarge white" title="White"></label>
                                                    <span class="tooltip-label top">White</span>
                                                </li>
                                                <li data-value="Yellow" class="swatch-element color yellow soldout">
                                                    <label class="swatchLbl rounded color xlarge yellow" title="Yellow"></label>
                                                    <span class="tooltip-label top">Yellow</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- End Swatches Color -->
                                    <!-- Swatches Size -->
                                    <div class="swatch clearfix swatch-1 option2" data-option-index="1">
                                        <div class="product-form__item">
                                            <label class="label d-flex">Size:<span class="required d-none">*</span><span class="slVariant ms-1 fw-bold">S</span></label>
                                            <ul class="swatches-size d-flex-center list-unstyled clearfix">
                                                <li data-value="S" class="swatch-element s available active">
                                                    <label class="swatchLbl rounded medium" title="S">S</label><span class="tooltip-label">S</span>
                                                </li>
                                                <li data-value="M" class="swatch-element m available">
                                                    <label class="swatchLbl rounded medium" title="M">M</label><span class="tooltip-label">M</span>
                                                </li>
                                                <li data-value="L" class="swatch-element l available">
                                                    <label class="swatchLbl rounded medium" title="L">L</label><span class="tooltip-label">L</span>
                                                </li>
                                                <li data-value="XL" class="swatch-element xl available">
                                                    <label class="swatchLbl rounded medium" title="XL">XL</label><span class="tooltip-label">XL</span>
                                                </li>
                                                <li data-value="XS" class="swatch-element xs soldout">
                                                    <label class="swatchLbl rounded medium" title="XS">XS</label><span class="tooltip-label">XS</span>
                                                </li>
                                                <li class="ms-1"><a href="#sizechart" class="sizelink link-underline text-uppercase"><i class="icon an an-ruler d-none"></i> Size Guide</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- End Swatches Size -->
                                    <!-- Product Action -->
                                    <div class="product-action w-100 clearfix">
                                        <div class="product-form__item--quantity d-flex-center mb-3">
                                            <div class="qtyField">
                                                <a class="qtyBtn minus" href="javascript:void(0);"><i class="icon an an-minus-r" aria-hidden="true"></i></a>
                                                <input type="text" name="quantity" value="1" class="product-form__input qty">
                                                <a class="qtyBtn plus" href="javascript:void(0);"><i class="icon an an-plus-r" aria-hidden="true"></i></a>
                                            </div>
                                            <div class="pro-stockLbl ms-3">
                                                <span class="d-flex-center stockLbl instock"><i class="icon an an-check-cil"></i><span> In stock</span></span>
                                                <span class="d-flex-center stockLbl preorder d-none"><i class="icon an an-clock-r"></i><span> Pre-order Now</span></span>
                                                <span class="d-flex-center stockLbl outstock d-none"><i class="icon an an-times-cil"></i> <span>Sold out</span></span>
                                                <span class="d-flex-center stockLbl lowstock d-none" data-qty="15"><i class="icon an an-exclamation-cir"></i><span> Order now, Only  <span class="items">10</span>  left!</span></span>
                                            </div>
                                        </div>
                                        <div class="product-form__item--submit">
                                            <button type="submit" name="add" class="btn rounded product-form__cart-submit"><span>Add to cart</span></button>
                                            <button type="submit" name="add" class="btn rounded product-form__sold-out d-none" disabled="disabled">Sold out</button>
                                        </div>
                                        <div class="product-form__item--buyit clearfix">
                                            <button type="button" class="btn rounded btn-outline-primary proceed-to-checkout">Buy it now</button>
                                        </div>
                                        <div class="agree-check customCheckbox clearfix d-none">
                                            <input id="prTearm" name="tearm" type="checkbox" value="tearm" required />
                                            <label for="prTearm">I agree with the terms and conditions</label>
                                        </div>
                                    </div>
                                    <!-- End Product Action -->
                                    <!-- Product Info link -->
                                    <p class="infolinks d-flex-center mt-2 mb-3">
                                        <a class="btn add-to-wishlist d-none" href="my-wishlist.html"><i class="icon an an-heart-l me-1" aria-hidden="true"></i> <span>Add to Wishlist</span></a>
                                        <a class="btn add-to-wishlist" href="compare-style1.html"><i class="icon an an-sync-ar me-1" aria-hidden="true"></i> <span>Add to Compare</span></a>
                                        <a class="btn shippingInfo" href="#ShippingInfo"><i class="icon an an-paper-l-plane me-1"></i> Delivery &amp; Returns</a>
                                        <a class="btn emaillink me-0" href="#productInquiry"> <i class="icon an an-question-cil me-1"></i> Ask A Question</a>
                                    </p>
                                    <!-- End Product Info link -->
                                </form>
                                <!-- End Product Form -->
                                <!-- Social Sharing -->
                                <div class="social-sharing d-flex-center mb-3">
                                    <span class="sharing-lbl me-2">Share :</span>
                                    <a href="#" class="d-flex-center btn btn-link btn--share share-facebook" data-bs-toggle="tooltip" data-bs-placement="top" title="Share on Facebook"><i class="icon an an-facebook mx-1"></i><span class="share-title d-none">Facebook</span></a>
                                    <a href="#" class="d-flex-center btn btn-link btn--share share-twitter" data-bs-toggle="tooltip" data-bs-placement="top" title="Tweet on Twitter"><i class="icon an an-twitter mx-1"></i><span class="share-title d-none">Tweet</span></a>
                                    <a href="#" class="d-flex-center btn btn-link btn--share share-pinterest" data-bs-toggle="tooltip" data-bs-placement="top" title="Pin on Pinterest"><i class="icon an an-pinterest-p mx-1"></i> <span class="share-title d-none">Pin it</span></a>
                                    <a href="#" class="d-flex-center btn btn-link btn--share share-linkedin" data-bs-toggle="tooltip" data-bs-placement="top" title="Share on Linkedin"><i class="icon an an-linkedin mx-1"></i><span class="share-title d-none">Linkedin</span></a>
                                    <a href="#" class="d-flex-center btn btn-link btn--share share-email" data-bs-toggle="tooltip" data-bs-placement="top" title="Share by Email"><i class="icon an an-envelope-l mx-1"></i><span class="share-title d-none">Email</span></a>
                                </div>
                                <!-- End Social Sharing -->
                                <!-- Product Info -->
                                <div class="freeShipMsg" data-price="199"><i class="icon an an-truck" aria-hidden="true"></i>SPENT <b class="freeShip"><span class="money" data-currency-usd="$199.00" data-currency="USD">$199.00</span></b> MORE FOR FREE SHIPPING</div>
                                <div class="shippingMsg"><i class="icon an an-clock-r" aria-hidden="true"></i>Estimated Delivery Between <b id="fromDate">Wed, May 1</b> and <b id="toDate">Tue, May 7</b>.</div>
                                <div class="userViewMsg" data-user="20" data-time="11000"><i class="icon an an-eye-r" aria-hidden="true"></i><strong class="uersView">21</strong> People are Looking for this Product</div>
                                <div class="trustseal-img mt-4"><img src="assets/images/powerby-cards.jpg" alt="powerby cards" /></div>
                                <!-- End Product Info -->
                            </div>
                        </div>
                    </div>
                    <!--Product Content-->

                    <!--Product Nav-->
                    <a href="product-layout7.html" class="product-nav prev-pro d-flex-center justify-content-between" title="Previous Product">
                        <span class="details">
                            <span class="name">Mini Sleev Top</span>
                            <span class="price">$199.00</span>
                        </span>
                        <span class="img"><img src="assets/images/products/product-7.jpg" alt="product" /></span>
                    </a>
                    <a href="product-layout2.html" class="product-nav next-pro d-flex-center justify-content-between" title="Next Product">
                        <span class="img"><img src="assets/images/products/product-2.jpg" alt="product"></span>
                        <span class="details">
                            <span class="name">Ditsy Floral Dress</span>
                            <span class="price">$99</span>
                        </span>
                    </a>
                    <!--End Product Nav-->

                    <!--Product Tabs-->
                    <div class="tabs-listing mt-2 mt-md-5">
                        <ul class="product-tabs list-unstyled d-flex-wrap border-bottom m-0 d-none d-md-flex">
                            <li rel="description" class="active"><a class="tablink">Description</a></li>
                            <li rel="size-chart"><a class="tablink">Size Chart</a></li>
                            <li rel="shipping-return"><a class="tablink">Shipping &amp; Return</a></li>
                            <li rel="reviews"><a class="tablink">Reviews</a></li>
                            <li rel="addtional-tabs"><a class="tablink">Addtional Tabs</a></li>
                        </ul>
                        <div class="tab-container">
                            <h3 class="tabs-ac-style d-md-none active" rel="description">Description</h3>
                            <div id="description" class="tab-content">
                                <div class="product-description">
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-8 col-lg-8 mb-4 mb-md-0">
                                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                                            <h4 class="pt-2 text-uppercase">Features</h4>
                                            <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable.</p>
                                            <ul>
                                                <li>Curabitur pulvinar ex at tempus sodales.</li>
                                                <li>Donec vitae ante sed ligula viverra fermentum</li>
                                                <li>Mauris efficitur magna quis lectus lobortis venenatis.</li>
                                                <li>Phasellus sagittis purus eu dolor porttitor </li>
                                            </ul>
                                            <h4 class="pt-2 text-uppercase">Variations of passages</h4>
                                            <p>All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words.</p>
                                            <h4 class="pt-2 text-uppercase">Popular belief specimen</h4>
                                            <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage.</p>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                            <img data-src="assets/images/about/about-info-s3.jpg" src="assets/images/about/about-info-s3.jpg" alt="image" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h3 class="tabs-ac-style d-md-none" rel="size-chart">Size Chart</h3>
                            <div id="size-chart" class="tab-content">
                                <h4 class="fw-bold text-center">Size Guide</h4>
                                <p class="text-center">This is a standardised guide to give you an idea of what size you will need, however some brands may vary from these conversions.</p>
                                <p class="text-center"><strong>Ready to Wear Clothing</strong></p>
                                <div class="table-responsive px-1">
                                    <table class="table table-bordered align-middle">
                                        <tbody>
                                            <tr>
                                                <th>Size</th>
                                                <th>XXS - XS</th>
                                                <th>XS - S</th>
                                                <th>S - M</th>
                                                <th>M - L</th>
                                                <th>L - XL</th>
                                                <th>XL - XXL</th>
                                            </tr>
                                            <tr>
                                                <td>UK</td>
                                                <td>6</td>
                                                <td>8</td>
                                                <td>10</td>
                                                <td>12</td>
                                                <td>14</td>
                                                <td>16</td>
                                            </tr>
                                            <tr>
                                                <td>US</td>
                                                <td>2</td>
                                                <td>4</td>
                                                <td>6</td>
                                                <td>8</td>
                                                <td>10</td>
                                                <td>12</td>
                                            </tr>
                                            <tr>
                                                <td>Italy (IT)</td>
                                                <td>38</td>
                                                <td>40</td>
                                                <td>42</td>
                                                <td>44</td>
                                                <td>46</td>
                                                <td>48</td>
                                            </tr>
                                            <tr>
                                                <td>France (FR/EU)</td>
                                                <td>34</td>
                                                <td>36</td>
                                                <td>38</td>
                                                <td>40</td>
                                                <td>42</td>
                                                <td>44</td>
                                            </tr>
                                            <tr>
                                                <td>Denmark</td>
                                                <td>32</td>
                                                <td>34</td>
                                                <td>36</td>
                                                <td>38</td>
                                                <td>40</td>
                                                <td>42</td>
                                            </tr>
                                            <tr>
                                                <td>Russia</td>
                                                <td>40</td>
                                                <td>42</td>
                                                <td>44</td>
                                                <td>46</td>
                                                <td>48</td>
                                                <td>50</td>
                                            </tr>
                                            <tr>
                                                <td>Germany</td>
                                                <td>32</td>
                                                <td>34</td>
                                                <td>36</td>
                                                <td>38</td>
                                                <td>40</td>
                                                <td>42</td>
                                            </tr>
                                            <tr>
                                                <td>Japan</td>
                                                <td>5</td>
                                                <td>7</td>
                                                <td>9</td>
                                                <td>11</td>
                                                <td>13</td>
                                                <td>15</td>
                                            </tr>
                                            <tr>
                                                <td>Australia</td>
                                                <td>6</td>
                                                <td>8</td>
                                                <td>10</td>
                                                <td>12</td>
                                                <td>14</td>
                                                <td>16</td>
                                            </tr>
                                            <tr>
                                                <td>Korea</td>
                                                <td>33</td>
                                                <td>44</td>
                                                <td>55</td>
                                                <td>66</td>
                                                <td>77</td>
                                                <td>88</td>
                                            </tr>
                                            <tr>
                                                <td>China</td>
                                                <td>160/84</td>
                                                <td>165/86</td>
                                                <td>170/88</td>
                                                <td>175/90</td>
                                                <td>180/92</td>
                                                <td>185/94</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Jeans</strong></td>
                                                <td>24-25</td>
                                                <td>26-27</td>
                                                <td>27-28</td>
                                                <td>29-30</td>
                                                <td>31-32</td>
                                                <td>32-33</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <h3 class="tabs-ac-style d-md-none" rel="shipping-return">Shipping &amp; Return</h3>
                            <div id="shipping-return" class="tab-content">
                                <h4 class="pt-2 text-uppercase">Delivery</h4>
                                <ul>
                                    <li>Dispatch: Within 24 Hours</li>
                                    <li>Free shipping across all products on a minimum purchase of $50.</li>
                                    <li>International delivery time - 7-10 business days</li>
                                    <li>Cash on delivery might be available</li>
                                    <li>Easy 30 days returns and exchanges</li>
                                </ul>
                                <h4 class="pt-2 text-uppercase">Returns</h4>
                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>
                                <h4 class="pt-2 text-uppercase">Shipping</h4>
                                <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage.</p>
                            </div>

                            <h3 class="tabs-ac-style d-md-none" rel="reviews">Review</h3>
                            <div id="reviews" class="tab-content">
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="spr-header clearfix d-flex-center justify-content-between">
                                            <div class="product-review d-flex-center me-auto">
                                                <a class="reviewLink" href="#"><i class="icon an an-star"></i><i class="icon an an-star mx-1"></i><i class="icon an an-star"></i><i class="icon an an-star mx-1"></i><i class="icon an an-star-o"></i></a>
                                                <span class="spr-summary-actions-togglereviews ms-2">Based on 6 reviews 234</span>
                                            </div>
                                            <div class="spr-summary-actions mt-3 mt-sm-0">
                                                <a href="#" class="spr-summary-actions-newreview write-review-btn btn rounded"><i class="icon an-1x an an-pencil-alt me-2"></i>Write a review</a>
                                            </div>
                                        </div>

                                        <form method="post" action="#" class="product-review-form new-review-form mb-4">
                                            <h4 class="spr-form-title text-uppercase">Write A Review</h4>
                                            <fieldset class="spr-form-contact">
                                                <div class="spr-form-contact-name form-group">
                                                    <label class="spr-form-label" for="nickname">Name <span class="required">*</span></label>
                                                    <input class="spr-form-input spr-form-input-text" id="nickname" type="text" name="name" placeholder="John smith" required />
                                                </div>
                                                <div class="spr-form-contact-email form-group">
                                                    <label class="spr-form-label" for="email">Email <span class="required">*</span></label>
                                                    <input class="spr-form-input spr-form-input-email " id="email" type="email" name="email" placeholder="info@example.com" required />
                                                </div>
                                                <div class="spr-form-review-rating form-group">
                                                    <label class="spr-form-label">Rating</label>
                                                    <div class="product-review pt-1">
                                                        <div class="review-rating">
                                                            <input type="radio" name="rating" id="rating-5"><label for="rating-5"></label>
                                                            <input type="radio" name="rating" id="rating-4"><label for="rating-4"></label>
                                                            <input type="radio" name="rating" id="rating-3"><label for="rating-3"></label>
                                                            <input type="radio" name="rating" id="rating-2"><label for="rating-2"></label>
                                                            <input type="radio" name="rating" id="rating-1"><label for="rating-1"></label>
                                                        </div>
                                                        <a class="reviewLink d-none" href="#"><i class="icon an an-star-o"></i><i class="icon an an-star-o mx-1"></i><i class="icon an an-star-o"></i><i class="icon an an-star-o mx-1"></i><i class="icon an an-star-o"></i></a>
                                                    </div>
                                                </div>
                                                <div class="spr-form-review-title form-group">
                                                    <label class="spr-form-label" for="review">Review Title </label>
                                                    <input class="spr-form-input spr-form-input-text " id="review" type="text" name="review" placeholder="Give your review a title" />
                                                </div>
                                                <div class="spr-form-review-body form-group">
                                                    <label class="spr-form-label" for="message">Body of Review <span class="spr-form-review-body-charactersremaining">(1500) characters remaining</span></label>
                                                    <div class="spr-form-input">
                                                        <textarea class="spr-form-input spr-form-input-textarea " id="message" name="message" rows="5" placeholder="Write your comments here"></textarea>
                                                    </div>
                                                </div>
                                            </fieldset>
                                            <div class="spr-form-actions clearfix">
                                                <input type="submit" class="btn btn-primary rounded spr-button spr-button-primary" value="Submit Review">
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="spr-reviews">
                                            <h4 class="spr-form-title text-uppercase mb-3">Customer Reviews</h4>
                                            <div class="review-inner">
                                                <div class="spr-review">
                                                    <div class="spr-review-header">
                                                        <span class="product-review spr-starratings"><span class="reviewLink"><i class="icon an an-star"></i><i class="icon an an-star mx-1"></i><i class="icon an an-star"></i><i class="icon an an-star mx-1"></i><i class="icon an an-star-o"></i></span></span>
                                                        <h5 class="spr-review-header-title mt-1">Lorem ipsum dolor sit amet</h5>
                                                        <span class="spr-review-header-byline"><strong>Avone</strong> on <strong>Apr 09, 2021</strong></span>
                                                    </div>
                                                    <div class="spr-review-content">
                                                        <p class="spr-review-content-body">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                                                    </div>
                                                </div>
                                                <div class="spr-review">
                                                    <div class="spr-review-header">
                                                        <span class="product-review spr-starratings"><span class="reviewLink"><i class="icon an an-star"></i><i class="icon an an-star mx-1"></i><i class="icon an an-star"></i><i class="icon an an-star-o mx-1"></i><i class="icon an an-star-o"></i></span></span>
                                                        <h5 class="spr-review-header-title mt-1">Simply text of the printing</h5>
                                                        <span class="spr-review-header-byline"><strong>Diva</strong> on <strong>May 30, 2021</strong></span>
                                                    </div>

                                                    <div class="spr-review-content">
                                                        <p class="spr-review-content-body">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="spr-review">
                                                    <div class="spr-review-header">
                                                        <span class="product-review spr-starratings"><span class="reviewLink"><i class="icon an an-star"></i><i class="icon an an-star mx-1"></i><i class="icon an an-star-o"></i><i class="icon an an-star-o mx-1"></i><i class="icon an an-star-o"></i></span></span>
                                                        <h5 class="spr-review-header-title mt-1">Neque porro quisquam est qui dolorem ipsum</h5>
                                                        <span class="spr-review-header-byline"><strong>Belle</strong> on <strong>Dec 30, 2021</strong></span>
                                                    </div>

                                                    <div class="spr-review-content">
                                                        <p class="spr-review-content-body">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h3 class="tabs-ac-style d-md-none" rel="addtional-tabs">Addtional Tabs</h3>
                            <div id="addtional-tabs" class="tab-content">
                                <p>You can set different tabs for each products.</p>
                                <ul>
                                    <li>Comodous in tempor ullamcorper miaculis.</li>
                                    <li>Pellentesque vitae neque mollis urna mattis laoreet.</li>
                                    <li>Divamus sit amet purus justo.</li>
                                    <li>Proin molestie egestas orci ac suscipit risus posuere loremous.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!--End Product Tabs-->
                </div>
                <!--End Container-->

                <!--You May Also Like Products-->
                <section class="section product-slider pb-0">
                    <div class="container">
                        <div class="row">
                            <div class="section-header col-12">
                                <h2 class="text-transform-none">You May Also Like</h2>
                            </div>
                        </div>
                        <div class="productSlider grid-products">
                            <div class="item">
                                <!--Start Product Image-->
                                <div class="product-image">
                                    <!--Start Product Image-->
                                    <a href="product-layout1.html" class="product-img">
                                        <!-- image -->
                                        <img class="primary blur-up lazyload" data-src="assets/images/products/product-8.jpg" src="assets/images/products/product-8.jpg" alt="" title="">
                                        <!-- End image -->
                                        <!-- Hover image -->
                                        <img class="hover blur-up lazyload" data-src="assets/images/products/product-8-1.jpg" src="assets/images/products/product-8-1.jpg" alt="" title="">
                                        <!-- End hover image -->
                                        <!-- product label -->
                                        <div class="product-labels"><span class="lbl on-sale">50% Off</span></div>
                                        <!-- End product label -->
                                    </a>
                                    <!--End Product Image-->

                                    <!--Product Button-->
                                    <div class="button-set style0 d-none d-md-block">
                                        <ul>
                                            <!--Cart Button-->
                                            <li><a class="btn-icon btn cartIcon pro-addtocart-popup" href="#pro-addtocart-popup"><i class="icon an an-cart-l"></i> <span class="tooltip-label top">Add to Cart</span></a></li>
                                            <!--End Cart Button-->
                                            <!--Quick View Button-->
                                            <li><a class="btn-icon quick-view-popup quick-view" href="javascript:void(0)" data-toggle="modal" data-target="#content_quickview"><i class="icon an an-search-l"></i> <span class="tooltip-label top">Quick View</span></a></li>
                                            <!--End Quick View Button-->
                                            <!--Wishlist Button-->
                                            <li><a class="btn-icon wishlist add-to-wishlist" href="my-wishlist.html"><i class="icon an an-heart-l"></i> <span class="tooltip-label top">Add To Wishlist</span></a></li>
                                            <!--End Wishlist Button-->
                                            <!--Compare Button-->
                                            <li><a class="btn-icon compare add-to-compare" href="compare-style2.html"><i class="icon an an-sync-ar"></i> <span class="tooltip-label top">Add to Compare</span></a></li>
                                            <!--End Compare Button-->
                                        </ul>
                                    </div>
                                    <!--End Product Button-->
                                </div>
                                <!--End Product Image-->
                                <!--Start Product Details-->
                                <div class="product-details text-center">
                                    <!--Product Name-->
                                    <div class="product-name text-uppercase">
                                        <a href="product-layout1.html">Martha Knit Top</a>
                                    </div>
                                    <!--End Product Name-->
                                    <!--Product Price-->
                                    <div class="product-price">
                                        <span class="old-price">$199.00</span>
                                        <span class="price">$219.00</span>
                                    </div>
                                    <!--End Product Price-->
                                    <!--Product Review-->
                                    <div class="product-review d-flex align-items-center justify-content-center"><i class="an an-star"></i> <i class="an an-star"></i> <i class="an an-star"></i> <i class="an an-star"></i> <i class="an an-star-o"></i></div>
                                    <!--End Product Review-->
                                    <!--Color Variant -->
                                    <ul class="image-swatches swatches">
                                        <li class="radius blue medium"><span class="swacth-btn"></span><span class="tooltip-label">Blue</span></li>
                                        <li class="radius pink medium"><span class="swacth-btn"></span><span class="tooltip-label">Pink</span></li>
                                        <li class="radius red medium"><span class="swacth-btn"></span><span class="tooltip-label">Red</span></li>
                                    </ul>
                                    <!-- End Variant -->
                                </div>
                                <!--End Product Details-->
                            </div>
                            <div class="item">
                                <!--Start Product Image-->
                                <div class="product-image">
                                    <!--Start Product Image-->
                                    <a href="product-layout1.html" class="product-img">
                                        <!-- image -->
                                        <img class="primary blur-up lazyload" data-src="assets/images/products/product-9-2.jpg" src="assets/images/products/product-9-2.jpg" alt="" title="">
                                        <!-- End image -->
                                        <!-- Hover image -->
                                        <img class="hover blur-up lazyload" data-src="assets/images/products/product-9-1.jpg" src="assets/images/products/product-9-1.jpg" alt="" title="">
                                        <!-- End hover image -->
                                    </a>
                                    <!--End Product Image-->

                                    <!--Countdown Timer-->
                                    <div class="saleTime desktop" data-countdown="2024/10/01"></div>
                                    <!--End Countdown Timer-->

                                    <!--Product Button-->
                                    <div class="button-set style0 d-none d-md-block">
                                        <ul>
                                            <!--Cart Button-->
                                            <li><a class="btn-icon btn cartIcon pro-addtocart-popup" href="#pro-addtocart-popup"><i class="icon an an-cart-l"></i> <span class="tooltip-label top">Add to Cart</span></a></li>
                                            <!--End Cart Button-->
                                            <!--Quick View Button-->
                                            <li><a class="btn-icon quick-view-popup quick-view" href="javascript:void(0)" data-toggle="modal" data-target="#content_quickview"><i class="icon an an-search-l"></i> <span class="tooltip-label top">Quick View</span></a></li>
                                            <!--End Quick View Button-->
                                            <!--Wishlist Button-->
                                            <li><a class="btn-icon wishlist add-to-wishlist" href="my-wishlist.html"><i class="icon an an-heart-l"></i> <span class="tooltip-label top">Add To Wishlist</span></a></li>
                                            <!--End Wishlist Button-->
                                            <!--Compare Button-->
                                            <li><a class="btn-icon compare add-to-compare" href="compare-style2.html"><i class="icon an an-sync-ar"></i> <span class="tooltip-label top">Add to Compare</span></a></li>
                                            <!--End Compare Button-->
                                        </ul>
                                    </div>
                                    <!--End Product Button-->
                                </div>
                                <!--End Product Image-->
                                <!--Start Product Details-->
                                <div class="product-details text-center">
                                    <!--Product Name-->
                                    <div class="product-name text-uppercase">
                                        <a href="product-layout1.html">Long Sleeve T-shirts</a>
                                    </div>
                                    <!--End Product Name-->
                                    <!--Product Price-->
                                    <div class="product-price">
                                        <span class="price">$199.00</span>
                                    </div>
                                    <!--End Product Price-->
                                    <!--Product Review-->
                                    <div class="product-review d-flex align-items-center justify-content-center"><i class="an an-star"></i> <i class="an an-star"></i> <i class="an an-star"></i><i class="an an-star"></i> <i class="an an-star"></i></div>
                                    <!--End Product Review-->
                                    <!-- Color Variant -->
                                    <ul class="swatches">
                                        <li class="swatch medium radius black"><span class="tooltip-label">Black</span></li>
                                        <li class="swatch medium radius navy"><span class="tooltip-label">Navy</span></li>
                                        <li class="swatch medium radius purple"><span class="tooltip-label">Purple</span></li>
                                    </ul>
                                    <!-- End Variant -->
                                </div>
                                <!--End Product Details-->
                            </div>
                            <div class="item">
                                <!--Start Product Image-->
                                <div class="product-image">
                                    <!--Start Product Image-->
                                    <a href="product-layout1.html" class="product-img">
                                        <!-- image -->
                                        <img class="primary blur-up lazyload" data-src="assets/images/products/product-7.jpg" src="assets/images/products/product-7.jpg" alt="" title="">
                                        <!-- End image -->
                                        <!-- Hover image -->
                                        <img class="hover blur-up lazyload" data-src="assets/images/products/product-7-1.jpg" src="assets/images/products/product-7-1.jpg" alt="" title="">
                                        <!-- End hover image -->
                                    </a>
                                    <!--End Product Image-->
                                    <!--Product label-->
                                    <div class="product-labels"><span class="lbl pr-label1">New</span></div>
                                    <!--Product label-->

                                    <!--Product Button-->
                                    <div class="button-set style0 d-none d-md-block">
                                        <ul>
                                            <!--Cart Button-->
                                            <li><a class="btn-icon btn cartIcon pro-addtocart-popup" href="#pro-addtocart-popup"><i class="icon an an-cart-l"></i> <span class="tooltip-label top">Add to Cart</span></a></li>
                                            <!--End Cart Button-->
                                            <!--Quick View Button-->
                                            <li><a class="btn-icon quick-view-popup quick-view" href="javascript:void(0)" data-toggle="modal" data-target="#content_quickview"><i class="icon an an-search-l"></i> <span class="tooltip-label top">Quick View</span></a></li>
                                            <!--End Quick View Button-->
                                            <!--Wishlist Button-->
                                            <li><a class="btn-icon wishlist add-to-wishlist" href="my-wishlist.html"><i class="icon an an-heart-l"></i> <span class="tooltip-label top">Add To Wishlist</span></a></li>
                                            <!--End Wishlist Button-->
                                            <!--Compare Button-->
                                            <li><a class="btn-icon compare add-to-compare" href="compare-style2.html"><i class="icon an an-sync-ar"></i> <span class="tooltip-label top">Add to Compare</span></a></li>
                                            <!--End Compare Button-->
                                        </ul>
                                    </div>
                                    <!--End Product Button-->
                                </div>
                                <!--End Product Image-->
                                <!--Start Product Details-->
                                <div class="product-details text-center">
                                    <!--Product Name-->
                                    <div class="product-name text-uppercase">
                                        <a href="product-layout1.html">Button Up Top Black</a>
                                    </div>
                                    <!--End Product Name-->
                                    <!--Product Price-->
                                    <div class="product-price">
                                        <span class="price">$99.00</span>
                                    </div>
                                    <!--End Product Price-->
                                    <!--Product Review-->
                                    <div class="product-review d-flex align-items-center justify-content-center"><i class="an an-star"></i> <i class="an an-star"></i> <i class="an an-star-o"></i> <i class="an an-star-o"></i> <i class="an an-star-o"></i></div>
                                    <!--End Product Review-->
                                    <!--Color Variant -->
                                    <ul class="swatches">
                                        <li class="swatch medium radius red"><span class="tooltip-label">red</span></li>
                                        <li class="swatch medium radius orange"><span class="tooltip-label">orange</span></li>
                                        <li class="swatch medium radius yellow"><span class="tooltip-label">yellow</span></li>
                                    </ul>
                                    <!-- End Variant -->
                                </div>
                                <!--End Product Details-->
                            </div>
                            <div class="item">
                                <!--Start Product Image-->
                                <div class="product-image">
                                    <!--Start Product Image-->
                                    <a href="product-layout1.html" class="product-img">
                                        <!-- image -->
                                        <img class="primary blur-up lazyload" data-src="assets/images/products/product-6.jpg" src="assets/images/products/product-6.jpg" alt="" title="">
                                        <!-- End image -->
                                        <!-- Hover image -->
                                        <img class="hover blur-up lazyload" data-src="assets/images/products/product-6-1.jpg" src="assets/images/products/product-6-1.jpg" alt="" title="">
                                        <!-- End hover image -->
                                    </a>
                                    <!--End Product Image-->

                                    <!--Product Button-->
                                    <div class="button-set style0 d-none d-md-block">
                                        <ul>
                                            <!--Cart Button-->
                                            <li><a class="btn-icon btn cartIcon pro-addtocart-popup" href="#pro-addtocart-popup"><i class="icon an an-cart-l"></i> <span class="tooltip-label top">Add to Cart</span></a></li>
                                            <!--End Cart Button-->
                                            <!--Quick View Button-->
                                            <li><a class="btn-icon quick-view-popup quick-view" href="javascript:void(0)" data-toggle="modal" data-target="#content_quickview"><i class="icon an an-search-l"></i> <span class="tooltip-label top">Quick View</span></a></li>
                                            <!--End Quick View Button-->
                                            <!--Wishlist Button-->
                                            <li><a class="btn-icon wishlist add-to-wishlist" href="my-wishlist.html"><i class="icon an an-heart-l"></i> <span class="tooltip-label top">Add To Wishlist</span></a></li>
                                            <!--End Wishlist Button-->
                                            <!--Compare Button-->
                                            <li><a class="btn-icon compare add-to-compare" href="compare-style2.html"><i class="icon an an-sync-ar"></i> <span class="tooltip-label top">Add to Compare</span></a></li>
                                            <!--End Compare Button-->
                                        </ul>
                                    </div>
                                    <!--End Product Button-->
                                </div>
                                <!--End Product Image-->
                                <!--Start Product Details-->
                                <div class="product-details text-center">
                                    <!--Product Name-->
                                    <div class="product-name text-uppercase">
                                        <a href="product-layout1.html">Sunset Sleep Scarf Top</a>
                                    </div>
                                    <!--End Product Name-->
                                    <!--Product Price-->
                                    <div class="product-price">
                                        <span class="price">$88.00</span>
                                    </div>
                                    <!--End Product Price-->
                                    <!--Product Review-->
                                    <div class="product-review d-flex align-items-center justify-content-center"><i class="an an-star"></i> <i class="an an-star-o"></i> <i class="an an-star-o"></i> <i class="an an-star-o"></i> <i class="an an-star-o"></i></div>
                                    <!--End Product Review-->
                                    <!-- Color Variant -->
                                    <ul class="image-swatches swatches">
                                        <li class="radius yellow medium"><span class="swacth-btn"></span><span class="tooltip-label">Yellow</span></li>
                                        <li class="radius blue medium"><span class="swacth-btn"></span><span class="tooltip-label">Blue</span></li>
                                        <li class="radius pink medium"><span class="swacth-btn"></span><span class="tooltip-label">Pink</span></li>
                                        <li class="radius red medium"><span class="swacth-btn"></span><span class="tooltip-label">Red</span></li>
                                    </ul>
                                    <!-- End Variant -->
                                </div>
                                <!--End Product Details-->
                            </div>
                            <div class="item">
                                <!--Start Product Image-->
                                <div class="product-image">
                                    <!--Start Product Image-->
                                    <a href="product-layout1.html" class="product-img">
                                        <!-- image -->
                                        <img class="primary blur-up lazyload" data-src="assets/images/products/product-10.jpg" src="assets/images/products/product-10.jpg" alt="" title="">
                                        <!-- End image -->
                                        <!-- Hover image -->
                                        <img class="hover blur-up lazyload" data-src="assets/images/products/product-10-1.jpg" src="assets/images/products/product-10-1.jpg" alt="" title="">
                                        <!-- End hover image -->
                                    </a>
                                    <!--End Product Image-->

                                    <!--Product Button-->
                                    <div class="button-set style0 d-none d-md-block">
                                        <ul>
                                            <!--Cart Button-->
                                            <li><a class="btn-icon btn cartIcon pro-addtocart-popup" href="#pro-addtocart-popup"><i class="icon an an-cart-l"></i> <span class="tooltip-label top">Add to Cart</span></a></li>
                                            <!--End Cart Button-->
                                            <!--Quick View Button-->
                                            <li><a class="btn-icon quick-view-popup quick-view" href="javascript:void(0)" data-toggle="modal" data-target="#content_quickview"><i class="icon an an-search-l"></i> <span class="tooltip-label top">Quick View</span></a></li>
                                            <!--End Quick View Button-->
                                            <!--Wishlist Button-->
                                            <li><a class="btn-icon wishlist add-to-wishlist" href="my-wishlist.html"><i class="icon an an-heart-l"></i> <span class="tooltip-label top">Add To Wishlist</span></a></li>
                                            <!--End Wishlist Button-->
                                            <!--Compare Button-->
                                            <li><a class="btn-icon compare add-to-compare" href="compare-style2.html"><i class="icon an an-sync-ar"></i> <span class="tooltip-label top">Add to Compare</span></a></li>
                                            <!--End Compare Button-->
                                        </ul>
                                    </div>
                                    <!--End Product Button-->   
                                </div>
                                <!--End Product Image-->
                                <!--Start Product Details-->
                                <div class="product-details text-center">
                                    <!--Product Name-->
                                    <div class="product-name text-uppercase">
                                        <a href="product-layout1.html">Backpack With Contrast Bow</a>
                                    </div>
                                    <!--End Product Name-->
                                    <!--Product Price-->
                                    <div class="product-price">
                                        <span class="price">$39.20</span>
                                    </div>
                                    <!--End Product Price-->
                                    <!--Product Review-->
                                    <div class="product-review d-flex align-items-center justify-content-center"><i class="an an-star"></i> <i class="an an-star"></i> <i class="an an-star"></i> <i class="an an-star"></i> <i class="an an-star-o"></i></div>
                                    <!--End Product Review-->
                                    <!-- Color Variant -->
                                    <ul class="swatches">
                                        <li class="swatch medium radius black"><span class="tooltip-label">black</span></li>
                                        <li class="swatch medium radius navy"><span class="tooltip-label">navy</span></li>
                                        <li class="swatch medium radius darkgreen"><span class="tooltip-label">darkgreen</span></li>
                                    </ul>
                                    <!-- End Variant -->
                                </div>
                                <!--End Product Details-->
                            </div>
                        </div>
                    </div>
                </section>
                <!--End You May Also Like Products-->

                <!--Recently Viewed Products-->
                <section class="section product-slider pb-0">
                    <div class="container">
                        <div class="row">
                            <div class="section-header col-12">
                                <h2 class="text-transform-none">Recently Viewed Products</h2>
                            </div>
                        </div>
                        <div class="productSlider grid-products">
                            <div class="item">
                                <!--Start Product Image-->
                                <div class="product-image">
                                    <!--Start Product Image-->
                                    <a href="product-layout1.html" class="product-img">
                                        <!-- image -->
                                        <img class="primary blur-up lazyload" data-src="assets/images/products/product-11.jpg" src="assets/images/products/product-11.jpg" alt="image" title="">
                                        <!-- End image -->
                                        <!-- Hover image -->
                                        <img class="hover blur-up lazyload" data-src="assets/images/products/product-11-1.jpg" src="assets/images/products/product-11-1.jpg" alt="image" title="">
                                        <!-- End hover image -->
                                    </a>
                                    <!--End Product Image-->

                                    <!--Product Button-->
                                    <div class="button-set style0 d-none d-md-block">
                                        <ul>
                                            <!--Cart Button-->
                                            <li><a class="btn-icon btn cartIcon pro-addtocart-popup" href="#pro-addtocart-popup"><i class="icon an an-cart-l"></i> <span class="tooltip-label top">Add to Cart</span></a></li>
                                            <!--End Cart Button-->
                                            <!--Quick View Button-->
                                            <li><a class="btn-icon quick-view-popup quick-view" href="javascript:void(0)" data-toggle="modal" data-target="#content_quickview"><i class="icon an an-search-l"></i> <span class="tooltip-label top">Quick View</span></a></li>
                                            <!--End Quick View Button-->
                                            <!--Wishlist Button-->
                                            <li><a class="btn-icon wishlist add-to-wishlist" href="my-wishlist.html"><i class="icon an an-heart-l"></i> <span class="tooltip-label top">Add To Wishlist</span></a></li>
                                            <!--End Wishlist Button-->
                                            <!--Compare Button-->
                                            <li><a class="btn-icon compare add-to-compare" href="compare-style2.html"><i class="icon an an-sync-ar"></i> <span class="tooltip-label top">Add to Compare</span></a></li>
                                            <!--End Compare Button-->
                                        </ul>
                                    </div>
                                    <!--End Product Button-->   
                                </div>
                                <!--End Product Image-->
                                <!--Start Product Details-->
                                <div class="product-details text-center">
                                    <!--Product Name-->
                                    <div class="product-name">
                                        <a href="product-layout1.html">Puffer Jacket</a>
                                    </div>
                                    <!--End Product Name-->
                                    <!--Product Price-->
                                    <div class="product-price">
                                        <span class="price">$89.00</span>
                                    </div>
                                    <!--End Product Price-->
                                    <!--Product Review-->
                                    <div class="product-review d-flex align-items-center justify-content-center">
                                        <i class="an an-star"></i><i class="an an-star"></i><i class="an an-star"></i><i class="an an-star"></i><i class="an an-star-o"></i>
                                        <span class="caption hidden ms-2">6 reviews</span>
                                    </div>
                                    <!--End Product Review-->
                                    <!--Color Variant -->
                                    <ul class="image-swatches swatches">
                                        <li class="radius blue medium"><span class="swacth-btn"></span><span class="tooltip-label">Blue</span></li>
                                        <li class="radius pink medium"><span class="swacth-btn"></span><span class="tooltip-label">Pink</span></li>
                                        <li class="radius red medium"><span class="swacth-btn"></span><span class="tooltip-label">Red</span></li>
                                    </ul>
                                    <!--End Color Variant-->
                                </div>
                                <!--End Product Details-->
                            </div>
                            <div class="item">
                                <!--Start Product Image-->
                                <div class="product-image">
                                    <!--Start Product Image-->
                                    <a href="product-layout1.html" class="product-img">
                                        <!-- image -->
                                        <img class="primary blur-up lazyload" data-src="assets/images/products/product-12.jpg" src="assets/images/products/product-12.jpg" alt="" title="">
                                        <!-- End image -->
                                        <!-- Hover image -->
                                        <img class="hover blur-up lazyload" data-src="assets/images/products/product-12-1.jpg" src="assets/images/products/product-12-1.jpg" alt="" title="">
                                        <!-- End hover image -->
                                    </a>
                                    <!--End Product Image-->

                                    <div class="button-set style0 d-none d-md-block">
                                        <ul>
                                            <!--Cart Button-->
                                            <li><a class="btn-icon btn cartIcon pro-addtocart-popup" href="#pro-addtocart-popup"><i class="icon an an-cart-l"></i> <span class="tooltip-label top">Add to Cart</span></a></li>
                                            <!--End Cart Button-->
                                            <!--Quick View Button-->
                                            <li><a class="btn-icon quick-view-popup quick-view" href="javascript:void(0)" data-toggle="modal" data-target="#content_quickview"><i class="icon an an-search-l"></i> <span class="tooltip-label top">Quick View</span></a></li>
                                            <!--End Quick View Button-->
                                            <!--Wishlist Button-->
                                            <li><a class="btn-icon wishlist add-to-wishlist" href="my-wishlist.html"><i class="icon an an-heart-l"></i> <span class="tooltip-label top">Add To Wishlist</span></a></li>
                                            <!--End Wishlist Button-->
                                            <!--Compare Button-->
                                            <li><a class="btn-icon compare add-to-compare" href="compare-style2.html"><i class="icon an an-sync-ar"></i> <span class="tooltip-label top">Add to Compare</span></a></li>
                                            <!--End Compare Button-->
                                        </ul>
                                    </div>
                                </div>
                                <!--End Product Image-->
                                <!--Start Product Details-->
                                <div class="product-details text-center">
                                    <!--Product Name-->
                                    <div class="product-name">
                                        <a href="product-layout1.html">Long Sleeve T-shirts</a>
                                    </div>
                                    <!--End Product Name-->
                                    <!--Product Price-->
                                    <div class="product-price">
                                        <span class="price">$199.00</span>
                                    </div>
                                    <!--End Product Price-->
                                    <!--Product Review-->
                                    <div class="product-review d-flex align-items-center justify-content-center">
                                        <i class="an an-star"></i><i class="an an-star"></i><i class="an an-star"></i><i class="an an-star"></i><i class="an an-star"></i>
                                        <span class="caption hidden ms-2">20 reviews</span>
                                    </div>
                                    <!--End Product Review-->
                                    <!--Color Variant-->
                                    <ul class="swatches">
                                        <li class="swatch medium radius black"><span class="tooltip-label">Black</span></li>
                                        <li class="swatch medium radius purple"><span class="tooltip-label">Purple</span></li>
                                    </ul>
                                    <!--End Color Variant-->
                                </div>
                                <!--End Product Details-->
                            </div>
                            <div class="item">
                                <!--Start Product Image-->
                                <div class="product-image">
                                    <!--Start Product Image-->
                                    <a href="product-layout1.html" class="product-img">
                                        <!-- image -->
                                        <img class="primary blur-up lazyload" data-src="assets/images/products/product-13.jpg" src="assets/images/products/product-13.jpg" alt="" title="">
                                        <!-- End image -->
                                        <!-- Hover image -->
                                        <img class="hover blur-up lazyload" data-src="assets/images/products/product-13-1.jpg" src="assets/images/products/product-13-1.jpg" alt="" title="">
                                        <!-- End hover image -->
                                    </a>
                                    <!--End Product Image-->
                                    <!--Product label-->
                                    <div class="product-labels"><span class="lbl pr-label1">HOT</span></div>
                                    <!--Product label-->

                                    <!--Product Button-->
                                    <div class="button-set style0 d-none d-md-block">
                                        <ul>
                                            <!--Cart Button-->
                                            <li><a class="btn-icon btn cartIcon pro-addtocart-popup" href="#pro-addtocart-popup"><i class="icon an an-cart-l"></i> <span class="tooltip-label top">Add to Cart</span></a></li>
                                            <!--End Cart Button-->
                                            <!--Quick View Button-->
                                            <li><a class="btn-icon quick-view-popup quick-view" href="javascript:void(0)" data-toggle="modal" data-target="#content_quickview"><i class="icon an an-search-l"></i> <span class="tooltip-label top">Quick View</span></a></li>
                                            <!--End Quick View Button-->
                                            <!--Wishlist Button-->
                                            <li><a class="btn-icon wishlist add-to-wishlist" href="my-wishlist.html"><i class="icon an an-heart-l"></i> <span class="tooltip-label top">Add To Wishlist</span></a></li>
                                            <!--End Wishlist Button-->
                                            <!--Compare Button-->
                                            <li><a class="btn-icon compare add-to-compare" href="compare-style2.html"><i class="icon an an-sync-ar"></i> <span class="tooltip-label top">Add to Compare</span></a></li>
                                            <!--End Compare Button-->
                                        </ul>
                                    </div>
                                    <!--End Product Button-->
                                </div>
                                <!--End Product Image-->
                                <!--Start Product Details-->
                                <div class="product-details text-center">
                                    <!--Product Name-->
                                    <div class="product-name">
                                        <a href="product-layout1.html">Stand Collar Slim Shirt</a>
                                    </div>
                                    <!--End Product Name-->
                                    <!--Product Price-->
                                    <div class="product-price">
                                        <span class="price">$399.00</span>
                                    </div>
                                    <!--End Product Price-->
                                    <!--Product Review-->
                                    <div class="product-review d-flex align-items-center justify-content-center">
                                        <i class="an an-star"></i><i class="an an-star"></i><i class="an an-star-o"></i><i class="an an-star-o"></i><i class="an an-star-o"></i>
                                        <span class="caption hidden ms-2">19 reviews</span>
                                    </div>
                                    <!--End Product Review-->
                                    <!--Color Variant-->
                                    <ul class="image-swatches swatches">
                                        <li class="radius yellow medium"><span class="swacth-btn"></span><span class="tooltip-label">Yellow</span></li>
                                        <li class="radius blue medium"><span class="swacth-btn"></span><span class="tooltip-label">Blue</span></li>
                                        <li class="radius pink medium"><span class="swacth-btn"></span><span class="tooltip-label">Pink</span></li>
                                        <li class="radius red medium"><span class="swacth-btn"></span><span class="tooltip-label">Red</span></li>
                                    </ul>
                                    <!--End Color Variant-->
                                </div>
                                <!--End Product Details-->
                            </div>
                            <div class="item">
                                <!--Start Product Image-->
                                <div class="product-image">
                                    <!--Start Product Image-->
                                    <a href="product-layout1.html" class="product-img">
                                        <!-- image -->
                                        <img class="primary blur-up lazyload" data-src="assets/images/products/product-14.jpg" src="assets/images/products/product-14.jpg" alt="" title="">
                                        <!-- End image -->
                                        <!-- Hover image -->
                                        <img class="hover blur-up lazyload" data-src="assets/images/products/product-14-1.jpg" src="assets/images/products/product-14-1.jpg" alt="" title="">
                                        <!-- End hover image -->
                                    </a>
                                    <!--End Product Image-->

                                    <!--Product Button-->
                                    <div class="button-set style0 d-none d-md-block">
                                        <ul>
                                            <!--Cart Button-->
                                            <li><a class="btn-icon btn cartIcon pro-addtocart-popup" href="#pro-addtocart-popup"><i class="icon an an-cart-l"></i> <span class="tooltip-label top">Add to Cart</span></a></li>
                                            <!--End Cart Button-->
                                            <!--Quick View Button-->
                                            <li><a class="btn-icon quick-view-popup quick-view" href="javascript:void(0)" data-toggle="modal" data-target="#content_quickview"><i class="icon an an-search-l"></i> <span class="tooltip-label top">Quick View</span></a></li>
                                            <!--End Quick View Button-->
                                            <!--Wishlist Button-->
                                            <li><a class="btn-icon wishlist add-to-wishlist" href="my-wishlist.html"><i class="icon an an-heart-l"></i> <span class="tooltip-label top">Add To Wishlist</span></a></li>
                                            <!--End Wishlist Button-->
                                            <!--Compare Button-->
                                            <li><a class="btn-icon compare add-to-compare" href="compare-style2.html"><i class="icon an an-sync-ar"></i> <span class="tooltip-label top">Add to Compare</span></a></li>
                                            <!--End Compare Button-->
                                        </ul>
                                    </div>
                                    <!--End Product Button-->
                                </div>
                                <!--End Product Image-->
                                <!--Start Product Details-->
                                <div class="product-details text-center">
                                    <!--Product Name-->
                                    <div class="product-name">
                                        <a href="product-layout1.html">Martha Knit Top</a>
                                    </div>
                                    <!--End Product Name-->
                                    <!--Product Price-->
                                    <div class="product-price">
                                        <span class="old-price">$199.00</span>
                                        <span class="price">$219.00</span>
                                    </div>
                                    <!--End Product Price-->
                                    <!--Product Review-->
                                    <div class="product-review d-flex align-items-center justify-content-center">
                                        <i class="an an-star"></i><i class="an an-star"></i><i class="an an-star"></i><i class="an an-star"></i><i class="an an-star-o"></i>
                                        <span class="caption hidden ms-2">24 reviews</span>
                                    </div>
                                    <!--End Product Review-->
                                    <!--Color Variant -->
                                    <ul class="swatches">
                                        <li class="swatch medium radius green"><span class="tooltip-label">Green</span></li>
                                        <li class="swatch medium radius orange"><span class="tooltip-label">Orange</span></li>
                                    </ul>
                                    <!--End Color Variant-->
                                </div>
                                <!--End Product Details-->
                            </div>
                            <div class="item">
                                <!--Start Product Image-->
                                <div class="product-image">
                                    <!--Start Product Image-->
                                    <a href="product-layout1.html" class="product-img">
                                        <!-- image -->
                                        <img class="primary blur-up lazyload" data-src="assets/images/products/product-15.jpg" src="assets/images/products/product-15.jpg" alt="" title="">
                                        <!-- End image -->
                                        <!-- Hover image -->
                                        <img class="hover blur-up lazyload" data-src="assets/images/products/product-15-1.jpg" src="assets/images/products/product-15-1.jpg" alt="" title="">
                                        <!-- End hover image -->
                                    </a>
                                    <!--End Product Image-->
                                    <!--Product Button-->
                                    <div class="button-set style0 d-none d-md-block">
                                        <ul>
                                            <!--Cart Button-->
                                            <li><a class="btn-icon btn cartIcon pro-addtocart-popup" href="#pro-addtocart-popup"><i class="icon an an-cart-l"></i> <span class="tooltip-label top">Add to Cart</span></a></li>
                                            <!--End Cart Button-->
                                            <!--Quick View Button-->
                                            <li><a class="btn-icon quick-view-popup quick-view" href="javascript:void(0)" data-toggle="modal" data-target="#content_quickview"><i class="icon an an-search-l"></i> <span class="tooltip-label top">Quick View</span></a></li>
                                            <!--End Quick View Button-->
                                            <!--Wishlist Button-->
                                            <li><a class="btn-icon wishlist add-to-wishlist" href="my-wishlist.html"><i class="icon an an-heart-l"></i> <span class="tooltip-label top">Add To Wishlist</span></a></li>
                                            <!--End Wishlist Button-->
                                            <!--Compare Button-->
                                            <li><a class="btn-icon compare add-to-compare" href="compare-style2.html"><i class="icon an an-sync-ar"></i> <span class="tooltip-label top">Add to Compare</span></a></li>
                                            <!--End Compare Button-->
                                        </ul>
                                    </div>
                                    <!--End Product Button-->
                                </div>
                                <!--End Product Image-->
                                <!--Start Product Details-->
                                <div class="product-details text-center">
                                    <!--Product Name-->
                                    <div class="product-name">
                                        <a href="product-layout1.html">Weave Hoodie Sweatshirt</a>
                                    </div>
                                    <!--End Product Name-->
                                    <!--Product Price-->
                                    <div class="product-price">
                                        <span class="price">$199.00</span>
                                    </div>
                                    <!--End Product Price-->
                                    <!--Product Review-->
                                    <div class="product-review d-flex align-items-center justify-content-center">
                                        <i class="an an-star"></i><i class="an an-star-o"></i><i class="an an-star-o"></i><i class="an an-star-o"></i><i class="an an-star-o"></i>
                                        <span class="caption hidden ms-2">2 reviews</span>
                                    </div>
                                    <!--End Product Review-->
                                    <!--Color Variant-->
                                    <ul class="swatches">
                                        <li class="swatch medium radius darkgreen"><span class="tooltip-label">darkgreen</span></li>
                                    </ul>
                                    <!--End Color Variant-->
                                </div>
                                <!--End Product Details-->
                            </div>
                        </div>
                    </div>
                </section>
                <!--End Recently Viewed Products-->

                <!--Customize Services-->
                <div class="section about-service product-service pb-0">
                    <div class="container">
                        <div class="section-header col-12">
                            <h2 class="text-transform-none">Why Optimal?</h2>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4 text-center mb-4 mb-md-0">
                                <div class="service-info">
                                    <i class="icon an an-desktop mb-3"></i>
                                    <div class="text">
                                        <h4>Design Quality</h4>
                                        <p>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for interested.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4 text-center mb-4 mb-md-0">
                                <div class="service-info">
                                    <i class="icon an an-mobile-alt mb-3"></i>
                                    <div class="text">
                                        <h4>Mobile First Design</h4>
                                        <p>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for interested.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4 text-center mb-0 mb-md-0">
                                <div class="service-info">
                                    <i class="icon an an-sort-amount-up mb-3"></i>
                                    <div class="text">
                                        <h4>High Speed & Performance</h4>
                                        <p>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for interested.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End Customize Services-->
            </div>
            <!--End Body Container-->

            <!--Footer-->
            <div class="footer footer-1">
                <div class="footer-top clearfix">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 text-center about-col mb-4">
                                <img src="assets/images/footer-logo.png" alt="Optimal" class="mb-3"/>
                                <p>55 Gallaxy Enque, 2568 steet, 23568 NY</p>
                                <p class="mb-0 mb-md-3">Phone: <a href="tel:+011234567890">(+01) 123 456 7890</a> <span class="mx-1">|</span> Email: <a href="mailto:info@example.com">info@example.com</a></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-3 footer-links">
                                <h4 class="h4">Informations</h4>
                                <ul>
                                    <li><a href="my-account.html">My Account</a></li>
                                    <li><a href="aboutus-style1.html">About us</a></li>
                                    <li><a href="login.html">Login</a></li>
                                    <li><a href="privacy-policy.html">Privacy policy</a></li>
                                    <li><a href="#">Terms &amp; condition</a></li>
                                </ul>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-2 footer-links">
                                <h4 class="h4">Quick Shop</h4>
                                <ul>
                                    <li><a href="#">Women</a></li>
                                    <li><a href="#">Men</a></li>
                                    <li><a href="#">Kids</a></li>
                                    <li><a href="#">Sportswear</a></li>
                                    <li><a href="#">Sale</a></li>
                                </ul>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-3 footer-links">
                                <h4 class="h4">Customer Services</h4>
                                <ul>
                                    <li><a href="#">Request Personal Data</a></li>
                                    <li><a href="faqs-style1.html">FAQ's</a></li>
                                    <li><a href="contact-style1.html">Contact Us</a></li>
                                    <li><a href="#">Orders and Returns</a></li>
                                    <li><a href="#">Support Center</a></li>
                                </ul>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4 newsletter-col">
                                <div class="display-table pt-md-3 pt-lg-0">
                                    <div class="display-table-cell footer-newsletter">
                                        <form action="#" method="post">
                                            <label class="h4">NEWSLETTER SIGN UP</label>
                                            <p>Enter Your Email To Receive Daily News And Get 20% Off Coupon For All Items.</p>
                                            <div class="input-group">
                                                <input type="email" class="brounded-start input-group__field newsletter-input mb-0" name="EMAIL" value="" placeholder="Email address" required>
                                                <span class="input-group__btn">
                                                    <button type="submit" class="btn newsletter__submit rounded-end" name="commit" id="Subscribe"><i class="an an-envelope-l"></i></button>
                                                </span>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <ul class="list-inline social-icons mt-3 pt-1">
                                    <li class="list-inline-item"><a href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Facebook"><i class="an an-facebook" aria-hidden="true"></i></a></li>
                                    <li class="list-inline-item"><a href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Twitter"><i class="an an-twitter" aria-hidden="true"></i></a></li>
                                    <li class="list-inline-item"><a href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Pinterest"><i class="an an-pinterest-p" aria-hidden="true"></i></a></li>
                                    <li class="list-inline-item"><a href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Instagram"><i class="an an-instagram" aria-hidden="true"></i></a></li>
                                    <li class="list-inline-item"><a href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="TikTok"><i class="an an-tiktok" aria-hidden="true"></i></a></li>
                                    <li class="list-inline-item"><a href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Whatsapp"><i class="an an-whatsapp" aria-hidden="true"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom clearfix">
                    <div class="container">
                        <div class="d-flex-center flex-column justify-content-md-between flex-md-row-reverse">
                            <img src="assets/images/payment.png" alt="Paypal Visa Payments"/>
                            <div class="copytext text-uppercase">&copy; 2023 Optimal. All Rights Reserved.</div>
                        </div>
                    </div>
                </div>
            </div>
            <!--End Footer-->

            <!--Scoll Top-->
            <span id="site-scroll"><i class="icon an an-chevron-up"></i></span>
            <!--End Scoll Top-->

            <!--MiniCart Drawer-->
            <div class="minicart-right-drawer modal right fade" id="minicart-drawer">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div id="cart-drawer" class="block block-cart">
                            <div class="minicart-header">
                                <a href="javascript:void(0);" class="close-cart" data-bs-dismiss="modal" aria-label="Close"><i class="an an-times-r" aria-hidden="true" data-bs-toggle="tooltip" data-bs-placement="left" title="Close"></i></a>
                                <h4 class="fs-6">Your cart (2 Items)</h4>
                            </div>
                            <div class="minicart-content">
                                <ul class="clearfix">
                                    <li class="item d-flex justify-content-center align-items-center">
                                        <a class="product-image" href="product-layout1.html">
                                            <img class="blur-up lazyload" src="assets/images/products/cart-product-img1.jpg" data-src="assets/images/products/cart-product-img1.jpg" alt="image" title="">
                                        </a>
                                        <div class="product-details">
                                            <a class="product-title" href="product-layout1.html">Floral Crop Top</a>
                                            <div class="variant-cart">Black / XL</div>
                                            <div class="priceRow">
                                                <div class="product-price">
                                                    <span class="money">$59.00</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="qtyDetail text-center">
                                            <div class="wrapQtyBtn">
                                                <div class="qtyField">
                                                    <a class="qtyBtn minus" href="javascript:void(0);"><i class="icon an an-minus-r" aria-hidden="true"></i></a>
                                                    <input type="text" name="quantity" value="1" class="qty">
                                                    <a class="qtyBtn plus" href="javascript:void(0);"><i class="icon an an-plus-l" aria-hidden="true"></i></a>
                                                </div>
                                            </div>
                                            <a href="#" class="edit-i remove"><i class="icon an an-edit-l" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"></i></a>
                                            <a href="#" class="remove"><i class="an an-times-r" data-bs-toggle="tooltip" data-bs-placement="top" title="Remove"></i></a>
                                        </div>
                                    </li>
                                    <li class="item d-flex justify-content-center align-items-center">
                                        <a class="product-image" href="product-layout1.html">
                                            <img class="blur-up lazyload" src="assets/images/products/cart-product-img2.jpg" data-src="assets/images/products/cart-product-img2.jpg" alt="image" title="">
                                        </a>
                                        <div class="product-details">
                                            <a class="product-title" href="product-layout1.html">V Neck T-shirts</a>
                                            <div class="variant-cart">Blue / XL</div>
                                            <div class="priceRow">
                                                <div class="product-price">
                                                    <span class="money">$199.00</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="qtyDetail text-center">
                                            <div class="wrapQtyBtn">
                                                <div class="qtyField">
                                                    <a class="qtyBtn minus" href="javascript:void(0);"><i class="icon an an-minus-r" aria-hidden="true"></i></a>
                                                    <input type="text" name="quantity" value="1" class="qty">
                                                    <a class="qtyBtn plus" href="javascript:void(0);"><i class="icon an an-plus-l" aria-hidden="true"></i></a>
                                                </div>
                                            </div>
                                            <a href="#" class="edit-i remove"><i class="icon an an-edit-l" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"></i></a>
                                            <a href="#" class="remove"><i class="an an-times-r" data-bs-toggle="tooltip" data-bs-placement="top" title="Remove"></i></a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="minicart-bottom">
                                <div class="shipinfo text-center mb-3 text-uppercase">
                                    <p class="freeShipMsg"><i class="an an-truck fs-5 me-2 align-middle"></i>SPENT <b>$199.00</b> MORE FOR FREE SHIPPING</p>
                                </div>
                                <div class="subtotal">
                                    <span>Total:</span>
                                    <span class="product-price">$93.13</span>
                                </div>
                                <a href="checkout-style1.html" class="w-100 p-2 my-2 btn btn-outline-primary proceed-to-checkout rounded">Proceed to Checkout</a>
                                <a href="cart-style1.html" class="w-100 btn-primary cart-btn rounded">View Cart</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--End MiniCart Drawer-->
            <div class="modalOverly"></div>

            <!-- Shipping Popup-->
            <div id="ShippingInfo" class="mfpbox mfp-with-anim mfp-hide">
                <h5>DELIVERY</h5>
                <ul>
                    <li>Dispatch: Within 24 Hours</li>
                    <li>Free shipping across all products on a minimum purchase of $50.</li>
                    <li>International delivery time - 7-10 business days</li>
                    <li>Cash on delivery might be available</li>
                    <li>Easy 30 days returns and exchanges</li>
                </ul>
                <h5>RETURNS</h5>
                <p>If you do not like the product you can return it within 15 days - no questions asked. This excludes bodysuits, swimwear and clearance sale items. We have an easy and hassle free return policy. Please look at our Delivery &amp; Returns section for further information.</p>
            </div>
            <!-- End Shipping Popup-->

            <!--Product Enuiry Popup-->
            <div id="productInquiry" class="mfpbox mfp-with-anim mfp-hide">
                <div class="contact-form form-vertical p-lg-1">
                    <div class="page-title"><h3>Product Inquiry Popup</h3></div>
                    <form method="post" action="#" id="contact_form" class="contact-form">
                        <div class="formFeilds">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <input type="text" id="ContactFormName" name="contact[name]" placeholder="Name" value="" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <input type="email" id="ContactFormEmail" name="contact[email]" placeholder="Email" value="" required />
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <input type="tel" id="ContactFormPhone" name="contact[phone]" pattern="[0-9\-]*" placeholder="Phone Number" value="" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <input type="text" id="ContactFormSubject" name="contact[subject]" placeholder="Subject" value="" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <textarea rows="8" id="ContactFormMessage" name="contact[body]" placeholder="Message" required></textarea>
                                    </div>
                                </div>  
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <input type="submit" class="btn rounded w-100" value="Send Message" />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--End Product Enuiry Popup-->

            <!--Size Chart-->
            <div id="sizechart" class="mfpbox mfp-with-anim mfp-hide">
                <h4 class="fw-bold">Size Guide</h4>
                <p class="text-center">This is a standardised guide to give you an idea of what size you will need, however some brands may vary from these conversions.</p>
                <p><strong>Ready to Wear Clothing</strong></p>
                <div class="table-responsive px-1">
                    <table class="table table-bordered align-middle">
                        <tbody>
                            <tr>
                                <th>Size</th>
                                <th>XXS - XS</th>
                                <th>XS - S</th>
                                <th>S - M</th>
                                <th>M - L</th>
                                <th>L - XL</th>
                                <th>XL - XXL</th>
                            </tr>
                            <tr>
                                <td>UK</td>
                                <td>6</td>
                                <td>8</td>
                                <td>10</td>
                                <td>12</td>
                                <td>14</td>
                                <td>16</td>
                            </tr>
                            <tr>
                                <td>US</td>
                                <td>2</td>
                                <td>4</td>
                                <td>6</td>
                                <td>8</td>
                                <td>10</td>
                                <td>12</td>
                            </tr>
                            <tr>
                                <td>Italy (IT)</td>
                                <td>38</td>
                                <td>40</td>
                                <td>42</td>
                                <td>44</td>
                                <td>46</td>
                                <td>48</td>
                            </tr>
                            <tr>
                                <td>France (FR/EU)</td>
                                <td>34</td>
                                <td>36</td>
                                <td>38</td>
                                <td>40</td>
                                <td>42</td>
                                <td>44</td>
                            </tr>
                            <tr>
                                <td>Denmark</td>
                                <td>32</td>
                                <td>34</td>
                                <td>36</td>
                                <td>38</td>
                                <td>40</td>
                                <td>42</td>
                            </tr>
                            <tr>
                                <td>Russia</td>
                                <td>40</td>
                                <td>42</td>
                                <td>44</td>
                                <td>46</td>
                                <td>48</td>
                                <td>50</td>
                            </tr>
                            <tr>
                                <td>Germany</td>
                                <td>32</td>
                                <td>34</td>
                                <td>36</td>
                                <td>38</td>
                                <td>40</td>
                                <td>42</td>
                            </tr>
                            <tr>
                                <td>Japan</td>
                                <td>5</td>
                                <td>7</td>
                                <td>9</td>
                                <td>11</td>
                                <td>13</td>
                                <td>15</td>
                            </tr>
                            <tr>
                                <td>Australia</td>
                                <td>6</td>
                                <td>8</td>
                                <td>10</td>
                                <td>12</td>
                                <td>14</td>
                                <td>16</td>
                            </tr>
                            <tr>
                                <td>Korea</td>
                                <td>33</td>
                                <td>44</td>
                                <td>55</td>
                                <td>66</td>
                                <td>77</td>
                                <td>88</td>
                            </tr>
                            <tr>
                                <td>China</td>
                                <td>160/84</td>
                                <td>165/86</td>
                                <td>170/88</td>
                                <td>175/90</td>
                                <td>180/92</td>
                                <td>185/94</td>
                            </tr>
                            <tr>
                                <td><strong>Jeans</strong></td>
                                <td>24-25</td>
                                <td>26-27</td>
                                <td>27-28</td>
                                <td>29-30</td>
                                <td>31-32</td>
                                <td>32-33</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button title="Close (Esc)" type="button" class="mfp-close">×</button>
            </div>
            <!--End Size Chart-->

            <!-- Sticky Cart -->
            <div class="stickyCart">
                <div class="container">
                    <form method="post" action="#" id="stickycart-form" class="d-flex-center justify-content-center">
                        <div class="img"><img src="assets/images/products/product-6-1.jpg" class="product-featured-img" alt="product" /></div>
                        <div class="sticky-title ms-2 ps-1 pe-5">Floral Crop Top</div>
                        <div class="stickyOptions rounded position-relative">
                            <div class="selectedOpt rounded">Red / S - <span class="money">$130.00</span></div>
                            <ul>
                                <li class="vrOpt" data-val="31677941252156" data-no="0">Red / S - $130.00</li>
                                <li class="vrOpt" data-val="31677941383228" data-no="1">Red / M - $130.00</li>
                                <li class="vrOpt" data-val="31677941514300" data-no="2">Green / L - $130.00</li>
                                <li class="vrOpt" data-val="31677941678140" data-no="3">Green / XL - $130.00</li>
                                <li class="vrOpt" data-val="31677941284924" data-no="4">Pink / S - $104.00</li>
                                <li class="vrOpt" data-val="31677941415996" data-no="5">Pink / M - $130.00</li>
                                <li class="vrOpt" data-val="31677941579836" data-no="6">Peach / L - $130.00</li>
                                <li class="vrOpt" data-val="31677941710908" data-no="7">Peach / XL - $130.00</li>
                                <li class="soldout">White / S - Sold out</li>
                                <li class="vrOpt" data-val="31677941481532" data-no="9">White / M - $130.00</li>
                                <li class="vrOpt" data-val="31677941612604" data-no="10">Blue / L - $130.00</li>
                                <li class="vrOpt" data-val="31677941776444" data-no="11">Blue / XL - $130.00</li>
                            </ul>
                        </div>
                        <select name="id" id="variantOptions1" class="product-form__variants selectbox no-js d-none ms-3">
                            <option selected="selected" value="31677941252156">Red / S</option>
                            <option value="31677941383228">Red / S</option>
                            <option value="31677941514300">Red / M</option>
                            <option value="31677941678140">Green / XL</option>
                            <option value="31677941284924">Pink / S</option>
                            <option value="31677941415996">Pink / M</option>
                            <option value="31677941579836">Peach / L</option>
                            <option value="31677941710908">Peach / XL</option>
                            <option disabled="disabled">White / S - Sold out</option>
                            <option value="31677941481532">White / M</option>
                            <option value="31677941612604">Blue / L</option>
                            <option value="31677941776444">Blue / XL</option>
                        </select>
                        <div class="qtyField mx-2" title="Quantity">
                            <a class="qtyBtn minus" href="javascript:void(0);"><i class="icon an an-minus-r" aria-hidden="true"></i></a>
                            <input type="text" id="quantity1" name="quantity" value="1" class="product-form__input qty">
                            <a class="qtyBtn plus" href="javascript:void(0);"><i class="icon an an-plus-r" aria-hidden="true"></i></a>
                        </div>
                        <button type="submit" name="add" class="btn rounded btn-secondary product-form__cart-submit"><span>Add to cart</span></button>                    
                    </form>
                </div>
            </div>
            <!-- End Sticky Cart -->

            <!--Quickview Popup-->
            <div class="loadingBox"><div class="an-spin"><i class="icon an an-spinner4"></i></div></div>
            <div id="quickView-modal" class="mfp-with-anim mfp-hide">
                <button title="Close (Esc)" type="button" class="mfp-close">×</button>
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-6 col-lg-6 mb-3 mb-md-0">
                        <!--Model thumbnail -->
                        <div id="quickView" class="carousel slide">
                            <!-- Image Slide carousel items -->
                            <div class="carousel-inner">
                                <div class="item carousel-item active" data-bs-slide-number="0">
                                    <img class="blur-up lazyload" data-src="assets/images/products/product-5.jpg" src="assets/images/products/product-5.jpg" alt="image" title="" />
                                </div>
                                <div class="item carousel-item" data-bs-slide-number="1">
                                    <img class="blur-up lazyload" data-src="assets/images/products/product-5-1.jpg" src="assets/images/products/product-5-1.jpg" alt="image" title="" />
                                </div>
                                <div class="item carousel-item" data-bs-slide-number="2">
                                    <img class="blur-up lazyload" data-src="assets/images/products/product-5-2.jpg" src="assets/images/products/product-5-2.jpg" alt="image" title="" />
                                </div>
                                <div class="item carousel-item" data-bs-slide-number="3">
                                    <img class="blur-up lazyload" data-src="assets/images/products/product-5-3.jpg" src="assets/images/products/product-5-3.jpg" alt="image" title="" />
                                </div>
                                <div class="item carousel-item" data-bs-slide-number="4">
                                    <img class="blur-up lazyload" data-src="assets/images/products/product-5-4.jpg" src="assets/images/products/product-5-4.jpg" alt="image" title="" />
                                </div>
                            </div>
                            <!-- End Image Slide carousel items -->
                            <!-- Thumbnail image -->
                            <div class="model-thumbnail-img">
                                <!-- Thumbnail slide -->
                                <div class="carousel-indicators list-inline">
                                    <div class="list-inline-item active" id="carousel-selector-0" data-bs-slide-to="0" data-bs-target="#quickView">
                                        <img class="blur-up lazyload" data-src="assets/images/products/product-5.jpg" src="assets/images/products/product-5.jpg" alt="image" title="" />
                                    </div>
                                    <div class="list-inline-item" id="carousel-selector-1" data-bs-slide-to="1" data-bs-target="#quickView">
                                        <img class="blur-up lazyload" data-src="assets/images/products/product-5-1.jpg" src="assets/images/products/product-5-1.jpg" alt="image" title="" />
                                    </div>
                                    <div class="list-inline-item" id="carousel-selector-2" data-bs-slide-to="2" data-bs-target="#quickView">
                                        <img class="blur-up lazyload" data-src="assets/images/products/product-5-2.jpg" src="assets/images/products/product-5-2.jpg" alt="image" title="" />
                                    </div>
                                    <div class="list-inline-item" id="carousel-selector-3" data-bs-slide-to="3" data-bs-target="#quickView">
                                        <img class="blur-up lazyload" data-src="assets/images/products/product-5-3.jpg" src="assets/images/products/product-5-3.jpg" alt="image" title="" />
                                    </div>
                                    <div class="list-inline-item" id="carousel-selector-4" data-bs-slide-to="4" data-bs-target="#quickView">
                                        <img class="blur-up lazyload" data-src="assets/images/products/product-5-4.jpg" src="assets/images/products/product-5-4.jpg" alt="image" title="" />
                                    </div>
                                </div>
                                <!-- End Thumbnail slide -->
                                <!-- Carousel arrow button -->
                                <a class="carousel-control-prev carousel-arrow" href="#quickView" data-bs-target="#quickView" data-bs-slide="prev"><i class="icon an-3x an an-angle-left"></i><span class="visually-hidden">Previous</span></a>
                                <a class="carousel-control-next carousel-arrow" href="#quickView" data-bs-target="#quickView" data-bs-slide="next"><i class="icon an-3x an an-angle-right"></i><span class="visually-hidden">Next</span></a>
                                <!-- End Carousel arrow button -->
                            </div>
                            <!-- End Thumbnail image -->
                        </div>
                        <!--End Model thumbnail -->
                        <div class="text-center mt-3"><a href="product-layout1.html">VIEW MORE DETAILS</a></div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                        <h2 class="product-title">Product Quick View Popup</h2>
                        <div class="product-review d-flex-center mb-2">
                            <div class="rating"><i class="icon an an-star"></i><i class="icon an an-star"></i><i class="icon an an-star"></i><i class="icon an an-star"></i><i class="icon an an-star-o"></i></div>
                            <div class="reviews ms-2"><a href="#">5 Reviews</a></div>
                        </div>
                        <div class="product-info">
                            <p class="product-vendor">Vendor:  <span class="fw-normal"><a href="#" class="fw-normal">Optimal</a></span></p>  
                            <p class="product-type">Product Type: <span class="fw-normal">Tops</span></p> 
                            <p class="product-sku">SKU:  <span class="fw-normal">50-ABC</span></p>
                        </div>
                        <div class="pro-stockLbl my-2">
                            <span class="d-flex-center stockLbl instock"><i class="icon an an-check-cil"></i><span> In stock</span></span>
                            <span class="d-flex-center stockLbl preorder d-none"><i class="icon an an-clock-r"></i><span> Pre-order Now</span></span>
                            <span class="d-flex-center stockLbl outstock d-none"><i class="icon an an-times-cil"></i> <span>Sold out</span></span>
                            <span class="d-flex-center stockLbl lowstock d-none" data-qty="15"><i class="icon an an-exclamation-cir"></i><span> Order now, Only  <span class="items">10</span>  left!</span></span>
                        </div>
                        <div class="pricebox">
                            <span class="price old-price">$400.00</span><span class="price product-price__sale">$300.00</span>
                        </div>
                        <div class="sort-description">Optimal Multipurpose Bootstrap 5 Html Template that will give you and your customers a smooth shopping experience which can be used for various kinds of stores such as fashion.. </div>
                        <form method="post" action="#" id="product_form--option" class="product-form">
                            <div class="product-options d-flex-wrap">
                                <div class="swatch clearfix swatch-0 option1">
                                    <div class="product-form__item">
                                        <label class="label d-flex">Color:<span class="required d-none">*</span> <span class="slVariant ms-1 fw-bold">Black</span></label>
                                        <ul class="swatches-image swatches d-flex-wrap list-unstyled clearfix">
                                            <li data-value="Black" class="swatch-element color available active">
                                                <label class="rounded swatchLbl small color black" title="Black"></label>
                                                <span class="tooltip-label top">Black</span>
                                            </li>
                                            <li data-value="Green" class="swatch-element color available">
                                                <label class="rounded swatchLbl small color green" title="Green"></label>
                                                <span class="tooltip-label top">Green</span>
                                            </li>
                                            <li data-value="Orange" class="swatch-element color available">
                                                <label class="rounded swatchLbl small color orange" title="Orange"></label>
                                                <span class="tooltip-label top">Orange</span>
                                            </li>
                                            <li data-value="Blue" class="swatch-element color available">
                                                <label class="rounded swatchLbl small color blue" title="Blue"></label>
                                                <span class="tooltip-label top">Blue</span>
                                            </li>
                                            <li data-value="Red" class="swatch-element color available">
                                                <label class="rounded swatchLbl small color red" title="Red"></label>
                                                <span class="tooltip-label top">Red</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="swatch clearfix swatch-1 option2">
                                    <div class="product-form__item">
                                        <label class="label">Size:<span class="required d-none">*</span> <span class="slVariant ms-1 fw-bold">XS</span></label>
                                        <ul class="swatches-size d-flex-center list-unstyled clearfix swatch-1 option2">
                                            <li data-value="XS" class="swatch-element xs available active">
                                                <label class="swatchLbl rounded medium" title="XS">XS</label>
                                                <span class="tooltip-label">XS</span>
                                            </li>
                                            <li data-value="S" class="swatch-element s available">
                                                <label class="swatchLbl rounded medium" title="S">S</label>
                                                <span class="tooltip-label">S</span>
                                            </li>
                                            <li data-value="M" class="swatch-element m available">
                                                <label class="swatchLbl rounded medium" title="M">M</label>
                                                <span class="tooltip-label">M</span>
                                            </li>
                                            <li data-value="L" class="swatch-element l available">
                                                <label class="swatchLbl rounded medium" title="L">L</label>
                                                <span class="tooltip-label">L</span>
                                            </li>
                                            <li data-value="XL" class="swatch-element xl available">
                                                <label class="swatchLbl rounded medium" title="XL">XL</label>
                                                <span class="tooltip-label">XL</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="product-action d-flex-wrap w-100 mb-3 clearfix">
                                    <div class="quantity">
                                        <div class="qtyField rounded">
                                            <a class="qtyBtn minus" href="javascript:void(0);"><i class="icon an an-minus-r" aria-hidden="true"></i></a>
                                            <input type="text" name="quantity" value="1" class="product-form__input qty">
                                            <a class="qtyBtn plus" href="javascript:void(0);"><i class="icon an an-plus-l" aria-hidden="true"></i></a>
                                        </div>
                                    </div>                                
                                    <div class="add-to-cart ms-3 fl-1">
                                        <button type="button" class="btn button-cart rounded"><span>Add to cart</span></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="wishlist-btn d-flex-center">
                            <a class="add-wishlist d-flex-center text-uppercase me-3" href="my-wishlist.html" title="Add to Wishlist"><i class="icon an an-heart-l me-1"></i> <span>Add to Wishlist</span></a>
                            <a class="add-compare d-flex-center text-uppercase" href="compare-style1.html" title="Add to Compare"><i class="icon an an-random-r me-2"></i> <span>Add to Compare</span></a>
                        </div>
                        <!-- Social Sharing -->
                        <div class="social-sharing share-icon d-flex-center mx-0 mt-3">
                            <span class="sharing-lbl me-2">Share :</span>
                            <a href="#" class="d-flex-center btn btn-link btn--share share-facebook" data-bs-toggle="tooltip" data-bs-placement="top" title="Share on Facebook"><i class="icon an an-facebook mx-1"></i><span class="share-title d-none">Facebook</span></a>
                            <a href="#" class="d-flex-center btn btn-link btn--share share-twitter" data-bs-toggle="tooltip" data-bs-placement="top" title="Tweet on Twitter"><i class="icon an an-twitter mx-1"></i><span class="share-title d-none">Tweet</span></a>
                            <a href="#" class="d-flex-center btn btn-link btn--share share-pinterest" data-bs-toggle="tooltip" data-bs-placement="top" title="Pin on Pinterest"><i class="icon an an-pinterest-p mx-1"></i> <span class="share-title d-none">Pin it</span></a>
                            <a href="#" class="d-flex-center btn btn-link btn--share share-linkedin" data-bs-toggle="tooltip" data-bs-placement="top" title="Share on Instagram"><i class="icon an an-instagram mx-1"></i><span class="share-title d-none">Instagram</span></a>
                            <a href="#" class="d-flex-center btn btn-link btn--share share-whatsapp" data-bs-toggle="tooltip" data-bs-placement="top" title="Share on WhatsApp"><i class="icon an an-whatsapp mx-1"></i><span class="share-title d-none">WhatsApp</span></a>
                            <a href="#" class="d-flex-center btn btn-link btn--share share-email" data-bs-toggle="tooltip" data-bs-placement="top" title="Share by Email"><i class="icon an an-envelope-l mx-1"></i><span class="share-title d-none">Email</span></a>
                        </div>
                        <!-- End Social Sharing -->
                    </div>
                </div>
            </div>
            <!--End Quickview Popup-->

            <!--Addtocart Added Popup-->
            <div id="pro-addtocart-popup" class="mfp-with-anim mfp-hide">
                <button title="Close (Esc)" type="button" class="mfp-close">×</button>
                <div class="addtocart-inner text-center clearfix">
                    <h4 class="title mb-3 text-success">Added to your shopping cart successfully.</h4>
                    <div class="pro-img mb-3">
                        <img class="img-fluid blur-up lazyload" src="assets/images/products/add-to-cart-popup.jpg" data-src="assets/images/products/add-to-cart-popup.jpg" alt="Added to your shopping cart successfully." title="Added to your shopping cart successfully." />
                    </div>
                    <div class="pro-details">   
                        <h5 class="pro-name mb-0">Ditsy Floral Dress</h5>
                        <p class="sku my-2">Color: Gray</p>
                        <p class="mb-0 qty-total">1 X $113.88</p>
                        <div class="addcart-total bg-light mt-3 mb-3 p-2">
                            Total: <b class="price">$113.88</b>
                        </div>
                        <div class="button-action">
                            <a href="checkout-style1.html" class="btn btn-primary view-cart mx-1 rounded">Go To Checkout</a>
                            <a href="index.html" class="btn btn-secondary rounded">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Addtocart Added Popup-->


            <!-- Including Jquery -->
            <script src="assets/js/vendor/jquery-min.js"></script>
            <script src="assets/js/vendor/js.cookie.js"></script>
            <!--Including Javascript-->
            <script src="assets/js/plugins.js"></script>
            <script src="assets/js/main.js"></script>
            <!-- Photoswipe Gallery -->
            <script src="assets/js/vendor/photoswipe.min.js"></script>
            <script>
                $(function () {
                    var $pswp = $('.pswp')[0],
                            image = [],
                            getItems = function () {
                                var items = [];
                                $('.lightboximages a').each(function () {
                                    var $href = $(this).attr('href'),
                                            $size = $(this).data('size').split('x'),
                                            item = {
                                                src: $href,
                                                w: $size[0],
                                                h: $size[1]
                                            };
                                    items.push(item);
                                });
                                return items;
                            };
                    var items = getItems();

                    $.each(items, function (index, value) {
                        image[index] = new Image();
                        image[index].src = value['src'];
                    });
                    $('.prlightbox').on('click', function (event) {
                        event.preventDefault();

                        var $index = $(".active-thumb").parent().attr('data-slick-index');
                        $index++;
                        $index = $index - 1;

                        var options = {
                            index: $index,
                            bgOpacity: 0.7,
                            showHideOpacity: true
                        };
                        var lightBox = new PhotoSwipe($pswp, PhotoSwipeUI_Default, items, options);
                        lightBox.init();
                    });
                });
            </script>
            <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="pswp__bg"></div>
                <div class="pswp__scroll-wrap">
                    <div class="pswp__container">
                        <div class="pswp__item"></div>
                        <div class="pswp__item"></div>
                        <div class="pswp__item"></div>
                    </div>
                    <div class="pswp__ui pswp__ui--hidden">
                        <div class="pswp__top-bar">
                            <div class="pswp__counter"></div>
                            <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                            <button class="pswp__button pswp__button--share" title="Share"></button>
                            <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                            <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                            <div class="pswp__preloader">
                                <div class="pswp__preloader__icn">
                                    <div class="pswp__preloader__cut">
                                        <div class="pswp__preloader__donut"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                            <div class="pswp__share-tooltip"></div>
                        </div>
                        <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>
                        <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
                        <div class="pswp__caption"><div class="pswp__caption__center"></div></div>
                    </div>
                </div>
            </div>

        </div>
        <!--End Page Wrapper-->
    </body>
</html>