<?php
    session_start();
    session_destroy();
   $_SESSION['message'] = "you are now logged out";
   header("location:login.php");
?>