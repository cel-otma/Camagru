<?php
    require_once("config/setup.php");
 
    if (isset($_GET["verived"]))
       {    
           $verived = $_GET["verived"];

           $req = $db->prepare("SELECT verived FROM user WHERE verived = :verived");
           $req->bindParam(':verived', $verived);
           $req->execute();
           if($req->rowCount() > 0)
           {
              try
               {
                   $update = $db->prepare("UPDATE user SET `verived` = 1 WHERE verived = :verived");
                   $update->bindParam(':verived', $verived);
                   $update->execute();
               }
               catch(PDOExeption $e)
               {
                   die($e->getMessage());
               }
       }
       }
       else
       {
          header('Location: index.php');
       }    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style type="text/css">
    body{
        background-color: rgb(90, 248, 248);
    } 
  p {
        color: rgb(236, 142, 248);
        text-align: center;
        font-size:150%;
    }
    img{
        width:100px;
        height:100px;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
    a{
        color: rgb(236, 142, 248);
    }
    .footer {
    position: absolute;
    left: 0;
    width: 100%;
    text-align: center;
}
.cm{
    margin-top: 10px;
    color: lavenderblush;
}
h1{
    color: rgb(171, 219, 247);
    text-align: center;
    font-weight: bolder;
    margin-top: -10px;
}
  </style>
</head>
<body>
<H1 class="cm">Camagru</H1>
    <p>The compt has been verivied</p>
    <img src="img/valid symbol.jpg" >
    <a href="login.php">  Back to  login page</a>
    <div class="footer">
        <p style="color:#ffff00;">© 2020. All rights reserved | Desin by
        <a style="color:#ffff00;">cel-otma</a>
        </p>
    </div>
</body>
</html>