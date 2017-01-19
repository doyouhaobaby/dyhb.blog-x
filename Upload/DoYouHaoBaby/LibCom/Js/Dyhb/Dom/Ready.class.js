/**
 * 使函数在页面dom节点加载完毕时调用
 *
 * < 如果有条件将js放在页面最底部, 也能达到同样效果，不必使用该方法 >
 * < 支持页面调用结束后的回调函数 Dyhb.Dom.Ready(callback) > 
 */
(function(){
	var ready=Dyhb.Dom.Ready=function(){/* 声明 */
		var bReadyBound=false,
			arrReadyList=[];
		function ready(){
			if(!ready._bIsReady){
				ready._bIsReady=true;
				for(var nI=0,nJ=arrReadyList.length;nI < nJ;nI++){
					arrReadyList[nI]();
				}
			}
		}

		function bindReady(){
			if(bReadyBound){
				return;
			}
			bReadyBound=true;
			var opera=Dyhb.Browser.Opera ? Dyhb.Browser.Version : false;
			/* Mozilla,Opera(see further below for it)and webkit nightlies currently support this event */
			if(document.addEventListener){
				/* Use the handy event callback */
				document.addEventListener('DOMContentLoaded',opera ? function(){
					if(ready._bIsReady){
						return;
					}
					for(var nI=0;nI < document.styleSheets.length;nI++){
						if(document.styleSheets[nI].disabled){
							/* 资料上说  
							// callee  
							// 返回正被执行的 Function 对象，也就是所指定的 Function 对象的正文。
							// callee 属性是 arguments 对象的一个成员，它表示对函数对象本身的引用，这有利于匿名  
							// 函数的递归或者保证函数的封装性 */
							setTimeout(arguments.callee,0);
							return;
						}
					}
					/* and execute any waiting functions */
					ready();
				} : ready,false);
			}
			else if(Dyhb.Browser.Ie && window==top){
				/* If IE is used and is not in a frame
				// Continually check to see if the doc is ready */
				(function(){
					if(ready._bIsReady){
						return;
					}
					try {
						/* If IE is used,use the trick by Diego Perini
						// http://javascript.nwbox.com/IEContentLoaded/ */
						document.documentElement.doScroll('left');
					}
					catch(error){
						setTimeout(arguments.callee,0);
						return;
					}
					/* and execute any waiting functions */
					ready();
				})();
			}
			else if(Dyhb.Browser.Safari){
				var nStyles;
				(function(){
					if(ready._bIsReady){
						return;
					}
					if(document.readyState !='loaded' && document.readyState !='complete'){
						setTimeout(arguments.callee,0);
						return;
					}
					if(nStyles===undefined){
						nStyles=0;
						var arrObj1=document.getElementsByTagName('style'),
							arrObj2=document.getElementsByTagName('link');
						if(arrObj1){
							nStyles +=arrObj1.length;
						}
						if(arrObj2){
							for(var nI=0,nJ=arrObj2.length;nI < nJ;nI++){
								if(arrObj2[nI].getAttribute('rel')=='stylesheet'){
									nStyles++;
								}
							}
						}
					}
					if(document.styleSheets.length !=nStyles){
						setTimeout(arguments.callee,0);
						return;
					}
					ready();
				})();
			}
			/* A fallback to window.onload,that will always work */
			window.attachEvent ? window.attachEvent('onload',ready): window.addEventListener('load',ready,false);
		}
		bindReady();
		return function(callback){
			ready._bIsReady ? callback():(arrReadyList[ arrReadyList.length ]=callback);
		};
	}();
	ready._bIsReady=false;
})();
Dyhb.Ready=Dyhb.R=Dyhb.Dom.Ready;
