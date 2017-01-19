/* 注册数据处理*/
function memberSubmit(){
	$("#member-submit").attr("disabled", "disabled");
	$("#member-submit").val(D.L('修改资料中'));
	Dyhb.AjaxSubmit('memberform',__member_update__,'',complete); 
}

function complete(data,status){
	$("#member-submit").attr("disabled", false);
	$("#member-submit").val(D.L('修改资料'));
}

/* 资料验证*/
$(document).ready(function(){
	var validator=$("#memberform").validate({
		rules: {
			user_nikename: {
				maxlength: 50
			},
			user_email: {
				required: true,
				email: true,
				maxlength: 150,
				remote: __check_email__
			},
		},
		messages: {
			user_nikename: {
				maxlength: jQuery.format(D.L("用户昵称最多 {0} 字符"))			
			},
			user_email: {
				required: D.L("E-mail 地址不能为空"),
				email: D.L("请输入一个正确的E-mail 地址"),
				maxlength: jQuery.format(D.L("E-mail 地址最多 {0} 字符")),
				remote: jQuery.format(D.L("{0} 该E-mail 地址已经被占用"))
			}
		},
		errorPlacement: function(error, element){
			if(element.is(":radio")){
				error.appendTo(element.parent().next().next());
			}
			else if(element.is(":checkbox")){
				error.appendTo(element.next());
			}
			else{
				error.appendTo(element.parent().next());
			}
		},
		submitHandler: function(){
			memberSubmit();
		},
		success: function(label){
			label.html("&nbsp;").addClass("checked");
		}
	});
});
