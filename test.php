<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <title>Document</title>
    <style>
        body {
            text-align:center;
            width:95%;
        }
        #down,#listen {
            display:none;
        }
    </style>
</head>
<body>
    <br>
<h1 align='center'>语音合成示例</h1>
<br><br>

<form class="form-horizontal">
    <!-- <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
        <div class="col-sm-10">
            <input type="email" class="form-control" id="inputEmail3" placeholder="Email">
        </div>
    </div> -->

    <div class="form-group">
        <label for="inputPassword3" class="col-sm-2 control-label">文本</label>
        <div class="col-sm-10">
            <textarea class="form-control content" rows="10">早啊！新闻来了，来看看昨夜今晨有哪些新闻 。
22日，首届数字中国建设峰会在福建福州开幕，中共中央总书记、国家主席、中央军委主席习近平发来贺信。习近平在贺信中强调，要以信息化培育新动能，用新动能推动新发展，以新发展创造新辉煌。
国务委员兼外交部长王毅22日宣布：经中印双方商定，中国国家主席习近平同印度总理莫迪将于4月27日至28日在湖北省武汉市举行非正式会晤。
商务部新闻发言人应询回答“关于美国财长姆努钦表示考虑来华磋商”时表示，中方已收到美方希来北京进行经贸问题磋商的信息，中方对此表示欢迎。
今天的新闻来了就是这样，请继续关注央视新闻的其它报道。</textarea>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10 ddv">
            <button type="button" class="btn btn-default read">合成</button>
            <a href="#" download="audio.mp3" class="btn btn-default" id="down">点击下载</a>
            <button type="button"  class="btn btn-default" id="listen">试听</button>
        </div>
    </div>
<!--     <audio src='#' class="au"></audio>-->
</form>

<script>
var url = '';
$(".read").click(function(){
    var content = $(".content").val();
    $.post("Voice.php", { content: content},
      function(data){
        if (data.path == 'error') {
            alert('文章过长');
        } else {
            var down = $("#down");
            var au = $(".au");
            for(i=0;i<au.length;i++) {
                au.get(i).pause();
            }
            audio = "<audio src='"+data.path+"' class='au'></audio>";
            $('.ddv').append(audio);
            down.attr('href', data.path);
            down.show();
            $("#listen").show();
            au.attr("controls", false);
        }
      },'json');
});
$("#listen").click(function(){
    var au = $(".au:last");
    au.attr("autoplay","autoplay");
    au.attr("controls","controls");
});
</script
</body>
</html>
