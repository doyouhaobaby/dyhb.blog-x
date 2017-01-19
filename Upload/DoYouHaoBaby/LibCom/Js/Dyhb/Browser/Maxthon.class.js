/**
 * 判断是否为maxthon浏览器
 */
try {
	Dyhb.Browser.Maxthon=/(\d+\.\d)/.test(external.max_version);
}
catch(e){}
