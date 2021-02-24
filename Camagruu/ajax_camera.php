<?php
        require_once("config/setup.php");
        session_start();
        $date = date('Y-m-d H:i:s');
        $username = $_SESSION['username'];
        chmod($_SERVER["DOCUMENT_ROOT"]."/upload/", 0777);
        $image = file_get_contents(str_replace(' ', '+',$_POST['image']));
        $target = 'upload/'.time().".png";
        file_put_contents($target, $image);
        if(GetimageSize($target))
        {
                $req = "INSERT INTO post( `username`, `image`, `date_creation`)  VALUES('$username', '$target', '$date')";
                $update = $db->prepare($req);
                $update->execute();
        }
        
?>