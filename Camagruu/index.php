<?php
    include "config/setup.php";
    session_start();
            //verived notification
    if (isset($_POST["id_post"]) || isset($_GET["id_post"]))
    {
        $id_post = $_POST["id_post"] ? $_POST["id_post"] :$_GET["id_post"];
        $req = $db->prepare("SELECT username FROM post WHERE `id_post` = ?");
        $req->execute([$id_post]);
        $username = $req->fetch();
        $req = $db->prepare("SELECT `notification` FROM user WHERE username = :username ");
        $req->bindParam(':username', $username["username"]);
        $req->execute();
        $notif_state = $req->fetch();
        $notif = $notif_state['notification'];
            // var_dump($notif_state);
            
    }
            //show post
        if (!isset($_GET['page'])) {
            $page = 1;
          } else {
            $page = $_GET['page'];
          }  
        $limit = 5;    
        //count the number post 

        $req =$db->prepare("SELECT * FROM post");
        $req->execute();
        $number_post = $req->rowCount();
        // var_dump($number_post) ; die;
        $number_page = ceil($number_post / $limit);
        if(is_numeric($page))
            $start = (($page - 1) * $limit);
        $req =$db->prepare("SELECT * FROM post ORDER BY date_creation DESC LIMIT $start, $limit");
        $req->execute(array());
        $count = $req->fetchAll();

            //add comment
    if(isset($_POST['commemt'],$_POST["tex_comment"],$_POST["id_post"]) && trim($_POST["tex_comment"]) != "")
    {
        if(isset($_SESSION["username"]))
        {
            $comment = htmlspecialchars($_POST["tex_comment"]); 
            $username = $_SESSION['username'];
            $date = date('Y-m-d H:i:s');
            $id_post = $_POST["id_post"];
            $update = $db->prepare("INSERT INTO comment( `username`, `comment`, `time_comemmt`, `id_post`)  VALUES(?, ?, ?, ?)");
            $update->execute([$username,$comment,$date, $id_post]);

            $req = $db->prepare("SELECT username FROM post WHERE `id_post` = ?");
            $req->execute([$id_post]);
            $username = $req->fetch();

            $req = $db->prepare("SELECT email FROM user WHERE username = :username ");
            $req->bindParam(':username', $username["username"]);
            $req->execute();
            $email_send = $req->fetch();
            $to = $email_send['email'];
            $subject = "comment notification ";
            $message = "".$_SESSION['username']." comment your poste";
            $headers = "Content-Type:text/html";
            if ($notif_state['notification'] == "1")
                if(mail($to,$subject,$message,$headers))
                {
                    header('location:index.php');
                }
        } else{header('location:login.php');}
    }
    

    //delete post
    if(isset($_POST['delete'],$_POST["id_post"]) && trim($_POST["id_post"]) != "" )
    {
        if(isset($_SESSION["username"]))
        {
            $id_post = $_POST["id_post"];
            $update = $db->prepare("SELECT * FROM `post` WHERE `id_post` = ? AND `username` = ? ");
            $update->execute([$id_post,$_SESSION["username"]]);
            $delete = $update->fetchAll();
            if($delete)
            {
                $update = $db->prepare("DELETE FROM `post` WHERE `id_post` = ? AND `username` = ? ");
                $update->execute([$id_post,$_SESSION["username"]]);
                $update = $db->prepare("DELETE FROM `comment` WHERE `id_post` = ? AND `username` = ?");
                $update->execute([$id_post, $_SESSION["username"]]);
                $update = $db->prepare("DELETE FROM `like` WHERE `id_post` = ? AND `username` = ?");
                $update->execute([$id_post, $_SESSION["username"]]);
                header('location:index.php');
            }
         } else{header('location:login.php');}
            
    }
     //delete comment
     if(isset($_GET['delete'],$_GET["id_comment"]))
     {
        if(isset($_SESSION["username"]))
        {
            $id_comment = $_GET["id_comment"];
            $update = $db->prepare("DELETE FROM `comment` WHERE  `id_comment` = ? AND `username`=?");
            $update->execute([$id_comment,$_SESSION["username"]]);
            header('location:index.php');
        }else{header('location:login.php');}
    }
    
     //add jaime
     if(isset($_GET['jaime'],$_GET["id_post"]))
    {
        if(isset($_SESSION["username"]))
        {
            
            $username = $_SESSION['username'];
            $id_post = $_GET["id_post"];
            $update = $db->prepare("SELECT * FROM `like` WHERE username = ? AND id_post=?");
            $update->execute([$username,$id_post]);
            $user_like = $update->fetchAll(); 
            if(!$user_like)
            {
                //insert likes if not exist
                $verifed = "true";
                $update = $db->prepare("INSERT INTO `like`( `username`, `id_post`, `verifed`)  VALUES(?, ?, ?)");
                $update->execute([$username,$id_post,$verifed]);

                //find email post
                $req = $db->prepare("SELECT username FROM post WHERE `id_post` = ?");
                $req->execute([$id_post]);
                $username = $req->fetch();
                $req = $db->prepare("SELECT email FROM user WHERE username = :username ");
                $req->bindParam(':username', $username["username"]);
                $req->execute();
                $email_send = $req->fetch();
                $to = $email_send['email'];
                $subject = "jaime notification ";
                $message = "".$_SESSION['username']." reacted a your poste";
                $headers = "Content-Type:text/html";
                if ($notif_state['notification'] == "1")
                    mail($to,$subject,$message,$headers);
                    
            }else 
            {
                if($user_like[0]['verifed'] == 'false')
                {
                    $update = $db->prepare("UPDATE `like` SET `verifed` = :verifed WHERE `username` = :username AND id_post = :id_post");
                    $verifed = "true";
                    $update->bindParam(':verifed', $verifed);
                    $update->bindParam(':username', $_SESSION['username']);
                    $update->bindParam(':id_post',  $id_post);
                    $update->execute();
                }else{
                    $update = $db->prepare("UPDATE `like` SET `verifed` = :verifed WHERE `username` = :username AND id_post = :id_post");
                    $verifed = "false";
                    $update->bindParam(':verifed', $verifed);
                    $update->bindParam(':username', $_SESSION['username']);
                    $update->bindParam(':id_post', $id_post);
                    $update->execute();
                }
                
            }
            header('location:index.php');
        }else{header('location:login.php');}
    }
    


        // echo "<pre>";
        // var_dump($username);
        // var_dump($id_post);

        // echo "</pre>";
        // die();
    ?>
