<?php
$title = 'Products List';
ob_start();
?>
<h1>Products List</h1>
<a href="/products/create" class="button create" style="float: right;">Create New Product</a>

<div class="sorting-controls" style="margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
    <form method="GET" action="" style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 5px;">
            <label for="sort" style="font-weight: bold;">Sort by:</label>
            <select name="sort" id="sort" style="padding: 5px 10px; border: 1px solid #ddd; border-radius: 3px;">
                <option value="name" <?= $sortBy === 'name' ? 'selected' : '' ?>>Name</option>
                <option value="quantity_available" <?= $sortBy === 'quantity_available' ? 'selected' : '' ?>>Stock</option>
                <option value="price" <?= $sortBy === 'price' ? 'selected' : '' ?>>Price</option>
            </select>
        </div>
        
        <div style="display: flex; align-items: center; gap: 5px;">
            <select name="direction" id="direction" style="padding: 5px 10px; border: 1px solid #ddd; border-radius: 3px;">
                <option value="ASC" <?= $sortDirection === 'ASC' ? 'selected' : '' ?>>Ascending</option>
                <option value="DESC" <?= $sortDirection === 'DESC' ? 'selected' : '' ?>>Descending</option>
            </select>
        </div>
        
        <input type="hidden" name="page" value="<?= $currentPage ?>">
        
        <button type="submit" class="button edit">Sort</button>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Stock</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $key => $product): ?>
            
            <?php
                $key = $key +1;
                if($currentPage > 1){
                    $key = $key + ($limit * ($currentPage-1));
                }
            ?>
            <tr>
                <td><?php echo $key; ?></td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo $product['quantity_available']; ?></td>
                <td>$<?php echo $product['price']; ?></td>
                <td>
                    <a href="/products/<?php echo $product['id']; ?>/update" class="button edit">Edit</a>
                    <a href="/products/<?php echo $product['id']; ?>/delete" class="button delete" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
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
        Showing <?= count($products) ?> of <?= $totalProducts ?> products
    </p>
<?php endif; ?>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
