<?php
ob_start();
session_start();
include("admin/inc/config.php");
include("admin/inc/functions.php");
include("admin/inc/CSRF_Protect.php");
include("_seo_meta_tag.php");
$csrf = new CSRF_Protect();
$error_message = '';
$success_message = '';
$error_message1 = '';
$success_message1 = '';
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
	$logo = $row['logo'];
	$favicon = $row['favicon'];
	$contact_email = $row['contact_email'];
	$contact_phone = $row['contact_phone'];
	$contact_address = $row['contact_address'];
}
// Checking the order table and removing the pending transaction that are 24 hours+ old. Very important
$current_date_time = date('Y-m-d H:i:s');
$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE payment_status=?");
$statement->execute(array('Pending'));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
	$ts1 = strtotime($row['payment_date']);
	$ts2 = strtotime($current_date_time);
	$diff = $ts2 - $ts1;
	$time = $diff / (3600);
	if ($time > 24) {
		// Return back the stock amount
		$statement1 = $pdo->prepare("SELECT * FROM tbl_order WHERE payment_id=?");
		$statement1->execute(array($row['payment_id']));
		$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result1 as $row1) {
			$statement2 = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
			$statement2->execute(array($row1['product_id']));
			$result2 = $statement2->fetchAll(PDO::FETCH_ASSOC);
			foreach ($result2 as $row2) {
				$p_qty = $row2['p_qty'];
			}
			$final = $p_qty + $row1['quantity'];
			$statement = $pdo->prepare("UPDATE tbl_product SET p_qty=? WHERE p_id=?");
			$statement->execute(array($final, $row1['product_id']));
		}
		// Deleting data from table
		$statement1 = $pdo->prepare("DELETE FROM tbl_order WHERE payment_id=?");
		$statement1->execute(array($row['payment_id']));
		$statement1 = $pdo->prepare("DELETE FROM tbl_payment WHERE id=?");
		$statement1->execute(array($row['id']));
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<!--Required Meta Tags-->
	<meta charset="utf-8" />
	<meta http-equiv="x-ua-compatible" content="ie=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="robots" content="index, follow" />
	<meta name="revisit-after" content="7 days" />
	<meta name="author"
		content="Designed and Promoted by Maharashtra Industries Directory, www.maharashtradirectory.com" />
	<?php
	$meta_tags = [
		'index.php' => ['title' => $meta_title_home, 'keywords' => $meta_keyword_home, 'description' => $meta_description_home],
		'about.php' => ['title' => $meta_title_about_us, 'keywords' => $meta_keyword_about_us, 'description' => $meta_description_about_us],
		'company_profile.php' => ['title' => $meta_title_company_profile, 'keywords' => $meta_keyword_company_profile, 'description' => $meta_description_company_profile],
		'our_team.php' => ['title' => $meta_title_our_team, 'keywords' => $meta_keyword_our_team, 'description' => $meta_description_our_team],
		'plain_partition_corrugated_box.php' => ['title' => $meta_title_plain_partition_corrugated_box, 'keywords' => $meta_keyword_plain_partition_corrugated_box, 'description' => $meta_description_plain_partition_corrugated_box],
		'corrugated_partition_box_rectangle.php' => ['title' => $meta_title_corrugated_partition_box_rectangle, 'keywords' => $meta_keyword_corrugated_partition_box_rectangle, 'description' => $meta_description_corrugated_partition_box_rectangle],
		'corrugated_partition_box_square.php' => ['title' => $meta_title_corrugated_partition_box_square, 'keywords' => $meta_keyword_corrugated_partition_box_square, 'description' => $meta_description_corrugated_partition_box_square],
		'quarantine_bed.php' => ['title' => $meta_title_quarantine_bed, 'keywords' => $meta_keyword_quarantine_bed, 'description' => $meta_description_quarantine_bed],
		'corrugated_punched_box.php' => ['title' => $meta_title_corrugated_punched_box, 'keywords' => $meta_keyword_corrugated_punched_box, 'description' => $meta_description_corrugated_punched_box],
		'duplex_corrugated_box.php' => ['title' => $meta_title_duplex_corrugated_box, 'keywords' => $meta_keyword_duplex_corrugated_box, 'description' => $meta_description_duplex_corrugated_box],
		'plain_corrugated_boxes.php' => ['title' => $meta_title_plain_corrugated_boxes, 'keywords' => $meta_keyword_plain_corrugated_boxes, 'description' => $meta_description_plain_corrugated_boxes],
		'plain_corrugated_packaging_box.php' => ['title' => $meta_title_plain_corrugated_packaging_box, 'keywords' => $meta_keyword_plain_corrugated_packaging_box, 'description' => $meta_description_plain_corrugated_packaging_box],
		'flat_corrugated_boxes.php' => ['title' => $meta_title_flat_corrugated_boxes, 'keywords' => $meta_keyword_flat_corrugated_boxes, 'description' => $meta_description_flat_corrugated_boxes],
		'fabric_corrugated_boxes.php' => ['title' => $meta_title_fabric_corrugated_boxes, 'keywords' => $meta_keyword_fabric_corrugated_boxes, 'description' => $meta_description_fabric_corrugated_boxes],
		'corrugated_carton_boxes.php' => ['title' => $meta_title_corrugated_carton_boxes, 'keywords' => $meta_keyword_corrugated_carton_boxes, 'description' => $meta_description_corrugated_carton_boxes],
		'corrugated_box_partitions.php' => ['title' => $meta_title_corrugated_box_partitions, 'keywords' => $meta_keyword_corrugated_box_partitions, 'description' => $meta_description_corrugated_box_partitions],
		'plywood_boxes.php' => ['title' => $meta_title_plywood_boxes, 'keywords' => $meta_keyword_plywood_boxes, 'description' => $meta_description_plywood_boxes],
		'wooden_packaging_boxes.php' => ['title' => $meta_title_wooden_packaging_boxes, 'keywords' => $meta_keyword_wooden_packaging_boxes, 'description' => $meta_description_wooden_packaging_boxes],
		'industrial_wooden_boxes.php' => ['title' => $meta_title_industrial_wooden_boxes, 'keywords' => $meta_keyword_industrial_wooden_boxes, 'description' => $meta_description_industrial_wooden_boxes],
		'heavy_duty_wooden_box.php' => ['title' => $meta_title_heavy_duty_wooden_box, 'keywords' => $meta_keyword_heavy_duty_wooden_box, 'description' => $meta_description_heavy_duty_wooden_box],
		'heavy_duty_wooden_packaging_box.php' => ['title' => $meta_title_heavy_duty_wooden_packaging_box, 'keywords' => $meta_keyword_heavy_duty_wooden_packaging_box, 'description' => $meta_description_heavy_duty_wooden_packaging_box],
		'plywood_pallets.php' => ['title' => $meta_title_plywood_pallets, 'keywords' => $meta_keyword_plywood_pallets, 'description' => $meta_description_plywood_pallets],
		'pinewood_pallets.php' => ['title' => $meta_title_pinewood_pallets, 'keywords' => $meta_keyword_pinewood_pallets, 'description' => $meta_description_pinewood_pallets],
		'corrugated_pallet_box.php' => ['title' => $meta_title_corrugated_pallet_box, 'keywords' => $meta_keyword_corrugated_pallet_box, 'description' => $meta_description_corrugated_pallet_box],
		'industrial_wooden_pallets.php' => ['title' => $meta_title_industrial_wooden_pallets, 'keywords' => $meta_keyword_industrial_wooden_pallets, 'description' => $meta_description_industrial_wooden_pallets],
		'corrugated_packaging_boxes.php' => ['title' => $meta_title_corrugated_packaging_boxes, 'keywords' => $meta_keyword_corrugated_packaging_boxes, 'description' => $meta_description_corrugated_packaging_boxes],
		'corrugated_packaging_carton_box.php' => ['title' => $meta_title_corrugated_packaging_carton_box, 'keywords' => $meta_keyword_corrugated_packaging_carton_box, 'description' => $meta_description_corrugated_packaging_carton_box],
		'laminated_packaging_box.php' => ['title' => $meta_title_laminated_packaging_box, 'keywords' => $meta_keyword_laminated_packaging_box, 'description' => $meta_description_laminated_packaging_box],
		'handle_corrugated_box.php' => ['title' => $meta_title_handle_corrugated_box, 'keywords' => $meta_keyword_handle_corrugated_box, 'description' => $meta_description_handle_corrugated_box],
		'handled_plain_corrugated_box.php' => ['title' => $meta_title_handled_plain_corrugated_box, 'keywords' => $meta_keyword_handled_plain_corrugated_box, 'description' => $meta_description_handled_plain_corrugated_box],
		'plastic_handle_corrugated_box.php' => ['title' => $meta_title_plastic_handle_corrugated_box, 'keywords' => $meta_keyword_plastic_handle_corrugated_box, 'description' => $meta_description_plastic_handle_corrugated_box],
		'corrugated_packaging_sheet.php' => ['title' => $meta_title_corrugated_packaging_sheet, 'keywords' => $meta_keyword_corrugated_packaging_sheet, 'description' => $meta_description_corrugated_packaging_sheet],
		'heavy_duty_corrugated_box.php' => ['title' => $meta_title_heavy_duty_corrugated_box, 'keywords' => $meta_keyword_heavy_duty_corrugated_box, 'description' => $meta_description_heavy_duty_corrugated_box],
		'heavy_duty_corrugated_packaging_box.php' => ['title' => $meta_title_heavy_duty_corrugated_packaging_box, 'keywords' => $meta_keyword_heavy_duty_corrugated_packaging_box, 'description' => $meta_description_heavy_duty_corrugated_packaging_box],
		'laminated_duplex_box.php' => ['title' => $meta_title_laminated_duplex_box, 'keywords' => $meta_keyword_laminated_duplex_box, 'description' => $meta_description_laminated_duplex_box],
		'heavy_duty_corrugated_pallet_box.php' => ['title' => $meta_title_heavy_duty_corrugated_pallet_box, 'keywords' => $meta_keyword_heavy_duty_corrugated_pallet_box, 'description' => $meta_description_heavy_duty_corrugated_pallet_box],
		'clients.php' => ['title' => $meta_title_clients, 'keywords' => $meta_keyword_clients, 'description' => $meta_description_clients],
		'testimonials.php' => ['title' => $meta_title_testimonials, 'keywords' => $meta_keyword_testimonials, 'description' => $meta_description_testimonials],
		'blogs.php' => ['title' => $meta_title_blogs, 'keywords' => $meta_keyword_blogs, 'description' => $meta_description_blogs],
		'career.php' => ['title' => $meta_title_career, 'keywords' => $meta_keyword_career, 'description' => $meta_description_career],
		'contact.php' => ['title' => $meta_title_contact, 'keywords' => $meta_keyword_contact, 'description' => $meta_description_contact]
	];
	$cur_page = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
	if (array_key_exists($cur_page, $meta_tags)) {
		$meta = $meta_tags[$cur_page];
		?>
		<title><?php echo $meta['title']; ?></title>
		<meta name="keywords" content="<?php echo $meta['keywords']; ?>">
		<meta name="description" content="<?php echo $meta['description']; ?>">
		<?php
	} else {
		?>
		<title>CompanY Name</title>
		<meta name="keywords" content="keywords">
		<meta name="description" content="description">
	<?php }
	?>
	<link rel="shortcut icon" href="assets/uploads/<?php echo $favicon; ?>" />
	<!-- Plugins CSS -->
	<link rel="stylesheet" href="assets/css/plugins.css" />
	<!-- Main Style CSS -->
	<link rel="stylesheet" href="assets/css/style.css" />
	<link rel="stylesheet" href="assets/css/responsive.css" />
