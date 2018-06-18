<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="layui.all.js"></script>
</head>
<body>
	<form class="layui-form" method="POST" action="">
    <label class="user"><input name="user_name" id="user_name" maxlength="50" type="text" value="" onclick="JavaScript:this.value=''" placeholder="请输入用户名" /></label>
    <label class="password"><input name="user_pass" id="user_pass" maxlength="50" type="password" onclick="JavaScript:this.value=''" placeholder="请输入密码" /></label>
    <label class="password">
        <div style="display: inline-block"><input name="captchacode" id="captchacode" style="min-width: auto;width: 118px;" maxlength="4" type="text" onclick="JavaScript:this.value=''" placeholder="验证码" /></div>
        <div style="display: inline-block;float: right"><img alt="<?= site_url('login/captcha'); ?>" src="<?= site_url('login/captcha') . "?t=" . time(); ?>" width="120" height="30" id="captcha_img" style="width: 120px;height: 30px" onclick="this.src=this.alt+'?t='+ Math.random();"></div>
    </label>
    <a onclick="javascript:void(0);" lay-submit lay-filter="login" id="login">登录</a>
	</form>
<script>
    layer.msg('haha',{
    	'icon' : 1,
    	'time' : 3*1000,
    }, function(){
    	alert('aa');
    });
   
</script>
</body>
</html>