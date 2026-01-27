<div class="container">
    <div class="box">
        <div class="level">
            <div class="level-left">
                <h1 class="title is-3">
                    <i class="fas fa-users"></i> Clients
                </h1>
            </div>
            <div class="level-right">
                <a href="/clients/create" class="button is-primary">
                    <span class="icon"><i class="fas fa-plus"></i></span>
                    <span>Add Client</span>
                </a>
            </div>
        </div>

        <!-- Notification Messages -->
        <?php if (app()->session->hasFlash('success')): ?>
            <div class="notification is-success is-light">
                <button class="delete" onclick="this.parentElement.remove()"></button>
                <?= htmlspecialchars(app()->session->getFlash('success')); ?>
            </div>
        <?php endif; ?>

        <!-- Search Form -->
        <form method="GET" action="/clients" class="mb-4">
            <div class="field has-addons">
                <div class="control is-expanded">
                    <input class="input" type="text" name="search" placeholder="Search by name or email"
                        value="<?= htmlspecialchars($search ?? ''); ?>">
                </div>
                <div class="control">
                    <button class="button is-info">
                        <span class="icon"><i class="fas fa-search"></i></span>
                        <span>Search</span>
                    </button>
                </div>
                <?php if (!empty($search)): ?>
                    <div class="control">
                        <a href="/clients" class="button is-light">
                            <span class="icon"><i class="fas fa-times"></i></span>
                            <span>Clear</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </form>

        <!-- Clients Table -->
        <table class="table is-fullwidth is-striped is-hoverable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="has-text-centered">No clients found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <?= $user['id']; ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($user['name']); ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($user['email']); ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($user['phone'] ?? 'N/A'); ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($user['address'] ?? 'N/A'); ?>
                            </td>
                            <td>
                                <a href="/clients/<?= $user['id']; ?>/edit" class="button is-small is-warning">
                                    <span class="icon"><i class="fas fa-edit"></i></span>
                                </a>
                                <form method="POST" action="/clients/<?= $user['id']; ?>/delete" style="display:inline;">
                                    <button type="submit" class="button is-small is-danger"
                                        onclick="return confirm('Are you sure you want to delete this client?')">
                                        <span class="icon"><i class="fas fa-trash"></i></span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav class="pagination is-centered" role="navigation" aria-label="pagination">
            <?php
            $prevPage = $currentPage > 1 ? $currentPage - 1 : 1;
            $nextPage = $currentPage < $totalPages ? $currentPage + 1 : $totalPages;
            $queryParams = $_GET; // Keep search params
            ?>

            <a class="pagination-previous" <?= $currentPage <= 1 ? 'disabled' : ''; ?> href="/clients?
                <?= http_build_query(array_merge($queryParams, ['page' => $prevPage])); ?>">Previous
            </a>

            <a class="pagination-next" <?= $currentPage >= $totalPages ? 'disabled' : ''; ?> href="/clients?
                <?= http_build_query(array_merge($queryParams, ['page' => $nextPage])); ?>">Nextpage
            </a>

            <ul class="pagination-list">
                <li>
                    <span class="pagination-link is-current">
                        <?= $currentPage; ?> /
                        <?= $totalPages == 0 ? 1 : $totalPages; ?>
                    </span>
                </li>
            </ul>
        </nav>
    </div>
</div>