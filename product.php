<?php
require_once('_header.php');

// Fetching category and product slugs from the URL
$categorySlug = $_GET['category'] ?? '';
$productSlug = $_GET['slug'] ?? '';

if (empty($categorySlug) || empty($productSlug)) {
    header('Location: index.php');
    exit;
} else {
    // Query to get product details
    $statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_slug = ?");
    $statement->execute([$productSlug]);
    $total = $statement->rowCount();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    if ($total == 0) {
        header('Location: index.php');
        exit;
    }
}

foreach ($result as $row) {
    $p_id = $row['p_id'];
    $p_name = $row['p_name'];
    $p_old_price = $row['p_old_price'];
    $p_current_price = $row['p_current_price'];
    $p_qty = $row['p_qty'];
    $p_featured_photo = $row['p_featured_photo'];
    $p_description = $row['p_description'];
    $p_short_description = $row['p_short_description'];
    $p_total_view = $row['p_total_view'];
    $p_is_featured = $row['p_is_featured'];
    $p_is_active = $row['p_is_active'];
    $tcat_id = $row['tcat_id'];
}

// Getting category name for breadcrumb
$statement = $pdo->prepare("SELECT tcat_id, tcat_name FROM tbl_top_category WHERE tcat_id = ?");
$statement->execute([$tcat_id]);
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {
    $tcat_name = $row['tcat_name'];
}

// Updating product views count
$p_total_view++;
$statement = $pdo->prepare("UPDATE tbl_product SET p_total_view = ? WHERE p_slug = ?");
$statement->execute([$p_total_view, $productSlug]);

// Fetching product sizes
$statement = $pdo->prepare("SELECT * FROM tbl_product_size WHERE p_id = ?");
$statement->execute([$p_id]);
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
    $size[] = $row['size_id'];
}

// Fetching product colors
$statement = $pdo->prepare("SELECT * FROM tbl_product_color WHERE p_id = ?");
$statement->execute([$p_id]);
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
    $color[] = $row['color_id'];
}

// Handling reviews
if (isset($_POST['form_review'])) {
    $statement = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id = ? AND cust_id = ?");
    $statement->execute([$p_id, $_SESSION['customer']['cust_id']]);
    $total = $statement->rowCount();

    if ($total) {
        $error_message = "You already have given a rating!";
    } else {
        $statement = $pdo->prepare("INSERT INTO tbl_rating (p_id, cust_id, comment, rating) VALUES (?, ?, ?, ?)");
        $statement->execute([$p_id, $_SESSION['customer']['cust_id'], $_POST['comment'], $_POST['rating']]);
        $success_message = "Rating is Submitted Successfully!";
    }
}

// Calculating the average rating for the product
$t_rating = 0;
$statement = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id = ?");
$statement->execute([$p_id]);
$tot_rating = $statement->rowCount();
if ($tot_rating == 0) {
    $avg_rating = 0;
} else {
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
        $t_rating += $row['rating'];
    }
    $avg_rating = $t_rating / $tot_rating;
}

