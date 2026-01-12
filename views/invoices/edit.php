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
    <title>Edit Invoice #<?= $invoice['id'] ?? ''; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f5f5f5; padding: 20px; }
        .item-row { background: #fafafa; padding: 15px; border-radius: 5px; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="container">
    <div class="columns is-centered">
        <div class="column is-8">
            <div class="box">
                <h1 class="title is-3">
                    <i class="fas fa-edit"></i> Edit Invoice #<?= $invoice['id'] ?? ''; ?>
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

                <form method="POST" action="/invoices/<?= $invoice['id']; ?>">
                    <!-- Customer Selection -->
                    <div class="field">
                        <label class="label">Customer</label>
                        <div class="control has-icons-left">
                            <div class="select is-fullwidth">
                                <select name="user_id" required>
                                    <option value="">Select Customer</option>
                                    <?php foreach ($users ?? [] as $user): ?>
                                        <option value="<?= $user['id']; ?>" <?= ($invoice['user_id'] ?? '') == $user['id'] ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($user['name']); ?> (<?= htmlspecialchars($user['email']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <span class="icon is-small is-left">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Paid Status -->
                    <div class="field">
                        <label class="checkbox">
                            <input type="checkbox" name="paid" value="1" <?= !empty($invoice['paid']) ? 'checked' : ''; ?>>
                            Mark as Paid
                        </label>
                    </div>

                    <!-- Invoice Items -->
                    <div class="field">
                        <label class="label">Invoice Items</label>
                        <div id="items-container">
                            <?php if (!empty($items)): ?>
                                <?php foreach ($items as $item): ?>
                                <div class="item-row">
                                    <div class="columns">
                                        <div class="column is-5">
                                            <div class="control">
                                                <div class="select is-fullwidth">
                                                    <select name="products[]" class="product-select" onchange="updatePrice(this)">
                                                        <option value="">Select Product</option>
                                                        <?php foreach ($products ?? [] as $product): ?>
                                                            <option value="<?= $product['id']; ?>" 
                                                                data-price="<?= $product['price']; ?>"
                                                                <?= $item['product_id'] == $product['id'] ? 'selected' : ''; ?>>
                                                                <?= htmlspecialchars($product['name']); ?> - <?= number_format($product['price'], 2); ?> EGP
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="column is-3">
                                            <input class="input" type="number" name="quantities[]" placeholder="Qty" min="1" value="<?= $item['quantity']; ?>">
                                        </div>
                                        <div class="column is-3">
                                            <input class="input price-input" type="number" step="0.01" name="prices[]" value="<?= $item['price']; ?>">
                                        </div>
                                        <div class="column is-1">
                                            <button type="button" class="button is-danger is-small" onclick="removeItem(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <div class="item-row">
                                <div class="columns">
                                    <div class="column is-5">
                                        <div class="control">
                                            <div class="select is-fullwidth">
                                                <select name="products[]" class="product-select" onchange="updatePrice(this)">
                                                    <option value="">Select Product</option>
                                                    <?php foreach ($products ?? [] as $product): ?>
                                                        <option value="<?= $product['id']; ?>" data-price="<?= $product['price']; ?>">
                                                            <?= htmlspecialchars($product['name']); ?> - <?= number_format($product['price'], 2); ?> EGP
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-3">
                                        <input class="input" type="number" name="quantities[]" placeholder="Qty" min="1" value="1">
                                    </div>
                                    <div class="column is-3">
                                        <input class="input price-input" type="number" step="0.01" name="prices[]" placeholder="Price">
                                    </div>
                                    <div class="column is-1">
                                        <button type="button" class="button is-danger is-small" onclick="removeItem(this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="button is-info is-small mt-2" onclick="addItem()">
                            <span class="icon"><i class="fas fa-plus"></i></span>
                            <span>Add Item</span>
                        </button>
                    </div>

                    <!-- Buttons -->
                    <div class="field is-grouped mt-5">
                        <div class="control">
                            <button class="button is-primary" type="submit">
                                <span class="icon"><i class="fas fa-save"></i></span>
                                <span>Update Invoice</span>
                            </button>
                        </div>
                        <div class="control">
                            <a href="/invoices" class="button is-light">
                                <span class="icon"><i class="fas fa-times"></i></span>
                                <span>Cancel</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function addItem() {
    const container = document.getElementById('items-container');
    const firstRow = container.querySelector('.item-row');
    const template = firstRow.cloneNode(true);
    template.querySelectorAll('input').forEach(input => input.value = input.type === 'number' && input.name === 'quantities[]' ? '1' : '');
    template.querySelector('select').selectedIndex = 0;
    container.appendChild(template);
}

function removeItem(btn) {
    const container = document.getElementById('items-container');
    if (container.querySelectorAll('.item-row').length > 1) {
        btn.closest('.item-row').remove();
    }
}

function updatePrice(select) {
    const price = select.options[select.selectedIndex].dataset.price || '';
    const row = select.closest('.item-row');
    row.querySelector('.price-input').value = price;
}
</script>

</body>
</html>