<?php

function clean($str)
{
    return htmlentities($str);
}

function redirect($location)
{
    header("location: {$location}");
    exit();
}

function set_message($message)
{
    if (!empty($message)) {
        $_SESSION['message'] = $message;
    } else {
        $message = "";
    }
}

function display_message()
{
    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
}

function token_generator()
{
    $token = $_SESSION['token'] = md5(uniqid(mt_rand(), true));
    return $token;
}


function email_exists($email)
{
    $email = filter_var($email,FILTER_SANITIZE_EMAIL);
    $query = "SELECT id FROM users WHERE email = '$email'";
    if (row_count(query($query))) {
        return true;
    } else {
        return false;
    }
}

function user_exists($user)
{
    $user = filter_var($user,   FILTER_SANITIZE_STRING);
    $query = "SELECT id FROM users WHERE username = '$user'";
    if (row_count(query($query))) {
        return true;
    } else {
        return false;
    }
}

function validate_user_registration()
{
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $first_name = clean($_POST['first_name']);
        $last_name = clean($_POST['last_name']);
        $username = clean($_POST['username']);
        $email = clean($_POST['email']);
        $password = clean($_POST['password']);
        $confirm_password = clean($_POST['confirm_password']);
        if (strlen($first_name) < 3) {
            $errors[] = "Your First Name cannot be less then 3 characters";
        }
        if (strlen($last_name) < 3) {
            $errors[] = "Your Last Name cannot be less then 3 characters";
        }
        if (strlen($username) < 3) {
            $errors[] = "Your Username cannot be less then 3 characters";
        }
        if (strlen($username) > 20) {
            $errors[] = "Your Username cannot be bigger then 20 characters";
        }
        if (email_exists($email)) {
            $errors[] = "Sorry that Email is already is taken";
        }
        if (user_exists($username)) {
            $errors[] = "Sorry that Username is already is taken";
        }
        if (strlen($password) < 8) {
            $errors[] = "Your Password cannot be less then 8 characters";
        }
        if ($password != $confirm_password) {
            $errors[] = "The password was not confirmed correctly";
        }
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo '<div class="alert alert alert-danger">' . $error . '
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span></button></div>';
            }
        } else {
            $first_name = filter_var($first_name,   FILTER_SANITIZE_STRING);
            $last_name  = filter_var($last_name,    FILTER_SANITIZE_STRING);
            $username   = filter_var($username,     FILTER_SANITIZE_STRING);
            $email      = filter_var($email,        FILTER_SANITIZE_EMAIL);
            $password   = filter_var($password,     FILTER_SANITIZE_STRING);
            $password   = password_hash($password,PASSWORD_DEFAULT );
            createuser($first_name, $last_name, $username, $email, $password);
        }
    }
}

function createuser($first_name, $last_name, $username, $email, $password)
{
    global $url;
    $first_name = escape($first_name);
    $last_name = escape($last_name);
    $username = escape($username);
    $email = escape($email);
    $password = escape($password);
    $password   = password_hash($password,PASSWORD_DEFAULT );
    $token = md5($username . microtime());
    $sql = "INSERT INTO users(first_name,last_name,username,email,password,token,activition) ";
    $sql .= "VALUES('$first_name','$last_name','$username','$email','$password','$token',0)";
    confirm(query($sql));
    set_message('Please Check Your Email or Spam Folder For activation Link');
    $subject = "Activate Account";
    $msg = "Please Click the link below to Activate Your Account
            $url/activate.php?email=$email&code=$token";
    $headers = "From: x24web@gmail.com";
    send_email($email, $subject, $msg, $headers);
    redirect('index.php');
}

function send_email($email, $subject, $msg, $headers)
{
    return mail($email, $subject, $msg, $headers);
}

function activate_user()
{
    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        $email = clean($_GET['email']);
        $code = clean($_GET['code']);
        $email      = filter_var($email,    FILTER_SANITIZE_EMAIL);
        $code   = filter_var($code, FILTER_SANITIZE_STRING);
        $query = "SELECT id FROM users WHERE email='$email' AND token='$code'";
        $queryEmail = "SELECT id FROM users WHERE email='$email'";
        $result = query($query);
        $resultEmail = query($queryEmail);
        confirm($result);
        confirm($resultEmail);

        if (row_count($result) == 1) {
            $query = "UPDATE users SET activition = 1, token = 0 Where email='$email' and token='$code'";
            confirm(query($query));
            set_message("<div class='alert alert-success'>Your Account has been Activated Please Login</div>");
            redirect('login.php');
        } else {
            if (row_count($resultEmail) == 1) {
                set_message("<div class='alert alert-success'>Your account is already activated</div>");
                redirect('login.php');
            } else {
                set_message("<div class='alert alert-danger'>The activation link is incorrect. Please create an account</div>");
                redirect('register.php');
            }
        }
    }
}

