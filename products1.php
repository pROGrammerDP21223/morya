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


    <?php if ($product): ?>
        <h1><?= htmlspecialchars($product['p_name']) ?></h1>
        <p><strong>Price:</strong> $<?= htmlspecialchars($product['p_current_price']) ?></p>
        <p><strong>Old Price:</strong> $<?= htmlspecialchars($product['p_old_price']) ?></p>
        <p><strong>Description:</strong> <?= htmlspecialchars($product['p_description']) ?></p>
        <p><strong>Short Description:</strong> <?= htmlspecialchars($product['p_short_description']) ?></p>
        <p><strong>Total Views:</strong> <?= htmlspecialchars($product['p_total_view']) ?></p>
        <p><strong>Available Sizes:</strong> <?= implode(", ", $sizes) ?></p>
        <p><strong>Available Colors:</strong> <?= implode(", ", $colors) ?></p>
        <p><strong>Rating:</strong> <?= round($ratingInfo['avg_rating'], 2) ?> / 5 (<?= $ratingInfo['total_ratings'] ?> reviews)</p>

        <form method="POST">
            <input type="number" name="p_qty" min="1" max="<?= $product['p_qty'] ?>" required>
            <button type="submit" name="form_add_to_cart">Add to Cart</button>
        </form>

        <form method="POST">
            <textarea name="comment" required></textarea>
            <select name="rating" required>
                <option value="1">1 Star</option>
                <option value="2">2 Stars</option>
                <option value="3">3 Stars</option>
                <option value="4">4 Stars</option>
                <option value="5">5 Stars</option>
            </select>
            <button type="submit" name="form_review">Submit Review</button>
        </form>
    <?php else: ?>
        <p>Product not found.</p>
    <?php endif; ?>

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
</body>
</html>
