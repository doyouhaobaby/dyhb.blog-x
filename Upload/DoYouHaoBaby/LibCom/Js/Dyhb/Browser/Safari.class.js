/**
 * 判断是否为safari浏览器, 支持ipad
 */
(function(){
	Dyhb.Browser.Safari=/(\d+\.\d)?(?:\.\d)?\s+safari\/?(\d+\.\d+)?/i.test(Dyhb.Browser.UserAgent) && !/chrome/i.test(Dyhb.Browser.UserAgent);
})();
