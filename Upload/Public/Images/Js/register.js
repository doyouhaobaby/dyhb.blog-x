/* 注册数据处理 */
function registerSubmit(){
	$("#signup-submit").attr("disabled", "disabled");
	$("#signup-submit").val(D.L('注册中'));
	Dyhb.AjaxSubmit('signupform',__register_insert__,'',complete); 
};

function complete(data,status){
	$("#signup-submit").attr("disabled", false);
	$("#signup-submit").val(D.L('注册'));
	if(status==1){
		window.location=__admin_login__;
	}
};

/* 注册验证 */
$(document).ready(function(){
	var validator=$("#signupform").validate({
		rules: {
			user_name: {
				required: true,
				maxlength: 50, 
				remote: __check_user__
			},
			user_nikename: {
				maxlength: 50
			},
			user_password: {
				required: true,
				minlength: 6,
				maxlength: 32
			},
			user_password_confirm: {
				required: true,
				minlength: 6,
				maxlength: 32,
				equalTo: "#user-password"
			},
			user_email: {
				required: true,
				email: true,
				maxlength: 150,
				remote: __check_email__
			},
			user_terms: "required"
		},
		messages: {
			user_name: {
				required: D.L("请输入你的注册用户名"),
				maxlength: jQuery.format(D.L("注册用户名最多 {0} 字符")),
				remote: jQuery.format(D.L("{0} 该用户已经被占用了"))
			},
			user_nikename: {
				maxlength: jQuery.format(D.L("用户昵称最多 {0} 字符"))			
			},
			user_password: {
				required: D.L("请输入你的用户密码"),
				minlength: jQuery.format(D.L("用户密码最少 {0} 字符")),
				maxlength: jQuery.format(D.L("用户密码最多 {0} 字符"))
			},
			user_password_confirm: {
				required: D.L("请输入你的确认密码"),
				minlength: jQuery.format(D.L("确认密码最少 {0} 字符")),
				maxlength: jQuery.format(D.L("确认密码最多 {0} 字符")),
				equalTo: D.L("确认密码不准确，请仔细填写")
			},
			user_email: {
				required: D.L("E-mail 地址不能为空"),
				email: D.L("请输入一个正确的E-mail 地址"),
				maxlength: jQuery.format(D.L("E-mail 地址最多 {0} 字符")),
				remote: jQuery.format(D.L("{0} 该E-mail 地址已经被占用"))
			},
			user_terms: " "
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
			registerSubmit();
		},
		success: function(label){
			label.html("&nbsp;").addClass("checked");
		}
	});
});
