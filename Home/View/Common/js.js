$(function(){
  $("form.add_comment_main").submit(function(){
    must_login();
    $(this).ajaxSubmit(function(html){
        $("form.add_comment_main").parent().after(html);
    })    
    return false;
  })
  $("form.address_form input[type=checkbox]").change(function(){
    var is_default = $(this).is(":checked")?1:0;
    $("form.address_form input[name=is_default]").val(is_default);
  });
  $("form.address_form").submit(function(d){
    $(this).ajaxSubmit(function(d){
      window.location.reload();
    })
    return false;
  })
  $("form.activity_detail").submit(function(){
    var content = $(this).find("[name=content]").val();
    if (content.length == 0) {
      alert("木有长度");
      return false;
    }
    $("p.activity_content").html(content);
    $('#activity_detail').hide();
    $(this).ajaxSubmit(function(){

    })
    return false;
  })
	

  $("dd.gonggao").click(function(){
    var val = $(this).html();
    if (val.indexOf("取消") != -1) {
    //取消公告, 现在就去取消
      $(this).html("圈子公告");
      $.post("{:U('/home/memberPost/unannouncement')}",{id:$(this).attr("data-id")},function(){
        window.location.reload();
      })
    }else{
      $(this).html("取消公告");
      $.post("{:U('/home/memberPost/announcement')}",{id:$(this).attr("data-id")},function(){
        window.location.reload();
      })
    }
  })

  $(".member_post .is_top").click(function(){
    var val = $(this).html();
    var id = $(this).attr("data-id");
    var is_top = (val.indexOf("取消") == -1) ? 1 :0;
    $.post("{:U('/home/memberPost/update')}",{id:id,circle_top:is_top},function(){
      window.location.reload();
    })
  })

  $(".member_post .is_hot").click(function(){
    var val = $(this).html();
    var id = $(this).attr("data-id");
    var is_hot = (val.indexOf("取消") == -1) ? 1 :0;
    $.post("{:U('/home/memberPost/update')}",{id:id,circle_hot:is_hot},function(){
      window.location.reload();
    })
  })

  $(".member_post .ms_top").click(function(){
    var val = $(this).html();
    var id = $(this).attr("data-id");
    var m_top = (val.indexOf("取消") == -1) ? 1 :0;
    $.post("{:U('/home/memberPost/update')}",{id:id,m_top:m_top},function(){
      window.location.reload();
    })
  })

  $(".member_post .delete").click(function(){
      var id = $(this).attr("data-id");
      $(this).parent().parent().parent().remove();
    $.post("{:U('/home/memberPost/curdDelete')}",{id:id},function(){
    })
  })

  $(".member_post .mpdelete").click(function(){
      var id = $(this).attr("data-id");
      $(this).parent().parent().parent().remove();
    $.post("{:U('/member/memberPost/deletemp')}",{id:id},function(){
    })
  })

  $(".member_post .comdelete").click(function(){
      var id = $(this).attr("data-id");
      $(this).parent().parent().parent().remove();
    $.post("{:U('/member/memberPost/deletecomment')}",{id:id},function(){
    })
  })
	

	$(".logout").click(function(){
		$.post($(this).attr("data-href"),function(){
			window.location.reload();
		});
	});

  $(":not(a)[href]").click(function(){
    var href = $(this).attr("href");
    window.location.href = href;
  });
   $(".apply").click(function(){
     var obj=$(this);
    if (!$(this).attr("data-href") || !$(this).attr("data-id")) {
      return false;
    }
   
     $.post($(this).attr("data-href"),{"open_class_id":$(this).attr("data-id")},function(d){
		 if(d.suc){
           layer.msg("报名成功");
		  obj.parent().html('<span class="u_btn_bk" style="background-color:grey;color:white;border: 0px">已报名</span>');
		 //  window.location=window.location;//这个暂时不知道怎么做，需要优化下
		   }
     else{
		 layer.msg(d.message);
           var url = location.search;

          /* if (url) {
             url = url.replace("?url=","");
           }
           if (!url || url.length == 0) {
             url = "/";
           }*/
		setTimeout(function(){
		window.location= d.url;
		},2000);
		 
	 }
	 });
   })
  $("form.audition_remarks").submit(function(){
      $(this).parent().hide();
      $(this).ajaxSubmit(function(d){
        
      })
      return false;
    })
  
  $("form.add_bid").go_validate(function(form){
    var f = $(form);
    if (f.find("[name=service_id]").val()=='') {
      alert("请选择服务");
      return false;
    }
    f.ajaxSubmit(function(d){
      if (d.suc) {
        window.location.href = "{:U('/member/bid/teacher')}";
      }else{
        alert(d.message);
      }
    });
  });
  $('.emotion1').qqFace({
    assign:'emotion_content1', 
    path:'/arclist/'  ,//表情存放的路径
  });
  $('.emotion').qqFace({
    assign:'emotion_content', 
    path:'/arclist/'  ,//表情存放的路径
  });
  $(".emotion_mp").each(function(){
    var id = $(this).parent().parent().find("textarea").attr("id");
    $(this).qqFace({
      assign:id,
      path:'/arclist/',
    });
  })
  $("form.yue").go_validate(function(form){
    $(form).ajaxSubmit(function(){
      window.location.reload();
    });
  });
  $("form.yue [name=set_time]").go_datetime();

  $(".show_essay_modal").click(function(){
    var m = $(".essay_modal");
    m.modal();
    var type=$(this).attr("type");
    $.get("{:U('/home/essay/curdShow')}",{type:type},function(d){
      m.find(".modal-header h4").html(d.message.title);
      m.find(".modal-body").html(d.message.content);
    })
  })

  $("select.show_contracts").change(function(){
    var id = $(this).val();
    id = parseInt(id);
    if (id > 0) {
      window.location.href = "{:U('/member/contract/show')}"+"?id="+id;
    }
  })

  $("form.add_rating").go_validate(function(f){
    $(f).ajaxSubmit(function(d){
      window.location.href = "{:U('/member/order')}";
    })
  });

  $("[datetime=true]").each(function(){
    $(this).go_datetime();
  });
});
//ready结束

