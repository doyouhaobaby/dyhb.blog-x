/**
 * 设置cookie的值，用encodeURIComponent进行编码
 *
 * < 1. 注意：该方法会对cookie值进行encodeURIComponent编码。如果想设置cookie源字符串，请使用setRaw方法。
 *   2. options参数包括：
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
Dyhb.Cookie.Set=function(key, value, options) {
	Dyhb.Cookie.SetRaw(key, encodeURIComponent(value), options);
};
