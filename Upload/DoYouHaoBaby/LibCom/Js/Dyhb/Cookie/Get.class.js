/**
 * 获取cookie的值，用decodeURIComponent进行解码
 * 
 * < 注意：该方法会对cookie值进行decodeURIComponent解码。如果想获得cookie源字符串，请使用getRaw方法。>
 *
 * @param string key 需要获取Cookie的键名
 * @returns string|null cookie的值，获取不到时返回null
 */
Dyhb.Cookie.Get=function(key) {
	var value=Dyhb.Cookie.GetRaw(key);
	if('string' == typeof value) {
		value=decodeURIComponent(value);
		return value;
	}
	return null;
};
