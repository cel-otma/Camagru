<?php
  require_once("config/setup.php");
  session_start();
   if (!isset($_SESSION["username"]))
          header('location:index.php');

          //show my posts
          $req =$db->prepare("SELECT * FROM post WHERE `username` = ? ORDER BY date_creation DESC");
          $req->execute([$_SESSION["username"]]);
          $count = $req->fetchAll();
          if(isset($_POST['publier']))
          {
            header('location:camera.php');
          }
          //delete post
    if(isset($_POST['delete'],$_POST["id_post"]) && trim($_POST["id_post"]) != "" )
    {
         $id_post = $_POST["id_post"];
            $update = $db->prepare("DELETE FROM `post` WHERE `id_post` = ? AND `username` = ? ");
            $update->execute([$id_post,$_SESSION["username"]]);
            $update = $db->prepare("DELETE FROM `comment` WHERE `id_post` = ? AND `username` = ?");
            $update->execute([$id_post, $_SESSION["username"]]);
            $update = $db->prepare("DELETE FROM `like` WHERE `id_post` = ?  AND `username` = ?");
            $update->execute([$id_post, $_SESSION["username"]]);
            header('location:camera.php');
            
    }
       
?>
<!doctype html>

<head>
  <link rel="stylesheet" type="text/css" href="css/camera.css">
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css">

  <title>My Favorite Sport</title>
</head>

<body>
<H1 class="cm">Camagru</H1>
  <div class="contentarea">
    
    <div class="camera">
      <video id="video"></video>
    </div>
    <button id="capture" onclick="capture()">Take photo</button>
    <input id="upload" onchange="upload_image()" type="file"> </input>
    <button id="upload1" onclick="upload.click()">upload</button>
    <div class="output" style="display:none;margin:auto">
      <img id="photo">
    </div>
  </div>
  </div>
  <div class="sticker">
    <div class="group_emoji">
      <img src="img/1.png" id="img" height="110" width="110" class="emoji" onclick="set_emoji(0)">
      <img src="img/2.png" id="img1" height="110" width="110" class="emoji" onclick="set_emoji(1)">
      <img src="img/3.png " id="img2" height="110" width="110" class="emoji" onclick="set_emoji(2)">
      <img src="img/4.png " id="img3" height="110" width="110" class="emoji" onclick="set_emoji(3)">
    </div>
    <div class="img">
      
      <form action="" method="POST">
        <button class="cancel"> <a href="index.php">cancel</a> </button>
        <button class="publier" onclick="send_data()" name="publier"> publier</button>
      </form>
      
    </div>
  </div>
  <div >
    <?php
        $i = 0;
        while($i < count($count))
      {?>
      <form  class="post" method="POST">
          <input type="hidden" name="id_post" value="<?php echo$count[$i]['id_post']?>">
          <div class="info_post">
              <div class="info">
                  <button type="submit" id="jaime_button" name ="delete"> <img id="delete" src="img/delete.png"></button>
              </div>
          </div>
            <div class="image">
                <?php echo '<img src="./'.$count[$i]['image'].'" class="image_post">';?>
            </div>
      </form>
      <?php
          $i++;
          }
      ?>
  </div >
  <script src="js/camera.js">

  </script>
  <div class="footer">
                    <p style="color:#ffff00;">© 2020. All rights reserved | Desin by
                    <a style="color:#ffff00;">cel-otma</a>
                    </p>
                </div>
</body>

</html>