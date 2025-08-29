<?php
$title = 'Register';
ob_start();
?>

<div>
    <h2>Register</h2>
    <form action="/register" method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <input class="create" type="submit" value="Register">
    </form>
    <div style="margin-top: 10px;">
        Already have an account? <a href="/login">Login</a>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__.'/../layout.php';
?>
