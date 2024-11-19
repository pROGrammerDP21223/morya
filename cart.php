<?php require_once('_header.php'); ?>
<?php
$error_message = '';
if (isset($_POST['form1'])) {

    $i = 0;
    $statement = $pdo->prepare("SELECT * FROM tbl_product");
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
        $i++;
        $table_product_id[$i] = $row['p_id'];
        $table_quantity[$i] = $row['p_qty'];
    }

    $i = 0;
    foreach ($_POST['product_id'] as $val) {
        $i++;
        $arr1[$i] = $val;
    }
    $i = 0;
    foreach ($_POST['quantity'] as $val) {
        $i++;
        $arr2[$i] = $val;
    }
    $i = 0;
    foreach ($_POST['product_name'] as $val) {
        $i++;
        $arr3[$i] = $val;
    }

    $allow_update = 1;
    for ($i = 1; $i <= count($arr1); $i++) {
        $temp_index = null;  // Initialize $temp_index to null or an invalid index

        for ($j = 1; $j <= count($table_product_id); $j++) {
            if ($arr1[$i] == $table_product_id[$j]) {
                $temp_index = $j;
                break;
            }
        }

        // If $temp_index is not set, we can either skip the iteration or show an error message
        if ($temp_index === null) {
            // No match found
            $error_message .= 'Product ID ' . $arr1[$i] . ' not found in the table.\n';
            $allow_update = 0;
            continue; // Skip this iteration and continue with the next one
        }

        // Now, use $temp_index safely, since we know it was set
        if ($table_quantity[$temp_index] < $arr2[$i]) {
            $error_message .= 'Table Quantity: ' . $table_quantity[$temp_index] . "\n";
            $error_message .= 'Requested Quantity: ' . $arr2[$i] . "\n";
            $allow_update = 0;
            $error_message .= '"' . $arr2[$i] . '" items are not available for "' . $arr3[$i] . '"\n';
        } else {
            $_SESSION['cart_p_qty'][$i] = $arr2[$i];
        }
    }

    $error_message .= '\nOther items quantity are updated successfully!';

    ?>
    <?php if ($allow_update == 0): ?>
        <script>alert('<?php echo $error_message; ?>');</script>
    <?php else: ?>
        <script>alert('All Items Quantity Update is Successful!');</script>
    <?php endif; ?>
