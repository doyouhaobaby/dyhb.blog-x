/**
 * DoYouHaoBaby 通过SOAP 通信接口与框架内部交互，并获取JS
 *
 * @param string sClassName 类名字
 * @returns bool
 */
Dyhb.Script.Import=function(sClassName){
	sSrc=Dyhb.Soap.Ssmi.GetRequestURL('Package','importJsClass', [sClassName]);/* 尝试获取 */
	this.Load(sSrc);
	var bSuc=false;/* 判断是否已经载入了Javascript文件 */
	eval("bSuc =(typeof("+sClassName+")=='undefined')");
	return bSuc;
};
