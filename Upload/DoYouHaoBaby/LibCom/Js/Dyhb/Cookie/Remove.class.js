/**
 * 删除cookie的值
 * 
 * @param string key 需要删除Cookie的键名
 * @param Object options 需要删除的cookie对应的 path domain 等值
 */
Dyhb.Cookie.Remove=function(key, options) {
	options=options || {};
	options.expires=new Date(0);
	Dyhb.Cookie.SetRaw(key, '', options);
};
