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
    <title>Add Product</title>
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
    <div class="columns is-centered">
        <div class="column is-6">
            <div class="box">
                <h1 class="title is-3">
                    <i class="fas fa-plus"></i> Add New Product
                </h1>

                <!-- Error Messages -->
                <?php 
                $errors = app()->session->getFlash('errors');
                if (is_array($errors) && isset($errors['general'])): 
                ?>
                    <div class="notification is-danger is-light">
                        <button class="delete" onclick="this.parentElement.remove()"></button>
                        <?= htmlspecialchars($errors['general'][0]); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/products">
                    <!-- Product Name -->
                    <div class="field">
                        <label class="label">Product Name</label>
                        <div class="control has-icons-left">
                            <input 
                                class="input <?php 
                                    $errors = app()->session->getFlash('errors');
                                    echo (is_array($errors) && isset($errors['name'])) ? 'is-danger' : ''; 
                                ?>" 
                                type="text" 
                                name="name" 
                                value="<?= htmlspecialchars(app()->session->getFlash('old')['name'] ?? ''); ?>"
                                placeholder="Enter product name">
                            <span class="icon is-small is-left">
                                <i class="fas fa-tag"></i>
                            </span>
                        </div>
                        <?php 
                        $errors = app()->session->getFlash('errors');
                        if (is_array($errors) && isset($errors['name'])): 
                        ?>
                            <p class="help is-danger">
                                <?= htmlspecialchars($errors['name'][0]); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Price -->
                    <div class="field">
                        <label class="label">Price (EGP)</label>
                        <div class="control has-icons-left">
                            <input 
                                class="input <?php 
                                    $errors = app()->session->getFlash('errors');
                                    echo (is_array($errors) && isset($errors['price'])) ? 'is-danger' : ''; 
                                ?>" 
                                type="number" 
                                step="0.01"
                                name="price" 
                                value="<?= htmlspecialchars(app()->session->getFlash('old')['price'] ?? ''); ?>"
                                placeholder="0.00">
                            <span class="icon is-small is-left">
                                <i class="fas fa-dollar-sign"></i>
                            </span>
                        </div>
                        <?php 
                        $errors = app()->session->getFlash('errors');
                        if (is_array($errors) && isset($errors['price'])): 
                        ?>
                            <p class="help is-danger">
                                <?= htmlspecialchars($errors['price'][0]); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- VAT -->
                    <div class="field">
                        <label class="label">VAT (%)</label>
                        <div class="control has-icons-left">
                            <input 
                                class="input <?php 
                                    $errors = app()->session->getFlash('errors');
                                    echo (is_array($errors) && isset($errors['vat'])) ? 'is-danger' : ''; 
                                ?>" 
                                type="number" 
                                step="0.01"
                                name="vat" 
                                value="<?= htmlspecialchars(app()->session->getFlash('old')['vat'] ?? '14'); ?>"
                                placeholder="14">
                            <span class="icon is-small is-left">
                                <i class="fas fa-percent"></i>
                            </span>
                        </div>
                        <?php 
                        $errors = app()->session->getFlash('errors');
                        if (is_array($errors) && isset($errors['vat'])): 
                        ?>
                            <p class="help is-danger">
                                <?= htmlspecialchars($errors['vat'][0]); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Buttons -->
                    <div class="field is-grouped">
                        <div class="control">
                            <button class="button is-primary" type="submit">
                                <span class="icon">
                                    <i class="fas fa-save"></i>
                                </span>
                                <span>Create Product</span>
                            </button>
                        </div>
                        <div class="control">
                            <a href="/products" class="button is-light">
                                <span class="icon">
                                    <i class="fas fa-times"></i>
                                </span>
                                <span>Cancel</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
