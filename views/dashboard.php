<style>
    body {
        background-color: #f5f5f5;
        padding: 20px;
    }

    .stat-card {
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: bold;
    }
</style>

<div class="container mt-5">
    <h1 class="title is-2">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </h1>
    <p class="subtitle">Welcome to your admin dashboard</p>

    <!-- Statistics Cards -->
    <div class="columns is-multiline">
        <!-- Users Card -->
        <div class="column is-4">
            <div class="box stat-card has-background-primary-light">
                <div class="level">
                    <div class="level-left">
                        <div>
                            <p class="heading has-text-primary">Total Users</p>
                            <p class="stat-value has-text-primary"><?= $totalUsers ?? 0; ?></p>
                        </div>
                    </div>
                    <div class="level-right">
                        <span class="stat-icon has-text-primary">
                            <i class="fas fa-users"></i>
                        </span>
                    </div>
                </div>
                <a href="/clients" class="button is-primary is-small is-outlined">
                    <span class="icon"><i class="fas fa-plus"></i></span>
                    <span>Manage User</span>
                </a>
            </div>
        </div>

        <!-- Products Card -->
        <div class="column is-4">
            <div class="box stat-card has-background-info-light">
                <div class="level">
                    <div class="level-left">
                        <div>
                            <p class="heading has-text-info">Total Products</p>
                            <p class="stat-value has-text-info"><?= $totalProducts ?? 0; ?></p>
                        </div>
                    </div>
                    <div class="level-right">
                        <span class="stat-icon has-text-info">
                            <i class="fas fa-box"></i>
                        </span>
                    </div>
                </div>
                <a href="/products" class="button is-info is-small is-outlined">
                    <span class="icon"><i class="fas fa-eye"></i></span>
                    <span>Manage Products</span>
                </a>
            </div>
        </div>

        <!-- Invoices Card -->
        <div class="column is-4">
            <div class="box stat-card has-background-warning-light">
                <div class="level">
                    <div class="level-left">
                        <div>
                            <p class="heading has-text-warning-dark">Total Invoices</p>
                            <p class="stat-value has-text-warning-dark"><?= $totalInvoices ?? 0; ?></p>
                        </div>
                    </div>
                    <div class="level-right">
                        <span class="stat-icon has-text-warning-dark">
                            <i class="fas fa-file-invoice"></i>
                        </span>
                    </div>
                </div>
                <a href="/invoices" class="button is-warning is-small is-outlined">
                    <span class="icon"><i class="fas fa-eye"></i></span>
                    <span>Manage Invoices</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Revenue Statistics -->
    <div class="columns">
        <!-- Total Revenue -->
        <div class="column is-4">
            <div class="box stat-card has-background-success-light">
                <div class="level">
                    <div class="level-left">
                        <div>
                            <p class="heading has-text-success">Total Revenue</p>
                            <p class="stat-value has-text-success" style="font-size: 1.8rem;">
                                <?= number_format($invoiceStats['total_revenue'] ?? 0, 2); ?> EGP
                            </p>
                        </div>
                    </div>
                    <div class="level-right">
                        <span class="stat-icon has-text-success">
                            <i class="fas fa-coins"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paid Invoices -->
        <div class="column is-4">
            <div class="box stat-card has-background-link-light">
                <div class="level">
                    <div class="level-left">
                        <div>
                            <p class="heading has-text-link">Paid Invoices</p>
                            <p class="stat-value has-text-link"><?= $invoiceStats['paid_count'] ?? 0; ?></p>
                            <p class="is-size-7 has-text-grey">
                                <?= number_format($invoiceStats['paid_amount'] ?? 0, 2); ?> EGP
                            </p>
                        </div>
                    </div>
                    <div class="level-right">
                        <span class="stat-icon has-text-link">
                            <i class="fas fa-check-circle"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unpaid Invoices -->
        <div class="column is-4">
            <div class="box stat-card has-background-danger-light">
                <div class="level">
                    <div class="level-left">
                        <div>
                            <p class="heading has-text-danger">Unpaid Invoices</p>
                            <p class="stat-value has-text-danger"><?= $invoiceStats['unpaid_count'] ?? 0; ?></p>
                            <p class="is-size-7 has-text-grey">
                                <?= number_format($invoiceStats['unpaid_amount'] ?? 0, 2); ?> EGP
                            </p>
                        </div>
                    </div>
                    <div class="level-right">
                        <span class="stat-icon has-text-danger">
                            <i class="fas fa-clock"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="columns">
        <!-- Recent Invoices -->
        <div class="column is-7">
            <div class="box">
                <h3 class="title is-5">
                    <i class="fas fa-history"></i> Recent Invoices
                </h3>
                <table class="table is-fullwidth is-striped is-hoverable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentInvoices)): ?>
                            <tr>
                                <td colspan="5" class="has-text-centered has-text-grey">No invoices yet</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentInvoices as $invoice): ?>
                                <tr>
                                    <td>
                                        <a href="/invoices/<?= $invoice['id']; ?>">#<?= $invoice['id']; ?></a>
                                    </td>
                                    <td><?= htmlspecialchars($invoice['user_name']); ?></td>
                                    <td><?= number_format($invoice['total_amount'], 2); ?> EGP</td>
                                    <td>
                                        <?php if (!empty($invoice['paid'])): ?>
                                            <span class="tag is-success">Paid</span>
                                        <?php else: ?>
                                            <span class="tag is-danger">Unpaid</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $invoice['created_at'] ?? 'N/A'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <a href="/invoices" class="button is-small is-link is-outlined">View All Invoices</a>
            </div>
        </div>

        <!-- Top Products -->
        <div class="column is-5">
            <div class="box">
                <h3 class="title is-5">
                    <i class="fas fa-trophy"></i> Top Selling Products
                </h3>
                <table class="table is-fullwidth is-striped is-hoverable">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Sold</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($topProducts)): ?>
                            <tr>
                                <td colspan="3" class="has-text-centered has-text-grey">No sales yet</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($topProducts as $product): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['name']); ?></td>
                                    <td><?= number_format($product['price'], 2); ?> EGP</td>
                                    <td>
                                        <span class="tag is-info"><?= $product['total_sold']; ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <a href="/products" class="button is-small is-info is-outlined">View All Products</a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="box">
        <h3 class="title is-5">
            <i class="fas fa-bolt"></i> Quick Actions
        </h3>
        <div class="buttons">
            <a href="/invoices/create" class="button is-primary">
                <span class="icon"><i class="fas fa-plus"></i></span>
                <span>New Invoice</span>
            </a>
            <a href="/products/create" class="button is-info">
                <span class="icon"><i class="fas fa-plus"></i></span>
                <span>New Product</span>
            </a>
            <a href="/clients/create" class="button is-success">
                <span class="icon"><i class="fas fa-user-plus"></i></span>
                <span>New User</span>
            </a>
        </div>
    </div>
</div>