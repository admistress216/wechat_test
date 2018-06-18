//API: http://www.longtailvideo.com/support/jw5/31164/javascript-api-reference#Events
jwplayer.key="iP+vLYU9H5KyhZeGt5eVuJJIoULUjltoaMeHXg==";
$(document).ready(function(){
	var url = "";
	var videourl1 = $("#videourl1").val();
	var hid_topicid = $('#htopicid').val();
	
	$.post("/ajaxprocess.php?menu=getvideourl",{videourl1:videourl1,hid_topicid:hid_topicid},function(result){
		//alert(result);
		if(result=='siyou'){
				alert('该视频是私有视频，无法播放！');return false;
		}
		if(result=='xiaowai'){
				alert('系统检测到您登录的IP是校外地址，本校师生请通过统一身份认证登录，本校研究生还可输入研究生信息平台的用户名和密码登录系统，其他用户请输入网络课堂系统的用户名和密码登录！');return false;
		}
		
		url = result;
		var videoid = $("#hvideoid").val();
		var h=document.documentElement.clientHeight;var w=document.documentElement.clientWidth-20;
		var H=h-50;
		var videoTag;
		var currentRate = 1;
		var isSeek = false;
		//var folder = $("#hfolder").val();
		var playsec = getCookie("playsec_"+videoid);
		var userid = parseInt($("#huserid").val());
		if(!playsec){
			var isauto = false;
		}else{
			//if(userid>0){
				var isauto = true;
			//}
		}
		var ispub = $("#ispub").val();//0私有 1校内 2校外
		var isxn = $("#isxn").val();
		if(ispub==0){alert('该视频是私有视频，无法播放！');return false;}
		if(ispub==1 && userid==0 && isxn==0){alert('系统检测到您登录的IP是校外地址，本校师生请通过统一身份认证登录，本校研究生还可输入研究生信息平台的用户名和密码登录系统，其他用户请输入网络课堂系统的用户名和密码登录。');location.href='/login/';return false;}
		
		
		//初始化
		playerBegin(url,isauto);
		
		//点击播放 -- 查看是否登录
		jwplayer().onPlay(function(){
			 var playsec = getCookie("playsec_"+videoid);
			 if(playsec && isSeek==false){
				jwplayer().seek(playsec);isSeek =true;
			 }
		});
		jwplayer().onReady(function(){
			//快速 慢速播放问题 http://www.jwplayer.com/blog/slow-motion-with-jw-player/
			//必须HTML5 但是flv似乎不支持
			if (jwplayer().getRenderingMode() == "html5"){
				videoTag = document.querySelector('video');
				if(videoTag.playbackRate) {
					jwplayer().addButton("slomo.png","Toggle Slow Motion",
						toggleSlomo,"slomo");
				}
			}							
		});
		
		//回调函数
		jwplayer().onPause(function(){
			//RegEndTime();//停止播放
		});
		//全屏
		jwplayer().onFullscreen(function(event){
			if(event.fullscreen){$("#isfullscreen").val(1);}
		});
		
		
		//拖动回调函数
		jwplayer().onSeek(function(event){
			var p1 = event.position; //当前时间
			var p2 = event.offset;//跳转到的位置
			isSeek =true; //请不要删除，否则第一次拖动会出现BUG
			//if(p2>p1){jwplayer().seek(p1);}//只允许往前拖，不允许往后拖
		});
		
		//播放回调函数
		jwplayer().onTime(function(event){
			var p = event.position; //当前时间
			//记录视频播放点
			var videoid = $("#hvideoid").val();
			setCookie("playsec_"+videoid,p,"d3");
		});
	});
	
	
});

function toggleSlomo() {
    currentRate == 1 ? currentRate = 0.2: currentRate = 1;
    videoTag.playbackRate = currentRate;
    videoTag.defaultPlaybackRate = currentRate;
    if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1){
        jwplayer().seek(jwplayer().getPosition());
    }
};


//初始化jwplayer
function playerBegin(url,isauto){
	 var srt_zh = $("#srt_zh").val();var srt_en = $("#srt_en").val();
	 var hid_video_type = $('#hid_video_type').val();
	 var player = jwplayer("video_place").setup({
				primary: 'html5',//html5 flash
				width: "100%",
				height: 500,
				autostart:isauto,  //是否自动播放
				controlbar: "bottom", //播放条位置
				repeat:"always",//list列模式、none单曲模式、always循环模式
				volume:80,  //音量
				stretching:"uniform",
				startparam: "start",
			captions:{
					color: "#99FF66",
					fontSize: "12",
					fontOpacity: "70",
					backgroundOpacity: "0",
					fontFamily: "黑体",
					windowColor: "#FF0000",
					edgeStyle: "uniform"//none raised depressed uniform
				},
            playlist: [{
                file: url,
				type:hid_video_type,//flv / mp4
				flashplayer: "/68/jwplayer.flash.swf",
				image:"\/images\/video_img.jpg",
                tracks: [{
                    file: ""+srt_zh+"",
                    kind: "captions",
					label: "中",
                    "default": true
                },{
                    file: ""+srt_en+"",
                    kind: "captions",
					label: "en"
                }]
            }]
				
        });
	//记录播放记录
	regView();
}

//播放记录 
function regView(){
	var videoid = $("#hvideoid").val();var userid = $("#huserid").val();var topicid = $("#htopicid").val();
	
	$.post("/ajaxprocess.php?menu=regview",{videoid:videoid,userid:userid,topicid:topicid},function(result){
		//alert(result);
	});	
}

var keyLogin = function(){
	var keyCode = event.keyCode;
	
	var p1 = jwplayer().getPosition()//jwplayer().seek(
	switch(keyCode){
		case 39: //右箭头			
			p1+=60;jwplayer().seek(p1);
			break;	
		case 37://左
			p1-=60;jwplayer().seek(p1);break;
		case 32://space
			jwplayer().pause();break;
	}
		
}
