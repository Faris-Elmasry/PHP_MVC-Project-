<div class="container">
    <div class="columns is-centered">
        <div class="column is-6">
            <div class="box">
                <h1 class="title is-3">
                    <i class="fas fa-edit"></i> Edit Client
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

                <form method="POST" action="/clients/<?= $user['id']; ?>">
                    <!-- Name -->
                    <div class="field">
                        <label class="label">Name</label>
                        <div class="control has-icons-left">
                            <input
                                class="input <?= (is_array($errors) && isset($errors['name'])) ? 'is-danger' : ''; ?>"
                                type="text" name="name"
                                value="<?= htmlspecialchars(app()->session->getFlash('old')['name'] ?? $user['name']); ?>"
                                placeholder="Client Name">
                            <span class="icon is-small is-left"><i class="fas fa-user"></i></span>
                        </div>
                        <?php if (is_array($errors) && isset($errors['name'])): ?>
                            <p class="help is-danger">
                                <?= htmlspecialchars($errors['name'][0]); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div class="field">
                        <label class="label">Email</label>
                        <div class="control has-icons-left">
                            <input
                                class="input <?= (is_array($errors) && isset($errors['email'])) ? 'is-danger' : ''; ?>"
                                type="email" name="email"
                                value="<?= htmlspecialchars(app()->session->getFlash('old')['email'] ?? $user['email']); ?>"
                                placeholder="Email Address">
                            <span class="icon is-small is-left"><i class="fas fa-envelope"></i></span>
                        </div>
                        <?php if (is_array($errors) && isset($errors['email'])): ?>
                            <p class="help is-danger">
                                <?= htmlspecialchars($errors['email'][0]); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Password (Optional) -->
                    <div class="field">
                        <label class="label">Password <small class="has-text-grey font-weight-normal">(Leave blank to
                                keep current)</small></label>
                        <div class="control has-icons-left">
                            <input
                                class="input <?= (is_array($errors) && isset($errors['password'])) ? 'is-danger' : ''; ?>"
                                type="password" name="password" placeholder="New Password">
                            <span class="icon is-small is-left"><i class="fas fa-lock"></i></span>
                        </div>
                        <?php if (is_array($errors) && isset($errors['password'])): ?>
                            <p class="help is-danger">
                                <?= htmlspecialchars($errors['password'][0]); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Phone -->
                    <div class="field">
                        <label class="label">Phone</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" name="phone"
                                value="<?= htmlspecialchars(app()->session->getFlash('old')['phone'] ?? $user['phone'] ?? ''); ?>"
                                placeholder="Phone Number (Optional)">
                            <span class="icon is-small is-left"><i class="fas fa-phone"></i></span>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="field">
                        <label class="label">Address</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" name="address"
                                value="<?= htmlspecialchars(app()->session->getFlash('old')['address'] ?? $user['address'] ?? ''); ?>"
                                placeholder="Address (Optional)">
                            <span class="icon is-small is-left"><i class="fas fa-map-marker-alt"></i></span>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="field is-grouped mt-5">
                        <div class="control">
                            <button class="button is-primary" type="submit">
                                <span class="icon"><i class="fas fa-save"></i></span>
                                <span>Update Client</span>
                            </button>
                        </div>
                        <div class="control">
                            <a href="/clients" class="button is-light">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>