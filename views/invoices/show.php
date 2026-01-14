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
    <title>Invoice #<?= $invoice['id'] ?? ''; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f5f5f5; padding: 20px; }
        .invoice-header { border-bottom: 2px solid #3273dc; padding-bottom: 20px; margin-bottom: 20px; }
        .tag.is-paid { background-color: #48c774; color: white; }
        .tag.is-unpaid { background-color: #f14668; color: white; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .box { box-shadow: none; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="box">
        <!-- Action Buttons -->
        <div class="level no-print">
            <div class="level-left">
                <a href="/invoices" class="button is-light">
                    <span class="icon"><i class="fas fa-arrow-left"></i></span>
                    <span>Back to Invoices</span>
                </a>
            </div>
            <div class="level-right">
                <div class="buttons">
                    <button onclick="window.print()" class="button is-info">
                        <span class="icon"><i class="fas fa-print"></i></span>
                        <span>Print</span>
                    </button>
                    <a href="/invoices/<?= $invoice['id']; ?>/edit" class="button is-warning">
                        <span class="icon"><i class="fas fa-edit"></i></span>
                        <span>Edit</span>
                    </a>
                    <form method="POST" action="/invoices/<?= $invoice['id']; ?>/delete" style="display: inline;">
                        <button type="submit" class="button is-danger" onclick="return confirm('Are you sure you want to delete this invoice?')">
                            <span class="icon"><i class="fas fa-trash"></i></span>
                            <span>Delete</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <?php if (app()->session->hasFlash('success')): ?>
            <div class="notification is-success is-light no-print">
                <button class="delete" onclick="this.parentElement.remove()"></button>
                <?= htmlspecialchars(app()->session->getFlash('success')); ?>
            </div>
        <?php endif; ?>

        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="columns">
                <div class="column">
                    <h1 class="title is-2">
                        <i class="fas fa-file-invoice"></i> Invoice #<?= $invoice['id']; ?>
                    </h1>
                    <p class="subtitle is-5 has-text-grey">
                        Created: <?= htmlspecialchars($invoice['created_at'] ?? 'N/A'); ?>
                    </p>
                </div>
                <div class="column has-text-right">
                    <?php if (!empty($invoice['paid'])): ?>
                        <span class="tag is-paid is-large">
                            <i class="fas fa-check-circle mr-2"></i> PAID
                        </span>
                    <?php else: ?>
                        <span class="tag is-unpaid is-large">
                            <i class="fas fa-clock mr-2"></i> UNPAID
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="columns">
            <div class="column is-6">
                <div class="box has-background-light">
                    <h3 class="title is-5">
                        <i class="fas fa-user"></i> Customer Information
                    </h3>
                    <table class="table is-fullwidth is-borderless has-background-light">
                        <tr>
                            <th width="100">Name:</th>
                            <td><?= htmlspecialchars($invoice['user_name'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td><?= htmlspecialchars($invoice['user_email'] ?? 'N/A'); ?></td>
                        </tr>
                        <?php if (!empty($invoice['user_phone'])): ?>
                        <tr>
                            <th>Phone:</th>
                            <td><?= htmlspecialchars($invoice['user_phone']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($invoice['user_address'])): ?>
                        <tr>
                            <th>Address:</th>
                            <td><?= htmlspecialchars($invoice['user_address']); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            <div class="column is-6">
                <div class="box has-background-light">
                    <h3 class="title is-5">
                        <i class="fas fa-info-circle"></i> Invoice Details
                    </h3>
                    <table class="table is-fullwidth is-borderless has-background-light">
                        <tr>
                            <th width="120">Invoice ID:</th>
                            <td>#<?= $invoice['id']; ?></td>
                        </tr>
                        <tr>
                            <th>Date:</th>
                            <td><?= htmlspecialchars($invoice['created_at'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <?php if (!empty($invoice['paid'])): ?>
                                    <span class="has-text-success"><i class="fas fa-check"></i> Paid</span>
                                <?php else: ?>
                                    <span class="has-text-danger"><i class="fas fa-times"></i> Unpaid</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <h3 class="title is-5 mt-5">
            <i class="fas fa-list"></i> Invoice Items
        </h3>
        <table class="table is-fullwidth is-striped is-hoverable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th class="has-text-centered">Quantity</th>
                    <th class="has-text-right">Unit Price</th>
                    <th class="has-text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $subtotal = 0;
                $itemNum = 1;
                foreach ($items ?? [] as $item): 
                    $itemTotal = $item['price'] * $item['quantity'];
                    $subtotal += $itemTotal;
                ?>
                    <tr>
                        <td><?= $itemNum++; ?></td>
                        <td>
                            <strong><?= htmlspecialchars($item['product_name'] ?? 'Unknown Product'); ?></strong>
                        </td>
                        <td class="has-text-centered"><?= $item['quantity']; ?></td>
                        <td class="has-text-right"><?= number_format($item['price'], 2); ?> EGP</td>
                        <td class="has-text-right"><?= number_format($itemTotal, 2); ?> EGP</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="has-text-right"><strong>Subtotal:</strong></td>
                    <td class="has-text-right"><?= number_format($subtotal, 2); ?> EGP</td>
                </tr>
                <tr class="has-background-primary-light">
                    <td colspan="4" class="has-text-right"><strong class="is-size-5">Total Amount:</strong></td>
                    <td class="has-text-right"><strong class="is-size-5"><?= number_format($invoice['total_amount'] ?? $subtotal, 2); ?> EGP</strong></td>
                </tr>
            </tfoot>
        </table>

        <!-- Footer Actions (Print Only) -->
        <div class="has-text-centered mt-6 no-print">
            <hr>
            <p class="has-text-grey">
                <small>Invoice generated on <?= date('Y-m-d H:i:s'); ?></small>
            </p>
        </div>
    </div>
</div>

</body>
</html>
