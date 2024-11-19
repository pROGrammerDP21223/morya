<?php require_once('_header.php');

if (isset($_SESSION['customer'])) {
    header('location: ' . BASE_URL . 'dashboard.php');
    exit;
}

?>

<?php
if(isset($_POST['form1'])) {

    // Initialize error_message
    $error_message = '';

    // Directly using the text from the provided language data
    if(empty($_POST['cust_email']) || empty($_POST['cust_password'])) {
        // Replaced LANG_VALUE_132 with the actual text
        $error_message = 'Email and/or Password can not be empty.' . '<br>';
    } else {
        $cust_email = strip_tags($_POST['cust_email']);
        $cust_password = strip_tags($_POST['cust_password']);

        $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email=?");
        $statement->execute(array($cust_email));
        $total = $statement->rowCount();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($result as $row) {
            $cust_status = $row['cust_status'];
            $row_password = $row['cust_password'];
        }

        if($total == 0) {
            // Replaced LANG_VALUE_133 with the actual text
            $error_message .= 'Email Address does not match.' . '<br>';
        } else {
            // Using MD5 form
            if($row_password != md5($cust_password)) {
                // Replaced LANG_VALUE_139 with the actual text
                $error_message .= 'Passwords do not match.' . '<br>';
            } else {
                if($cust_status == 0) {
                    // Replaced LANG_VALUE_148 with the actual text
                    $error_message .= 'Sorry! Your account is inactive. Please contact the administrator.' . '<br>';
                } else {
                    $_SESSION['customer'] = $row;

                    // Encode session data to JSON and save it to a file
                    $json_data = json_encode($_SESSION['customer']);
                    file_put_contents('customer_session_data.json', $json_data);
                    header("location: dashboard.php");
                }
            }
        }
    }
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
                            <h1 class="collection-hero__title">Login</h1>
                            <div class="breadcrumbs text-uppercase mt-1 mt-lg-2"><a href="index.html" title="Back to the home page">Home</a><span>|</span><span class="fw-bold">Login</span></div>
                        </div>
                    </div>
                </div>
                <!--End Collection Banner-->

                <!--Container-->
                <div class="container">
                    <!--Main Content-->
                    <div class="login-register pt-2 pt-lg-5">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-4 mb-md-0">
                                <div class="inner">
                                <form action="" method="post" class="customer-form">
                                <?php $csrf->echoInputField(); ?> 

                                        <h3 class="h4 text-uppercase">REGISTERED CUSTOMERS</h3>
                                        <p>If you have an account with us, please log in.</p>
                                        <?php
                                if($error_message != '') {
                                    echo "<div class='error' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$error_message."</div>";
                                }
                                if($success_message != '') {
                                    echo "<div class='success' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$success_message."</div>";
                                }
                                ?>
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                                <div class="form-group">
                                                    <label for="CustomerEmail" class="d-none">Email <span class="required">*</span></label>
                                                    <input type="email" name="cust_email" placeholder="Email" id="CustomerEmail" value="" required />
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                                <div class="form-group">
                                                    <label for="CustomerPassword" class="d-none">Password <span class="required">*</span></label>
                                                    <input type="password" name="cust_password" placeholder="Password" id="CustomerPassword" value="" required />                        	
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="text-left col-12 col-sm-12 col-md-12 col-lg-12">
                                                <p class="d-flex-center">
                                                    <input type="submit" name="form1" class="btn rounded me-auto" value="Sign In">
                                                    <a href="forgot-password.php">Forgot your password?</a>
                                                </p>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="inner">
                                    <h3 class="h4 text-uppercase">NEW CUSTOMER?</h3>
                                    <p>Registering for this site allows you to access your order status and history. We’ll get a new account set up for you in no time. For this will only ask you for information necessary to make the purchase process faster and easier</p>
                                    <a href="register.php" class="btn rounded">Create an account</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--End Main Content-->
                </div>
                <!--End Container-->
            </div>
            <!--End Body Container-->

            <?php require_once('_footer.php'); ?>