function must_login(){
  var mid = "{$_SESSION.id}";
  if (mid == "") {
    window.location.href = "{:U('/home/member/login')}?url="+window.location.pathname+window.location.search;
    return false;
  }
}

function gailou(t){
  $p = $(t).parent();
  var id = $(t).attr("data-id");
  var html = "<form class='add_comment' action='{:U('/home/comment/add')}' method='POST'> ";
  html += "<input name='pid' type='hidden' value='"+id+"'>";
  html += '<div style="background:#f8f8f8; padding:10px; margin-bottom:10px;" class="ucl_ssinput"><textarea style="min-height:30px; height:40px;" name="content" id="emotion_content_'+id+'" class="u_inp_t"></textarea><div class="ucl_ssinputbtnbox f_clearfix"><input type="submit" style="padding:0 10px;" value="回复" class="f_fr u_btn_sl"><button onclick="close_add_comment(this);return false;">X</button><span class="emotion_'+id+'">表情</span></div></div>';
  html += "</form>";
  $p.after(html);
  $('.emotion_'+id).qqFace({
      assign:'emotion_content_'+id, 
      path:'/arclist/'  ,//表情存放的路径
    });
  $("form.add_comment").submit(function(){
    must_login();
    var f = $(this);
    f.ajaxSubmit(function(html){
      var dl = f.parent().parent();
      if (dl.hasClass("f_clearfix")) {
        //外面的
        f.after(html);
        f.remove();
      }else{
        f.parent().parent().after(html);
        f.remove();
      }
    })
    return false;
  })
  return false;
}

function delete_comment(cid,oid,t){
  var dl = $(t).parent().parent().parent();
  if (dl.hasClass("f_clearfix")) {
    //外面的pc
    dl.parent().remove();
  }else{
    dl.remove();
  }
  $.post("{:U('/home/comment/delete')}",{id:cid},function(){
	  var comnum_box = "cnum_"+oid;
      var comnum = document.getElementById(comnum_box).innerText;
	 comnum = parseInt(comnum);
	   comnum = comnum - 1;
		  document.getElementById(comnum_box).innerHTML = comnum;
  })
}

