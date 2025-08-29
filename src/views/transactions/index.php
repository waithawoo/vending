<?php
$title = 'Transaction List';
ob_start();
?>
<h1>Transaction List</h1>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($transactions as $key => $transaction): ?>
            
            <?php
                $key = $key +1;
                if($currentPage > 1){
                    $key = $key + ($limit * ($currentPage-1));
                }
            ?>
            <tr>
                <td><?php echo $key; ?></td>
                <td><?php echo htmlspecialchars($transaction['user_email']); ?></td>
                <td><?php echo $transaction['product_name']; ?></td>
                <td>$<?php echo $transaction['product_price']; ?></td>
                <td><?php echo $transaction['quantity']; ?></td>
                <td><?php echo $transaction['total_price']; ?></td>
                <td><?php echo $transaction['transaction_date']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>

</table>
<?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&sort=<?= urlencode($sortBy) ?>&direction=<?= urlencode($sortDirection) ?>"
                class="<?= $i === $currentPage ? 'current' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>

    <p style="text-align: center; color: #6c757d;">
        Showing <?= count($transactions) ?> of <?= $totalTransactions ?> transaction
    </p>
<?php endif; ?>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
