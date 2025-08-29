<?php
$title = 'Create Product';
ob_start();
?>

<div>
    <h2>Create Product</h2>
    <form action="/products/create" method="post">
        <input type="text" name="name" placeholder="Product Name" required>
        <input type="number" name="price" placeholder="Price" step="0.01" required>
        <input type="number" name="quantity" placeholder="Quantity" value="1" required>
        <input class="create" type="submit" value="Add">
    </form>
</div>
<?php
$content = ob_get_clean();
include __DIR__.'/../layout.php';
?>