function go_pca(pname,cname,aname,pid,cid,aid){
	pid = pid ? pid : 0;
	cid = cid ? cid : 0;
	aid = aid ? aid : 0;
  if (pid > 0 && cid>0 && aid>0) {
    $.get("{:U('/home/area/pca')}",{pid:pid,cid:cid},function(d){
        var poptions = build_options(d.message[0]);
        $(pname).html(poptions);
        $(pname).val(pid);
        var coptions = build_options(d.message[1]);
        $(cname).html(coptions);
        $(cname).val(cid);
        var aoptions = build_options(d.message[2]);
        $(aname).html(aoptions);
        $(aname).val(aid);
    })
  }else{
  //没有回填
    $.get("{:U('/home/area/index1')}",function(d){
      var poptions = build_options(d.message[0]);
      $(pname).html(poptions);
      var coptions = build_options(d.message[1]);
      $(cname).html(coptions);
      var aoptions = build_options(d.message[2]);
      $(aname).html(aoptions);
    });
  }
	
  $(pname).change(function(){
    var pid = $(this).val();
    $.get("{:U('/home/area/index1')}",{pid:pid},function(d){
      var coptions = build_options(d.message[0]);
      $(cname).html(coptions);
      var aoptions = build_options(d.message[1]);
      $(aname).html(aoptions);
    })
  });
  $(cname).change(function(){
    var pid = $(this).val();
    $.get("{:U('/home/area/index1')}",{pid:pid},function(d){
      var aoptions = build_options(d.message[0]);
      $(aname).html(aoptions);
    })
  });
}
function build_options(data){
	var html = "<option value='0' selected>请选择</option>";
	for(var key in data){
		html = html + '<option value='+key+' >'+data[key]+'</option>';
	}
//  console.log(html);
	return html;
}
function build_hidden(name,value){
    var hidden = "<input type=hidden name="+name+" value="+value+">";
    return hidden;
}

function is_phone(phone){
  return /^1[\d]{10}$/.test(phone);
}
function is_email(email){
  return /^.+@.+$/.test(email);
}
function is_gt0(d){
  return d.length != 0 && !isNaN(d) && parseInt(d) > 0;
}

function show_yue(t){
  var li = $(t).parent().parent().parent();
  $("#show_yue").find('[name=name]').html(li.find('[name=name]').html());
  $("#show_yue").find('[name=set_time]').html(li.find('[name=set_time]').html());
  //$("#show_yue").find('[name=target]').html(li.find('[name=target]').val());
  $("#show_yue").find('[name=listen_type]').html(li.find('[name=listen_type]').val());
  $("#show_yue").show();
  $(t).parent().parent().find('img').hide();
}

function edit_yue(t){   
  var li = $(t).parent().parent();
  $("form.yue").find('[name=name]').val(li.find('[name=name]').html());
  $("form.yue").find('[name=set_time]').val(li.find('[name=set_time]').html());
  $("form.yue").find('[name=target]').val(li.find('[name=target]').val());
  $("form.yue").find('[name=listen_type]').val(li.find('[name=listen_type]').val());
  $("form.yue").find('[name=id]').val(li.find('[name=id]').val());
  $("#add_yue").show();
}
function ok_yue(id,period){
  $.post("{:U('/member/plan/update')}",{id:id,status:1,period:period},function(){
    window.location.reload();
  })
}
function cancel_yue(id,period){
  $.post("{:U('/member/plan/update')}",{id:id,status:2,period:period},function(){
    window.location.reload();
  })
}
function go_alipay(html){
  $("body").append(html);
  $("#alipaysubmit").submit();
}
function go_wxPay(code,id,type){
  var params = $.param({code:code,id:id,type:type}); 
  window.location.href = "{:U('/home/common/wxPay')}?"+params;
}
jQuery.fn.extend({
  'go_validate' : function(suc,v){
    $.validator.setDefaults({
      submitHandler: function(form) {
        suc(form);
      }
    })
    if (v) {
      $(this).validate(v)
    }else{
      $(this).validate();
    }
  }
});
jQuery.fn.extend({
  'go_datetime':function(){
    $(this).datetimepicker({
        format: 'yyyy-mm-dd hh:ii',
        language:'zh-CN'
    });
  }
})
jQuery.validator.addMethod("isPhone", function(value, element) {    
    var tel = /^1[\d]{10}$/;    
    return this.optional(element) || (tel.test(value));    
  }, "手机号格式错误");