</head>
<body class="template-index index-demo1">
	<!-- Page Loader -->
	<!-- <div id="pre-loader">
		<img src="assets/images/loader.gif" alt="Loading..." />
	</div> -->
	<!-- End Page Loader -->
	<!--Page Wrapper-->
	<div class="page-wrapper">
		<!--Header-->
		<div id="header" class="header header-1">
			<div class="header-main">
				<header class="header-wrap container d-flex align-items-center">
					<div class="row g-0 align-items-center w-100">
						<!--Social Icons-->
						<div class="col-4 col-sm-4 col-md-4 col-lg-5 d-none d-lg-block">
							<ul class="social-icons list-inline">
								<!-- <li class="list-inline-item">
									<a href="#"><i class="an an-facebook" aria-hidden="true"></i><span
											class="tooltip-label">Facebook</span></a>
								</li> -->
								<?php
								$statement = $pdo->prepare("SELECT * FROM tbl_social");
								$statement->execute();
								$result = $statement->fetchAll(PDO::FETCH_ASSOC);
								foreach ($result as $row) {
									?>
									<?php if ($row['social_url'] != ''): ?>
										<li class="list-inline-item"><a href="<?php echo $row['social_url']; ?>"><i
													class="<?php echo $row['social_icon']; ?> " aria-hidden="true"></i><span
													class="tooltip-label"><?php echo $row['social_name']; ?></span></a></li>
									<?php endif; ?>
									<?php
								}
								?>
							</ul>
						</div>
						<!--End Social Icons-->
						<!--Logo / Menu Toggle-->
						<div class="col-6 col-sm-6 col-md-6 col-lg-2 d-flex">
							<!--Mobile Toggle-->
							<button type="button"
								class="btn--link site-header__menu js-mobile-nav-toggle mobile-nav--open me-3 d-lg-none">
								<i class="icon an an-times-l"></i><i class="icon an an-bars-l"></i>
							</button>
							<!--End Mobile Toggle-->
							<!--Logo-->
							<div class="logo mx-lg-auto">
								<a href="index.php"><img class="logo-img" src="assets/uploads/<?php echo $logo; ?>"
										alt="Company Name " title="Company Name " /><span
										class="logo-txt d-none">Company Name</span></a>
							</div>
							<!--End Logo-->
						</div>
						<!--End Logo / Menu Toggle-->
						<!--Right Action-->
						<div class="col-6 col-sm-6 col-md-6 col-lg-5 icons-col text-right d-flex justify-content-end">
							<!--Search-->
							<div class="site-search iconset">
								<i class="icon an an-search-l"></i><span class="tooltip-label">Search</span>
							</div>
							<div class="user-link iconset">
								<i class="icon an an-user-expand"></i><span class="tooltip-label">Account</span>
							</div>
							<div id="userLinks">
								<ul class="user-links">
									<?php
									if (isset($_SESSION['customer'])) {
										?>
										<li><a href="login.php">Login</a></li>
										<li><a href="register.php">Sign Up</a></li>
										<?php
									} else {
										?>
										<li><a href="dashboard.php">Dashboard</a></li>
										<?php
									}
									?>
								</ul>
							</div>
							<!--End Setting Dropdown-->
							<!--Minicart Drawer-->
							<div class="header-cart iconset">
								<a href="cart-style1.php" class="site-header__cart btn-minicart" data-bs-toggle="modal"
									data-bs-target="#minicart-drawer">
									<i class="icon an an-sq-bag"></i><span
										class="site-cart-count counter d-flex-center justify-content-center position-absolute translate-middle rounded-circle">
										<?php
										$table_total_price = 0;
										$total_items = 0;
										if (isset($_SESSION['cart_p_id'])) {
											foreach ($_SESSION['cart_p_qty'] as $key => $quantity) {
												$price = $_SESSION['cart_p_current_price'][$key] ?? 0; // Ensure price exists
												$table_total_price += $price * $quantity;
												$total_items += $quantity; // Count total items
											}
											echo $total_items;
										} else {
											echo 0;
										}
										?>
									</span><span class="tooltip-label">Cart</span>
								</a>
							</div>
						</div>
						<!--End Right Action-->
					</div>
				</header>
				<!--Main Navigation Desktop-->
				<div class="menu-outer">
					<nav class="container">
						<div class="row">
							<div class="col-1 col-sm-12 col-md-12 col-lg-12 align-self-center d-menu-col">
								<!--Desktop Menu-->
								<nav class="grid__item" id="AccessibleNav">
									<ul id="siteNav" class="site-nav medium center hidearrow">
										<li class="lvl1 parent"> <a href="#;">Home </a> </li>
										<li class="lvl1 parent dropdown">
											<a href="#;">Blog <i class="an an-angle-down-l"></i></a>
											<ul class="dropdown">
												<li>
													<a href="blog-left-sidebar.php" class="site-nav">Company
														Profile</a>
												</li>
												<li>
													<a href="blog-right-sidebar.php" class="site-nav">Our Team</a>
												</li>
											</ul>
										</li>
										<li class="lvl1 parent dropdown">
											<a href="#;">Pages <i class="an an-angle-down-l"></i></a>
											<ul class="dropdown">
												<?php
												$statement = $pdo->prepare("SELECT * FROM tbl_top_category WHERE show_on_menu=1");
												$statement->execute();
												$result = $statement->fetchAll(PDO::FETCH_ASSOC);
												foreach ($result as $row) {
													?>
													<li>
														<a href="#" class="site-nav"><?php echo $row['tcat_name']; ?> <i
																class="an an-angle-right-l"></i></a>
														<ul class="dropdown second-ul">
															<?php
															$statement1 = $pdo->prepare("SELECT * FROM tbl_product WHERE tcat_id=?");
															$statement1->execute(array($row['tcat_id']));
															$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
															foreach ($result1 as $row1) {
																?>
																<li>
																	<a href="<?php echo $row['p_slug']; ?>" class="site-nav"><?php echo $row['p_name']; ?></a>
																</li>
																<?php
															}
															?>
														</ul>
													</li>
													<?php
												}
												?>
											</ul>
										</li>
										<li class="lvl1 parent"> <a href="#;">Clients </a> </li>
										<li class="lvl1 parent"> <a href="#;">Testimonials </a> </li>
										<li class="lvl1 parent"> <a href="#;">Blogs </a> </li>
										<li class="lvl1 parent"> <a href="#;">Career </a> </li>
										<li class="lvl1 parent"> <a href="#;">Contact </a> </li>
										<li class="lvl1 parent"> <a href="#;">Enquiry </a> </li>
									</ul>
								</nav>
								<!--End Desktop Menu-->
							</div>
						</div>
					</nav>
				</div>
				<!--End Main Navigation Desktop-->
				<!--Search Popup-->
				<div id="search-popup" class="search-drawer">
					<div class="container">
						<span class="closeSearch an an-times-l"></span>
						<form class="form minisearch" id="header-search" action="search-result.php" method="get">
							<label class="label"><span>Search</span></label>
							<div class="control">
								<div class="searchField">
									<div class="input-box">
										<input type="text" name="search_text" value=""
											placeholder="Search by keyword or #" class="input-text" />
										<button type="submit" title="Search" class="action search" disabled="">
											<i class="icon an an-search-l"></i>
										</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
				<!--End Search Popup-->
			</div>
		</div>
		<!--End Header-->
		<!--Mobile Menu-->
		<div class="mobile-nav-wrapper" role="navigation">
			<div class="closemobileMenu">
				<i class="icon an an-times-l pull-right"></i> Close Menu
			</div>
			<ul id="MobileNav" class="mobile-nav">
				<li class="lvl1 parent ">
					<a href="index.php">Home </a>
				</li>
				<li class="lvl1 parent megamenu">
					<a href="#">Blog <i class="an an-plus-l"></i></a>
					<ul>
						<li>
							<a href="blog-left-sidebar.php" class="site-nav">Company Profile</a>
						</li>
						<li>
							<a href="blog-right-sidebar.php" class="site-nav">Our Team</a>
						</li>
					</ul>
				</li>
				<li class="lvl1 parent megamenu">
					<a href="#">Pages <i class="an an-plus-l"></i></a>
					<ul>
						<?php
						$statement = $pdo->prepare("SELECT * FROM tbl_top_category WHERE show_on_menu=1");
						$statement->execute();
						$result = $statement->fetchAll(PDO::FETCH_ASSOC);
						foreach ($result as $row) {
							?>
							<li>
								<a href="#" class="site-nav">About Us <i class="an an-plus-l"></i></a>
								<ul class="dropdown">
									<?php
									$statement1 = $pdo->prepare("SELECT * FROM tbl_product WHERE tcat_id=?");
									$statement1->execute(array($row['tcat_id']));
									$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
									foreach ($result1 as $row1) {
										?>
										<li>
											<a href="<?php echo $row['p_slug']; ?>" class="site-nav"><?php echo $row['p_name']; ?></a>
										</li>
										<?php
									}
									?>
								</ul>
							</li>
							<?php
						}
						?>
					</ul>
				</li>
				<li class="">
					<a href="clients.php">Clients </a>
				</li>
				<li class="">
					<a href="testimonials.php">Testimonials </a>
				</li>
				<li class="">
					<a href="blogs.php">Blogs </a>
				</li>
				<li class="">
					<a href="career.php">Career </a>
				</li>
				<li class="">
					<a href="contact.php">Contact </a>
				</li>
				<li class="">
					<a href="enquiry.php">Enquiry </a>
				</li>
				<li class="acLink"></li>
				<li class="lvl1 bottom-link"><a href="login.php">Login</a></li>
				<li class="lvl1 bottom-link"><a href="register.php">Signup</a></li>
				<li class="help bottom-link">
					<b>NEED HELP?</b><br />Call: <?php echo $contact_phone; ?>
				</li>
			</ul>
		</div>
		<!--End Mobile Menu-->