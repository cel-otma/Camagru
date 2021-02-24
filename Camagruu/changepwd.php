<?php
require_once("config/setup.php");
session_start();
    if (empty($_GET['verived_password']))
    {
        header('Location: login.php');
        exit;
    }
        

        if(isset($_POST['submit']) && isset($_GET['verived_password']))
        {
            $verived = $_GET['verived_password'];
            $newpassord = $_POST['new_password'];
            $confirmnewpassord = $_POST['Confirm_password'];
            $err = 0;

            $uppercass = preg_match("/[A-Z]/", $newpassord);
            $lowercass = preg_match("/[a-z]/", $newpassord);
            $number = preg_match("/[0-9]/", $newpassord);
            if($newpassord ==  "" || $confirmnewpassord ==  "" )
            {
                $err = 1;
                $_SESSION['log_error'] = "require new password or confirm new passord .";
                // header('Location: changepwd.php');exit;
            }
            else   
            {  
                if ($newpassord != $confirmnewpassord)
                {
                    $err = 1;
                    $_SESSION['log_error'] = "Your new Password and confirm new password do not match ";
                    // header('Location: changepwd.php');exit;
                } 
                if (strlen($newpassord) < 8 || !$uppercass || !$lowercass || !$number  )
                {
                    $err = 1;
                    $_SESSION['log_error'] = "Password must be at least 8 characters long contain a number and an uppercase letter.";
                    // header('Location: changepwd.php');exit;
                }
                
                if(!$err)
                {
                    $req =$db->prepare("SELECT `password` FROM user WHERE verived_password = ?");
                    $req->execute(array($verived));
                    $count = $req->rowCount();
                    if($count == 1)
                    {
                        try
                        {
                            $update = $db->prepare("UPDATE user SET `password` = :password1 WHERE verived_password = :verived ");
                            $update->bindParam(':password1',  hash('whirlpool', $newpassord));
                            $update->bindParam(':verived', $verived);
                            $update->execute();
                            header('Location: login.php');
                        }
                        catch(PDOExeption $e)
                        {
                            die($e->getMessage());
                        }
                    }
                }
            }
        }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" type="text/css" href="css/changepwd.css">
    <title>Document</title>
</head>
<body>
<H1 class="cm">Camagru</H1>
<div id ="container" class="container">
<form id="sign" method="POST">
        <?php if(isset($_SESSION['log_error']))
			{?>
				<div class="alert alert-danger">
                         <?php echo $_SESSION['log_error']; unset($_SESSION['log_error']);?>
				</div>
			<?php } ?>
        <h2>Change Password</h2>
		<div class="form-group">
		       <label>New Password</label>
            <div class="form-group "> 
                <input type="password" name="new_password" class="form-control" placeholder="New Password"> 
            </div> 
		       <label>Confirm Password</label>
            <div class="form-group "> 
                <input type="password" name="Confirm_password" class="form-control" placeholder="Confirm Password"> 
            </div> 
            <button id ="save"  type="submit" name="submit"  >Save</button>
            
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