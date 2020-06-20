<?php
include 'functions/init.php';
session_destroy();

if(isset($_COOKIE['email'])){
    setcookie("email", "", time() - 3600);
}

redirect('index.php');