jQuery.validator.addMethod("equal", function(value, element) {    
    return this.optional(element) || (value == $(element).attr('equal'));    
  }, "输入不对");

jQuery.validator.addMethod("isChinese", function(value, element) {    
    var tel = /[\w]+/;    
    return this.optional(element) || (!tel.test(value));    
}, "只能输入中文");

jQuery.validator.addMethod("isIdCard", function(value, element) {    
    var tel = /^\d{17}(\d|x)$/;    
    return this.optional(element) || (tel.test(value));    
}, "身份证格式错误");
function go_upload(callback){
  var html = "<form id='go_upload' action='{:U('/home/common/upload')}' method='post'><input type='file' name='image' class='hiddenUpload' /></form>";
  $("body").append(html);
  var f = $("#go_upload");
  f.find('input').change(function(){	  
      f.ajaxSubmit(function(d){
        f.remove();
        callback(d.message);
      })
  });
  f.find('input').click();
}

function go_upload1(callback){
  var html = "<form id='go_upload1' action='{:U('/home/common/upload')}' method='post'><input type='file' name='image'/></form>";
  $("body").append(html);
  var f = $("#go_upload1");
  f.find('input').change(function(){
      f.ajaxSubmit(function(d){
        f.remove();
        callback(d.message);
      })
  });
  f.find('input').click();
}

function go_textarea_modal(title,callback){
  $(".modal_textarea").modal();
  $(".modal_textarea .modal-title").html(title);
  $(".modal_textarea input[type=button]").click(function(){
    var body = $(".modal textarea").val();
    if (body.length==0) {
     // alert("没有内容");
      return;
    }
    callback(body);
    $(".modal textarea").val("");
    $(".modal_textarea").modal('hide');
  })
}

function edit_service_order_teacher_remark(t){
  var t = $(t);
  go_textarea_modal('修改学员备注',function(body){
    var id = t.attr("data-id");
    $.post("{:U('/member/ServiceOrder/curdUpdate')}",{id:id,teacher_remark:body},function(d){
      t.parent().parent().find("span.teacher_remark").html(body);
    });
  });
}

function open_class_show_follow(t,mid){
  var html = $(t).html();
  if (html.indexOf("取消") == -1) {
    //没有取消, 就是关注
    $(t).html('取消关注');
    $.post("{:U('/home/member/follow')}",{member_id:mid});
  }else{
    $(t).html('关注');
    $.post("{:U('/home/member/unfollow')}",{member_id:mid});
  }
}
function handle_paypwd(suc){
  var pwd = "{$m.paypassword}";
  if (pwd.length == 0) {
    alert("你还没有设置支付密码");
    return false;
  }
  var html = '<div class="modal fade modal_paypwd" id="paywd" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">\
  <div class="modal-dialog modal-sm">\
    <div class="modal-content">\
          <div class="modal-header">\
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
          <h4 class="modal-title">请输入支付密码</h4>\
          </div>\
          <div class="modal-body">\
            <input type="text" style="width:100%"/>\
          </div>\
          <div class="modal-footer">\
              <input type="button" value="确认支付" class="btn btn-primary">\
          </div>\
    </div>\
  </div>\
</div>';
  $("body").append(html);
  $('.modal_paypwd').modal();
  $(".modal_paypwd").find("input[type=button]").click(function(){
    var paypwd = $(".modal_paypwd input[type=text]").val();
    if (paypwd.length == 0) {
      return false;
    }
    if (MD5(paypwd)!=pwd) {
      alert("密码错误");
      return false;
    }
    suc();
  })
}
function handle_openClass_praise(t,open_class_id,member_id){
  var n = parseInt($(t).find("span").html());
   // $(t).find("img").attr("src","/images/icons/unzan.png");
    $.post("{:U('/home/common/newPraise')}",{object_id:open_class_id,type:3,member_id:member_id},function(d){
      if(d.suc)
      {
        layer.msg(d.message);
        $(t).find("span").html(n+1);
      }else{
        if(n>0) {
          layer.msg(d.message);
          $(t).find("span").html(n - 1);
        }else{
          layer.msg(d.message);
          $(t).find("span").html(n);
        }
      }
    })
}
function list_filter_go_circle(){
  var f = $("#list_filter_form");
  var school_id = f.find("[name=school_id]").val();
  school_id  = parseInt(school_id);
  if (school_id > 0) {
    $.get("{:U('/home/school/id2circle')}",{id:school_id},function(d){
      if (d.status == 1) {
          window.location.href = d.url;
      }else{
        alert("请选择学校");
      }
    })
  }
}
function list_filter_changeArea(t){
  var div = $("div.school");
  $("#list_filter_form input[name=unified_id]").val("");
  $("#list_filter_form input[name=public_subject_id]").val("");
  $("#list_filter_form input[name=school_id]").val("");
  $("#list_filter_form").submit();
}
function list_filter_changeSchool(t){
  var area_id = $(t).attr("data-id");
  area_id  =parseInt(area_id);
  if (area_id > 0) {
    var div = $("div.school");
    $("#list_filter_form input[name=unified_id]").val("");
    $("#list_filter_form input[name=public_subject_id]").val("");
    $("#list_filter_form input[name=school_id]").val(area_id);
    $("#list_filter_form").submit();
  }  
}
function list_filter_changeYuan(t){
  var school_id = $(t).attr("data-id");
  school_id  =parseInt(school_id);
  if (school_id > 0) {
    var div = $("div.school");
    $("#list_filter_form input[name=unified_id]").val("");
    $("#list_filter_form input[name=public_subject_id]").val("");
    $("#list_filter_form input[name=school_id]").val(school_id);
    $("#list_filter_form").submit();
  }  
}
function untuo(t){
    $.post($(t).attr("href"),function(d){
      window.location.reload();
    })
}

	// $Revision: 2.0.2.2 $

