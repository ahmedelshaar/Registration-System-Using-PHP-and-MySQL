
<nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">User Registration System</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse pull-right">
          <ul class="nav navbar-nav">
            <li class="active"><a href="index.php">Home</a></li>
              <?php if(!isset($_SESSION['email'])): ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="Register.php">Register</a></li>
            <li><a href="recover.php">Recover</a></li>
              <?php else: ?>
            <li><a href="admin.php">admin</a></li>
            <li><a href="logout.php">logout</a></li>
              <?php endif;?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
</nav>
<p class="alert alert-info text-center">This project was built by Ahmed Elshaar: <a href="https://www.facebook.com/A7med.Elshaar6">Account FaceBook</a> And <a href="https://github.com/ahmedelshaar">Account Github</a></p>