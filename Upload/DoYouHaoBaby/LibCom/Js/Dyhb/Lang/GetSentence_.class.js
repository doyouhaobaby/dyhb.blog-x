/**
 * 向PHP 传输一条新语句
 *
 * @access private
 * @param string sSentence 语句
 * @param string sLangName 语言名字
 * @param string sPackageName 语言包名字
 * @returns string
 */
Dyhb.Lang.GetSentence_=function(sSentence,sLangName,sPackageName) {
	if(typeof(Dyhb.Lang._arrLangPackage[ sLangName ])=='undefined' || typeof(Dyhb.Lang._arrLangPackage[sLangName][sPackageName])=='undefined'){/* 确保语言包已被载入 */
		Dyhb.Lang.LoadPackage_(sLangName,sPackageName);
	}
	sSentenceKey=Dyhb.Lang.MakeSentenceKey(sSentence);
	if(typeof(Dyhb.Lang._arrLangPackage[sLangName][sPackageName][sSentenceKey])=='undefined'){/* 保存 语言包中不存在的 新语句 */
		Dyhb.Lang._arrLangPackage[sLangName][sPackageName][sSentenceKey]=sSentence;
		Dyhb.Lang.SaveNewSentence(sSentenceKey,sSentence,sLangName,sPackageName);/* 传递给 PHP 保存 */
	}
	return Dyhb.Lang._arrLangPackage[sLangName][sPackageName][sSentenceKey];
};
