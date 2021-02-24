var video = document.getElementById('video');
var canvas = document.createElement('canvas');
var ctx = canvas.getContext('2d');
var image_draw = null;
var final_image = document.getElementById('photo');
var button_capture = document.getElementById('capture');
var button_upload = document.getElementById('upload1');
var array_emoji = new Array(4);
var mediaStream = null;
var width = 320;
var height = 240;
video.width = 320;
video.height = 240;
canvas.width = 320;
canvas.height = 240;
navigator.getUserMedia = ( navigator.getUserMedia ||
    navigator.webkitGetUserMedia ||
    navigator.mozGetUserMedia ||
    navigator.msGetUserMedia);

navigator.getUserMedia({
    video:true
},function(stream){
    
    // if(typeof video.srcObject == "object")
    //     video.srcObject = mediaStream;
    // else
    //     video.src = URL.createObjectURL(mediaStream);
    video.srcObject = stream;
    video.play();
    image_draw = video;
},function(error){console.log(error)}
);

function upload_image(){
    var input_upload = document.getElementById('upload');
    var file_reader = new FileReader();
    file_reader.onload = function(){
        var img_load = new Image();
        img_load.src = this.result;
        img_load.onload = function(){
            image_draw = img_load;
            array_emoji = new Array(4);
            document.getElementsByClassName('output')[0].style.display = "block";
            ctx.drawImage(image_draw,0,0,width,height);
            data = canvas.toDataURL('image/jpg');
            final_image.setAttribute('src',data);
        }
    }
    if (input_upload.files[0])
    file_reader.readAsDataURL(input_upload.files[0]);
}
function capture(){
    if (image_draw)
    {
        array_emoji = new Array(4);
        document.getElementsByClassName('output')[0].style.display = "block";
        image_draw = video;
        ctx.drawImage(image_draw,0,0,width,height);
        data = canvas.toDataURL('image/jpg');
        final_image.setAttribute('src',data);
    }
}

//creat objet Ajax

    function send_data(){

        if(image_draw != "" && final_image.src != ""){
        var xhttp = new XMLHttpRequest();
        xhttp.open("POST", "ajax_camera.php");
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("image="+final_image.src);
        }
    }
function set_emoji(index){
    if (image_draw)
    {
        var emoji = document.getElementsByClassName("emoji")[index];
        var img_load = new Image();
        img_load.src = emoji.src;
        img_load.onload = function(){
            for(var i = 0;i < 4;i++)
            {
                if (!array_emoji[i])
                {
                    array_emoji[i] = emoji;
                    break;
                }
            }
            draw_emoji();
        }
    }
}
function draw_emoji(){
    if (image_draw)
    {
        for(var i = 0;i < 4;i++){
            if (array_emoji[i])
            ctx.drawImage(array_emoji[i],i*60,0,60,60);
        }
        data = canvas.toDataURL('image/jpg');
        final_image.setAttribute('src',data);
    }
}