<?php
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
                <h1 class="collection-hero__title">Cart Page Style1</h1>
                <div class="breadcrumbs text-uppercase mt-1 mt-lg-2"><a href="index.html"
                        title="Back to the home page">Home</a><span>|</span><span class="fw-bold">Cart Page
                        Style1</span></div>
            </div>
        </div>
    </div>
    <!--End Collection Banner-->

    <!--Main Content-->
    <div class="container">
        <!--Cart Page-->
        <?php if (!isset($_SESSION['cart_p_id'])): ?>
            <?php echo 'Cart is empty'; ?>
        <?php else: ?>
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-8 main-col">

                    <form action="" method="post" class="cart style2">
                        <?php $csrf->echoInputField(); ?>

                        <table class="align-middle">
                            <thead class="cart__row cart__header small--hide">
                                <tr>
                                    <th class="action">&nbsp;</th>
                                    <th colspan="2" class="text-start">Product</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                $table_total_price = 0;


                                $i = 0;
                                foreach ($_SESSION['cart_p_id'] as $key => $value) {
                                    $i++;
                                    $arr_cart_p_id[$i] = $value;
                                    // Display the individual value
                            
                                }



                                $i = 0;
                                foreach ($_SESSION['cart_size_id'] as $key => $value) {
                                    $i++;
                                    $arr_cart_size_id[$i] = $value;
                                }

                                $i = 0;
                                foreach ($_SESSION['cart_size_name'] as $key => $value) {
                                    $i++;
                                    $arr_cart_size_name[$i] = $value;
                                }

                                $i = 0;
                                foreach ($_SESSION['cart_color_id'] as $key => $value) {
                                    $i++;
                                    $arr_cart_color_id[$i] = $value;
                                }

                                $i = 0;
                                foreach ($_SESSION['cart_color_name'] as $key => $value) {
                                    $i++;
                                    $arr_cart_color_name[$i] = $value;
                                }

                                $i = 0;
                                foreach ($_SESSION['cart_p_qty'] as $key => $value) {
                                    $i++;
                                    $arr_cart_p_qty[$i] = $value;
                                }

                                $i = 0;
                                foreach ($_SESSION['cart_p_current_price'] as $key => $value) {
                                    $i++;
                                    $arr_cart_p_current_price[$i] = $value;
                                }

                                $i = 0;
                                foreach ($_SESSION['cart_p_name'] as $key => $value) {
                                    $i++;
                                    $arr_cart_p_name[$i] = $value;
                                }

                                $i = 0;
                                foreach ($_SESSION['cart_p_featured_photo'] as $key => $value) {
                                    $i++;
                                    $arr_cart_p_featured_photo[$i] = $value;
                                }
                                ?>
                                <?php for ($i = 1; $i <= count($arr_cart_p_id); $i++): ?>
                                    <tr class="cart__row border-bottom line1 cart-flex border-top">
                                        <td class="cart-delete text-center small--hide">

                                            <a onclick="return confirmDelete();"
                                                href="cart-item-delete.php?id=<?php echo $arr_cart_p_id[$i]; ?>&size=<?php echo $arr_cart_size_id[$i]; ?>&color=<?php echo $arr_cart_color_id[$i]; ?>"
                                                class="btn btn--secondary cart__remove remove-icon position-static"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Remove item"><i
                                                    class="icon an an-times-r"></i></a>


                                        </td>
                                        <td class="cart__image-wrapper cart-flex-item">
                                            <a href="product-layout1.html"><img class="cart__image blur-up lazyload"
                                                    data-src="assets/uploads/<?php echo $arr_cart_p_featured_photo[$i]; ?>"
                                                    src="assets/uploads/<?php echo $arr_cart_p_featured_photo[$i]; ?>"
                                                    alt="<?php echo $arr_cart_p_name[$i]; ?>" width="80" /></a>
                                        </td>
                                        <td class="cart__meta small--text-left cart-flex-item">
                                            <div class="list-view-item__title">
                                                <a href="product-layout1.html"><?php echo $arr_cart_p_name[$i]; ?></a>
                                            </div>
                                            <div class="cart__meta-text">
                                                Color: <?php echo $arr_cart_color_name[$i]; ?><br>Size:
                                                <?php echo $arr_cart_size_name[$i]; ?>
                                            </div>
                                            <div class="cart-price d-md-none">
                                                <span class="money fw-500">$<?php echo $arr_cart_p_current_price[$i]; ?></span>
                                            </div>
                                        </td>

                                        <td class="cart__price-wrapper cart-flex-item text-center small--hide">
                                            <span class="money">$<?php echo $arr_cart_p_current_price[$i]; ?></span>
                                        </td>
                                        <td class="cart__update-wrapper cart-flex-item text-end text-md-center">
                                            <div class="cart__qty d-flex justify-content-end justify-content-md-center">
                                                <div class="qtyField">
                                                    <input type="hidden" name="product_id[]"
                                                        value="<?php echo $arr_cart_p_id[$i]; ?>">
                                                    <input type="hidden" name="product_name[]"
                                                        value="<?php echo $arr_cart_p_name[$i]; ?>">
                                                    <a class="qtyBtn minus" href="javascript:void(0);"><i
                                                            class="icon an an-minus-r"></i></a>
                                                    <input step="1" min="1" max="" name="quantity[]"
                                                        value="<?php echo $arr_cart_p_qty[$i]; ?>" title="Qty" size="4"
                                                        pattern="[0-9]*" inputmode="numeric" class="cart__qty-input qty"
                                                        pattern="[0-9]*" />
                                                    <a class="qtyBtn plus" href="javascript:void(0);"><i
                                                            class="icon an an-plus-r"></i></a>
                                                </div>
                                            </div>
                                            <a href="#" title="Remove"
                                                class="removeMb d-md-none d-inline-block text-decoration-underline mt-2 me-3">Remove</a>
                                        </td>
                                        <td class="cart-price cart-flex-item text-center small--hide">
                                            <span class="money fw-500">$<?php
                                            $row_total_price = $arr_cart_p_current_price[$i] * $arr_cart_p_qty[$i];
                                            $table_total_price = $table_total_price + $row_total_price;
                                            ?>
                                                <?php echo $row_total_price; ?></span>
                                        </td>
                                    </tr>
                                <?php endfor; ?>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-start pt-3"><a href="#"
                                            class="btn btn--link d-inline-flex align-items-center btn--small p-0 cart-continue"><i
                                                class="me-1 icon an an-angle-left-l"></i><span
                                                class="text-decoration-underline">Continue shopping</span></a></td>
                                    <td colspan="3" class="text-end pt-3">
                                        <a name="clear" onclick="return confirmDelete1();" href="remove_cart.php"
                                            class="btn btn--link d-inline-flex align-items-center btn--small small--hide">
                                            <i class="me-1 icon an an-times-r"></i>
                                            <span class="ms-1 text-decoration-underline">Clear Shopping Cart</span>
                                        </a>

                                        <button type="submit" name="form1"
                                            class="btn btn--small d-inline-flex align-items-center rounded cart-continue ml-2"><i
                                                class="me-1 icon an an-sync-ar d-none"></i>Update Cart</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </form>




                </div>
                <?php
