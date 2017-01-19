/**
 * 获取cookie的值，不对值进行解码
 * 
 * @param string key 需要获取Cookie的键名		 
 * @returns string|null 获取的Cookie值，获取不到时返回null
 */
Dyhb.Cookie.GetRaw=function(key) {
	if(Dyhb.Cookie.IsValidKey_(key)) {
		var reg=new RegExp("(^|)" + key + "=([^;]*)(;|\x24)"),
			result=reg.exec(document.cookie);
		if(result) {
			return result[2] || null;
		}
	}
	return null;
};
