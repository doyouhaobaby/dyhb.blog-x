/**
 * 模拟框架URL 生成函数
 *
 * < 本函数用于特殊请求,用于框架加载数据
 *   请在模板中预先调用 App::U()初始化相关参数
 *   U('admin.php://module/action?param1=1&param1=2',new Array('extraparam=hello','extraparam2=hello2','extraparam3=hello3'));
 *   U('module/action?param1=1&param1=2',new Array('extraparam=hello','extraparam2=hello2','extraparam3=hello3'));
 * >
 * 
 * @param string sUrl 初始化URL
 * @param arrParams arrParams 附件参数
 * @param bool bRedirect 是否返回
 * @param bool bSuffix 是否加上静态化后缀
 * @returns string
 */
Dyhb.Url.U=function(sUrl,arrParams,bRedirect,bSuffix){
	var sWebsite=_ROOT_+'/';
	arrParams=arrParams || new Array();/* 初始化参数 */
	bRedirect=bRedirect || false;
	bSuffix=bSuffix || false;
	var sLeave=sExtra='';
	arrUrl=sUrl.split('?');/* 提取URL中的 额外参数 */
	if(arrUrl[1]){
		sExtra=arrUrl[1]; 
	}
	sLeave=arrUrl[0];
	var sEnter='';/* 提取入口 */
	if(sLeave.indexOf('://')<0){
		sEnter=_ENTER_;
	}
	else{
		arrUrl=sLeave.split('://');
		if(arrUrl[0]=='@'){
			sEnter=_ENTER_;
			sLeave=arrUrl[1];
		}
		else if(!arrUrl[1]){
			sEnter=_ENTER_;
			sLeave=arrUrl[0];
		}
		else{
			sEnter=arrUrl[0];
			sLeave=arrUrl[1];
		}
	}
	arrUrl=sLeave.split('/');/* 提取模块和方法 */
	var sModule=sAction='';
	if(!arrUrl[1]){
		if(arrUrl[0]){
			sModule=_MODULE_NAME_; 
			sAction=arrUrl[0]; 
		}
		else{
			sModule=_MODULE_NAME_;
			sAction=_ACTION_NAME_; 
		}
		
	}
	else{
		if(arrUrl[0]=='@'){
			sModule=_MODULE_NAME_;
		}
		else{
			sModule=arrUrl[0];
		}
		if(arrUrl[1]=='@'){
			sAction=_ACTION_NAME_;
		}
		else{
			sAction=arrUrl[1];
		} 
	}
	sWebsite=sWebsite+sEnter+'?'+_CONTROL_VAR_NAME_+'='+sModule+'&'+_ACTION_VAR_NAME_+'='+sAction;
	if(sExtra){/* sUrl 额外参数*/
		sWebsite+='&'+sExtra;
	}
	if(arrParams&&arrParams.length>0){/* 数组 额外参数 */
		arrParams=arrParams.join('&');
		sWebsite +='&' + arrParams;
	}
	if(bSuffix){
		sWebsite +=_URL_HTML_SUFFIX_;
	}
	if(bRedirect){
		window.location.href=sWebsite;
	}
	else{
		return sWebsite;
	}
};
Dyhb.U=Dyhb.Url.U;

/**
 * 模拟框架URL 生成函数
 *
 * < 本函数用于特殊请求,用于框架加载数据
 *   请在模板中预先调用 App::U2()初始化相关参数
 *   U('app://module/action?param1=1&param1=2',new Array('extraparam=hello','extraparam2=hello2','extraparam3=hello3'));
 *   U('module/action?param1=1&param1=2',new Array('extraparam=hello','extraparam2=hello2','extraparam3=hello3'));
 * >
 * 
 * @param string sUrl 初始化URL   
 * @param arrParams arrParams 附件参数
 * @param bool bRedirect 是否返回
 * @param bool bSuffix 是否加上静态化后缀
 * @returns string
 */
Dyhb.Url.U2=function(sUrl,arrParams,bRedirect,bSuffix){
	var sWebsite=_ROOT_+'/index.php';
	arrParams=arrParams || new Array();/* 初始化参数 */
	bRedirect=bRedirect || false;
	bSuffix=bSuffix || false;
	var sLeave=sExtra='';
	arrUrl=sUrl.split('?');/* 提取URL中的 额外参数 */
	if(arrUrl[1]){
		sExtra=arrUrl[1]; 
	}
	sLeave=arrUrl[0];
	var sApp='';/* 提取项目 */
	if(sLeave.indexOf('://')<0){
		sApp=_APP_NAME_;
	}
	else{
		arrUrl=sLeave.split('://');
		if(arrUrl[0]=='@'){
			sApp=_APP_NAME_;
			sLeave=arrUrl[1];
		}
		else if(!arrUrl[1]){
			sApp=_APP_NAME_;
			sLeave=arrUrl[0];
		}
		else{
			sApp=arrUrl[0];
			sLeave=arrUrl[1];
		}
	}
	arrUrl=sLeave.split('/');/* 提取模块和方法 */
	var sModule=sAction='';
	if(!arrUrl[1]){
		if(arrUrl[0]){
			sModule=_MODULE_NAME_; 
			sAction=arrUrl[0]; 
		}
		else{
			sModule=_MODULE_NAME_;
			sAction=_ACTION_NAME_; 
		}
		
	}
	else{
		if(arrUrl[0]=='@'){
			sModule=_MODULE_NAME_;
		}
		else{
			sModule=arrUrl[0];
		}
		if(arrUrl[1]=='@'){
			sAction=_ACTION_NAME_;
		}
		else{
			sAction=arrUrl[1];
		} 
	}
	sWebsite=sWebsite+'?'+_APP_VAR_NAME_+'='+sApp+'&'+_CONTROL_VAR_NAME_+'='+sModule+'&'+_ACTION_VAR_NAME_+'='+sAction;
	if(sExtra){/* sUrl 额外参数*/
		sWebsite+='&'+sExtra;
	}
	if(arrParams&&arrParams.length>0){/* 数组 额外参数*/
		arrParams=arrParams.join('&');
		sWebsite +='&' + arrParams;
	}
	if(bSuffix){
		sWebsite+=_URL_HTML_SUFFIX_;
	}
	if(bRedirect){
		window.location.href=sWebsite;
	}
	else{
		return sWebsite;
	}
};
Dyhb.U2=Dyhb.Url.U2;
