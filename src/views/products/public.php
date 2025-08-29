<?php
$title = 'Products List';
ob_start();
?>
<h1>Products List</h1>

<div class="sorting-controls" style="margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
    <form method="GET" action="" style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 5px;">
            <label for="sort" style="font-weight: bold;">Sort by:</label>
            <select name="sort" id="sort" style="padding: 5px 10px; border: 1px solid #ddd; border-radius: 3px;">
                <option value="id" <?= $sortBy === 'id' ? 'selected' : '' ?>>ID</option>
                <option value="name" <?= $sortBy === 'name' ? 'selected' : '' ?>>Name</option>
                <option value="quantity_available" <?= $sortBy === 'quantity_available' ? 'selected' : '' ?>>Stock</option>
                <option value="price" <?= $sortBy === 'price' ? 'selected' : '' ?>>Price</option>
            </select>
        </div>
        
        <div style="display: flex; align-items: center; gap: 5px;">
            <label for="direction" style="font-weight: bold;">Direction:</label>
            <select name="direction" id="direction" style="padding: 5px 10px; border: 1px solid #ddd; border-radius: 3px;">
                <option value="ASC" <?= $sortDirection === 'ASC' ? 'selected' : '' ?>>Ascending</option>
                <option value="DESC" <?= $sortDirection === 'DESC' ? 'selected' : '' ?>>Descending</option>
            </select>
        </div>
        
        <input type="hidden" name="page" value="<?= $currentPage ?>">
        
        <button type="submit" style="padding: 6px 15px; background-color: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer;">
            Apply Sort
        </button>
    </form>
</div>

<div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin: 20px 0;">
    <?php foreach ($products as $key => $product): ?>
        <?php
            $key = $key +1;
            if($currentPage > 1){
                $key = $key + ($limit * ($currentPage-1));
            }
        ?>
        <div class="product-card" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; background-color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s;">
            <div class="card-header" style="margin-bottom: 15px;">
                <div class="product-id" style="font-size: 12px; color: #666; margin-bottom: 5px;">ID: <?php echo $key; ?></div>
                <h3 class="product-name" style="margin: 0; font-size: 18px; color: #333; font-weight: 600;">
                    <?php echo htmlspecialchars($product['name']); ?>
                </h3>
            </div>
            
            <div class="card-body" style="margin-bottom: 20px;">
                <div class="product-details" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <div class="stock-info">
                        <span style="font-size: 14px; color: #666;">Stock:</span>
                        <span style="font-weight: bold; color: <?= $product['quantity_available'] > 10 ? '#28a745' : ($product['quantity_available'] > 0 ? '#ffc107' : '#dc3545') ?>;">
                            <?php echo $product['quantity_available']; ?>
                        </span>
                    </div>
                    <div class="price-info">
                        <span style="font-size: 20px; font-weight: bold; color: #007bff;">
                            $<?php echo $product['price']; ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="card-footer">
                
                <button 
                    type="button"
                    onclick="openModal(<?= $product['id'] ?>, <?= $product['quantity_available'] ?>)"
                    <?= $product['quantity_available'] <= 0 ? 'disabled' : '' ?>
                    style="width: 100%; padding: 12px; border: none; border-radius: 5px; font-size: 16px; font-weight: 600; 
                        cursor: <?= $product['quantity_available'] <= 0 ? 'not-allowed' : 'pointer' ?>; 
                        <?= $product['quantity_available'] <= 0 ? 'background-color: #6c757d; color: white;' : 'background-color: #28a745; color: white;' ?>">
                    <?= $product['quantity_available'] <= 0 ? 'Out of Stock' : 'Purchase' ?>
                </button>

            </div>
        </div>
    <?php endforeach; ?>
</div>

<div id="purchaseModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
    background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div style="background:white; padding:20px; border-radius:8px; width:300px; text-align:center;">
        <h3>Purchase Product</h3>
        <form method="POST" action="/products/purchase">
            <input type="hidden" name="product_id" id="modalProductId">
            
            <label for="quantity" style="display:block; margin:10px 0 5px;">Quantity</label>
            <input type="number" name="quantity" id="modalQuantity" min="1" value="1" required
                style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
            
            <div style="margin-top:20px; display:flex; justify-content:space-between; gap:10px;">
                <button type="button" onclick="closeModal()" 
                    style="flex:1; padding:10px; background:#6c757d; color:white; border:none; border-radius:4px;">
                    Cancel
                </button>
                <button type="submit" 
                    style="flex:1; padding:10px; background:#28a745; color:white; border:none; border-radius:4px;">
                    Confirm
                </button>
            </div>
        </form>
    </div>
</div>

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

<script>
function openModal(productId, maxStock) {
    document.getElementById('modalProductId').value = productId;
    const qtyInput = document.getElementById('modalQuantity');
    qtyInput.max = maxStock;
    qtyInput.value = 1;
    document.getElementById('purchaseModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('purchaseModal').style.display = 'none';
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
