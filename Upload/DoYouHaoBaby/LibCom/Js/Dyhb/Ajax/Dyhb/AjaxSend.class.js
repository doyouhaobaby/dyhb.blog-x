/**
 * 表单提交ajax
 *
 * @param string url Ajax请求url
 * @param string target 返回的消息的div id值
 * @param function response 回调函数 
 * @param string sTips  Ajax加载消息
 * @param string sType Ajax发送类型[get/post,默认为get]
 */
Dyhb.Ajax.Dyhb.AjaxSend=function(sUrl,Params,sTarget,Response,sTips,sType){
	sUrl =(sUrl === undefined || sUrl==''||sUrl===null)? Dyhb.Ajax.Dyhb.Options['url']: sUrl; /* 请求URL */
	if(sTarget === undefined || sTarget =='' || sTarget ===null){ /* 消息DIV id */
		Dyhb.Ajax.Dyhb.TipTarget =(Dyhb.Ajax.Dyhb.Options['target'])? Dyhb.Ajax.Dyhb.Options['target'] : Dyhb.Ajax.Dyhb.TipTarget;
	}
	else{
		Dyhb.Ajax.Dyhb.TipTarget=sTarget;
	}
	Dyhb.Ajax.Dyhb.Response =(Response === undefined||Response==''||Response===null)? Dyhb.Ajax.Dyhb.Response : Response;/* 成功后回调函数 */
	if(sTips === undefined||sTips==''||sTips===null){
		sTips =(Dyhb.Ajax.Dyhb.Options['tips'])? Dyhb.Ajax.Dyhb.Options['tips']: Dyhb.Ajax.Dyhb.UpdateTips;
	}
	if(Dyhb.Ajax.Dyhb.ShowTip){
		Dyhb.Ajax.Dyhb.Loading(Dyhb.Ajax.Dyhb.TipTarget,sTips);
	}
	if(Params === undefined || Params==''||Params===null){
		Params =(Dyhb.Ajax.Dyhb.Options['var'])?Dyhb.Ajax.Dyhb.Options['var']:'ajax=1';
	}
	if(sType=='post'){
		Dyhb.Ajax.Post(sUrl,
			Params,
			function(xhr,responseText){
				Dyhb.Ajax.Dyhb.AjaxResponse(xhr,Dyhb.Ajax.Dyhb.TipTarget,Response);
			}
		);
 	}
	else{
		Dyhb.Ajax.Get(sUrl,
			Params,
			function(xhr,responseText){
				Dyhb.Ajax.Dyhb.AjaxResponse(xhr,Dyhb.Ajax.Dyhb.TipTarget,Response);
		}
		);
	}
};
Dyhb.AjaxSend=Dyhb.Ajax.Dyhb.AjaxSend;
