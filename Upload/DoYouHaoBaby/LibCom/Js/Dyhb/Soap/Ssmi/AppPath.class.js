/**
 * 框架语言包入口请求
 * 需要在框架Js库使用前定义
 */
if(typeof(__DYHB_JS_LANG_ENTER__)!='undefined' && __DYHB_JS_LANG_ENTER__!=''){
	Dyhb.Soap.Ssmi._sAppPath=__DYHB_JS_LANG_ENTER__;
}
else{
	Dyhb.Soap.Ssmi._sAppPath='index.php';
}

/**
 * DoYouHaoBaby Framework入口文件
 *
 * @param string sAppPath 框架入口文件
 * @returns 文本结果
 */
Dyhb.Soap.Ssmi.AppPath=function(sAppPath){
	if(typeof(sAppPath)!='undefined' && sAppPath!='')
		Dyhb.Soap.Ssmi._sAppPath=sAppPath;
};
