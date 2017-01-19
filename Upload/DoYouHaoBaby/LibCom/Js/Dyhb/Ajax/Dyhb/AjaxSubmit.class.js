/**
 * 表单提交ajax
 *
 * @param string form 表单ID
 * @param string url Ajax请求url
 * @param string target 返回的消息的div id值
 * @param function response 回调函数
 * @param string sTips Ajax加载消息
 */
Dyhb.Ajax.Dyhb.AjaxSubmit=function(sForm,sUrl,sTarget,Response,sTips){
	sUrl=(sUrl===undefined || sUrl=='' || sUrl===null)? Dyhb.Ajax.Dyhb.Options['url']: sUrl; /* 请求URL */
	if(sTarget===undefined || sTarget=='' || sTarget===null){ /* 消息DIV id */
		Dyhb.Ajax.Dyhb.TipTarget=(Dyhb.Ajax.Dyhb.Options['target'])? Dyhb.Ajax.Dyhb.Options['target'] : Dyhb.Ajax.Dyhb.TipTarget;
	}
	else{
		Dyhb.Ajax.Dyhb.TipTarget=sTarget;
	}
	Dyhb.Ajax.Dyhb.Response=(Response===undefined||Response==''||Response===null)? Dyhb.Ajax.Dyhb.Response : Response;/* 成功后回调函数 */
	if(sTips===undefined||sTips==''||sTips===null){
		sTips=(Dyhb.Ajax.Dyhb.Options['tips'])? Dyhb.Ajax.Dyhb.Options['tips']: Dyhb.Ajax.Dyhb.UpdateTips;
	}
	if(Dyhb.Ajax.Dyhb.ShowTip){
		Dyhb.Ajax.Dyhb.Loading(Dyhb.Ajax.Dyhb.TipTarget,sTips);
	}
	var oSubmitFrom=document.getElementById(sForm);
	oSubmitFrom.action=sUrl;
	arrAjaxOption={
		async: true,
		onsuccess:function(xhr,responseText){
			Dyhb.Ajax.Dyhb.AjaxResponse(xhr,Dyhb.Ajax.Dyhb.TipTarget,Response);
		},
		onfailure:function(xhr){ alert('Request Error!');}
	};
	Dyhb.Ajax.Form(oSubmitFrom, arrAjaxOption);/* 提交Ajax */
};
Dyhb.AjaxSubmit=Dyhb.Ajax.Dyhb.AjaxSubmit;