// Handling "Add to Cart" functionality
if (isset($_POST['form_add_to_cart'])) {
    // Get the current stock of this product
    $statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id = ?");
    $statement->execute([$p_id]);
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
        $current_p_qty = $row['p_qty'];
    }

    // Check if the quantity exceeds the available stock
    if ($_POST['p_qty'] > $current_p_qty) {
        $temp_msg = 'Sorry! There are only ' . $current_p_qty . ' item(s) in stock';
        echo "<script>alert('$temp_msg');</script>";
    } else {
        // Initialize session arrays if not already set
        if (!isset($_SESSION['cart_p_id'])) {
            $_SESSION['cart_p_id'] = [];
            $_SESSION['cart_size_id'] = [];
            $_SESSION['cart_color_id'] = [];
            $_SESSION['cart_p_qty'] = [];
            $_SESSION['cart_p_current_price'] = [];
            $_SESSION['cart_p_name'] = [];
            $_SESSION['cart_p_featured_photo'] = [];
        }

        // Fetch size and color details
        $size_id = isset($_POST['size_id']) ? $_POST['size_id'] : 0;
        $color_id = isset($_POST['color_id']) ? $_POST['color_id'] : 0;

        // Check if the product is already in the cart
        $added = false;
        foreach ($_SESSION['cart_p_id'] as $key => $value) {
            if ($_SESSION['cart_p_id'][$key] == $p_id && $_SESSION['cart_size_id'][$key] == $size_id && $_SESSION['cart_color_id'][$key] == $color_id) {
                $added = true;
                break;
            }
        }

        if ($added) {
            $error_message1 = 'This product is already added to the shopping cart.';
        } else {
            // Get the next available key for cart
            $new_key = count($_SESSION['cart_p_id']) + 1;

            // Fetch size and color names
            if ($size_id > 0) {
                $statement = $pdo->prepare("SELECT * FROM tbl_size WHERE size_id = ?");
                $statement->execute([$size_id]);
                $result = $statement->fetch(PDO::FETCH_ASSOC);
                $size_name = $result ? $result['size_name'] : '';
            } else {
                $size_name = '';
            }

            if ($color_id > 0) {
                $statement = $pdo->prepare("SELECT * FROM tbl_color WHERE color_id = ?");
                $statement->execute([$color_id]);
                $result = $statement->fetch(PDO::FETCH_ASSOC);
                $color_name = $result ? $result['color_name'] : '';
            } else {
                $color_name = '';
            }

            // Add the product to the cart
            $_SESSION['cart_p_id'][$new_key] = $p_id;
            $_SESSION['cart_size_id'][$new_key] = $size_id;
            $_SESSION['cart_size_name'][$new_key] = $size_name;
            $_SESSION['cart_color_id'][$new_key] = $color_id;
            $_SESSION['cart_color_name'][$new_key] = $color_name;
            $_SESSION['cart_p_qty'][$new_key] = $_POST['p_qty'];
            $_SESSION['cart_p_current_price'][$new_key] = $_POST['p_current_price'];
            $_SESSION['cart_p_name'][$new_key] = $_POST['p_name'];
            $_SESSION['cart_p_featured_photo'][$new_key] = $_POST['p_featured_photo'];

            $success_message1 = 'Product is added to the cart successfully!';
        }
    }
}

// Output error or success message after form submission
if (!empty($error_message1)) {
    echo "<script>alert('" . htmlspecialchars($error_message1) . "');</script>";
}

if (!empty($success_message1)) {
    echo "<script>alert('" . htmlspecialchars($success_message1) . "');</script>";
    header('Location: /' . $_GET['category'] . '/' . $_GET['slug']);
    exit;
}
?>


<style>
    #page-content {
        padding-top: 140px;
    }

    #input-container {
        position: absolute;
        margin-top: 44px;

    }
</style>

