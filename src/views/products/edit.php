<?php
$title = 'Edit Product';
ob_start();
?>

<div>
    <h2>Edit Product</h2>
    <form action="/products/<?php echo $product['id']; ?>/update" method="post">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" placeholder="Product Name" required>
        <input type="number" name="price" value="<?php echo $product['price']; ?>" placeholder="Price" step="0.01" required>
        <input type="number" name="quantity" value="<?php echo $product['quantityAvailable']; ?>" placeholder="Quantity" value="1" required>
        <input class="create" type="submit" value="Update">
    </form>
</div>
<?php
$content = ob_get_clean();
include __DIR__.'/../layout.php';
?>
