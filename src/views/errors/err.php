<!DOCTYPE html>
<html>

<head>
    <title>Error</title>
    <style>
        body {
            font-family: Arial;
            background: #f8f8f8;
            text-align: center;
            padding: 50px;
        }

        .error {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
        }
    </style>
</head>

<body>
    <div class="error">
        <h1>Oops!</h1>
        <?php
        $redirectText = '';
        if ($status === 401) {
            $redirectText = 'Please <a href="/login">login</a> to access this page.';
        }
        ?>
        <p><?= htmlspecialchars($message ?? 'Something went wrong'); ?></p>
        <p><?= $redirectText ?></p>

    </div>
</body>

</html>
