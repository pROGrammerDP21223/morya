<?php require_once('_header.php'); ?>
<?php
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
                    <li><a class="nav-link active" data-bs-toggle="tab" href="#dashboard">Dashboard</a></li>
                    <li><a class="nav-link" href="customer-order.php">Orders</a></li>
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
                    <div id="dashboard" class="tab-pane fade active show">
                        <h3>Dashboard </h3>

                        <div class="row user-profile mt-4">
                            <div class="col-12 col-lg-6">
                                <div class="profile-img" style="box-shadow: none;">

                                    <div class="detail ms-3">
                                        <h5 class="mb-1">Welcome <?php echo $_SESSION['customer']['cust_name']; ?>!!!
                                        </h5>

                                    </div>

                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <ul class="profile-order mt-3 mt-lg-0">
                                    <li>
                                        <h3 class="mb-1">
                                            <?php
                                        $customer_id = $_SESSION['customer']['cust_id'];
                                        $statement = $pdo->prepare("SELECT COUNT(*) FROM tbl_order WHERE cust_id = :cust_id");
                                        $statement->bindParam(':cust_id', $customer_id, PDO::PARAM_INT);
                                        
                                        // Execute the statement
                                        $statement->execute();
                                        
                                        // Fetch the result, which will be the number of rows
                                        $row_count = $statement->fetchColumn();
                                        echo $row_count;
                                        
                                        
                                        ?>
                                        </h3>
                                        All Orders
                                    </li>
                                    <li>
                                        <h3 class="mb-1"><?php
$customer_id = $_SESSION['customer']['cust_id'];
$statement = $pdo->prepare("SELECT COUNT(*) FROM tbl_payment WHERE customer_id = :customer_id AND payment_status = :payment_status");
$statement->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
$payment_status = 'pending'; // Specify the status you're looking for
$statement->bindParam(':payment_status', $payment_status, PDO::PARAM_STR);

// Execute the statement
$statement->execute();

// Fetch the result, which will be the number of rows
$row_count = $statement->fetchColumn();
echo $row_count;
?></h3>
                                        Awaiting Payments
                                    </li>
                         
                                    <li>
                                        <h3 class="mb-1"><?php
$customer_id = $_SESSION['customer']['cust_id'];
$statement = $pdo->prepare("SELECT COUNT(*) FROM tbl_payment WHERE customer_id = :customer_id AND shipping_status = :shipping_status");
$statement->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
$shipping_status = 'pending'; // Specify the status you're looking for
$statement->bindParam(':shipping_status', $shipping_status, PDO::PARAM_STR);

// Execute the statement
$statement->execute();

// Fetch the result, which will be the number of rows
$row_count = $statement->fetchColumn();
echo $row_count;
?></h3>
                                        Awaiting Delivery
                                    </li>
                                </ul>
                            </div>
                            <div class="col-12 col-lg-12">
                                <div class="profile-img" style="box-shadow: none;">

                                    <div class="detail ms-3">
                                        <p>We're excited to have you here. Take a moment to check out your dashboard and get the most out of your experience. If you need help, we're just a click away. Let's get started!

</p>
                                        

                                    </div>

                                </div>
                            </div>
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