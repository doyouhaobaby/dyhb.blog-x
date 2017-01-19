/**
 * 从PHP 中下载语言包
 *
 * @access private
 * @param string sLangName 语言名字
 * @param string sPackageName 语言包名字
 * @returns string
 */
Dyhb.Lang.LoadPackage_=function(sLangName,sPackageName) {
	if(typeof(this._arrLangPackage[sLangName])=='undefined'){/* 没有设置语言名字 */
		this._arrLangPackage[sLangName]={} ;
	}
	if(typeof(this._arrLangPackage[sLangName][sPackageName])=='undefined'){/* 没有设置语言包 */
		sSentensesJson=Dyhb.Soap.Ssmi.Invoke('LangPackageForClient','getLangPackage',[sLangName,sPackageName]) ;/* 下载语言包 */
		if(sSentensesJson){/* 载入 */
			sScript='Dyhb.Lang._arrLangPackage[sLangName][sPackageName]='+sSentensesJson ;
			eval(sScript) ;
		}
		else{
			Dyhb.Lang._arrLangPackage[sLangName][sPackageName]={} ;
		}
	}
};
