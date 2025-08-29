<?php
$title = 'Login';
ob_start();
?>

<div>
    <h2>Login</h2>
    <form action="/login" method="post">
        <input type="email" name="email" placeholder="Email" value="" required>
        <input type="password" name="password" placeholder="Password" required>
        <input class="create" type="submit" value="Login">
    </form>
    <div style="margin-top: 10px;">
        Don't have an account? <a href="/register">Register</a>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__.'/../layout.php';
?>
