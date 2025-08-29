<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo $title ?? 'Vending'; ?></title>
    <style>
        body {
            font-family: Arial;
            margin: 0;
            background: #f9f9f9;
        }

        .header {
            padding: 10px; 
            margin-bottom: 20px;
            text-align: center;
        }

        .nav {
            background: white;
            padding: 15px;
            color: white;
        }

        .nav a {
            color: black;
            text-decoration: none;
            margin-left: 10px;
            padding: 10px;
            background-color: #bbc3caff;
            border-radius: 5px;
        }

        .container {
            padding: 30px;
            max-width: 900px;
            margin: auto;
            background: #fff;
            margin-top: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }

        tr:hover {
            background: #f5f5f5;
        }

        .button {
            padding: 10px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            margin-right: 5px;
        }

        .create {
            background: #28a745;
        }

        .edit {
            background: #007BFF;
        }

        .delete {
            background: #dc3545;
        }

        .button:hover {
            opacity: 0.8;
        }

        input[type="text"],
        input[type="number"],
        input[type="email"],
        input[type="password"] {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 100%;
            margin-bottom: 15px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        .flash {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }

        .flash.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .flash.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .pagination {
            text-align: center;
            margin: 20px 0;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 2px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #007bff;
        }

        .pagination a:hover,
        .pagination .current {
            background: #007bff;
            color: white;
        }
    </style>
    </style>
</head>

<body>
    <div class="header">
        <h1>Vending Machine System</h1>
        <nav class="nav">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/products">Products</a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="/transactions">Transactions</a>
                <?php endif; ?>
                <a href="/logout">Logout</a>
            <?php else: ?>
                <a href="/login">Login</a>
                <a href="/register">Register</a>
            <?php endif; ?>
        </nav>
    </div>
    <div class="container">
        <?php if (isset($_SESSION['flash'])): ?>
            <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                <div class="flash <?= $type ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
                <?php unset($_SESSION['flash'][$type]); ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (isset($errors)): ?>
            <?php foreach ($errors as $message): ?>
                <div class="flash error">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php
        echo $content ?? '';
        ?>
    </div>
</body>

</html>
