<?php
require_once("config/setup.php");
session_start();
if (isset($_POST['submit']))
{

$email = htmlspecialchars(trim($_POST['email']));
$username = htmlspecialchars(trim($_POST['username']));
$password = htmlspecialchars(trim($_POST['paswd']));
$repit_password = htmlspecialchars(trim($_POST['rpaswd']));
$number_phone = htmlspecialchars(trim($_POST['phone']));
//validation
if ( $email != "" && $username != "" && $password != "" && $number_phone != "" && $repit_password != "")
{
        if($password == $repit_password) {
        if (strlen($password) <= '8') {
            $_SESSION["log"] = "Your Password Must Contain At Least 8 Characters!";
            header("location:Register.php");exit();
            }
        else if(!preg_match("#[0-9]+#",$password)) {
            $_SESSION["log"] = "Your Password Must Contain At Least 1 Number!";
            header("location:Register.php");exit();
            }
            else if(!preg_match("#[A-Z]+#",$password)) {
                $_SESSION["log"] = "Your Password Must Contain At Least 1 Capital Letter!";
                header("location:Register.php");exit();
            }
            else if(!preg_match("#[a-z]+#",$password)) {
                $_SESSION["log"] = "Your Password Must Contain At Least 1 Lowercase Letter!";
                header("location:Register.php");exit();
            } 
            else
            {
                if(!filter_var($email, FILTER_VALIDATE_EMAIL))
                {
                    $_SESSION["log"] =  "Email n'est pas valide !";
                    header("location:Register.php");exit();
                }
                $uppercass = preg_match("/[A-Z]/", $username);
                $lowercass = preg_match("/[a-z]/", $username);
                $number = preg_match("/[0-9]/", $username);
                if( !$uppercass || !$lowercass || !$number || strlen($username) >= '20')
                {
                    $_SESSION["log"] = "the username  must be At most 20 characters long contain a number and an uppercase letter.!";
                    header("location:Register.php");exit();
                }
            else{
           
            $sql = "SELECT * FROM user WHERE username=? OR email=?";
            $stdm = $db->prepare($sql);
            $stdm->execute(array($username,$email));
            $reslt = $stdm->fetch();
                if($reslt){
                    $_SESSION["log"] =  "username or email already exists";
                    header("location:Register.php");exit();
                }
                else{
                    $password = hash('whirlpool', $password);
                    $notification = true;
                    $verived = hash('whirlpool', time());
                    $verived_password = hash('whirlpool', time());
                    // on se connecte à MySQL et on sélectionne la base
                   
                    //On créé la requête
                    $update = $db->prepare("INSERT INTO user SET `email` = ?, `username` = ? , `password` = ? , `phone_number` = ? ,  `notification` = '1' , `verived` = ? , `verived_password` = ?");  
                    
                    $update->execute([$email, $username, $password, $number_phone, $verived, $verived_password]);
                    
                    $req = $db->prepare("SELECT * FROM user WHERE email LIKE :EMAILID");
                    $req->bindParam(':EMAILID', $email);
                    $req->execute();
                    $verived = $req->fetch();
                    $to = $email;
                    $subject = "Email Verification";
                    $message = "Please click here to  <a href='http://".$_SERVER['HTTP_HOST']."/valid.php?verived=".$verived['verived']."'>verified</a> your account";
                    $headers = "Content-Type:text/html";
                    if(mail($to,$subject,$message,$headers))
                    {
                        $_SESSION['succes'] = "account has created successfully verified your email";
                        header("location:Register.php");exit();
                    }
                }
            }
        }
                    // login user
                    // $_SESSION['username'] = $username;
            }
            else{
            $_SESSION["log"] =  "les modes passes sont deffirents";      
            header("location:Register.php");exit();}
            
}
else{
    $_SESSION["log"] =  "Veuillez saisir tous les champs !";
    header("location:Register.php");exit();}


}
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru</title>
  <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> -->
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="css/Register.css">
</head>
<body class="bg">
    <H1 class="cm"> <a href="index.php"> Camagru</a></H1>
            <a id="sam1" href="Register.php">Sing up</a>            
            <a id="sam2" href="login.php">Sing in</a>

                <form id="sign" method="POST" enctype="multipart/form-data">
                    <h1>Sign Up</h1>
                    <?php if(isset($_SESSION['log']))
                    {?>
                    <div class="alert alert-danger">
                         <?php echo $_SESSION['log']; unset($_SESSION['log']);?> 
                    </div>
                    <?php } ?>
                <?php if(isset($_SESSION['succes']))
                    {?>
                            <div class="alert alert-success ">
                                <?php echo $_SESSION['succes']; unset($_SESSION['succes']);?>
                            </div>
                 <?php } ?>
                    
                    <div class="form-group">
                        <label> E-mail</label>
                        <input type="text" name="email"  class="form-control" placeholder="Enter your E-mail">
                    </div>
                    <div class="form-group">
                        <label> User name</label>
                        <input type="text" name="username"  class="form-control" placeholder="Enter your username">
                    </div>
                    <div class="form-group">
                        <label> Password</label>
                        <input type="password" name="paswd" class="form-control" placeholder="Enter your Password">
                    </div>
                    <div class="form-group">
                        <label> Repite Password</label>
                        <input type="password" name="rpaswd" class="form-control" placeholder="Repite your Password">
                    </div>
                    <div class="form-group">
                        <label> Number Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="Enter your Number">
                    </div>
                    <button id="up" name="submit"  type="submit" class="btn btn-success ">Sign Up</button>
                    <button id="cancel" type="button" class="btn btn-danger" onclick="window.location.href='/login.php'" >Cancel</button>
                </form>
                <div class="footer">
                    <p style="color:#ffff00;">© 2020. All rights reserved | Desin by
                    <a style="color:#ffff00;">cel-otma</a>
                    </p>
                </div>
</body>
</html>
<?php
    $msg = "";
?>
