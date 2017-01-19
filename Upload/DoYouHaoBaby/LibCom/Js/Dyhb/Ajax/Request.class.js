/**
 * 发送一个ajax请求
 *
 * <!-- 说明 -->
 * < Function [onsuccess] 请求成功时触发，function(XMLHttpRequest oXmlHttp, string responseText)。
 *   Function [onfailure] 请求失败时触发，function(XMLHttpRequest oXmlHttp)。
 *   Function [onbeforerequest] 发送请求之前触发，function(XMLHttpRequest oXmlHttp)。
 *   Function [ on{STATUS_CODE}] 当请求为相应状态码时触发的事件，如on302、on404、on500，function(XMLHttpRequest xhr)。3XX的状态码浏览器无法获取，4xx的，可能因为未知问题导致获取失败。>
 *
 * @param string sUrl 发送请求的url
 * @param Object arrOptions 发送请求的选项参数
 * @returns {XMLHttpRequest} 发送请求的XMLHttpRequest对象
 */
Dyhb.Ajax.Request=function(sUrl,arrOptions){
	arrOptions=arrOptions || {};/* Ajax请求配置 */
	var sData=arrOptions.data || "",/* 需要发送的数据。如果是GET请求的话，不需要这个属性 | string */
		bAsync=!(arrOptions.async===false),/* 是否异步请求。默认为true（异步）| boolean */
		sUsername=arrOptions.username || "",/* 用户名 */
		sPassword=arrOptions.password || "",/* 密码 */
		sMethod=(arrOptions.method || "GET").toUpperCase(),/* 发送方式 ：POST,GET */
		arrHeaders=arrOptions.headers || {},/* 要设置的http request header(HTTP 请求头)*/
		arrEventHandlers={},/* 事件句柄 */
		sKey,oXmlHttp; /* 参数中的键值，XMLHttpRequest对象 */

	/**
	 * readyState发生变更时调用
	 * 
	 * <!--readyState 详解-->
	 * < readyState表示XMLHttpRequest对象的处理状态：
	 *   0:XMLHttpRequest对象还没有完成初始化。
	 *   1:XMLHttpRequest对象开始发送请求。
	 *   2:XMLHttpRequest对象的请求发送完成。
	 *   3:XMLHttpRequest对象开始读取服务器的响应。
	 *   4:XMLHttpRequest对象读取服务器响应结束。
	 *   另：在IE(即Internet Explorer)浏览器中可以不区分大小写，但在其他浏览器中将严格区分大小写。所以为了保证更好的跨浏览器效果，建议采用严格区分大小写的形式。>
	 *
	 * @return void
	 */
	function stateChangeHandler(){
		if(oXmlHttp.readyState==4){
			try {
				var nStat=oXmlHttp.status;
			}
			catch(ex){
				fire('failure');/* 在请求时，如果网络中断，Firefox会无法取得status */
				return;
			}
			fire(nStat);
			if((nStat >=200 && nStat < 300)|| nStat==304 || nStat==1223){
				fire('success');
			}
			else{
				fire('failure');
			}
			
			/*
			 * NOTE: Testing discovered that for some bizarre reason,on Mozilla,the
			 * JavaScript <code>XmlHttpRequest.onreadystatechange</code> handler
			 * function maybe still be called after it is deleted. The theory is that the
			 * callback is cached somewhere. Setting it to null or an empty function does
			 * seem to work properly,though.
			 * 
			 * On IE,there are two problems: Setting onreadystatechange to null(as
			 * opposed to an empty function)sometimes throws an exception. With
			 * particular(rare)versions of jscript.dll,setting onreadystatechange from
			 * within onreadystatechange causes a crash. Setting it from within a timeout
			 * fixes this bug(see issue 1610).
			 * 
			 * End result: *always* set onreadystatechange to an empty function(never to
			 * null). Never set onreadystatechange from within onreadystatechange(always
			 * in a setTimeout()).
			 */
			window.setTimeout(
				function(){
					/* 避免内存泄露. 
					// 由new Function改成不含此作用域链的 Dyhb.Fn.Blank 函数,
					// 以避免作用域链带来的隐性循环引用导致的IE下内存泄露 */
					oXmlHttp.onreadystatechange=Dyhb.Fn.Blank; 
					if(bAsync){
						oXmlHttp=null;
					}
				}
			,0);
		}
	}

	/**
	 * 获取XMLHttpRequest对象
	 * 
	 * @return {XMLHttpRequest} XMLHttpRequest对象
	 */
	function getXmlHttpRequest(){
		var oXmlHttp=null; /* XMLHttpRequest 请求对象 */
		if(window.XMLHttpRequest){ 
			oXmlHttp=new XMLHttpRequest(); 
		}
		if(!oXmlHttp&&window.ActiveXObject){ 
			try {
				oXmlHttp=new ActiveXObject("Msxml2.XMLHTTP.5.0")
			}
			catch(e){
				try {
					oXmlHttp=new ActiveXObject("Msxml2.XMLHTTP.4.0")
				}
				catch(e){
					try {
						oXmlHttp=new ActiveXObject("Msxml2.XMLHTTP")
					}
					catch(e){
						try{
							oXmlHttp=new ActiveXObject("Microsoft.XMLHTTP")
						}
						catch(e){
							alert("The Browser Not Support XMLHttp。");
						}
					}
				}
			}
		}
		return oXmlHttp;/* 返回生成的对象 */
	}

	/**
	 * 触发事件
	 * 
	 * @param string sType 事件类型
	 * @return void
	 */
	function fire(sType){
		sType='on' + sType;/* 初始化参数 */
		var sHandler=arrEventHandlers[ sType ],
			sGlobelHandler=Dyhb.Ajax[ sType ];
		if(sHandler){/* 不对事件类型进行验证 */
			if(sType !='onsuccess'){
				sHandler(oXmlHttp);
			}
			else {
				try {/* 处理获取oXmlHttp.responseText导致出错的情况,比如请求图片地址. */
					oXmlHttp.responseText;
				}
				catch(error){
					return sHandler(oXmlHttp);
				}
				sHandler(oXmlHttp,oXmlHttp.responseText);
			}
		}
		else if(sGlobelHandler){
			if(sType=='onsuccess'){/* onsuccess不支持全局事件 */
				return;
			}
			sGlobelHandler(oXmlHttp);
		}
	}
	for(sKey in arrOptions){/* 将arrOptions参数中的事件参数复制到 eventHandlers 对象中 */
		 if(sKey!=='data' && sKey!=='async' && sKey!=='username' && sKey!=='password' && sKey!=='headers')
			 arrEventHandlers[ sKey ]=arrOptions[ sKey ];
	}
	arrHeaders['X-Requested-With']='XMLHttpRequest';/* 标识XMLHttpRequest */
	try {
		oXmlHttp=getXmlHttpRequest();/* 获取XMLHttp对象 */
		if(sMethod=='GET'){/* GET 方式 */
			if(sData){/* 如果设置了发送数据，那么在URL添加上 */
				/* indexOf()方法可返回某个指定的字符串值在字符串中首次出现的位置。
				// stringObject.indexOf(searchvalue,fromindex)
				// indexOf()方法对大小写敏感！
				// 如果要检索的字符串值没有出现，则该方法返回 -1。 */
				sUrl +=(sUrl.indexOf('?')>=0 ? '&' : '?')+ sData;
				sData=null;
			}
			if(arrOptions['noCache'])/* 如果设置了不需要缓存，默认需要缓存 */
				sUrl +=(sUrl.indexOf('?')>=0 ? '&' : '?')+ 'b' +(+ new Date)+ '=1';
		}
		/* 创建一个新的http请求，并指定此请求的方法、URL以及验证信息
		// 语法
		// oXMLHttpRequest.open(bstrMethod,bstrUrl,varAsync,bstrUser,bstrPassword);
		// 参数
		// bstrMethod
		// http方法，例如：POST、GET、PUT及PROPFIND。大小写不敏感。
		// bstrUrl
		// 请求的URL地址，可以为绝对地址也可以为相对地址。
		// varAsync[可选]
		// 布尔型，指定此请求是否为异步方式，默认为true。如果为真，当状态改变时会调用onreadystatechange属性指定的回调函数。
		// 1. 当该boolean值为true时，服务器请求是异步进行的，也就是脚本执行send（）方法后不等待服务器的执行结果，而是继续执行脚本代码；
		// 2. 当该boolean值为false时，服务器请求是同步进行的，也就是脚本执行send（）方法后等待服务器的执行结果的返回，若在等待过程中超时，则不再等待，继续执行后面的脚本代码！
		// bstrUser[可选]
		// 如果服务器需要验证，此处指定用户名，如果未指定，当服务器需要验证时，会弹出验证窗口。
		// bstrPassword[可选]
		// 验证信息中的密码部分，如果用户名为空，则此值将被忽略。
		// 如果设置了用户名，那么加密发送 */
		if(sUsername){
			oXmlHttp.open(sMethod,sUrl,bAsync,sUsername,sPassword);
		}
		else {
			oXmlHttp.open(sMethod,sUrl,bAsync);
		}
		if(bAsync){/* 异步执行，不用等待 */
			oXmlHttp.onreadystatechange=stateChangeHandler;
		}
		if(sMethod=='POST'){/* 在open之后再进行http请求头设定 */
			oXmlHttp.setRequestHeader("Method","POST " + sUrl + " HTTP/1.1");
			oXmlHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		}
		for(sKey in arrHeaders){
			/* hasOwnProperty
			// 此方法无法检查该对象的原型链中是否具有该属性；该属性必须是对象本身的一个成员。 
			// in 操作检查对象中是否有名为 property 的属性。也可以检查对象的原型，判断该属性是否为原型链的一部分。 
			// 代码如下:
			//  function Test(){ 
			//	this. a='abc'; 
			//  } 
			// Test.prototype.b='efg'; 
			// var test=new Test; 
			// alert(test.hasOwnProperty('a'));//输出 true 
			// alert(test.hasOwnProperty('b'));//输出 false 
			// alert('a' in test);//输出 true 
			// alert('b' in test);//输出 true */
			if(arrHeaders.hasOwnProperty(sKey)){
				oXmlHttp.setRequestHeader(sKey,arrHeaders[sKey]);
			}
		}
		fire('beforerequest');/* 触发数据发送前事件 */
		oXmlHttp.send(sData);/* 正式发送数据 */
		if(!bAsync){/* 如果是同步，返回成功执行结果 */
			stateChangeHandler();
		}
	}
	catch(ex){
		fire('failure');/* 触发失败事件 */
	}
	return oXmlHttp;/* 返回XMLHttp 对象 */
};
