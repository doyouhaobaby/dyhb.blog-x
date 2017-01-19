/**
 * 获取DoYouHaoBaby Framework通信接口 Url接口
 *
 * @param string sClassName DoYouHaoBaoBaby Framework 通信接口类
 * @param string sMethodName DoYouHaoBaoBaby Framework 通信接口方法
 * @param array arrParams 请求数组参数
 */
Dyhb.Soap.Ssmi.GetRequestURL=function(sClassName,sMethodName,arrParams){
	if(typeof(arrParams)=='undefined'){/* 参数整理 */
		arrParams=[];
	}
	if(!(arrParams instanceof Array)){/* 参数必须是数组，否则抛出异常 */
		throw new Error('arrParams must be a Array!');
	};
	sUrl='./'+Dyhb.Soap.Ssmi._sAppPath+'?c=ssmi&a=index&class='+sClassName+'&method='+sMethodName;/* API */
	for(nI=0;nI<arrParams.length;nI++){/* 参数 */
		sUrl+='&param_soap'+(nI+1)+'='+arrParams[nI];
	};
	return sUrl;
};
