<nav class="navbar" role="navigation" aria-label="main navigation">
  <div class="navbar-brand">


    <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
    </a>
  </div>

  <div id="navbarBasicExample" class="navbar-menu">
    <div class="navbar-start">

      <a class="navbar-item" href="./dashboard">
        dashboard
      </a>
      <a class="navbar-item" href="./products">
        products
      </a>
      <a class="navbar-item" href="./invoices">
        invoices
      </a>


    </div>

    <div class="navbar-end">
      <div class="navbar-item">
        <div class="buttons">
          <?php if (!isset($_SESSION['user_id'])): ?>
            <a class="button is-primary" href="/signup">
              <strong>Sign up</strong>
            </a>
            <a class="button is-light" href="/login">
              Log in
            </a>
          <?php else: ?>
            <a class="button is-danger" href="/logout">
              Log out
            </a>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</nav>