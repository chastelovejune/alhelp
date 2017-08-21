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
    $.post("{:U('/home/memberPost/update')}",{id:id,is_top:is_top},function(){
      window.location.reload();
    })
  })

  $(".member_post .is_hot").click(function(){
    var val = $(this).html();
    var id = $(this).attr("data-id");
    var is_hot = (val.indexOf("取消") == -1) ? 1 :0;
    $.post("{:U('/home/memberPost/update')}",{id:id,is_hot:is_hot},function(){
      window.location.reload();
    })
  })

  $(".member_post .delete").click(function(){
      var id = $(this).attr("data-id");
      $(this).parent().parent().parent().remove();
    $.post("{:U('/home/memberPost/curdDelete')}",{id:id},function(){
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
    if (!$(this).attr("data-href") || !$(this).attr("data-id")) {
      return false;
    }
     $(this).parent().html('<span class="applyed">已申请试听</span>');
     $.post($(this).attr("data-href"),{"open_class_id":$(this).attr("data-id")},function(d){

     })
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

function delete_comment(cid,t){
  var dl = $(t).parent().parent().parent();
  if (dl.hasClass("f_clearfix")) {
    //外面的pc
    dl.parent().remove();
  }else{
    dl.remove();
  }
  $.post("{:U('/home/comment/delete')}",{id:cid},function(){
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
	var html = "";
	for(var key in data){
		html = html + '<option value='+key+' >'+data[key]+'</option>';
	}
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
  var li = $(t).parent().parent();
  $("#show_yue").find('[name=name]').html(li.find('[name=name]').html());
  $("#show_yue").find('[name=set_time]').html(li.find('[name=set_time]').html());
  //$("#show_yue").find('[name=target]').html(li.find('[name=target]').val());
  $("#show_yue").find('[name=listen_type]').html(li.find('[name=listen_type]').val());
  $("#show_yue").show();
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
function ok_yue(id){
  $.post("{:U('/member/plan/update')}",{id:id,status:1},function(){
    window.location.reload();
  })
}
function cancel_yue(id){
  $.post("{:U('/member/plan/update')}",{id:id,status:2},function(){
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
  var html = "<form id='go_upload' action='{:U('/home/common/upload')}' method='post'><input type='file' name='image'/></form>";
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
  var html = '<div class="modal fade modal_paypwd" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">\
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
    if (paypwd!=pwd) {
      alert("密码错误");
      return false;
    }
    suc();
  })
}
function handle_openClass_praise(t,open_class_id){
  var n = parseInt($(t).find("span").html());
  if ($(t).find("img").attr("src").indexOf("unzan")==-1) {
    $(t).find("span").html(n+1);
    $(t).find("img").attr("src","/images/icons/unzan.png");
    $.post("{:U('/home/openClass/praise')}",{id:open_class_id},function(){

    })
  }else{
    $(t).find("span").html(n-1);
    $(t).find("img").attr("src","/images/icons/zan.png");
    $.post("{:U('/home/openClass/unpraise')}",{id:open_class_id},function(){

    })
  }
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