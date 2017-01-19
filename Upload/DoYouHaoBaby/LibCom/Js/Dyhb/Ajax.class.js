/**
 * Ajax请求
 *
 * < 对XMLHttpRequest请求的封装。>
 * < onfailure 请求失败的全局事件，function(XMLHttpRequest oXmlHttp) >
 * < onbeforerequest 请求发送前触发的全局事件，function(XMLHttpRequest oXmlHttp) >
 * < onStatusCode 状态码触发的全局事件，function( XMLHttpRequest oXmlHttp ),注意：onStatusCode中的StatusCode需用404,320等状态码替换。如on404 >
 */
Dyhb.Ajax=Dyhb.Ajax || {};
