/**
 * 验证字符串是否合法的cookie键名
 * 
 * @param string source 需要遍历的数组
 * @meta standard
 * @return boolean 是否合法的cookie键名
 */
Dyhb.Cookie.IsValidKey_=function(key) {
	return(new RegExp("^[^\\x00-\\x20\\x7f\\(\\)<>@,;:\\\\\\\"\\[\\]\\?=\\{\\}\\/\\u0080-\\uffff]+\x24")).test(key);
};