/************************************************************************/
/* phpAdsNew 2                                                          */
/* ===========                                                          */
/*                                                                      */
/* Copyright (c) 2000-2005 by the phpAdsNew developers                  */
/* For more information visit: http://www.phpadsnew.com                 */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

/*
 *  md5.jvs 1.0b 27/06/96
 *
 * Javascript implementation of the RSA Data Security, Inc. MD5
 * Message-Digest Algorithm.
 *
 * Copyright (c) 1996 Henri Torgemane. All Rights Reserved.
 *
 * Permission to use, copy, modify, and distribute this software
 * and its documentation for any purposes and without
 * fee is hereby granted provided that this copyright notice
 * appears in all copies. 
 *
 * Of course, this soft is provided "as is" without express or implied
 * warranty of any kind.
 *
 * $Id: md5.js,v 2.0.2.2 2005/01/13 15:44:05 ciaccia Exp $
 *
 */



function array(n) {
  for(i=0;i<n;i++) this[i]=0;
  this.length=n;
}

/* Some basic logical functions had to be rewritten because of a bug in
 * Javascript.. Just try to compute 0xffffffff >> 4 with it..
 * Of course, these functions are slower than the original would be, but
 * at least, they work!
 */

function integer(n) { return n%(0xffffffff+1); }

function shr(a,b) {
  a=integer(a);
  b=integer(b);
  if (a-0x80000000>=0) {
    a=a%0x80000000;
    a>>=b;
    a+=0x40000000>>(b-1);
  } else
    a>>=b;
  return a;
}

function shl1(a) {
  a=a%0x80000000;
  if (a&0x40000000==0x40000000)
  {
    a-=0x40000000;  
    a*=2;
    a+=0x80000000;
  } else
    a*=2;
  return a;
}

function shl(a,b) {
  a=integer(a);
  b=integer(b);
  for (var i=0;i<b;i++) a=shl1(a);
  return a;
}

function and(a,b) {
  a=integer(a);
  b=integer(b);
  var t1=(a-0x80000000);
  var t2=(b-0x80000000);
  if (t1>=0) 
    if (t2>=0) 
      return ((t1&t2)+0x80000000);
    else
      return (t1&b);
  else
    if (t2>=0)
      return (a&t2);
    else
      return (a&b);  
}

