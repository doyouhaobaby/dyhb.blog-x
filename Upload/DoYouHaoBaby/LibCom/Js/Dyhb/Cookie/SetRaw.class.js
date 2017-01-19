/**
 * 设置cookie的值，不对值进行编码
 * 
 * < options参数包括：
 *   path:cookie路径
 *   expires:cookie过期时间，Number型，单位为毫秒。
 *   domain:cookie域名
 *   secure:cookie是否安全传输 >
 *
 * < @config string [path] cookie路径
 *   @config Date|number [expires] cookie过期时间,如果类型是数字的话, 单位是毫秒
 *   @config string [domain] cookie域名
 *   @config string [secure] cookie是否安全传输 >
 *
 * @param string key 需要设置Cookie的键名
 * @param string value 需要设置Cookie的值
 * @param Object [options] 设置Cookie的其他可选参数
 */
Dyhb.Cookie.SetRaw=function(key, value, options) {
	if(!Dyhb.Cookie.IsValidKey_(key)) {
		return;
	}
	options=options || {};
	var expires=options.expires;/* 计算cookie过期时间 */
	if('number' == typeof options.expires) {
		expires=new Date();
		expires.setTime(expires.getTime() + options.expires);
	}
	document.cookie =
		key + "=" + value
		+(options.path ? "; path=" + options.path : "")
		+(expires ? "; expires=" + expires.toGMTString() : "")
		+(options.domain ? "; domain=" + options.domain : "")
		+(options.secure ? "; secure" : ''); 
};