<div id="page-content">


    <!-- Breadcrumbs -->
    <div class="breadcrumbs-wrapper text-uppercase">
        <div class="container">
            <div class="breadcrumbs"><a href="index.html" title="Back to the home page">Home</a><span>|</span><span
                    class="fw-bold">Product Layout Style1</span></div>
        </div>
    </div>
    <!-- End Breadcrumbs -->
    <div class="container">
        <!-- Product Content -->
        <div class="product-single">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                    <img class="blur-up lazyload"
                        data-src="<?php echo BASE_URL; ?>assets/uploads/<?php echo $p_featured_photo; ?>"
                        src="<?php echo BASE_URL; ?>assets/uploads/<?php echo $p_featured_photo; ?>" alt="product" />
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                    <div class="product-single__meta">
                        <h1 class="product-single__title"><?php echo $p_name; ?></h1>
                        <div class="product-review mb-2">
                            <a class="reviewLink d-flex-center" href="#reviews">
                                <?php
                                if ($avg_rating == 0) {
                                    echo '';
                                } elseif ($avg_rating == 1.5) {
                                    echo '
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-o"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                        ';
                                } elseif ($avg_rating == 2.5) {
                                    echo '
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-o"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                        ';
                                } elseif ($avg_rating == 3.5) {
                                    echo '
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-o"></i>
                                            <i class="fa fa-star-o"></i>
                                        ';
                                } elseif ($avg_rating == 4.5) {
                                    echo '
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-o"></i>
                                        ';
                                } else {
                                    for ($i = 1; $i <= 5; $i++) {
                                        ?>
                                        <?php if ($i > $avg_rating): ?>
                                            <i class="fa fa-star-o"></i>
                                        <?php else: ?>
                                            <i class="fa fa-star"></i>
                                        <?php endif; ?>
                                        <?php
                                    }
                                }
                                ?>
                                <!-- <span class="spr-badge-caption ms-2"><?= $totalReviews ?> Reviews</span> -->
                            </a>
                        </div>
                        <div class="product-info">
                            <p class="product-type">
                                <span> <?php echo $p_short_description; ?></span>
                            </p>
                        </div>
                        <div class="product-single__price pb-1">
                            <span class="product-price__sale--single">
                                <span class="product-price-old-price">$<?php echo $p_old_price; ?></span><span
                                    class="product-price__price product-price__sale">$<?php echo $p_current_price; ?></span>
                            </span>
                        </div>
                    </div>
                    <!-- Product Form -->
                    <form action="" method="post" class="product-form hidedropdown">

                        <!-- Swatches Color -->
                        <div class=" d-flex justify-content-center align-items-start flex-column">
                            <?php if (isset($color)): ?>
                                <div class="mydict mb-3">
                                    <div>
                                        <div class="mt-0">Select Color : &nbsp;</div>
                                        <?php
                                        $statement = $pdo->prepare("SELECT * FROM tbl_color");
                                        $statement->execute();
                                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($result as $row) {
                                            if (in_array($row['color_id'], $color)) {
                                                ?>
                                                <label>
                                                    <input type="radio" name="color_id" value="<?= $row['color_id'] ?>" required>
                                                    <span><?= htmlspecialchars($row['color_name']) ?></span>
                                                </label>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <!-- Swatches Size -->
                            <?php if (isset($size)): ?>
                                <div class="mydict mb-3">
                                    <div style="width:260px;">
                                        <!-- <div class="mt-0">Select Size : &nbsp;</div> -->
                                        <?php
                                        $statement = $pdo->prepare("SELECT * FROM tbl_size");
                                        $statement->execute();
                                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                        ?>
                                        <select name="size_id" required>
                                            <option value="" disabled selected>Select Size</option>
                                            <?php
                                            foreach ($result as $row) {
                                                // Check if the size_id exists in the $size array
                                                if (in_array($row['size_id'], $size)) {
                                                    ?>
                                                    <option value="<?= $row['size_id'] ?>">
                                                        <?= htmlspecialchars($row['size_name']) ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>


                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php
                        $statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
                        $statement->execute(array($p_id));
                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($result as $row) {
                            $current_p_qty = $row['p_qty'];
                        }



                        ?>

                        <input type="hidden" name="p_current_price" value="<?php echo $p_current_price; ?>">
                        <input type="hidden" name="p_name" value="<?php echo $p_name; ?>">
                        <input type="hidden" name="p_featured_photo" value="<?php echo $p_featured_photo; ?>">
                        <!-- Quantity and Add to Cart Button -->
                        <div class="product-action w-100 clearfix" style="margin-top:20px">
                            <?php if ($current_p_qty > 0) { ?>
                                <div class="product-form__item--quantity d-flex-center mb-3" style="gap:40px;">
                                    <div class="qtyField">
                                        <a class="qtyBtn minus" href="javascript:void(0);">
                                            <i class="icon an an-minus-r" aria-hidden="true"></i>
                                        </a>
                                        <input type="text" name="p_qty" value="100" class="product-form__input qty">
                                        <a class="qtyBtn plus" href="javascript:void(0);">
                                            <i class="icon an an-plus-r" aria-hidden="true"></i>
                                        </a>
                                    </div>

                                    <div class="product-form__item--submit">
                                        <button type="submit" name="form_add_to_cart"
                                            class="btn rounded product-form__cart-submit mb-0">
                                            <span>Add to cart</span>
                                        </button>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="product-form__item--submit">
                                    <button type="submit" name="add" class="btn rounded product-form__sold-out"
                                        disabled="disabled">
                                        Sold out
                                    </button>
                                    <button type="button" name="enquiry"
                                        class="btn rounded btn-outline-primary proceed-to-checkout">
                                        Enquiry Now
                                    </button>
                                </div>
                            <?php } ?>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <!-- End Product Content -->



        <!--Product Tabs-->
        <div class="tabs-listing mt-2 mt-md-5">
            <ul class="product-tabs list-unstyled d-flex-wrap border-bottom m-0 d-none d-md-flex">
                <li rel="description" class="active"><a class="tablink">Description</a></li>
                <li rel="reviews"><a class="tablink">Reviews</a></li>
            </ul>
            <div class="tab-container">
                <h3 class="tabs-ac-style d-md-none active" rel="description">Description</h3>
                <div id="description" class="tab-content active">
                    <div class="product-description">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-8 col-lg-8 mb-4 mb-md-0">

                                <h4 class="pt-2 text-uppercase">Features</h4>
                                <p>There are many variations of passages of Lorem Ipsum available, but the majority have
                                    suffered alteration in some form, by injected humour, or randomised words which
                                    don't look even slightly believable.</p>

                            </div>

                        </div>
                    </div>
                </div>

                <?php
                $statement = $pdo->prepare("SELECT * 
                                                            FROM tbl_rating t1 
                                                            JOIN tbl_customer t2 
                                                            ON t1.cust_id = t2.cust_id 
                                                            WHERE t1.p_id=?");
                $statement->execute(array($p_id));
                $row = $statement->fetch(PDO::FETCH_ASSOC);
                // Check if $row is valid and contains 'avg_rating'
                if ($row !== false && isset($row['avg_rating'])) {
                    // Get the average rating and round it
                    $average_rating = $row['avg_rating'] !== null ? round($row['avg_rating']) : 0; // Use 0 if the average is null
                } else {
                    // Set a default value (e.g., 0) if no valid rating is found
                    $average_rating = 0;
                }
                $total = $statement->rowCount();
                ?>
                <h3 class="tabs-ac-style d-md-none" rel="reviews">Review</h3>
                <div id="reviews" class="tab-content">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="spr-header clearfix d-flex-center justify-content-between">
                                <div class="product-review d-flex-center me-auto">
                                    <a class="reviewLink" href="#">
                                        <?php
                                        // Loop through to display filled stars (an-star) based on the average rating
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $average_rating) {
                                                // Show filled star
                                                echo '<i class="icon an an-star"></i>';
                                            } else {
                                                // Show empty star
                                                echo '<i class="icon an an-star-o"></i>';
                                            }
                                        }
                                        ?>
                                    </a>
                                    <span class="spr-summary-actions-togglereviews ms-2">Based on <?php echo $total; ?>
                                        reviews</span>
                                </div>
                                <?php if (isset($_SESSION['customer'])): ?>
                                    <div class="spr-summary-actions mt-3 mt-sm-0">
                                        <a href="#" class="spr-summary-actions-newreview write-review-btn  btn rounded"><i
                                                class="icon an-1x an an-pencil-alt me-2"></i>Write a review</a>
                                    </div>
                                <?php else: ?>

                                    <div class="spr-summary-actions mt-3 mt-sm-0">
                                        <a href="login.php" class="spr-summary-actions-newreview  btn rounded"><i
                                                class="icon an-1x an "></i>Login</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <form action="" method="post" id="review-form"
                                class="product-review-form new-review-form mb-4">
                                <h4 class="spr-form-title text-uppercase">Write A Review</h4>
                                <fieldset class="spr-form-contact">
                                    <div class="spr-form-review-rating form-group">
                                        <label class="spr-form-label">Rating</label>
                                        <div class="product-review pt-1">
                                            <div class="review-rating">
                                                <input type="radio" name="rating" id="rating-5" value="5">
                                                <label for="rating-5"></label>

                                                <input type="radio" name="rating" id="rating-4" value="4">
                                                <label for="rating-4"></label>

                                                <input type="radio" name="rating" id="rating-3" value="3">
                                                <label for="rating-3"></label>

                                                <input type="radio" name="rating" id="rating-2" value="2">
                                                <label for="rating-2"></label>

                                                <input type="radio" name="rating" id="rating-1" value="1">
                                                <label for="rating-1"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="spr-form-review-body form-group">
                                        <label class="spr-form-label" for="message">Review</label>
                                        <div class="spr-form-input">
                                            <textarea class="spr-form-input spr-form-input-textarea" id="message"
                                                name="message" rows="5"
                                                placeholder="Write your comments here"></textarea>
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="spr-form-actions clearfix">
                                    <input type="submit" name="form_review"
                                        class="btn btn-primary rounded spr-button spr-button-primary"
                                        value="Submit Review">
                                </div>
                            </form>

                        </div>



                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="spr-reviews">
                                <h4 class="spr-form-title text-uppercase mb-3">Customer Reviews</h4>
                                <div class="review-inner">
                                    <?php
                                    if ($total) {
                                        $j = 0;
                                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($result as $row) {
                                            $j++;
                                            ?>
                                            <div class="spr-review">
                                                <div class="spr-review-header">
                                                    <span class="product-review spr-starratings"><span class="reviewLink">


                                                            <?php
                                                            for ($i = 1; $i <= 5; $i++) {
                                                                ?>
                                                                <?php if ($i > $row['rating']): ?>
                                                                    <i class="icon an an-star-o"></i>
                                                                <?php else: ?>
                                                                    <i class="icon an an-star"></i>
                                                                <?php endif; ?>
                                                                <?php
                                                            }
                                                            ?>


                                                        </span></span>
                                                    <h5 class="spr-review-header-title mt-1">Lorem ipsum dolor sit amet</h5>
                                                    <span
                                                        class="spr-review-header-byline"><strong><?php echo $row['cust_name']; ?></strong></span>
                                                </div>
                                                <div class="spr-review-content">
                                                    <p class="spr-review-content-body"><?php echo $row['comment']; ?></p>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } else {
                                        echo "No Reviews found";
                                    }
                                    ?>
                                    <?php
                                    if ($error_message != '') {
                                        echo "<script>alert('" . $error_message . "')</script>";
                                    }
                                    if ($success_message != '') {
                                        echo "<script>alert('" . $success_message . "')</script>";
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--End Product Tabs-->
    </div>
    <style>
        /* Initially hide all tab contents */
        .tab-content {
            display: none;
        }

        /* Show active tab content */
        .tab-content.active {
            display: block;
        }

        /* Highlight the active tab */
        .product-tabs li.active a {
            font-weight: bold;
            color: #000;
            /* Or any color to distinguish the active tab */
        }

        #review-form {
            display: none;
        }

        .show {
            display: block;
        }
    </style>
    <script>
        // Ensure script runs after DOM is loaded
        document.addEventListener('DOMContentLoaded', function () {
            const reviewBtn = document.querySelector('.write-review-btn');
            const reviewForm = document.getElementById('review-form');

            // Add event listener to the button
            reviewBtn.addEventListener('click', function (event) {
                event.preventDefault(); // Prevent the default button action

                // Toggle the "show" class on the review form
                reviewForm.classList.toggle('show');
            });
        });
    </script>

    <script>
        // Toggle input field visibility based on selected radio option
        function toggleInputField(show) {
            const inputContainer = document.getElementById('input-container');
            const inputField = document.getElementById('custom-input');
            if (show) {
                inputContainer.style.display = 'block';
            } else {
                inputContainer.style.display = 'none';
                inputField.value = ''; // Clear the input field when Option 1 or Option 2 is selected
            }
        }

        // Handle form submission
        function handleSubmit(event) {
            // No need to prevent form submission since we're using action="submit_form.php"
            const form = document.getElementById('myForm');
            const selectedOption = form.querySelector('input[name="option"]:checked');
            const inputField = form.querySelector('input[name="custom-input"]');

            let formData = new FormData(form);

            // If Option 3 is selected, use the input value instead of the radio option value
            if (selectedOption && selectedOption.value === 'option3') {
                formData.set('option', inputField.value); // Set the input field value as the selected option value
            } else {
                // Ensure that the input field value is not submitted if Option 1 or Option 2 is selected
                formData.delete('custom-input');
            }

            // For demonstration purposes, we'll log the form data
            let data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });

            console.log(data); // Logs the form data (can be replaced with AJAX or actual form submission)
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const minusButton = document.querySelector('.qtyBtn.minus');
            const plusButton = document.querySelector('.qtyBtn.plus');
            const qtyInput = document.querySelector('.product-form__input.qty');
            const minQty = 100;  // Set the minimum quantity to 100
            // Event listener for the minus button
            minusButton.addEventListener('click', function () {
                let currentValue = parseInt(qtyInput.value);
                if (currentValue > minQty) {
                    qtyInput.value = currentValue - 1;
                }
            });
            // Event listener for the plus button
            plusButton.addEventListener('click', function () {
                let currentValue = parseInt(qtyInput.value);
                qtyInput.value = currentValue + 1;
            });
            // Prevent invalid input (non-numeric)
            qtyInput.addEventListener('input', function () {
                if (isNaN(qtyInput.value) || qtyInput.value < minQty) {
                    qtyInput.value = minQty;  // Reset to min quantity if invalid
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get all tab links
            const tabs = document.querySelectorAll('.product-tabs li');
            const tabContents = document.querySelectorAll('.tab-content');
            // Add click event to each tab
            tabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    // Remove active class from all tabs and tab contents
                    tabs.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    // Add active class to the clicked tab and its corresponding content
                    const activeTab = tab.getAttribute('rel');
                    tab.classList.add('active');
                    document.getElementById(activeTab).classList.add('active');
                });
            });
        });
    </script>
    <?php require_once('_footer.php'); ?>
