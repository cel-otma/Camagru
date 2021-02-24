<?php
require_once("config/setup.php");
session_start();
if (!isset($_SESSION["username"]))
	header('location:index.php');

if (isset($_POST['submit']) && isset($_SESSION['username']))
{
	$email = $_POST['email'];
	$err = 0;

	if ($email != ""){
		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$_SESSION["log_error"] =  "Email n'est pas valide !";
			header("location:edit_profil.php");exit();
		}
	    
	else
	{
		$req =$db->prepare("SELECT * FROM user WHERE email=?");
		$req->execute(array($email));
		$count = $req->fetch();
		if($count){
			$_SESSION["log_error"] = "Sorry... email already taken ";
			header("location:edit_profil.php");exit();}
		else
		{
        	try
        	{
				$update = $db->prepare("UPDATE `user` SET `email` = :email WHERE `username` = :username ");
				$update->bindParam(':email', $email);
				$update->bindParam(':username', $_SESSION['username']);
				$update->execute();
				$verived = $req->fetch();
				$to = $email;
				$subject = "Email modifed ";
				$message = "".$email." is your new email ";
				$headers = "Content-Type:text/html";
				$err = 1;
				if(mail($to,$subject,$message,$headers)){
					$_SESSION["succes"] = "the email modification is well done";
				}
				
        	}
        	catch(PDOExeption $e)
        	{
        	    die($e->getMessage());
			}
		}
	}
}
	$username = $_POST['username'];
	if ( !empty($username))
	{
		$uppercass = preg_match("/[A-Z]/", $username);
		$lowercass = preg_match("/[a-z]/", $username);
		$number = preg_match("/[0-9]/", $username);
		if(  !$uppercass || !$lowercass || !$number || strlen($username) >= '20')
		{
			$_SESSION["log_error"] = "the username  must be At most 20 characters long contain a number and an uppercase letter.!";
			header("location:edit_profil.php");exit();
		}
		$req =$db->prepare("SELECT * FROM user WHERE username=?");
		$req->execute(array($username));
		$count = $req->fetch();
		if($count){
			$_SESSION["log_error"] = "Sorry... username already taken ";
			header("location:edit_profil.php");exit();}
		else
		{
			try
			{
				$update = $db->prepare("UPDATE `user` SET `username` = :username1 WHERE `username` = :username2 ");
				$update->bindParam(':username1', $username);
				$update->bindParam(':username2', $_SESSION['username']);
				$update->execute();
				//update user post
				$update = $db->prepare("UPDATE `post` SET `username` = :username1 WHERE `username` = :username2 ");
				$update->bindParam(':username1', $username);
				$update->bindParam(':username2', $_SESSION['username']);
				$update->execute();
				//update user commet
				$update = $db->prepare("UPDATE `comment` SET `username` = :username1 WHERE `username` = :username2 ");
				$update->bindParam(':username1', $username);
				$update->bindParam(':username2', $_SESSION['username']);
				$update->execute();
				$_SESSION['username'] = $username;
				$_SESSION["succes"] = "the username modification is well done";
				// header('Location: index.php');
				$err =1;
				
			}
			catch(PDOExeption $e)
			{
				die($e->getMessage());
			}
		}
	}
    $currentpassword = $_POST['old_pass'];
	$newpassord = $_POST['paswd'];
	$confirmnewpassord = $_POST['rpaswd'];

	$uppercass = preg_match("/[A-Z]/", $newpassord);
	$lowercass = preg_match("/[a-z]/", $newpassord);
  	$number = preg_match("/[0-9]/", $newpassord);


	if($newpassord != "" || $confirmnewpassord !=  "" || $currentpassword !=  "")
	  if($newpassord ==  "" || $confirmnewpassord ==  "" || $currentpassword ==  ""){
	  $_SESSION["log_error"] = "require password , confirmnewpassord or currentpassword .";
	  header("location:edit_profil.php");exit();}
	else{
	if (strlen($newpassord) < 8 || !$uppercass || !$lowercass || !$number  )
	{
		$valid_password = 1;
		$_SESSION["log_error"] = "Password must be at least 8 characters long contain a number and an uppercase letter.";
		header("location:edit_profil.php");exit();
	}
	if ($newpassord != $confirmnewpassord)
	{
		$_SESSION["log_error"] = "Your new Password and confirm new password do not match ";
		header("location:edit_profil.php");exit();
	}
	if(!$valid_password)
	{
		$req =$db->prepare("SELECT password from user WHERE password = ?");
		$old_password = hash('whirlpool', $currentpassword);
		$req->execute(array($old_password));
		$count = $req->rowCount();
		if($count != 1){
		$_SESSION["log_error"] = "The password you entred is incorrect.";
		header("location:edit_profil.php");exit();}
		else
    	{
        	try
        	{
				$update = $db->prepare("UPDATE user SET `password` = :password WHERE username = :username ");
				$update->bindParam(':password',  hash('whirlpool', $newpassord));
				$update->bindParam(':username', $_SESSION['username']);
				$update->execute();
				$_SESSION["succes"] = "the password modification is well done";
				$err = 1;
				// header('Location: index.php');
        	}
        	catch(PDOExeption $e)
        	{
        	    die($e->getMessage());
			}
		}
	}
	}
	$Phone = $_POST['phone'];
	if (!empty($Phone))
	{
			try
			{
				$update = $db->prepare("UPDATE `user` SET `phone_number` = :phone WHERE `username` = :username ");
				$update->bindParam(':phone', $Phone);
				$update->bindParam(':username', $_SESSION['username']);
				$update->execute();
				$_SESSION["succes"] = "the phone modification is well done";
				$err =1;
				// header('Location: index.php');
				
			}
			catch(PDOExeption $e)
			{
				die($e->getMessage());
			}
	}
	$notif = (isset($_POST['notification']) && $_POST['notification'] == "on") ? 1 : 0;
		try
			{
				$update = $db->prepare("UPDATE `user` SET `notification` = :notif WHERE `username` = :username ");
				$update->bindParam(':notif', $notif);
				$update->bindParam(':username', $_SESSION['username']);
				$update->execute();
				$_SESSION["succes"] = "the notivication  modification is well done";
				$err = 1;
				// header('Location: index.php');
				
			}
			catch(PDOExeption $e)
			{
				die($e->getMessage());
			}
	if ($err)
	{
		header('Location: edit_profil.php');
		exit();
	}
}
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/profil.css">
</head>
<body>
<H1 class="cm" >Camagru</H1>
<center>
<form action="" method="POST">
    
        <div class="box">
	<h1>Edit profil</h1>
	
			<?php if(isset($_SESSION['log_error']))
			{?>
				<div class="alert alert-danger">
                         <?php echo $_SESSION['log_error']; unset($_SESSION['log_error']);?>
				</div>
			<?php } ?>
			<?php if(isset($_SESSION['succes']))
                    {?>
                            <div class="alert alert-success ">
                                <?php echo $_SESSION['succes']; unset($_SESSION['succes']);?>
                            </div>
                 <?php } ?>
            <input type="text" name="email" placeholder="Enter email" />
            <input type="text" name="username" placeholder="Enter username" />
            <input type="password" name="old_pass" placeholder="Old password" />
            <input type="password" name="paswd" placeholder="Enter pssword"/>
            <input type="password" name="rpaswd" placeholder="Enter pssword again" />
            <input type="text" name="phone" placeholder="phone number"/>
             <div class="form-group">
                        <label> Active notification</label>
                        <input type="checkbox" name="notification" class="form-control">
                    </div>
            <button id="cancel" class="btn btn-danger" > <a href="index.php">  Cancel </a></button>
            <button id ="done"class="btn btn-success" type="submit" name="submit" value="submit" >Done</button>
        </div>
   
	</form> 
</center>
    <div class="footer">
        <p style="color:#ffff00;">© 2020. All rights reserved | Desin by
        <a style="color:#ffff00;">cel-otma</a>
        </p>
    </div>
 </body>
</html>