<?php
if(isset($_POST['remove_cart'])) {
   

unset($_SESSION['cart_p_id']);
unset($_SESSION['cart_size_id']);
unset($_SESSION['cart_size_name']);
unset($_SESSION['cart_color_id']);
unset($_SESSION['cart_color_name']);
unset($_SESSION['cart_p_qty']);
unset($_SESSION['cart_p_current_price']);
unset($_SESSION['cart_p_name']);
unset($_SESSION['cart_p_featured_photo']);

// Redirect to the cart page
header('location: cart.php');
exit;
}
else{
    header('location: cart.php');
    exit;
}
?>