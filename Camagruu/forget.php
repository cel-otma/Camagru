<?php
require_once("config/setup.php");
    session_start();
    if(isset($_POST['submit'])){
        $email = $_POST['email'];
        $req = $db->prepare("SELECT * FROM user WHERE email LIKE :EMAILID");
        $req->bindParam(':EMAILID', $email);
        $req->execute();
        $verived = $req->fetch();
        // $erro = 0;
        $number_row = 0;
        if($email != "")
        {
            if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $_SESSION["log_error"] =  "Email n'est pas valide !";
                header("location:forget.php");exit();
                // $erro = 1;
            }
            else
            {
                $req = $db->prepare("SELECT * FROM user WHERE email LIKE :EMAILID");
                $req->bindParam(':EMAILID', $email);
                $req->execute();
                $number_row = $req->rowCount();
                if($number_row > 0)
                {
                    $to = $email;
                    $subject = "Change The Password";
                    $message = "Please click here to  <a href='http://".$_SERVER['HTTP_HOST']."/changepwd.php?verived_password=".$verived['verived_password']."'>Change</a> your password";
                    $headers = "Content-Type:text/html";
                    if(mail($to,$subject,$message,$headers))
                    {
                    echo "<script>location.href='change.php';</script>";
                        exit();
                    }
                }
                else{
                        $_SESSION["log_error"] = "The email you entred is incorrect.";
                        header("location:forget.php");exit();}
            }
        }
        else{
                $_SESSION["log_error"] = "Email required";
                 header("location:forget.php");exit();}
 }
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <title>forget password</title>
    <link rel="stylesheet" type="text/css" href="css/forget.css">
</head>
<body>
<H1 class="cm">Camagru</H1>
<div class="form-container">
    <form action="#" method="POST" class="form-wrap">
        <h2>Forgot Password</h2>
        <?php if(isset($_SESSION['log_error']))
                    {?>
                    <div class="alert alert-danger">
                          <?php echo $_SESSION['log_error']; unset($_SESSION['log_error']);?>
                    </div>
                    <?php } ?>
        <div class="form-box">
            <input type="text" name = "email" placeholder="Enter Email" />
        </div>
        <div class="form-submit">
            <input type="submit" name="submit" value="Send" />
        </div>
    </form>
</div>
<div class="footer">
                    <p style="color:#ffff00;">© 2020. All rights reserved | Desin by
                    <a style="color:#ffff00;">cel-otma</a>
                    </p>
                </div>
</body>
</html>