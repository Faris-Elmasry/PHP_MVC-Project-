<style>
    body {
        background-color: #f5f5f5;
        padding: 20px;
    }

    .tag.is-paid {
        background-color: #48c774;
        color: white;
    }

    .tag.is-unpaid {
        background-color: #f14668;
        color: white;
    }
</style>

<div class="container">
    <div class="box">
        <div class="level">
            <div class="level-left">
                <h1 class="title is-3">
                    <i class="fas fa-file-invoice"></i> Invoices
                </h1>
            </div>
            <div class="level-right">
                <a href="/invoices/create" class="button is-primary">
                    <span class="icon"><i class="fas fa-plus"></i></span>
                    <span>New Invoice</span>
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

        <!-- Invoices Table -->
        <table class="table is-fullwidth is-striped is-hoverable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $invoiceList = $invoices['data'] ?? $invoices ?? [];
                if (empty($invoiceList)):
                    ?>
                    <tr>
                        <td colspan="6" class="has-text-centered">No invoices found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($invoiceList as $invoice): ?>
                        <tr>
                            <td><?= htmlspecialchars($invoice['id']); ?></td>
                            <td>
                                <strong><?= htmlspecialchars($invoice['user_name'] ?? 'N/A'); ?></strong><br>
                                <small class="has-text-grey"><?= htmlspecialchars($invoice['user_email'] ?? ''); ?></small>
                            </td>
                            <td><?= number_format($invoice['total_amount'], 2); ?> EGP</td>
                            <td>
                                <?php if (!empty($invoice['paid'])): ?>
                                    <span class="tag is-paid">Paid</span>
                                <?php else: ?>
                                    <span class="tag is-unpaid">Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($invoice['created_at'] ?? ''); ?></td>
                            <td>
                                <a href="/invoices/<?= $invoice['id']; ?>" class="button is-small is-info" title="View">
                                    <span class="icon"><i class="fas fa-eye"></i></span>
                                </a>
                                <a href="/invoices/<?= $invoice['id']; ?>/edit" class="button is-small is-warning" title="Edit">
                                    <span class="icon"><i class="fas fa-edit"></i></span>
                                </a>
                                <form method="POST" action="/invoices/<?= $invoice['id']; ?>/delete" style="display: inline;">
                                    <button type="submit" class="button is-small is-danger"
                                        onclick="return confirm('Are you sure?')" title="Delete">
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