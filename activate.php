<?php
include('inc/header.php');
include('inc/nav.php');
login_check_pages();
?>

<div class="jumbotron">
    <h1 class="text-center"><?php activate_user(); ?></h1>
</div>
<?php
include('inc/footer.php');
?>