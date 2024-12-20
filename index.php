<?php require_once('_header.php'); ?>


<!--Body Container-->
<div id="page-content">
    <!--Home Slider-->
    <!-- <section class="slideshow slideshow-wrapper">
                <div class="home-slideshow">
                    <div class="slide">
                        <div class="blur-up lazyload">
                            <img class="blur-up lazyload desktop-hide"
                                data-src="assets/images/slideshow/demo1-banner1.jpg"
                                src="assets/images/slideshow/demo1-banner1.jpg" alt="HIGH CONVERTING"
                                title="HIGH CONVERTING" width="2000" height="840" />
                            <img class="blur-up lazyload mobile-hide"
                                data-src="assets/images/slideshow/demo1-banner1-m.jpg"
                                src="assets/images/slideshow/demo1-banner1-m.jpg" alt="HIGH CONVERTING"
                                title="HIGH CONVERTING" width="705" height="780" />
                            <div class="container">
                                <div
                                    class="slideshow-content slideshow-overlay bottom-middle d-flex justify-content-center align-items-center">
                                    <div class="slideshow-content-in text-center">
                                        <div class="wrap-caption animation style2 whiteText px-2">
                                            <p class="ss-small-title fs-5 mb-2">Simple, Clean</p>
                                            <h1 class="h1 mega-title ss-mega-title fs-1">
                                                HIGH CONVERTING
                                            </h1>
                                            <span class="mega-subtitle fs-6 ss-sub-title">Creative, Flexible and High
                                                Performance Html
                                                Template!</span>
                                            <div class="ss-btnWrap">
                                                <a class="btn btn-lg rounded btn-primary"
                                                    href="shop-left-sidebar.html">Shop Women</a>
                                                <a class="btn btn-lg rounded btn-primary"
                                                    href="shop-left-sidebar.html">Shop Men</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="slide">
                        <div class="blur-up lazyload">
                            <img class="blur-up lazyload desktop-hide"
                                data-src="assets/images/slideshow/demo1-banner2.jpg"
                                src="assets/images/slideshow/demo1-banner2.jpg" alt="MAKING BRAND VISIBLE"
                                title="MAKING BRAND VISIBLE" width="2000" height="840" />
                            <img class="blur-up lazyload mobile-hide"
                                data-src="assets/images/slideshow/demo1-banner2-m.jpg"
                                src="assets/images/slideshow/demo1-banner2-m.jpg" alt="MAKING BRAND VISIBLE"
                                title="MAKING BRAND VISIBLE" width="705" height="780" />
                            <div
                                class="slideshow-content slideshow-overlay bottom-middle container d-flex justify-content-center align-items-center">
                                <div class="slideshow-content-in text-center">
                                    <div class="wrap-caption animation style2 whiteText px-2">
                                        <h2 class="mega-title ss-mega-title fs-1">
                                            MAKING BRAND VISIBLE
                                        </h2>
                                        <span class="mega-subtitle ss-sub-title fs-6">Runs faster. Costs less and never
                                            breaks.<br />
                                            We like to make things look pretty.</span>
                                        <div class="ss-btnWrap">
                                            <a class="btn btn-lg rounded btn-primary" href="shop-left-sidebar.html">Shop
                                                Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section> -->
    <!--End Home Slider-->

    <!--Banner Masonary-->
    <section class="collection-banners style1 ">
        <div class="container-fluid px-0  about-bnr-text">
            <div class="row g-0 align-items-center">
                <div class="col-12 col-sm-12 col-md-12 col-lg-6 row_text">
                    <div class="about-info2 row-text">
                        <h3 class="h6">SINCE 2005 <b class="h1 fs-26 d-block mt-2 fw-bold">The Founder</b></h3>
                        <p>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those
                            interested. "de Finibus Bonorum et Malorum" by Cicero reproduced in their 1914 translation
                            by H. Rackham.</p>
                        <p>There are many variations of passages of Lorem Ipsum available, but the majority have
                            suffered alteration in some form, by injected humour, even slightly believable.</p>
                        <p>All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary,
                            making this the first true generator on the Internet. It uses a dictionary</p>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                    <img class="about-info-img blur-up lazyload" data-src="assets/images/about/about-info-s1.jpg"
                        src="assets/images/about/about-info-s1.jpg" alt="about" />
                </div>
            </div>
        </div>
    </section>
    <!--End Banner Masonary-->






    <!--Spring Summer Product Slider-->
    <section class="section product-slider">
        <div class="container">
            <div class="row">
                <div class="section-header text-uppercase col-12">
                    <h2>Spring Summer</h2>
                    <p>Shop The Latest</p>
                </div>
            </div>
            <div class="productSlider grid-products">
                <?php
                // Retrieve products with their respective category slug
                $statement1 = $pdo->prepare("SELECT p.p_slug, p.p_name, p.p_old_price, p.p_current_price, p.p_qty, p.p_featured_photo, c.tcat_slug 
                                 FROM tbl_product p 
                                 JOIN tbl_top_category c ON p.tcat_id = c.tcat_id");
                $statement1->execute();
                $result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);

                foreach ($result1 as $row1) {
                    // Construct the URL using category slug and product slug
                    $url = $row1['tcat_slug'] . '/' . $row1['p_slug'];
                    ?>
                    <div class="item">
                        <!--Start Product Image-->
                        <div class="product-image">
                            <!--Product Image-->
                            <a href="<?php echo $url ?>" class="product-img">
                                <img class="primary blur-up lazyload"
                                    data-src="assets/uploads/<?php echo htmlspecialchars($row1['p_featured_photo']); ?>"
                                    src="assets/uploads/<?php echo htmlspecialchars($row1['p_featured_photo']); ?>"
                                    alt="<?php echo htmlspecialchars($row1['p_name']); ?>" title="" width="800"
                                    height="960" />
                                <img class="hover blur-up lazyload"
                                    data-src="assets/uploads/<?php echo htmlspecialchars($row1['p_featured_photo']); ?>"
                                    src="assets/uploads/<?php echo htmlspecialchars($row1['p_featured_photo']); ?>"
                                    alt="<?php echo htmlspecialchars($row1['p_name']); ?>" title="" width="800"
                                    height="960" />
                                <?php if ($row1['p_qty'] == 0): ?>
                                    <span class="sold-out"><span class="rounded"><?php echo $row1['p_name']; ?></span></span>
                                <?php endif; ?>
                            </a>
                            <!--end product image-->
                        </div>
                        <!--End Product Image-->
                        <!--Start Product Details-->
                        <div class="product-details ">
                            <!--Product Name-->
                            <div class="product-name text-uppercase">
                                <a href="<?php echo $url ?>"><?php echo htmlspecialchars($row1['p_name']); ?></a>
                            </div>
                            <!--End Product Name-->
                            <!--Product Price-->
                            <div class="product-price">
                                <?php if ($row1['p_old_price']): ?>
                                    <span class="old-price">$<?php echo number_format($row1['p_old_price'], 2); ?></span>
                                <?php endif; ?>
                                <span class="price">$<?php echo number_format($row1['p_current_price'], 2); ?></span>
                            </div>
                            <div class="col-12 mt-3">
                                <a href="<?php echo $url ?>" class="btn-primary btn-lg rounded">Learn More...</a>
                            </div>
                        </div>
                        <!--End Product Details-->
                    </div>
                <?php } ?>
            </div>

        </div>
    </section>
    <!--End Spring Summer Product Slider-->



    <!--Testimonial Slider-->
    <section class="section testimonial-slider style1">
        <div class="container">
            <div class="row">
                <div class="col-12 section-header style1">
                    <div class="section-header-left">
                        <h2>Testimonials</h2>
                    </div>
                </div>
            </div>
            <div class="quote-wraper">
                <!--Testimonial Slider Items-->
                <div class="quotes-slider">
                    <div class="quotes-slide">
                        <blockquote class="quotes-slider__text text-center">
                            <div class="testimonial-image">
                                <img class="blur-up lazyload" data-src="assets/images/testimonial-photo2.jpg"
                                    src="assets/images/testimonial-photo2.jpg" alt="Shetty Jamie"
                                    title="Shetty Jamie" />
                            </div>
                            <div class="rte-setting">
                                <p>
                                    Lorem Ipsum is simply dummy text of the printing and
                                    typesetting industry. Lorem Ipsum has been the
                                    industry's standard dummy text ever since the 1500s.
                                </p>
                            </div>
                            <div class="product-review">
                                <i class="an an-star"></i>
                                <i class="an an-star"></i>
                                <i class="an an-star"></i>
                                <i class="an an-star"></i>
                                <i class="an an-star"></i>
                            </div>
                            <p class="authour">Shetty Jamie,</p>
                            <p class="cmp-name">Kollision</p>
                        </blockquote>
                    </div>

                </div>
                <!--Testimonial Slider Items-->
            </div>
        </div>
    </section>
    <!--End Testimonial Slider-->
    <!--Banner Masonary-->

    <!--End Blog Post-->

    <!--Brand Logo Slider-->
    <section class="section logo-section ">
        <div class="container">
            <div class="section-header ">
                <h2>Our Brands</h2>
                <p>Lorem ipsum dolor sit amet</p>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="logo-bar">
                        <div class="logo-bar__item">
                            <a href="#"><img class="blur-up lazyload" data-src="assets/images/logo/brandlogo1.png"
                                    src="assets/images/logo/brandlogo1.png" alt="brand" title="" /></a>
                        </div>
                        <div class="logo-bar__item">
                            <a href="#"><img class="blur-up lazyload" data-src="assets/images/logo/brandlogo2.png"
                                    src="assets/images/logo/brandlogo2.png" alt="brand" title="" /></a>
                        </div>
                        <div class="logo-bar__item">
                            <a href="#"><img class="blur-up lazyload" data-src="assets/images/logo/brandlogo3.png"
                                    src="assets/images/logo/brandlogo3.png" alt="brand" title="" /></a>
                        </div>
                        <div class="logo-bar__item">
                            <a href="#"><img class="blur-up lazyload" data-src="assets/images/logo/brandlogo4.png"
                                    src="assets/images/logo/brandlogo4.png" alt="brand" title="" /></a>
                        </div>
                        <div class="logo-bar__item">
                            <a href="#"><img class="blur-up lazyload" data-src="assets/images/logo/brandlogo5.png"
                                    src="assets/images/logo/brandlogo5.png" alt="brand" title="" /></a>
                        </div>
                        <div class="logo-bar__item">
                            <a href="#"><img class="blur-up lazyload" data-src="assets/images/logo/brandlogo6.png"
                                    src="assets/images/logo/brandlogo6.png" alt="brand" title="" /></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--End Brand Logo Slider-->
</div>
<!--End Body Container-->
<?php require_once('_footer.php'); ?>