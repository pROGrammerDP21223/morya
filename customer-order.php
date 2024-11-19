<?php require_once('_header.php'); ?>
<?php


echo '<pre>';
var_dump($_SESSION);
echo '</pre>';
// Check if the customer is logged in or not
if (!isset($_SESSION['customer'])) {
    header('location: ' . BASE_URL . 'logout.php');
    exit;
} else {
    // If customer is logged in, but admin make him inactive, then force logout this user.
    $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_id=? AND cust_status=?");
    $statement->execute(array($_SESSION['customer']['cust_id'], 0));
    $total = $statement->rowCount();
    if ($total) {
        header('location: ' . BASE_URL . 'logout.php');
        exit;
    }
}
?>
<!--Body Container-->
<div id="page-content">
    <!--Collection Banner-->
    <div class="collection-header">
        <div class="collection-hero">
            <div class="collection-hero__image"></div>
            <div class="collection-hero__title-wrapper container">
                <h1 class="collection-hero__title">Dashboard</h1>
                <div class="breadcrumbs text-uppercase mt-1 mt-lg-2"><a href="index.html"
                        title="Back to the home page">Home</a><span>|</span><span class="fw-bold">My Account</span>
                </div>
            </div>
        </div>
    </div>
    <!--End Collection Banner-->

    <!--Container-->
    <div class="container pt-2">
        <!--Main Content-->
        <div class="dashboard-upper-info">
            <div class="row align-items-center g-0">
                <div class="col-xl-3 col-lg-3 col-sm-6">
                    <div class="d-single-info">
                        <p class="user-name">Hello <span
                                class="fw-600"><?php echo $_SESSION['customer']['cust_name']; ?></span></p>
                        <p>(not <?php echo $_SESSION['customer']['cust_name']; ?>? <a class="link-underline fw-600"
                                href="logout.php">Log Out</a>)</p>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <div class="d-single-info">
                        <p>Need Assistance? Customer service at.</p>
                        <p><a href="mailto:admin@example.com">admin@example.com</a></p>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-sm-6">
                    <div class="d-single-info">
                        <p>E-mail them at </p>
                        <p><a href="mailto:support@example.com">support@example.com</a></p>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-sm-6">
                    <div class="d-single-info text-lg-center">
                        <a class="link-underline fw-600 view-cart" href="cart.php"><i
                                class="icon an an-sq-bag me-2"></i>View Cart</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4 mb-lg-5 pb-lg-5">
            <div class="col-xl-3 col-lg-2 col-md-12 mb-4 mb-lg-0">
                <!-- Nav tabs -->
                <ul class="nav flex-column bg-light h-100 dashboard-list" role="tablist">
                    <li><a class="nav-link " data-bs-toggle="tab" href="#dashboard">Dashboard</a></li>
                    <li><a class="nav-link active" href="customer-order.php">Orders</a></li>
                    <li><a class="nav-link" href="#address">Addresses</a></li>
                    <li><a class="nav-link" href="#account-details">Account details</a></li>
                    <li><a class="nav-link" href="#wishlist">Password Update</a></li>
                    <li><a class="nav-link" href="logout.php.html">logout</a></li>
                </ul>
                <!-- End Nav tabs -->
            </div>

            <div class="col-xl-9 col-lg-10 col-md-12">
                <!-- Tab panes -->
                <div class="tab-content dashboard-content">
                    <!-- Dashboard -->

                    <div id="orders" class="product-order tab-pane fade active show">
                        <h3>Orders</h3>

                        <?php
// Define the number of entries per page
$entries_per_page = 7;

// Get the current page from the URL, default to page 1 if not set
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

// Calculate the offset
$offset = ($current_page - 1) * $entries_per_page;

// Fetch the customer_id from session
$customer_id = $_SESSION['customer']['cust_id'];  // Assuming the customer ID is stored in the session

// Prepare the SQL statement with LIMIT and OFFSET
$statement = $pdo->prepare("
    SELECT p.*, o.product_name, o.quantity 
    FROM tbl_payment p
    JOIN tbl_order o ON o.payment_id = p.payment_id
    WHERE p.customer_id = :customer_id
    ORDER BY p.id DESC
    LIMIT :limit OFFSET :offset
");

// Bind the parameters
$statement->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
$statement->bindParam(':limit', $entries_per_page, PDO::PARAM_INT);
$statement->bindParam(':offset', $offset, PDO::PARAM_INT);

// Execute the query
$statement->execute();

// Fetch the results
$payments = $statement->fetchAll(PDO::FETCH_ASSOC);

// Get the total number of records for pagination
$total_records_statement = $pdo->prepare("
    SELECT COUNT(*) 
    FROM tbl_payment p
    JOIN tbl_order o ON o.payment_id = p.payment_id
    WHERE p.customer_id = :customer_id
");
$total_records_statement->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
$total_records_statement->execute();
$total_records = $total_records_statement->fetchColumn();

// Calculate the total number of pages
$total_pages = ceil($total_records / $entries_per_page);
?>




                        <div class="table-responsive order-table">
                            <table class="table table-bordered table-hover align-middle text-center mb-0">
                                <thead class="alt-font">
                                    <tr>
                                        <th>Order</th>
                                        <th>Product</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($payments)): ?>
                                        <tr>
                                            <td colspan="6">No orders found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($payments as $payment): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($payment['id']) ?></td>
                                                <td><?= htmlspecialchars($payment['product_name']) ?></td>
                                                <td><?= date("F j, Y", strtotime($payment['payment_date'])) ?></td>
                                                <td class="<?php
                                                echo match ($payment['shipping_status']) {
                                                    '2' => 'text-success',
                                                    '1' => 'text-warning',
                                                    default => 'text-danger',
                                                };
                                                ?>">
                                                    <?php
                                                    echo match ($payment['shipping_status']) {
                                                        '2' => 'Completed',
                                                        '1' => 'In-Process',
                                                        default => 'Pending',
                                                    };
                                                    ?>
                                                </td>

                                                <td>$<?= number_format($payment['paid_amount'], 2) ?> for
                                                    <?= $payment['quantity'] ?> item(s)
                                                </td>
                                                <td><a class="link-underline view" href="view_invoice.php?id=<?= htmlspecialchars($payment['payment_id']) ?>" target="_blank">View</a></td>

                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <!-- Pagination Controls -->


                            <nav aria-label="Page navigation example" class="mt-3">
    <?php if ($total_pages > 1): ?>
        <ul class="pagination">
            <?php if ($current_page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $current_page - 1 ?>">Previous</a></li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $current_page + 1 ?>">Next</a></li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
</nav>

                        </div>
                    </div>


                    <!-- End Dashboard -->

                </div>
                <!-- End Tab panes -->
            </div>
        </div>
        <!--End Main Content-->
    </div>
    <!--End Container-->
</div>
<!--End Body Container-->

<?php require_once('_footer.php'); ?>