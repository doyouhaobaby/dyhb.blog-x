/**
 * 发送一个get请求
 *
 * @param string sUrl 发送请求的url地址
 * @param string sData 发送的数据
 * @param Function [onsuccess] 请求成功之后的回调函数，function(XMLHttpRequest oXmlHttp, string responseText)
 * @returns {XMLHttpRequest} 发送请求的XMLHttpRequest对象
 */
Dyhb.Ajax.Get=function(sUrl,sData,onsuccess){
	return Dyhb.Ajax.Request(sUrl,{'onsuccess': onsuccess,'data':sData});
};