<!DOCTYPE html>
<html >

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>index</title>
  <link rel="stylesheet" type="text/css" href="css/index.css">
  <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->

  <body>
        
        <?php
                if(isset($_SESSION["username"]))    
                {?>
                <div class="navbar">
                        <nav>
                            <ul>
                                <li><a href="index.php">Home</a></li>
                                <li><a href="edit_profil.php">Edit profil</a></li>
                                <li><a href="camera.php">Create Post</a></li>
                                <li id="log"><a href="logout.php">Log out</a></li>
                            </ul>
                        </nav>
                    </div>
        <?php
        }?>
          <H1 class="cm">Camagru</H1>
          <?php
            if(!isset($_SESSION["username"]))    
            {?>
                    <a id="sam1" href="Register.php">Sing up</a>            
                    <a id="sam2" href="login.php">Sing in</a>
            <?php
            }?>
          <div id ="container">
                <?php
                  $i = 0;
                  while($i < count($count))
                  {?>
                  <form action="" class="post" method="post">
                      <input type="hidden" name="id_post" value="<?php echo$count[$i]['id_post']?>">
                      <div class="info_post">
                          <div class="info">
                              <?php echo'<p> '.$count[$i]['username'].'</p>'?>
                          </div>
                          <div class="info">
                              <?php echo'<p> '.$count[$i]['date_creation'].'</p>'?>
                          </div>
                            <div class="info">
                            <button type="submit" id="jaime_button" name ="delete"> <img id="delete" src="img/delete.png"></button>
                            </div>
                      </div>
                      <div class="image">
                            <?php echo '<img src="./'.$count[$i]['image'].'" class="image_post">';?>
                      </div>
                      <div class="jaime">
                      <?php
                             //count likes
                                $verived = "true";
                                $req =$db->prepare(" SELECT COUNT(*) as 'count' FROM  `like` where `id_post`=? AND verifed = ?");
                                $req->execute([$count[$i]['id_post'], $verived]);
                                $likes = $req->fetchAll();
                        ?>
                          <!-- <button type="submit" id="jaime_button" name="jaime_button"> <img id="jaime" src="img/like.png">jaime</button>     -->
                          <img id="jaime" src="img/like.png">
                            <a href="index.php?jaime=like&id_post=<?php echo $count[$i]['id_post']?>">jaime</a> <?php echo $likes[0]['count'];?>
                      </div>
                      <input type="text" name = "tex_comment" placeholder="your comment">
                          <input type="submit" class="add" name="commemt" value="Add comment">
                      <div class="commentContainer">
                          
                          <?php
                          //show comment
                              $req =$db->prepare("SELECT * FROM comment where id_post = ?");
                              $req->execute([$count[$i]['id_post']]);
                              $data = $req->fetchAll();
                            $j = 0;
                            while($j < count($data))
                            {?>
                                  <div class="comment">
                                      <div class="info_comment"><?php echo'<p> '.$data[$j]['username'].'</p>';?></div>
                                      <div class="info_comment"><?php echo'<p> '.$data[$j]['comment'].'</p>';?></div>
                                      <div class="info_comment"><?php echo'<p> '.$data[$j]['time_comemmt'].'</p>';?></div>
                                      <a id="cmt_btn" href="index.php?delete=comment&id_comment=<?php echo$data[$j]['id_comment'];?>"class="delete">delete</a>
                                  </div>
                          <?php $j++;} 
                          ?> 
                         
                      </div>
                      
                        </form> 
                    <?php
                    $i++;
                  }
                ?>
            </div>
            <div class="pagination">
                <?php
                
                    for ($page=1; $page<=$number_page; $page++) {
                            echo '<a href="index.php?page=' . $page . '">' . $page . '</a> ';
                    }
                ?>
            </div>
    <div class="footer">
        <p id="footerp">© 2020. All rights reserved | Desin by
          <span id="footer">cel-otma</span>
        </p>
      </div>
</body>
</html>