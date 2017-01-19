/**
 * 浏览器判断
 */
Dyhb.Browser=Dyhb.Browser || {};

/* 浏览器的 UserAgent */
Dyhb.Browser.UserAgent=navigator.userAgent.toLowerCase();

/* 浏览器的版本 */
Dyhb.Browser.Version=( Dyhb.Browser.UserAgent.match( /.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/ ) || [] )[1];