// Initialize variables
$valid_shipping = false;
$shipping_cost = 0.00;


// Check if the form is submitted (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch the default shipping cost from the database
    $sql_default = "SELECT setting_value FROM settings WHERE setting_name = :setting_name";
    $stmt = $pdo->prepare($sql_default);
    $stmt->bindParam(':setting_name', $setting_name, PDO::PARAM_STR);
    $setting_name = 'default_shipping_cost'; // Default setting for shipping cost
    $stmt->execute();

    // Default shipping cost value
    $default_cost = 10.00; // Set fallback value if not found
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $default_cost = (float) $row['setting_value']; // Convert to float for calculation
    }

    // Check if postal code is provided and valid
    if (isset($_POST['postal_code']) && !empty($_POST['postal_code'])) {
        $postal_code = $_POST['postal_code'];

        // Query to get shipping cost based on postal code
        $sql = "SELECT cost FROM shipping_costs WHERE postal_code = :postal_code";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':postal_code', $postal_code, PDO::PARAM_STR);
        $stmt->execute();

        // If postal code found in DB, show the respective cost
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $shipping_cost = (float) $row['cost'];
            $valid_shipping = true; // Set flag to true if shipping cost is valid
        } else {
            // Use default shipping cost if postal code not found
            $shipping_cost = $default_cost;
            $valid_shipping = true; // Set flag to true since we are using a valid default cost
        }
    } else {
        // If postal code is missing
        $valid_shipping = false; // Disable checkout if no postal code is provided
        $shipping_cost = 0;
    }
}
?>


<div class="col-12 col-sm-12 col-md-12 col-lg-4 cart__footer">
    <div class="cart_info">
        <div id="shipping-calculator" class="mb-4 cart-col">
            <h5>Get shipping estimates</h5>
            <form class="estimate-form pt-1" action="" method="post">
                <div class="form-group">
                    <label for="address_zip">Postal/Zip Code</label>
                    <input type="text" id="address_zip" name="postal_code" value="<?php echo isset($_POST['postal_code']) ? $_POST['postal_code'] : ''; ?>" />
                </div>
                <div class="actionRow">
                    <input type="submit" class="btn rounded get-rates w-100" value="Calculate shipping" />
                </div>
            </form>
        </div>

        <div class="cart-order_detail cart-col">
            <div class="row">
                <span class="col-6 col-sm-6 cart__subtotal-title"><strong>Total</strong></span>
                <span class="col-6 col-sm-6 cart__subtotal-title cart__subtotal text-end">
                    <span class="money">$<?php echo $table_total_price; ?></span>
                </span>
            </div>
            <div class="row">
                <span class="col-6 col-sm-6 cart__subtotal-title"><strong>Shipping Cost</strong></span>
                <span class="col-6 col-sm-6 cart__subtotal-title cart__subtotal text-end">
                    <span class="money">
                        <?php
                        if ($valid_shipping) {
                            $_SESSION['shipping_message'] =  number_format($shipping_cost, 2);
                            echo "$".$_SESSION['shipping_message'];
                        } else {
                            echo "Please enter a postal code.";
                        }
                        ?>
                    </span>
                </span>
            </div>
            <div class="row">
                <span class="col-6 col-sm-6 cart__subtotal-title"><strong>Subtotal</strong></span>
                <span class="col-6 col-sm-6 cart__subtotal-title cart__subtotal text-end">
                    <span class="money">
                        <?php
                        // Calculate the subtotal by adding table total price and shipping cost
                        $subtotal = $table_total_price + $shipping_cost;
                        echo "$" . number_format($subtotal, 2);
                        ?>
                    </span>
                </span>
            </div>

            <!-- Proceed to Checkout Button, hidden by default -->
            <?php if ($valid_shipping): ?>
                <a href="checkout.php" id="cartCheckout" class="btn btn--small-wide rounded my-4 checkout">
                    Proceed To Checkout
                </a>
            <?php else: ?>
                <div class="alert alert-warning mt-3">
                    Please enter a postal code to proceed to checkout.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>




            <?php endif; ?>

            <!--End Cart Page-->
        </div>
        <!--End Main Content-->
    </div>
    <!--End Body Container-->
    <script>
        function confirmDelete() {
            return confirm('Are you sure you want to delete this item?');
        }
        function confirmDelete1() {
            return confirm('Are you sure you want to Clear Shopping Cart?');
        }
    </script>

    <!--Footer-->
    <?php require_once('_footer.php'); ?>