<?php require_once('_header.php'); ?>
<?php
if (isset($_POST['form1'])) {

    $valid = 1;

    if(empty($_POST['cust_name'])) {
        $valid = 0;
        $error_message .= "Name is required.<br>";
    }

    if(empty($_POST['cust_email'])) {
        $valid = 0;
        $error_message .= "Email is required.<br>";
    } else {
        if (filter_var($_POST['cust_email'], FILTER_VALIDATE_EMAIL) === false) {
            $valid = 0;
            $error_message .= "Please enter a valid email address.<br>";
        } else {
            $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email=?");
            $statement->execute(array($_POST['cust_email']));
            $total = $statement->rowCount();                            
            if($total) {
                $valid = 0;
                $error_message .= "This email is already registered.<br>";
            }
        }
    }

    if(empty($_POST['cust_phone'])) {
        $valid = 0;
        $error_message .= "Phone number is required.<br>";
    }

    if(empty($_POST['cust_address'])) {
        $valid = 0;
        $error_message .= "Address is required.<br>";
    }

    if(empty($_POST['cust_country'])) {
        $valid = 0;
        $error_message .= "Country is required.<br>";
    }

    if(empty($_POST['cust_city'])) {
        $valid = 0;
        $error_message .= "City is required.<br>";
    }

    if(empty($_POST['cust_state'])) {
        $valid = 0;
        $error_message .= "State is required.<br>";
    }

    if(empty($_POST['cust_zip'])) {
        $valid = 0;
        $error_message .= "Zip code is required.<br>";
    }

    if( empty($_POST['cust_password']) || empty($_POST['cust_re_password']) ) {
        $valid = 0;
        $error_message .= "Password and confirm password are required.<br>";
    }

    if( !empty($_POST['cust_password']) && !empty($_POST['cust_re_password']) ) {
        if($_POST['cust_password'] != $_POST['cust_re_password']) {
            $valid = 0;
            $error_message .= "Password and confirm password do not match.<br>";
        }
    }

    if($valid == 1) {

        $token = md5(time());
        $cust_datetime = date('Y-m-d h:i:s');
        $cust_timestamp = time();

        // saving into the database
        $statement = $pdo->prepare("INSERT INTO tbl_customer (
                                        cust_name,
                                        cust_cname,
                                        cust_email,
                                        cust_phone,
                                        cust_country,
                                        cust_address,
                                        cust_city,
                                        cust_state,
                                        cust_zip,
                                        cust_b_name,
                                        cust_b_cname,
                                        cust_b_phone,
                                        cust_b_country,
                                        cust_b_address,
                                        cust_b_city,
                                        cust_b_state,
                                        cust_b_zip,
                                        cust_s_name,
                                        cust_s_cname,
                                        cust_s_phone,
                                        cust_s_country,
                                        cust_s_address,
                                        cust_s_city,
                                        cust_s_state,
                                        cust_s_zip,
                                        cust_password,
                                        cust_token,
                                        cust_datetime,
                                        cust_timestamp,
                                        cust_status
                                    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $statement->execute(array(
                                        strip_tags($_POST['cust_name']),
                                        strip_tags($_POST['cust_cname']),
                                        strip_tags($_POST['cust_email']),
                                        strip_tags($_POST['cust_phone']),
                                        strip_tags($_POST['cust_country']),
                                        strip_tags($_POST['cust_address']),
                                        strip_tags($_POST['cust_city']),
                                        strip_tags($_POST['cust_state']),
                                        strip_tags($_POST['cust_zip']),
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        md5($_POST['cust_password']),
                                        $token,
                                        $cust_datetime,
                                        $cust_timestamp,
                                        0
                                    ));

        // Send email for confirmation of the account
        $to = $_POST['cust_email'];
        
        $subject = "Account Verification";
        $verify_link = BASE_URL.'verify.php?email='.$to.'&token='.$token;
        $message = '
Thank you for registering with us.<br><br>

Please click the link below to verify your account:<br><br>

<a href="'.$verify_link.'">'.$verify_link.'</a>';

        $headers = "From: noreply@" . BASE_URL . "\r\n" .
                   "Reply-To: noreply@" . BASE_URL . "\r\n" .
                   "X-Mailer: PHP/" . phpversion() . "\r\n" . 
                   "MIME-Version: 1.0\r\n" . 
                   "Content-Type: text/html; charset=ISO-8859-1\r\n";
        
        // Sending Email
        mail($to, $subject, $message, $headers);

        unset($_POST['cust_name']);
        unset($_POST['cust_cname']);
        unset($_POST['cust_email']);
        unset($_POST['cust_phone']);
        unset($_POST['cust_address']);
        unset($_POST['cust_city']);
        unset($_POST['cust_state']);
        unset($_POST['cust_zip']);

        $success_message = "Your account has been created successfully. Please check your email for the verification link.";
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
                            <h1 class="collection-hero__title">Registration</h1>
                            <div class="breadcrumbs text-uppercase mt-1 mt-lg-2"><a href="index.html" title="Back to the home page">Home</a><span>|</span><span class="fw-bold">Registration</span></div>
                        </div>
                    </div>
                </div>
                <!--End Collection Banner-->

                <!--Container-->
                <div class="container">
                    <!--Main Content-->
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 box mt-2 mt-lg-5">	
                            <h3 class="h4 text-uppercase mb-3">Personal Information</h3>
                            <form action="" method="post"  class="customer-form">
                            <?php $csrf->echoInputField(); ?>
                            <?php
                                if($error_message != '') {
                                    echo "<div class='error' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$error_message."</div>";
                                }
                                if($success_message != '') {
                                    echo "<div class='success' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$success_message."</div>";
                                }
                                ?>
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="CustomerFirstName" class="d-none">Full Name <span class="required">*</span></label>
                                            <input id="CustomerFirstName" type="text" name="cust_name" placeholder="Full Name" value="<?php if(isset($_POST['cust_name'])){echo $_POST['cust_name'];} ?>" required="">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="CustomerLastName" class="d-none">Company Name <span class="required">*</span></label>
                                            <input id="CustomerLastName" type="text" name="cust_cname" placeholder="Company Name" required="" value="<?php if(isset($_POST['cust_cname'])){echo $_POST['cust_cname'];} ?>">                       	
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="CustomerFirstName" class="d-none">Email Id <span class="required">*</span></label>
                                            <input id="CustomerFirstName" type="text" name="cust_email" placeholder="Email Id" value="<?php if(isset($_POST['cust_email'])){echo $_POST['cust_email'];} ?>" required="">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="CustomerLastName" class="d-none">Contact Number <span class="required">*</span></label>
                                            <input id="CustomerLastName" type="text" name="cust_phone" placeholder="Contact Number" required="" value="<?php if(isset($_POST['cust_phone'])){echo $_POST['cust_phone'];} ?>">                       	
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="form-group">
                                            <label for="CustomerEmail" class="d-none">Address <span class="required">*</span></label>
                                            <input id="CustomerLastName" type="text" name="cust_address" placeholder="Address" required="" value="<?php if(isset($_POST['cust_address'])){echo $_POST['cust_address'];} ?>">                       	
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="CustomerFirstName" class="d-none">Country <span class="required">*</span></label>
                                            <input id="CustomerFirstName" type="text" name="cust_country" placeholder="Country" value="<?php if(isset($_POST['cust_country'])){echo $_POST['cust_country'];} ?>" required="">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="CustomerLastName" class="d-none">City <span class="required">*</span></label>
                                            <input id="CustomerLastName" type="text" name="cust_city" placeholder="City" required="" value="<?php if(isset($_POST['cust_city'])){echo $_POST['cust_city'];} ?>">                       	
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="CustomerFirstName" class="d-none">State <span class="required">*</span></label>
                                            <input id="CustomerFirstName" type="text" name="cust_state" placeholder="State" value="<?php if(isset($_POST['cust_state'])){echo $_POST['cust_state'];} ?>" required="">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="CustomerLastName" class="d-none">Zip Address <span class="required">*</span></label>
                                            <input id="CustomerLastName" type="text" name="cust_zip" placeholder="City" required="" value="<?php if(isset($_POST['cust_zip'])){echo $_POST['cust_zip'];} ?>">                       	
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="CustomerFirstName" class="d-none">Enter Password <span class="required">*</span></label>
                                            <input id="CustomerFirstName" type="text" name="cust_password" placeholder="Enter Password"  required="">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="CustomerLastName" class="d-none">Confirm Password <span class="required">*</span></label>
                                            <input id="CustomerLastName" type="text" name="cust_re_password" placeholder="Confirm Password" required="" >                       	
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="text-left col-12 col-sm-12 col-md-6 col-lg-6">
                                        <input type="submit" class="btn rounded mb-3" name="form1" value="Submit">
                                    </div>
                                    <div class="text-right col-12 col-sm-12 col-md-6 col-lg-6">
                                        <a href="login.php"><i class="align-middle icon an an-an-double-left me-2"></i>Back To Login</a>
                                    </div>
                                </div>
                               
                          
                            </form>                       
                        </div>
                    </div>
                    <!--End Main Content-->
                </div>
                <!--End Container-->
            </div>
            <!--End Body Container-->

            <?php require_once('_footer.php'); ?>