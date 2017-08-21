$(function(){
  $('.im_emotion').qqFace({
    assign:'im_content', 
    path:'/arclist/'  ,//表情存放的路径
  });
  $(".chats").scrollTop($(".chats ul").height());

	$("textarea.content").keydown(function(e){
		if(e.keyCode==13){
			send();
		}
	});
	$("textarea.content").focus();
});
function send(){
	var content = $("textarea.content").val();
	if (content.length == 0) {
		return false;
	}
	$("textarea.content").val('');
	
	//对内容进行处理
	content = content.replace(/\[em_(\d+)\]/gi, '<img src="/arclist/$1.gif">');
	content = content.replace(/\[img_(.+?)\]/gi, '<img src="$1">');
	
	var html = 
	'<li class="me clear">\
	<div class="me_face">\
	<img src="{$m.avatar}"></div><div class="me_content">\
    <h5>{$m.nickname}</h5>\
    <div class="me_talk_con">\
<p>'+content+'</p>\
      <i class="ta_me"></i>\
       <i class="ta_me_bl"></i>\
    </div>\
    </div>\
  </li>';
  	$(".chats ul").append(html);
	$(".chats").scrollTop($(".chats ul").height());
	$.post("",{content:content},function(d){
		/*$.get("{:U('/home/im/one')}",{id:d.message},function(html){
			$(".chats ul li").last().remove();
			$(".chats ul").append(html);
		})*/
	})
}
function pull(id,dlgid){
	var lastid = $(".chats ul li").last().attr("data-id");
	$.get("{:U('/home/im/pull')}",{id:id,dlgid:dlgid},function(html){
		if (html.length > 0) {
			$(".chats ul").append(html);
			$(".chats").scrollTop($(".chats ul").height());
			$(".im_recent").append(html);
		}
	})
}

function addtalk(id,dlgid){
	
   var im_user = document.getElementById('im_user');
  var im_talk = document.getElementById('im_talk_box');
    
   im_user.style.display = 'block';
   im_user.style.zIndex = '1000';	
   im_talk.style.display = 'block';
   im_talk.style.zIndex = '1500';
   
   im_talk.style.zIndex = '1500';
   window.location.href="{:U('/home/im/index')}?id=" + id + "&dlgid=" + dlgid;

}

/*关闭聊天*/
function closetalk(){
	window.close();
}

/*关闭聊天框*/
function closetalkbox(){
	var talkbox = document.getElementById('im_talk_box');
	talkbox.style.display='none';
}

function addImImage(){
	var html = "<form id='go_upload' action='{:U('/home/common/upload')}' method='post'><input type='file' name='image' style='display:none'/></form>";
  $("body").append(html);
  var f = $("#go_upload");
  f.find('input').change(function(){	 
      f.ajaxSubmit(function(d){
        f.remove();
	    $('#im_content').val("[img_"+d.message+"]");
		//$('.talk_send_btn .u_btn_sl').click();
      })
  });
  f.find('input').click();
}

/*关注*/
function follow(id,member_id){
	$.post("{:U('/home/member/follow')}",{"member_id":member_id},function(){
		document.getElementById(id).innerHTML = '<img src="images/icons/ico_idxft05.png" style="width:25px; height:25px;">';
	})
}

function friendtalk(id){
  $.post("{:U('/home/im/getdlgid')}",{mid:id},function(d){
	  var im_user = document.getElementById('im_user');
        var im_talk = document.getElementById('im_talk_box');
        im_user.style.display = 'block';
        im_user.style.zIndex = '1000';
        im_talk.style.display = 'block';
        im_talk.style.zIndex = '1500';
		var t = d.message;
		window.location.href="{:U('/home/im/index')}?id=" + id +"&dlgid=" +t;			
	});	
}

function circletalk(){
	
}

function openbook(){
	  var im_user = document.getElementById('im_user');
        var im_talk = document.getElementById('im_talk_box');
        im_user.style.display = 'block';
        im_user.style.zIndex = '1500';
        im_talk.style.display = 'block';
        im_talk.style.zIndex = '1000';
}


