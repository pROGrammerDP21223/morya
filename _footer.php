
<!--Footer-->
<div class="footer footer-1">
    <div class="footer-top clearfix">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 text-center about-col mb-4">
                    <img src="assets/uploads/<?php echo $logo; ?>" alt="Optimal" class="mb-3" />
                    <p><?php echo $contact_address; ?></p>
                    <p class="mb-0 mb-md-3">
                        Phone: <a href="tel:<?php echo $contact_phone; ?>"><?php echo $contact_phone; ?></a>
                        <span class="mx-1">|</span> Email:
                        <a href="mailto:<?php echo $contact_email; ?>"><?php echo $contact_email; ?></a>
                    </p>
                </div>
                <div class="row text-center justify-content-center">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-4 newsletter-col text-center">
                        <div class="display-table pt-md-3 pt-lg-0">
                            <div class="display-table-cell footer-newsletter">
                                <form action="#" method="post">

                                    <div class="input-group">
                                        <input type="email"
                                            class="brounded-start input-group__field newsletter-input mb-0" name="EMAIL"
                                            value="" placeholder="Email address" required />
                                        <span class="input-group__btn">
                                            <button type="submit" class="btn newsletter__submit rounded-end"
                                                name="commit" id="Subscribe">
                                                <i class="an an-envelope-l"></i>
                                            </button>
                                        </span>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <ul class="list-inline social-icons mt-3 pt-1 justify-content-center">
                            <li class="list-inline-item">
                                <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Facebook"><i
                                        class="an an-facebook" aria-hidden="true"></i></a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Twitter"><i
                                        class="an an-twitter" aria-hidden="true"></i></a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Pinterest"><i
                                        class="an an-pinterest-p" aria-hidden="true"></i></a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Instagram"><i
                                        class="an an-instagram" aria-hidden="true"></i></a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="TikTok"><i
                                        class="an an-tiktok" aria-hidden="true"></i></a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Whatsapp"><i
                                        class="an an-whatsapp" aria-hidden="true"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
            <style>
                .lead-ft-name {
                    font-weight: 600;

                }
            </style>
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 footer-links py-5">

                    <ul class="" aria-labelledby="menu-4">
                        <li class="row">
                            <ul class="col footer-products">
                                <?php
                                $statement = $pdo->prepare("SELECT * FROM tbl_top_category WHERE show_on_menu=1");
                                $statement->execute();
                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($result as $row) {
                                    ?>
                                    <li>
                                        <a class="lead-ft-name" href="#">
                                            <?php echo $row['tcat_name']; ?>

                                        </a>
                                        <ul class="col">
                                            <?php
                                            $statement1 = $pdo->prepare("SELECT * FROM tbl_product WHERE tcat_id=?");
                                            $statement1->execute(array($row['tcat_id']));
                                            $result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result1 as $row1) {
                                                ?>
                                                <li><a class=""
                                                        href="<?php echo $row1['p_slug']; ?>"><?php echo $row1['p_name']; ?></a>
                                                </li>
                                                <?php
                                            }
                                            ?>

                                        </ul>
                                    </li>
                                    <?php
                                }
                                ?>

                            </ul>
                        </li>
                    </ul>
                </div>



            </div>
        </div>
    </div>
    <div class="footer-bottom clearfix">
        <div class="container">
            <div class="d-flex-center flex-column justify-content-md-between flex-md-row-reverse">
                <img src="assets/images/payment.png" alt="Paypal Visa Payments" />
                <div class="copytext text-uppercase">
                    &copy; 2023 Optimal. All Rights Reserved.
                </div>
            </div>
        </div>
    </div>
</div>
<!--End Footer-->

<!--Sticky Menubar Mobile-->
<div class="menubar-mobile d-flex align-items-center justify-content-between d-lg-none">
    <div class="menubar-account menubar-item">
        <a href="<?php if (isset($_SESSION['customer'])) {
            echo 'my-account.php';
        } else {
            echo 'login.php';
        } ?>">
            <span class="menubar-icon an an-user-expand"></span>
            <span class="menubar-label">Account</span></a>
    </div>
    <div class="menubar-search menubar-item">
        <a href="index.php"><span class="menubar-icon an an-home-l"></span><span class="menubar-label">Home</span></a>
    </div>

    <div class="menubar-cart menubar-item">
        <a href="cart.php" class="cartBtn">
            <span class="span-count position-relative text-center">
                <span class="menubar-icon an an-sq-bag"></span>
                <span
                    class="menubar-count counter d-flex-center justify-content-center position-absolute translate-middle rounded-circle">
                    <?php
										$table_total_price = 0;
										$total_items = 0;

										if (isset($_SESSION['cart_p_id'])) {
											foreach ($_SESSION['cart_p_qty'] as $key => $quantity) {
												$price = $_SESSION['cart_p_current_price'][$key] ?? 0; // Ensure price exists
												$table_total_price += $price * $quantity;
												$total_items += $quantity; // Count total items
												echo $total_items;

											}
										} else {
											echo "0";
										}
										?>
                    </span></span>
            <span class="menubar-label">Cart</span>
        </a>
    </div>
</div>
<!--End Sticky Menubar Mobile-->

<!-- Including Jquery -->
<script src="<?php echo BASE_URL; ?>assets/js/vendor/jquery-min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/vendor/js.cookie.js"></script>
<!--Including Javascript-->
<script src="<?php echo BASE_URL; ?>assets/js/plugins.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>

<!--Newsletter Popup Cookies-->
<script>
    function newsletter_popup() {
        var cookieSignup = "cookieSignup",
            date = new Date();
        if (
            $.cookie(cookieSignup) != "true" &&
            window.location.href.indexOf("challenge#newsletter-modal") <= -1
        ) {
            setTimeout(function () {
                $.magnificPopup.open({
                    items: {
                        src: "#newsletter-modal",
                    },
                    type: "inline",
                    removalDelay: 300,
                    mainClass: "mfp-zoom-in",
                });
            }, 5000);
        }
        $.magnificPopup.instance.close = function () {
            if ($("#dontshow").prop("checked") == true) {
                $.cookie(cookieSignup, "true", {
                    expires: 1,
                    path: "/",
                });
            }
            $.magnificPopup.proto.close.call(this);
        };
    }
    newsletter_popup();
</script>
<!--End Newsletter Popup Cookies-->
</div>
<!--End Page Wrapper-->
</body>

</html>