function or(a,b) {
  a=integer(a);
  b=integer(b);
  var t1=(a-0x80000000);
  var t2=(b-0x80000000);
  if (t1>=0) 
    if (t2>=0) 
      return ((t1|t2)+0x80000000);
    else
      return ((t1|b)+0x80000000);
  else
    if (t2>=0)
      return ((a|t2)+0x80000000);
    else
      return (a|b);  
}

function xor(a,b) {
  a=integer(a);
  b=integer(b);
  var t1=(a-0x80000000);
  var t2=(b-0x80000000);
  if (t1>=0) 
    if (t2>=0) 
      return (t1^t2);
    else
      return ((t1^b)+0x80000000);
  else
    if (t2>=0)
      return ((a^t2)+0x80000000);
    else
      return (a^b);  
}

function not(a) {
  a=integer(a);
  return (0xffffffff-a);
}

/* Here begin the real algorithm */

    var state = new array(4); 
    var count = new array(2);
	count[0] = 0;
	count[1] = 0;                     
    var buffer = new array(64); 
    var transformBuffer = new array(16); 
    var digestBits = new array(16);

    var S11 = 7;
    var S12 = 12;
    var S13 = 17;
    var S14 = 22;
    var S21 = 5;
    var S22 = 9;
    var S23 = 14;
    var S24 = 20;
    var S31 = 4;
    var S32 = 11;
    var S33 = 16;
    var S34 = 23;
    var S41 = 6;
    var S42 = 10;
    var S43 = 15;
    var S44 = 21;

    function F(x,y,z) {
	return or(and(x,y),and(not(x),z));
    }

    function G(x,y,z) {
	return or(and(x,z),and(y,not(z)));
    }

    function H(x,y,z) {
	return xor(xor(x,y),z);
    }

    function I(x,y,z) {
	return xor(y ,or(x , not(z)));
    }

    function rotateLeft(a,n) {
	return or(shl(a, n),(shr(a,(32 - n))));
    }

    function FF(a,b,c,d,x,s,ac) {
        a = a+F(b, c, d) + x + ac;
	a = rotateLeft(a, s);
	a = a+b;
	return a;
    }

    function GG(a,b,c,d,x,s,ac) {
	a = a+G(b, c, d) +x + ac;
	a = rotateLeft(a, s);
	a = a+b;
	return a;
    }

    function HH(a,b,c,d,x,s,ac) {
	a = a+H(b, c, d) + x + ac;
	a = rotateLeft(a, s);
	a = a+b;
	return a;
    }

    function II(a,b,c,d,x,s,ac) {
	a = a+I(b, c, d) + x + ac;
	a = rotateLeft(a, s);
	a = a+b;
	return a;
    }

    function transform(buf,offset) { 
	var a=0, b=0, c=0, d=0; 
	var x = transformBuffer;
	
	a = state[0];
	b = state[1];
	c = state[2];
	d = state[3];
	
	for (i = 0; i < 16; i++) {
	    x[i] = and(buf[i*4+offset],0xff);
	    for (j = 1; j < 4; j++) {
		x[i]+=shl(and(buf[i*4+j+offset] ,0xff), j * 8);
	    }
	}

	/* Round 1 */
	a = FF ( a, b, c, d, x[ 0], S11, 0xd76aa478); /* 1 */
	d = FF ( d, a, b, c, x[ 1], S12, 0xe8c7b756); /* 2 */
	c = FF ( c, d, a, b, x[ 2], S13, 0x242070db); /* 3 */
	b = FF ( b, c, d, a, x[ 3], S14, 0xc1bdceee); /* 4 */
	a = FF ( a, b, c, d, x[ 4], S11, 0xf57c0faf); /* 5 */
	d = FF ( d, a, b, c, x[ 5], S12, 0x4787c62a); /* 6 */
	c = FF ( c, d, a, b, x[ 6], S13, 0xa8304613); /* 7 */
	b = FF ( b, c, d, a, x[ 7], S14, 0xfd469501); /* 8 */
	a = FF ( a, b, c, d, x[ 8], S11, 0x698098d8); /* 9 */
	d = FF ( d, a, b, c, x[ 9], S12, 0x8b44f7af); /* 10 */
	c = FF ( c, d, a, b, x[10], S13, 0xffff5bb1); /* 11 */
	b = FF ( b, c, d, a, x[11], S14, 0x895cd7be); /* 12 */
	a = FF ( a, b, c, d, x[12], S11, 0x6b901122); /* 13 */
	d = FF ( d, a, b, c, x[13], S12, 0xfd987193); /* 14 */
	c = FF ( c, d, a, b, x[14], S13, 0xa679438e); /* 15 */
	b = FF ( b, c, d, a, x[15], S14, 0x49b40821); /* 16 */

	/* Round 2 */
	a = GG ( a, b, c, d, x[ 1], S21, 0xf61e2562); /* 17 */
	d = GG ( d, a, b, c, x[ 6], S22, 0xc040b340); /* 18 */
	c = GG ( c, d, a, b, x[11], S23, 0x265e5a51); /* 19 */
	b = GG ( b, c, d, a, x[ 0], S24, 0xe9b6c7aa); /* 20 */
	a = GG ( a, b, c, d, x[ 5], S21, 0xd62f105d); /* 21 */
	d = GG ( d, a, b, c, x[10], S22,  0x2441453); /* 22 */
	c = GG ( c, d, a, b, x[15], S23, 0xd8a1e681); /* 23 */
	b = GG ( b, c, d, a, x[ 4], S24, 0xe7d3fbc8); /* 24 */
	a = GG ( a, b, c, d, x[ 9], S21, 0x21e1cde6); /* 25 */
	d = GG ( d, a, b, c, x[14], S22, 0xc33707d6); /* 26 */
	c = GG ( c, d, a, b, x[ 3], S23, 0xf4d50d87); /* 27 */
	b = GG ( b, c, d, a, x[ 8], S24, 0x455a14ed); /* 28 */
	a = GG ( a, b, c, d, x[13], S21, 0xa9e3e905); /* 29 */
	d = GG ( d, a, b, c, x[ 2], S22, 0xfcefa3f8); /* 30 */
	c = GG ( c, d, a, b, x[ 7], S23, 0x676f02d9); /* 31 */
	b = GG ( b, c, d, a, x[12], S24, 0x8d2a4c8a); /* 32 */

	/* Round 3 */
	a = HH ( a, b, c, d, x[ 5], S31, 0xfffa3942); /* 33 */
	d = HH ( d, a, b, c, x[ 8], S32, 0x8771f681); /* 34 */
	c = HH ( c, d, a, b, x[11], S33, 0x6d9d6122); /* 35 */
	b = HH ( b, c, d, a, x[14], S34, 0xfde5380c); /* 36 */
	a = HH ( a, b, c, d, x[ 1], S31, 0xa4beea44); /* 37 */
	d = HH ( d, a, b, c, x[ 4], S32, 0x4bdecfa9); /* 38 */
	c = HH ( c, d, a, b, x[ 7], S33, 0xf6bb4b60); /* 39 */
	b = HH ( b, c, d, a, x[10], S34, 0xbebfbc70); /* 40 */
	a = HH ( a, b, c, d, x[13], S31, 0x289b7ec6); /* 41 */
	d = HH ( d, a, b, c, x[ 0], S32, 0xeaa127fa); /* 42 */
	c = HH ( c, d, a, b, x[ 3], S33, 0xd4ef3085); /* 43 */
	b = HH ( b, c, d, a, x[ 6], S34,  0x4881d05); /* 44 */
	a = HH ( a, b, c, d, x[ 9], S31, 0xd9d4d039); /* 45 */
	d = HH ( d, a, b, c, x[12], S32, 0xe6db99e5); /* 46 */
	c = HH ( c, d, a, b, x[15], S33, 0x1fa27cf8); /* 47 */
	b = HH ( b, c, d, a, x[ 2], S34, 0xc4ac5665); /* 48 */

	/* Round 4 */
	a = II ( a, b, c, d, x[ 0], S41, 0xf4292244); /* 49 */
	d = II ( d, a, b, c, x[ 7], S42, 0x432aff97); /* 50 */
	c = II ( c, d, a, b, x[14], S43, 0xab9423a7); /* 51 */
	b = II ( b, c, d, a, x[ 5], S44, 0xfc93a039); /* 52 */
	a = II ( a, b, c, d, x[12], S41, 0x655b59c3); /* 53 */
	d = II ( d, a, b, c, x[ 3], S42, 0x8f0ccc92); /* 54 */
	c = II ( c, d, a, b, x[10], S43, 0xffeff47d); /* 55 */
	b = II ( b, c, d, a, x[ 1], S44, 0x85845dd1); /* 56 */
	a = II ( a, b, c, d, x[ 8], S41, 0x6fa87e4f); /* 57 */
	d = II ( d, a, b, c, x[15], S42, 0xfe2ce6e0); /* 58 */
	c = II ( c, d, a, b, x[ 6], S43, 0xa3014314); /* 59 */
	b = II ( b, c, d, a, x[13], S44, 0x4e0811a1); /* 60 */
	a = II ( a, b, c, d, x[ 4], S41, 0xf7537e82); /* 61 */
	d = II ( d, a, b, c, x[11], S42, 0xbd3af235); /* 62 */
	c = II ( c, d, a, b, x[ 2], S43, 0x2ad7d2bb); /* 63 */
	b = II ( b, c, d, a, x[ 9], S44, 0xeb86d391); /* 64 */

	state[0] +=a;
	state[1] +=b;
	state[2] +=c;
	state[3] +=d;

    }

    function init() {
	count[0]=count[1] = 0;
	state[0] = 0x67452301;
	state[1] = 0xefcdab89;
	state[2] = 0x98badcfe;
	state[3] = 0x10325476;
	for (i = 0; i < digestBits.length; i++)
	    digestBits[i] = 0;
    }

    function update(b) { 
	var index,i;
	
	index = and(shr(count[0],3) , 0x3f);
	if (count[0]<0xffffffff-7) 
	  count[0] += 8;
        else {
	  count[1]++;
	  count[0]-=0xffffffff+1;
          count[0]+=8;
        }
	buffer[index] = and(b,0xff);
	if (index  >= 63) {
	    transform(buffer, 0);
	}
    }

    function finish() {
	var bits = new array(8);
	var	padding; 
	var	i=0, index=0, padLen=0;

	for (i = 0; i < 4; i++) {
	    bits[i] = and(shr(count[0],(i * 8)), 0xff);
	}
        for (i = 0; i < 4; i++) {
	    bits[i+4]=and(shr(count[1],(i * 8)), 0xff);
	}
	index = and(shr(count[0], 3) ,0x3f);
	padLen = (index < 56) ? (56 - index) : (120 - index);
	padding = new array(64); 
	padding[0] = 0x80;
        for (i=0;i<padLen;i++)
	  update(padding[i]);
        for (i=0;i<8;i++) 
	  update(bits[i]);

	for (i = 0; i < 4; i++) {
	    for (j = 0; j < 4; j++) {
		digestBits[i*4+j] = and(shr(state[i], (j * 8)) , 0xff);
	    }
	} 
    }

