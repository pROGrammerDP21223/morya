<?php
require_once('_header.php');

// Fetch product details from the database
function fetchProductData($pdo, $categorySlug, $productSlug)
{
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
function incrementProductView($pdo, $productId)
{
    $stmt = $pdo->prepare("UPDATE tbl_product SET p_total_view = p_total_view + 1 WHERE p_id = :p_id");
    $stmt->execute(['p_id' => $productId]);
}

// Fetch available sizes and colors for the product
function fetchProductVariants($pdo, $productId)
{
    $stmt = $pdo->prepare(
        "SELECT s.size_name, s.size_id, c.color_name, c.color_id
        FROM tbl_product_variant pv
        JOIN tbl_size s ON pv.size_id = s.size_id
        JOIN tbl_color c ON pv.color_id = c.color_id
        WHERE pv.p_id = :p_id"
    );
    $stmt->execute(['p_id' => $productId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Calculate the average rating of a product
function calculateAverageRating($pdo, $productId)
{
    $stmt = $pdo->prepare("SELECT rating FROM tbl_rating WHERE p_id = :p_id");
    $stmt->execute(['p_id' => $productId]);
    $ratings = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $totalRatings = count($ratings);
    $averageRating = $totalRatings > 0 ? array_sum($ratings) / $totalRatings : 0;
    return ['avg_rating' => $averageRating, 'total_ratings' => $totalRatings];
}

// Add product to cart
function addToCart($pdo, $productId, $sizeId, $colorId, $quantity)
{
    // Fetch current stock of product
    $stmt = $pdo->prepare("SELECT p_qty FROM tbl_product WHERE p_id = ?");
    $stmt->execute([$productId]);
    $stockQty = $stmt->fetchColumn();
    
    if ($quantity > $stockQty) {
        return "Sorry! There are only $stockQty item(s) in stock.";
    }

    // Check if product is already in the cart with same size/color
    foreach ($_SESSION['cart_p_id'] ?? [] as $key => $value) {
        if ($value == $productId && $_SESSION['cart_size_id'][$key] == $sizeId && $_SESSION['cart_color_id'][$key] == $colorId) {
            return "This product is already added to the shopping cart.";
        }
    }

    // Add to cart session
    $newKey = count($_SESSION['cart_p_id'] ?? []) + 1;
    $_SESSION['cart_p_id'][$newKey] = $productId;
    $_SESSION['cart_size_id'][$newKey] = $sizeId;
    $_SESSION['cart_color_id'][$newKey] = $colorId;
    $_SESSION['cart_p_qty'][$newKey] = $quantity;

    // Fetch size and color names
    $sizeName = '';
    if ($sizeId) {
        $stmt = $pdo->prepare("SELECT size_name FROM tbl_size WHERE size_id = ?");
        $stmt->execute([$sizeId]);
        $sizeName = $stmt->fetchColumn();
    }

    $colorName = '';
    if ($colorId) {
        $stmt = $pdo->prepare("SELECT color_name FROM tbl_color WHERE color_id = ?");
        $stmt->execute([$colorId]);
        $colorName = $stmt->fetchColumn();
    }

    $_SESSION['cart_size_name'][$newKey] = $sizeName;
    $_SESSION['cart_color_name'][$newKey] = $colorName;

    return "Product is added to the cart successfully!";
}

// Handle review submission
function submitReview($pdo, $productId, $customerId, $comment, $rating)
{
    $stmt = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id = :p_id AND cust_id = :cust_id");
    $stmt->execute(['p_id' => $productId, 'cust_id' => $customerId]);
    
    if ($stmt->rowCount() > 0) {
        return "You have already rated this product.";
    }

    $stmt = $pdo->prepare("INSERT INTO tbl_rating (p_id, cust_id, comment, rating) VALUES (:p_id, :cust_id, :comment, :rating)");
    $stmt->execute([
        'p_id' => $productId,
        'cust_id' => $customerId,
        'comment' => $comment,
        'rating' => $rating
    ]);
    return "Thank you for your review!";
}

// Main code execution
$categorySlug = $_GET['category'] ?? '';
$productSlug = $_GET['slug'] ?? '';
$product = null;
$ratingInfo = null;
$sizes = [];
$colors = [];
$errorMessage = '';
$successMessage = '';

if ($categorySlug && $productSlug) {
    $product = fetchProductData($pdo, $categorySlug, $productSlug);
    
    if ($product) {
        incrementProductView($pdo, $product['p_id']);
        $variants = fetchProductVariants($pdo, $product['p_id']);
        
        foreach ($variants as $variant) {
            $sizes[] = [
                'size_id' => $variant['size_id'],
                'size_name' => $variant['size_name']
            ];
            $colors[] = [
                'color_id' => $variant['color_id'],
                'color_name' => $variant['color_name']
            ];
        }

        $ratingInfo = calculateAverageRating($pdo, $product['p_id']);
        
        // Handle add to cart submission
        if (isset($_POST['form_add_to_cart'])) {
            $productId = (int)$_POST['id'];
            $sizeId = (int)($_POST['size_id'] ?? 0);
            $colorId = (int)($_POST['color_id'] ?? 0);
            $quantity = (int)$_POST['p_qty'];

            $errorMessage = addToCart($pdo, $productId, $sizeId, $colorId, $quantity);
            if (!$errorMessage) {
                $successMessage = "Product is added to the cart successfully!";
                header('Location: product.php?id=' . $productId);
                exit;
            }
        }

        // Handle review submission
        if (isset($_POST['form_review']) && isset($_SESSION['customer']['cust_id'])) {
            $customerId = $_SESSION['customer']['cust_id'];
            $comment = $_POST['comment'] ?? '';
            $rating = (int)$_POST['rating'];
            $errorMessage = submitReview($pdo, $product['p_id'], $customerId, $comment, $rating);
        }
    }
}

?>

<!-- HTML for Product Page -->

<?php if ($product): ?>
    <h1><?php echo htmlspecialchars($product['p_name']); ?></h1>
    <p>Category: <?php echo htmlspecialchars($product['tcat_name']); ?></p>
    <p>Price: $<?php echo number_format($product['p_price'], 2); ?></p>
    <p>Average Rating: <?php echo number_format($ratingInfo['avg_rating'], 1); ?> (<?php echo $ratingInfo['total_ratings']; ?> ratings)</p>

    <!-- Display Sizes and Colors -->
    <form method="POST">
        <label for="size">Size:</label>
        <select name="size_id" id="size">
            <?php foreach ($sizes as $size): ?>
                <option value="<?php echo $size['size_id']; ?>"><?php echo htmlspecialchars($size['size_name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="color">Color:</label>
        <select name="color_id" id="color">
            <?php foreach ($colors as $color): ?>
                <option value="<?php echo $color['color_id']; ?>"><?php echo htmlspecialchars($color['color_name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="qty">Quantity:</label>
        <input type="number" name="p_qty" id="qty" value="1" min="1">

        <input type="submit" name="form_add_to_cart" value="Add to Cart">
    </form>

    <!-- Display Error or Success Message -->
    <?php if ($errorMessage): ?>
        <div class="error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <?php if ($successMessage): ?>
        <div class="success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <!-- Review Form -->
    <?php if (isset($_SESSION['customer']['cust_id'])): ?>
        <form method="POST">
            <textarea name="comment" placeholder="Write your review..."></textarea>
            <label for="rating">Rating:</label>
            <select name="rating" id="rating">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
            <input type="submit" name="form_review" value="Submit Review">
        </form>
    <?php endif; ?>

<?php else: ?>
    <p>Product not found.</p>
<?php endif; ?>

<?php
// Optionally include footer and other page content
?>
