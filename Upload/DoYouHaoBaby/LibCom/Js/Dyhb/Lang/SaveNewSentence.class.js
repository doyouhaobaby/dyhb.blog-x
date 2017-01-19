/**
 * 保存一条新语句
 *
 * @param string sSentenceKey 语句Key
 * @param string sSentence 语句
 * @param string sLangName 语言名字
 * @param string sPackageName 语言包名字
 * @returns
 */
Dyhb.Lang.SaveNewSentence=function(sSentenceKey,sSentence,sLangName,sPackageName/*=null*/) {
	sResult=Dyhb.Soap.Ssmi.Invoke('LangPackageForClient','setNewSentence',[sSentenceKey,sSentence,sLangName,sPackageName]);
	if(sResult=='1'){
		return true;
	}
	throw new Error(sResult);
};
