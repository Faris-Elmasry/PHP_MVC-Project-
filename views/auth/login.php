<div class="container">

    <!-- Success Message -->
    <?php if (app()->session->hasFlash('success')): ?>
        <div class="notification is-success">
            <?= app()->session->getFlash('success'); ?>
        </div>
    <?php endif; ?>

    <!-- General Error Message -->
    <?php if (app()->session->hasFlash('error')): ?>
        <div class="notification is-danger">
            <?= app()->session->getFlash('error'); ?>
        </div>
    <?php endif; ?>



    <form method="POST" action="/login">

        <!-- Email Field -->
        <div class="field">
            <label class="label">Email</label>
            <div class="control">
                <input class="input <?php
                $errors = app()->session->getFlash('errors');
                echo (is_array($errors) && isset($errors['email'])) ? 'is-danger' : '';
                ?>" type="email" name="email"
                    value="<?= htmlspecialchars(app()->session->getFlash('old')['email'] ?? ''); ?>"
                    placeholder="Enter your email">
            </div>
            <?php
            $errors = app()->session->getFlash('errors');
            if (is_array($errors) && isset($errors['email'])):
                ?>
                <p class="help is-danger">
                    <?= htmlspecialchars($errors['email'][0]); ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Password Field -->
        <div class="field">
            <label class="label">Password</label>
            <div class="control">
                <input class="input <?php
                $errors = app()->session->getFlash('errors');
                echo (is_array($errors) && isset($errors['password'])) ? 'is-danger' : '';
                ?>" type="password" name="password" placeholder="Enter your password">
            </div>
            <?php
            $errors = app()->session->getFlash('errors');
            if (is_array($errors) && isset($errors['password'])):
                ?>
                <p class="help is-danger">
                    <?= htmlspecialchars($errors['password'][0]); ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Remember Me (Optional) -->
        <div class="field">
            <div class="control">
                <label class="checkbox">
                    <input type="checkbox" name="remember">
                    Remember me
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="field">
            <div class="control">
                <button class="button is-link is-fullwidth" type="submit">
                    Login
                </button>
            </div>
        </div>

    </form>

    <!-- Debug Section (Remove in production) -->
    <?php if (isset($_GET['debug'])): ?>
        <div class="box mt-4">
            <h3 class="title is-5">Debug Info:</h3>
            <pre><?php
            echo "Session Data:\n";
            print_r($_SESSION);
            echo "\n\nFlash Errors:\n";
            print_r(app()->session->getFlash('errors'));
            echo "\n\nFlash Old:\n";
            print_r(app()->session->getFlash('old'));
            ?></pre>
        </div>
    <?php endif; ?>

</div>