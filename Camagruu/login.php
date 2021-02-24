<?php
require_once("config/setup.php");
    session_start();
    function check_login($db){
        $index = 0;
        $req = $db->prepare("SELECT * FROM `user` WHERE `username` = ? AND `password` = ?");
        $req->execute(array($_POST["log_user"], hash('whirlpool', $_POST["password"])));
        $table = $req->fetch();
        if ($table != '')
        {
            if ($table["verived"] != 1){
                $_SESSION["log_error"] = "Please Verify you email!";
                header("location:login.php");exit();}
            else
            {
                $_SESSION["username"] = $table["username"];
                $_SESSION["id"] = $table["id"];
                $_SESSION["log_error"] = "";
                header('location:index.php');
            }
        }
        else{
            $_SESSION["log_error"] = "The password or the login is incorrect!";
            header("location:login.php");exit();}
    } 
    if (isset($_SESSION["username"]))
        if ($_SESSION["username"] != "")
            header('location:index.php');
    if (isset($_POST["log_submit"],$_POST["log_user"],$_POST["password"]))
    {
        if ($_POST["log_submit"] == "ok" && $_POST["log_user"] != '' && $_POST["password"] != '')
            check_login($db);
        else
        {
            $_SESSION["log_error"] = "Remplir tous les zones!";
            echo "<script>location.href='login.php';</script>";
            exit();
        }
    }
 
?>
<html><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru</title>
  <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> -->
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="css/login.css">
</head>
<body>
    <div class="container-fluid bg">
        <H1 class="cm"> <a href="index.php"> Camagru</a></H1>
        <a id="sam1" href="Register.php">Sing up</a>            
            <a id="sam2" href="login.php">Sing in</a>
        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-12"> </div>
            <div class="col-md-4 col-sm-4 col-xs-12"> 
                <form id="log" method="POST">
                    <h1>Login Form</h1>
                    <?php if(isset($_SESSION['log_error']))
                    {?>
                    <div class="alert alert-danger">
                         <?php echo $_SESSION['log_error']; unset($_SESSION['log_error']);?>
                    </div>
                    <?php } ?>
                    <img class="img img-responsive img-circle" src="img/imagelog2.png">
                    
                    <div class="form-group">
                        <label> login</label>
                        <input type="text" class="form-control" name="log_user" placeholder="Enter your login">
                    </div>
                    <div class="form-group">
                        <label> Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Enter your Password">
                    </div>
                    <button type="submit" name="log_submit" class="btn btn-success btn-block" value="ok">Login</button>
                    <a class="fb" href="forget.php"> Forget Password?</a>
                </form>
                <div class="footer">
                    <p style="color:#ffff00;">© 2020. All rights reserved | Desin by
                    <a style="color:#ffff00;">cel-otma</a>
                    </p>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12"> </div>
        </div>
    </div>
    

</body></html>