function validate_user_login()
{
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $email = clean($_POST['email']);
        $password = clean($_POST['password']);
        $remember = clean(isset($_POST['remember']));
        $password   = password_hash($password,PASSWORD_DEFAULT );
        if (empty($email)) {
            $errors[] = "Email field cannot be empty";
        }
        if (empty($password)) {
            $errors[] = "Password field cannot be empty";
        }
        if (empty($errors)) {
            if (user_login($email, $password, $remember)) {
                redirect('admin.php');
            } else {
                $errors[] = "your email or password is incorrect. please try again";
            }
        }
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo '<div class="alert alert alert-danger">' . $error . '
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

}

function user_login($email, $password, $remember)
{
    $password   = filter_var($password, FILTER_SANITIZE_STRING);
    $password   = password_hash($password,PASSWORD_DEFAULT );
    $email      = filter_var($email,    FILTER_SANITIZE_EMAIL);
    $remember   = filter_var($remember, FILTER_SANITIZE_STRING);

    $query = "SELECT id FROM users WHERE email='$email' AND password='$password'";
    $result = query($query);
    if (row_count($result) == 1) {
        if ($remember == "1") {
            setcookie('email', $email, time() + (86400 * 30));
        }
        $_SESSION['email'] = $email;
        return true;
    } else {
        return false;
    }
}

function login_check_admin()
{
    if (isset($_SESSION['email']) || isset($_COOKIE['email'])) {
        return true;
    } else {
        redirect('index.php');
    }
}

function login_check_pages()
{
    if (isset($_SESSION['email']) || isset($_COOKIE['email'])) {
        redirect('admin.php');
    }
}

function recover()
{
    global $url;
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if (isset($_POST['cancel-submit'])) {
            redirect('login.php');
        }
        if (isset($_POST['recover-submit'])) {
            $email = $_POST['email'];
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            $query = "SELECT id FROM users WHERE email='$email'";
            $result = query($query);
            if (row_count($result) == 1) {
                $token = token_generator();
                $query = "UPDATE users set token='$token' WHERE email='$email'";
                query($query);
                set_message('Please Check Your Email or Spam Folder For Recover Link');
                $subject = "Activate Account";
                $msg = "Please Click the link below to Activate Your Account
                $url/code.php?email=$email&code=$token";
                $headers = "From: x24web@gmail.com";
                send_email($email, $subject, $msg, $headers);
                redirect('index.php');
            } else {
                set_message("This Email does not Exist");
                redirect('recover.php');
            }
        }
        echo "<p class='alert alert-danger text-center'>";
        display_message();
        echo "</p>";
    }
}

function check_code()
{
    if ($_SERVER['REQUEST_METHOD'] == "GET")
    {
        $email = $_GET['email'];
        $token = $_GET['token'];
        $email  = filter_var($email,   FILTER_SANITIZE_EMAIL);
        $token  = filter_var($token,    FILTER_SANITIZE_STRING);
        $query = "SELECT id FROM users WHERE email='$email' AND token='$token'";
        $result = query($query);
        if (row_count($result) == 1) {
            return true;
        }
    }
    if ($_SERVER['REQUEST_METHOD'] == "POST"){
        if(isset($_POST['reset-password-submit'])){
            $email = $_GET['email'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            $email              = filter_var($email,               FILTER_SANITIZE_EMAIL);
            $password           = filter_var($password,            FILTER_SANITIZE_STRING);
            $confirm_password   = filter_var($confirm_password,    FILTER_SANITIZE_STRING);

            if($password == $confirm_password){
                $password   = password_hash($password,PASSWORD_DEFAULT );
                $query = "UPDATE users set password='$password', token='0' WHERE email='$email'";
                query($query);
                set_message('<p class="alert alert-success">The password has been updated. Can Be Login Now</p>');
                redirect('login.php');
            }
        }
    }
}