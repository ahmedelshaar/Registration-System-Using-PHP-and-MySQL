<?php
include('inc/header.php');
login_check_admin();
include('inc/nav.php');
?>

<div class="jumbotron">
		<h1 class="text-center"><?php echo $_SESSION['email'];?> </h1>
	</div>
<?php
include('inc/footer.php');
?>