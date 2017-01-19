/**
 * 发送一个post请求
 * 
 * @param string sUrl 发送请求的url地址
 * @param string sData 发送的数据
 * @param Function [onsuccess] 请求成功之后的回调函数，function(XMLHttpRequest oXmlHttp, string responseText)
 * @meta standard
 * @returns {XMLHttpRequest} 发送请求的XMLHttpRequest对象
 */
Dyhb.Ajax.Post=function(sUrl, sData, onsuccess){
	return Dyhb.Ajax.Request(
		sUrl,{
			'onsuccess': onsuccess,
			'method': 'POST',
			'data': sData
		}
	);
};
