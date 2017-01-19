/**
 * 发送一条语句
 *
 * @param string sSentence 语句
 * @param string sPackageName 语言包名字
 * @param string sLangName 语言名字
 * @returns
 */
Dyhb.Lang.Sentence=function(sSentence,sPackageName,sLangName /*=null*/) {
	if(typeof(sSentence)!='string'){/* 语句只能是字符串 */
		throw new Error('sSentence must be a string!');
	}
	if(typeof(sLangName)=='undefined' || sLangName==null){/* sLangName 缺省使用当前语言 */
		sLangName=Dyhb.Lang.GetCurrentLang();
	}
	if(typeof(sPackageName)=='undefined' || sPackageName==null){/* sPackageName 缺省使用当前语言包 */
		sPackageName=Dyhb.Lang.GetCurrentPackageName();
	}
	sSentenceForThisLang=Dyhb.Lang.GetSentence_(sSentence,sLangName,sPackageName);/* 取得语言包中的语句 */
	arrFormatArgs=[];/* 带入参数 */
	for(nIdx=3; nIdx<arguments.length; nIdx++){
		arrFormatArgs.push(arguments[nIdx]);
	}
	if(arrFormatArgs.length){
		return Dyhb.String.Format(sSentenceForThisLang,arrFormatArgs);
	}
	else{
		return sSentenceForThisLang;
	}
};
Dyhb.L=Dyhb.Lang.Sentence;
