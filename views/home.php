<style>
    .hero-body {
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
</style>

<section class="hero is-primary is-fullheight-with-navbar">
    <div class="hero-body">
        <div class="container">
            <p class="title is-1">
                Welcome to MVC Project
            </p>
            <p class="subtitle is-3">
                A simple and elegant Invoice Management System
            </p>

            <div class="buttons is-centered mt-6">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="/login" class="button is-white is-large is-outlined">
                        <span class="icon"><i class="fas fa-sign-in-alt"></i></span>
                        <span>Log In</span>
                    </a>
                    <a href="/signup" class="button is-white is-large">
                        <span class="icon"><i class="fas fa-user-plus"></i></span>
                        <span>Sign Up</span>
                    </a>
                <?php else: ?>
                    <a href="/dashboard" class="button is-white is-large is-outlined">
                        <span class="icon"><i class="fas fa-tachometer-alt"></i></span>
                        <span>Go to Dashboard</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>