<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['customer'])) {
    header('location: ' . BASE_URL . 'logout.php');
    exit;
} 


$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($order_id <= 0) {
    die("Invalid order ID.");
}
$query = "SELECT * FROM tbl_payment WHERE id = ? AND user_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$order_id, $_SESSION['user_id']]);  // Fetch only orders that belong to the logged-in user

// Check if the order exists
if ($stmt->rowCount() === 0) {
    die("You do not have permission to view this invoice.");
}

// Fetch the order data
$order = $stmt->fetch(PDO::FETCH_ASSOC);
?>
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
                    <td>$<?= number_format($payment['paid_amount'], 2) ?> for <?= $payment['quantity'] ?> item(s)</td>
                    <td><a class="link-underline view" href="view_invoice.php?id=<?= htmlspecialchars($payment['id']) ?>" target="_blank">View</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
