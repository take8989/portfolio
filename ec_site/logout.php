<?php
if (isset($_COOKIE['user_name']) === TRUE) {
   setcookie('user_name', '', time() - 3600);
}

header("location: login.php");