<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="box">
        <div class="level">
            <div class="level-left">
                <h1 class="title is-3">
                    <i class="fas fa-boxes"></i> Products
                </h1>
            </div>
            <div class="level-right">
                <a href="/products/create" class="button is-primary">
                    <span class="icon"><i class="fas fa-plus"></i></span>
                    <span>Add Product</span>
                </a>
            </div>
        </div>

        <!-- Success Message -->
        <?php if (app()->session->hasFlash('success')): ?>
            <div class="notification is-success is-light">
                <button class="delete" onclick="this.parentElement.remove()"></button>
                <?= htmlspecialchars(app()->session->getFlash('success')); ?>
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if (app()->session->hasFlash('error')): ?>
            <div class="notification is-danger is-light">
                <button class="delete" onclick="this.parentElement.remove()"></button>
                <?= htmlspecialchars(app()->session->getFlash('error')); ?>
            </div>
        <?php endif; ?>

        <!-- Products Table -->
        <table class="table is-fullwidth is-striped is-hoverable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Price (EGP)</th>
                    <th>VAT (%)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $productList = $products['data'] ?? $products ?? [];
                if (empty($productList)): 
                ?>
                    <tr>
                        <td colspan="5" class="has-text-centered">No products found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($productList as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['id']); ?></td>
                            <td><?= htmlspecialchars($product['name']); ?></td>
                            <td><?= number_format($product['price'], 2); ?></td>
                            <td><?= htmlspecialchars($product['vat']); ?>%</td>
                            <td>
                                <a href="/products/<?= $product['id']; ?>/edit" class="button is-small is-info">
                                    <span class="icon"><i class="fas fa-edit"></i></span>
                                </a>
                                <form method="POST" action="/products/<?= $product['id']; ?>/delete" style="display: inline;">
                                    <button type="submit" class="button is-small is-danger" onclick="return confirm('Are you sure?')">
                                        <span class="icon"><i class="fas fa-trash"></i></span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

