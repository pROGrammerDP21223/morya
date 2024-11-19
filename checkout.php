<?php require_once('_header.php'); ?>



<?php
if (!isset($_SESSION['cart_p_id'])) {
    header('location: cart.php');
    exit;
}
?>
<style>
    #page-content {
        padding-top: 130px;
    }
</style>
<!--Body Container-->
<div id="page-content">
    <!--Collection Banner-->
    <div class="collection-header">
        <div class="collection-hero">
            <div class="collection-hero__image"></div>
            <div class="collection-hero__title-wrapper container">
                <h1 class="collection-hero__title">Checkout</h1>
                <div class="breadcrumbs text-uppercase mt-1 mt-lg-2"><a href="index.html"
                        title="Back to the home page">Home</a><span>|</span><span class="fw-bold">Checkout</span></div>
            </div>
        </div>
    </div>
    <!--End Collection Banner-->
    <?php if(!isset($_SESSION['customer'])): ?>
                    <p>
                        <a href="login.php" class="btn btn-md btn-danger">Please login as customer to checkout</a>
                    </p>
                <?php else: ?>
    <!--Container-->
    <div class="container">
        <form class="checkout-form" method="post" action="payment.php">
            <div class="row">
            <?php
                    $checkout_access = 1;
                    if (
                        ($_SESSION['customer']['cust_b_name'] == '') ||
                        ($_SESSION['customer']['cust_b_cname'] == '') ||
                        ($_SESSION['customer']['cust_b_phone'] == '') ||
                        ($_SESSION['customer']['cust_b_country'] == '') ||
                        ($_SESSION['customer']['cust_b_address'] == '') ||
                        ($_SESSION['customer']['cust_b_city'] == '') ||
                        ($_SESSION['customer']['cust_b_state'] == '') ||
                        ($_SESSION['customer']['cust_b_zip'] == '') ||
                        ($_SESSION['customer']['cust_s_name'] == '') ||
                        ($_SESSION['customer']['cust_s_cname'] == '') ||
                        ($_SESSION['customer']['cust_s_phone'] == '') ||
                        ($_SESSION['customer']['cust_s_country'] == '') ||
                        ($_SESSION['customer']['cust_s_address'] == '') ||
                        ($_SESSION['customer']['cust_s_city'] == '') ||
                        ($_SESSION['customer']['cust_s_state'] == '') ||
                        ($_SESSION['customer']['cust_s_zip'] == '')
                    ) {
                        $checkout_access = 0;
                    }
                    ?>
                    <?php if ($checkout_access == 0): ?>
                        <div class="col-12">
                            <div class="alert alert-custom">
                                You must fill out all billing and shipping information in your dashboard before checking out.
                                Please update your information <a href="customer-billing-shipping-update.php"
                                    class="text-danger text-decoration-underline">here</a>.
                            </div>
                        </div>
                    <?php else: ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card card--grey">
                        <div class="card-body">
                            <h2 class="fs-6">SHIPPING ADDRESS</h2>
                            <p><a class="text-decoration-underline" href="login.html">Login</a> or
                                <a class="text-decoration-underline" href="register.html">Register</a> for faster
                                payment.
                            </p>
                            <div class="row mt-2">
                                <div class="col-sm-6">
                                    <label class="text-uppercase">First Name:</label>
                                    <div class="form-group">
                                        <input type="text" placeholder="First Name" class="form-control"
                                            name="cust_s_name"
                                            value="<?php echo $_SESSION['customer']['cust_s_name']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="text-uppercase">Company Name:</label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Last Name" class="form-control"
                                            name="cust_s_cname"
                                            value="<?php echo $_SESSION['customer']['cust_s_cname']; ?>">
                                    </div>
                                </div>
                            </div>
                            <label class="text-uppercase">Contact Number :</label>
                            <div class="form-group">
                                <input type="text" placeholder="Contact Number " class="form-control" name="cust_s_phone"
                                    value="<?php echo $_SESSION['customer']['cust_s_phone']; ?>">
                            </div>
                            <label class="text-uppercase">Address :</label>
                            <div class="form-group">
                                <input type="text" placeholder="Address " class="form-control" name="cust_s_address"
                                    value="<?php echo $_SESSION['customer']['cust_s_address']; ?>">
                            </div>
                            <label class="text-uppercase">Country:</label>
                            <div class="form-group select-wrapper">
                                <input type="text" placeholder="Country" class="form-control" name="cust_s_country"
                                    value="<?php echo $_SESSION['customer']['cust_s_country']; ?>">
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="text-uppercase">State:</label>
                                    <div class="form-group select-wrapper">
                                        <input type="text" placeholder="State" class="form-control" name="cust_s_state"
                                            value="<?php echo $_SESSION['customer']['cust_s_state']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="text-uppercase">Zip/postal code:</label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Zip/postal code" class="form-control"
                                            name="cust_s_zip"
                                            value="<?php echo $_SESSION['customer']['cust_s_zip']; ?>">
                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mt-2 mt-md-0">
                    <div class="card card--grey">
                        <div class="card-body">
                            <h2 class="fs-6">BILING ADDRESS</h2>
                            <p><a class="text-decoration-underline" href="login.html">Login</a> or
                                <a class="text-decoration-underline" href="register.html">Register</a> for faster
                                payment.
                            </p>
                            <div class="row mt-2">
                                <div class="col-sm-6">
                                    <label class="text-uppercase">Full Name:</label>
                                    <div class="form-group">
                                        <input type="text" placeholder="First Name" class="form-control"
                                            name="cust_b_name"
                                            value="<?php echo $_SESSION['customer']['cust_b_name']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="text-uppercase">Company Name:</label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Last Name" class="form-control"
                                            name="cust_b_cname"
                                            value="<?php echo $_SESSION['customer']['cust_b_cname']; ?>">
                                    </div>
                                </div>
                            </div>
                            <label class="text-uppercase">Contact Number :</label>
                            <div class="form-group">
                                <input type="text" placeholder="Contact Number " class="form-control" name="cust_b_phone"
                                    value="<?php echo $_SESSION['customer']['cust_b_phone']; ?>">
                            </div>
                            <label class="text-uppercase">Address :</label>
                            <div class="form-group">
                                <input type="text" placeholder="Address " class="form-control" name="cust_b_address"
                                    value="<?php echo $_SESSION['customer']['cust_b_address']; ?>">
                            </div>
                            <label class="text-uppercase">Country:</label>
                            <div class="form-group select-wrapper">
                                <input type="text" placeholder="Country" class="form-control" name="cust_b_country"
                                    value="<?php echo $_SESSION['customer']['cust_b_country']; ?>">
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="text-uppercase">State:</label>
                                    <div class="form-group select-wrapper">
                                        <input type="text" placeholder="State" class="form-control" name="cust_b_state"
                                            value="<?php echo $_SESSION['customer']['cust_b_state']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="text-uppercase">Zip/postal code:</label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Zip/postal code" class="form-control"
                                            name="cust_b_zip"
                                            value="<?php echo $_SESSION['customer']['cust_b_zip']; ?>">
                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>
                </div>


                <div class="col-md-12 col-lg-4 mt-2 mt-lg-0">
                    <h2 class="title fs-6">ORDER SUMMARY</h2>
                    <div class="table-responsive order-table style1">
                        <table class="table table-bordered align-middle table-hover text-center mb-1">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-start">Name</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                        $table_total_price = 0;

                        $i=0;
                        foreach($_SESSION['cart_p_id'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_id[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_size_id'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_size_id[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_size_name'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_size_name[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_color_id'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_color_id[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_color_name'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_color_name[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_p_qty'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_qty[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_p_current_price'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_current_price[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_p_name'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_name[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_p_featured_photo'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_featured_photo[$i] = $value;
                        }
                        ?>
                        <?php for($i=1;$i<=count($arr_cart_p_id);$i++): ?>
                                <tr>
                                    <td class="thumbImg"><a href="product-layout1.html"
                                            class="thumb d-inline-block"><img class="cart__image"
                                                src="assets/uploads/<?php echo $arr_cart_p_featured_photo[$i]; ?>"
                                                alt="<?php echo $arr_cart_p_name[$i]; ?>" width="80"></a></td>
                                    <td class="text-start">
                                        <a href="product-layout1.html"><?php echo $arr_cart_p_name[$i]; ?></a>
                                        <div class="cart__meta-text">
                                            Color: <?php echo $arr_cart_color_name[$i]; ?><br>Size: <?php echo $arr_cart_size_name[$i]; ?><br>
                                        </div>
                                    </td>
                                    <td>$<?php echo $arr_cart_p_current_price[$i]; ?></td>
                                    <td><?php echo $arr_cart_p_qty[$i]; ?></td>
                                    <td class="fw-500">$ <?php
                                $row_total_price = $arr_cart_p_current_price[$i]*$arr_cart_p_qty[$i];
                                $table_total_price = $table_total_price + $row_total_price;
                                ?>
                            <?php echo $row_total_price; ?></td>
                                </tr>
                                <?php endfor; ?>           
                               
                            </tbody>
                            <tfoot class="font-weight-600">
    <tr>
        <td colspan="4" class="text-end fw-bolder">Total</td>
        <td class="fw-bolder">$<?php echo number_format($table_total_price, 2); ?></td>
    </tr>
    <tr>
        <td colspan="4" class="text-end fw-bolder">Shipping</td>
        <td class="fw-bolder">$<?php echo number_format($_SESSION['shipping_message'], 2); ?></td>
    </tr>
    <tr>
        <td colspan="4" class="text-end fw-bolder">SubTotal</td>
        <td class="fw-bolder">$<?php echo number_format($table_total_price + $_SESSION['shipping_message'], 2); ?></td>
    </tr>
</tfoot>


                        </table>
                    </div>

                    <input type="hidden" id="udf5" name="udf5" value="PayUBiz_PHP7_Kit" />				
                                    <input type="hidden" id="txnid" name="txnid" placeholder="Transaction ID" value="<?php echo  "Txn" . rand(10000,99999999)?>" />
                                  
                                    <input type="hidden" name="amount" value="<?php echo number_format($table_total_price + $_SESSION['shipping_message'], 2); ?>" >
                          
                                    <div class="order-button-payment mt-2 clearfix">
                                        <input type="submit" class="cartCheckout fs-6 btn btn-lg rounded w-100 fw-600 text-white" value="Place order"
                                         >
                                    </div>


                  

                </div>
                <?php endif; ?>
              
            </div>
        </form>
    </div>
    <!--End Container-->

    <?php endif; ?>
</div>
<!--End Body Container-->

<?php require_once('_footer.php'); ?>