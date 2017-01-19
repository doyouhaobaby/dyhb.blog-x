/**
 * 将一个表单用ajax方式提交
 * 
 * <!-- 说明 -->
 * < Function [replacer] 对参数值特殊处理的函数,replacer(string value, string key)
 *   Function [onbeforerequest] 发送请求之前触发，function(XMLHttpRequest oXmlHttp)。
 *   Function [onsuccess] 请求成功时触发，function(XMLHttpRequest oXmlHttp, string responseText)。
 *   Function [onfailure] 请求失败时触发，function(XMLHttpRequest oXmlHttp)。
 *   Function [on{STATUS_CODE}] 当请求为相应状态码时触发的事件，如on302、on404、on500，function(XMLHttpRequest oXmlHttp)。3XX的状态码浏览器无法获取，4xx的，可能因为未知问题导致获取失败。 >
 *
 * @param HTMLFormElement oForm 需要提交的表单元素
 * @param Object [arrOptions] 发送请求的选项参数
 * @returns {XMLHttpRequest} 发送请求的XMLHttpRequest对象
 */
Dyhb.Ajax.Form=function(oForm,arrOptions){
	arrOptions=arrOptions || {};/* 初始化配置参数 */
	var arrElements=oForm.elements,
		nLen=arrElements.length,
		sMethod=oForm.getAttribute('method'),
		sUrl=oForm.getAttribute('action'),
		replacer=arrOptions.replacer || function(sValue,sName){
			return sValue;
		},
		arrSendOptions={},
		arrData=[],
		nI,oItem,sItemType,sItemName,sItemValue,
		arrOpts,oOI,nOLen,oOItem;

	function addData(sName,sValue){/* 向缓冲区添加参数数据 */
		arrData.push(encodeURIComponent(sName)+ '=' + encodeURIComponent(sValue));
	}

	for(nI in arrOptions){/* 复制发送参数选项对象 */
		if(arrOptions.hasOwnProperty(nI)){
			arrSendOptions[ nI ]=arrOptions[ nI ];
		}
	}
	for(nI=0; nI < nLen; nI++){
		oItem=arrElements[nI];
		sItemName=oItem.name;
		if(!oItem.disabled && sItemName){/* 处理：可用并包含表单name的表单项 */
			sItemType=oItem.type;
			sItemValue=oItem.value;
			switch(sItemType){
				case 'radio':/* radio和checkbox被选中时，拼装queryString数据 */
				case 'checkbox':
				if(!oItem.checked){
					break;
				}
				case 'textarea':/* 默认类型，拼装queryString数据 */
				case 'text':
				case 'password':
				case 'hidden':
				case 'select-one':
					addData(sItemName,replacer(sItemValue,sItemName));
				break;
				case 'select-multiple':/* 多行选中select，拼装所有选中的数据 */
					arrOpts=oTtem.arrOptions;
					nOLen=arrOpts.length;
					for(nOI=0; nOI < nOLen; nOI++){
						oOItem=arrOpts[nOI];
						if(oOItem.selected){
							addData(sItemName,replacer(oOItem.value,sItemName));
						}
				}
				break;
			}
		}
	}
	arrSendOptions.data=arrData.join('&');/* 完善发送请求的参数选项 */
	arrSendOptions.method=oForm.getAttribute('method')|| 'GET';
	return Dyhb.Ajax.Request(sUrl,arrSendOptions);/* 发送请求 */
};
