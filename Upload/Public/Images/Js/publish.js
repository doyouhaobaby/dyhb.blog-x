/* 投稿数据处理*/
function registerSubmit(){
	$("#publish-submit").attr("disabled", "disabled");
	$("#publish-submit").val(D.L('投稿中'));
	Dyhb.AjaxSubmit('publishform',D.U('publish/insert?id='+$('#id').val()),'',complete); 
}

function complete(data,status){
	$("#publish-submit").attr("disabled", false);
	$("#publish-submit").val(D.L('投稿'));
	if(status==1){
		window.location=data.url;
	}
}

/* 投稿表单验证 */
$(document).ready(function(){
	var validator=$("#publishform").validate({
		rules: {
			blog_title: {
				required: true,
				maxlength: 300
			},
			blog_content: "required",
			blog_urlname: {
				maxlength: 300, 
				remote: __check_urlname__
			},
			blog_from: {
				maxlength: 25
			},
			blog_fromurl: {
				maxlength: 300
			},
			publish_terms: "required"
		},
		messages: {
			blog_title: {
				required: D.L("请输入文章标题"),
				maxlength: jQuery.format(D.L("文章标题最多为 {0} 个字符")),
			},
			blog_content: D.L("请输入文章内容"),
			blog_urlname: {
				maxlength: jQuery.format(D.L("文章别名最多为 {0} 个字符")), 
				remote: D.L("日志别名重复")
			},
			blog_from: {
				maxlength: jQuery.format(D.L("文章来源最多为 {0} 个字符"))
			},
			blog_fromurl: {
				maxlength: jQuery.format(D.L("文章来源URL 最多为 {0} 个字符"))
			},
			publish_terms: D.L("你必须同意我们的投稿条款")
		},
		errorPlacement: function(error, element){
			error.html(error.html());
			$("#errorContainer").css('display','block');
			error.appendTo("#errorContainer");
		},
		submitHandler: function(){
			registerSubmit();
		},
		success: function(label){
			label.html("&nbsp;").addClass("checked");
		}
	});
});
