<?php require_once('_header.php'); ?>
<?php
if (isset($_POST['form1'])) {

    $valid = 1;
    
    // Check if the email is empty
    if (empty($_POST['cust_email'])) {
        $valid = 0;
        $error_message .= "Email is required.\n";
    } else {
        // Check if the email is valid
        if (filter_var($_POST['cust_email'], FILTER_VALIDATE_EMAIL) === false) {
            $valid = 0;
            $error_message .= "Please enter a valid email address.\n";
        } else {
            // Check if the email exists in the database
            $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email=?");
            $statement->execute(array($_POST['cust_email']));
            $total = $statement->rowCount();
            if (!$total) {
                $valid = 0;
                $error_message .= "The email address is not registered with us.\n";
            }
        }
    }

    // If validation passes
    if ($valid == 1) {

        // Fetch the message to be shown after password reset link is sent
        $statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $forget_password_message = $row['forget_password_message'];
        }

        // Generate a unique token for password reset
        $token = md5(rand());
        $now = time();

        // Update the customer record with the token and timestamp
        $statement = $pdo->prepare("UPDATE tbl_customer SET cust_token=?, cust_timestamp=? WHERE cust_email=?");
        $statement->execute(array($token, $now, strip_tags($_POST['cust_email'])));

        // Prepare the password reset email message
        $message = '<p>We received a request to reset your password. If you did not request this change, please ignore this email.<br> 
                    Click the link below to reset your password:<br> 
                    <a href="'.BASE_URL.'reset-password.php?email='.$_POST['cust_email'].'&token='.$token.'">Click here to reset your password</a></p>';

        // Send the password reset email
        $to      = $_POST['cust_email'];
        $subject = "Password Reset Request";
        $headers = "From: noreply@" . BASE_URL . "\r\n" .
                   "Reply-To: noreply@" . BASE_URL . "\r\n" .
                   "X-Mailer: PHP/" . phpversion() . "\r\n" . 
                   "MIME-Version: 1.0\r\n" . 
                   "Content-Type: text/html; charset=ISO-8859-1\r\n";

        mail($to, $subject, $message, $headers);

        // Show success message
        $success_message = $forget_password_message;
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
                            <h1 class="collection-hero__title">Forgot Password</h1>
                            <div class="breadcrumbs text-uppercase mt-1 mt-lg-2"><a href="index.html" title="Back to the home page">Home</a><span>|</span><span class="fw-bold">Forgot Password</span></div>
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

                                        <h3 class="h4 text-uppercase">FORGOT PASSWORD</h3>
                                     
                                        <?php
                    if($error_message != '') {
                        echo "<script>alert('".$error_message."')</script>";
                    }
                    if($success_message != '') {
                        echo "<script>alert('".$success_message."')</script>";
                    }
                    ?>
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                                <div class="form-group">
                                                    <label for="CustomerEmail" class="d-none">Email <span class="required">*</span></label>
                                                    <input type="email" name="cust_email" placeholder="Email" id="CustomerEmail" value="" required />
                                                </div>
                                            </div>
                                        
                                        </div>
                                        <div class="row">
                                            <div class="text-left col-12 col-sm-12 col-md-12 col-lg-12">
                                                <p class="d-flex-center">
                                                    <input type="submit" name="form1" class="btn rounded me-auto" value="Submit">
                                                    <a href="login.php">Back to Login</a>
                                                </p>
                                            </div>
                                        </div>
                                    </form>
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