/* End of the MD5 algorithm */

function hexa(n) {
 var hexa_h = "0123456789abcdef";
 var hexa_c=""; 
 var hexa_m=n;
 for (hexa_i=0;hexa_i<8;hexa_i++) {
   hexa_c=hexa_h.charAt(Math.abs(hexa_m)%16)+hexa_c;
   hexa_m=Math.floor(hexa_m/16);
 }
 return hexa_c;
}


var ascii="01234567890123456789012345678901" +
          " !\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ"+
          "[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~";

function MD5(entree) 
{
 var l,s,k,ka,kb,kc,kd;

 init();
 for (k=0;k<entree.length;k++) {
   l=entree.charAt(k);
   update(ascii.lastIndexOf(l));
 }
 finish();
 ka=kb=kc=kd=0;
 for (i=0;i<4;i++) ka+=shl(digestBits[15-i], (i*8));
 for (i=4;i<8;i++) kb+=shl(digestBits[15-i], ((i-4)*8));
 for (i=8;i<12;i++) kc+=shl(digestBits[15-i], ((i-8)*8));
 for (i=12;i<16;i++) kd+=shl(digestBits[15-i], ((i-12)*8));
 s=hexa(kd)+hexa(kc)+hexa(kb)+hexa(ka);
 return s; 
}