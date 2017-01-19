/* 嵌套评论 */
function commentReply(pid,c){
	var response=document.getElementById('comment-post');
	document.getElementById('comment-parentid').value=pid;
	document.getElementById('cancel-reply').style.display='';
	c.parentNode.parentNode.appendChild(response);
}
function cancelReply(){
	var commentPlace=document.getElementById('comment-place'),response=document.getElementById('comment-post');
	document.getElementById('comment-parentid').value=0;
	document.getElementById('cancel-reply').style.display='none';
	commentPlace.appendChild(response);
}

/* AJAX评论*/
function checkEmail(sEmail){
	var emailRegExp=new RegExp("[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?");
	if(!emailRegExp.test(sEmail)||sEmail.indexOf('.')==-1){
		return false;
	}
	else{
		return true;
	}
}

/* 引用&&回复 */
function commentQuote(sStr){
	var author=$(authorId).innerHTML.replace(/<.+?>/gim,"").replace(/\t|\n|\r\n/g, "");
	var comment=$(commentBodyId).innerHTML;
	$("comment").value+='<blockquote>\n<strong><a href="#comment-' + authorId.replace(/author-/,"")+ '">' + author + '</a> :</strong>' + comment.replace(/\t/g, "")+ '</blockquote>\n';
	$("comment").focus();
}

function checkUrl(sUrl){
	var strRegex="^((https|http|ftp|rtsp|mms)?://)"
		+ "?(([0-9a-z_!~*'().&=+$%-]+:)?[0-9a-zA-Z_!~*'().&=+$%-]+@)?"
		+ "(([0-9]{1,3}\.){3}[0-9]{1,3}"
		+ "|"
		+ "([0-9a-zA-Z_!~*'()-]+\.)*"
		+ "([0-9a-zA-Z][0-9a-zA-Z-]{0,61})?[0-9a-z]\."
		+ "[a-zA-Z]{2,6})"
		+ "(:[0-9]{1,4})?"
		+ "((/?)|"
		+ "(/[0-9a-zA-Z_!~*'().;?:@&=+$,%#-]+)+/?)$";
		var re=new RegExp(strRegex);
		if(re.test(sUrl)){
			return true;
		}
		else{
			return false;
		}
}

function commentCheckForm(){
	var comment_name=$.trim($('#comment-name').val());
	var comment_email=$.trim($("#comment-email").val());
	var comment_url =$.trim($("#comment-url").val());
	var comment_content =$.trim($("#comment-content").val());
	if(comment_name == ""){
		showDialog(D.L('评论名字不能为空'),'alert',D.L('评论发生错误'));
		return false;
	}

	if(comment_name.length > 25){
		showDialog(D.L('评论名字长度只能小于等于25个字符串'),'alert',D.L('评论发生错误'));
		return false;
	}

	if(comment_email!='' && !checkEmail(comment_email)){
		showDialog(D.L('评论E-mail 格式错误'),'alert',D.L('评论发生错误'));
		return false;
	}

	if(comment_url!='' && !checkUrl(comment_url)){
		showDialog(D.L('评论Url 格式错误'),'alert',D.L('评论发生错误'));
		return false;
	}

	if(comment_content == ""){
		showDialog(D.L('评论内容不能为空'),'alert',D.L('评论发生错误'));
		return false;
	}

	return true;
}

function commentSubmit(){
	$("#comment-submit").val(D.L("正在提交评论"));
	$("#comment-submit").attr("disabled", "disabled");
	Dyhb.AjaxSubmit('commentform',_COMMENT_URL_,'',commentComplete);
}

function commentComplete(data,status){
	$("#comment-submit").attr("disabled", false);
	$("#comment-submit").val(D.L("提交评论"));
	if(status==1){
		if(template_type=='blog'||template_type=='cms'){
			$('#comment-totalnums').html(data.commentnum);
			$(data.ajaxbackhtml).insertBefore("#comment-post");
		}
		else if(template_type=='bbs'){
			window.location.href=data.jumpurl;
		}
	}
}

/* media link*/
function playmedia(strID,strType,strURL,intWidth,intHeight,sBgColor){
	var objDiv=document.getElementById(strID);
	if(!objDiv)return false;
	if(objDiv.style.display!='none'){
		objDiv.innerHTML='';
		objDiv.style.display='none';
	} else {
		objDiv.innerHTML=makemedia(strType,strURL,intWidth,intHeight,strID,sBgColor);
		objDiv.style.display='block';
	}
}

/*Media Build*/
function makemedia(strType,strURL,intWidth,intHeight,strID,sBgColor){
	var strHtml;
	var strBg=sBgColor!='' && typeof(sBgColor)!='underfined' ? 'bgcolor="'+sBgColor+'"' :'';
	switch(strType){
		case 'wmp':
			strHtml="<object width='"+intWidth+"' height='"+intHeight+"' classid='CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6'><param name='url' value='"+strURL+"'/><embed width='"+intWidth+"' height='"+intHeight+"' type='application/x-mplayer2' src='"+strURL+"' ></embed></object>";
			break;
		case 'swf':
			strHtml="<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' width='"+intWidth+"' height='"+intHeight+"'><param name='movie' value='"+strURL+"'/><param name='quality' value='high' /><embed src='"+strURL+"' quality='high' type='application/x-shockwave-flash' width='"+intWidth+"' height='"+intHeight+"' "+strBg+" ></embed></object>";
			break;
		case 'flv':
			strHtml="<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' width='"+intWidth+"' height='"+intHeight+"'><param name='movie' value='"+_PUBLIC_+"/Images/Media/mediaplayer.swf?file="+strURL+"&bufferlength=10'/><param name='quality' value='high' /><param name='allowFullScreen' value='true' /><embed src='"+_PUBLIC_+"/Images/Media/mediaplayer.swf?file="+strURL+"&bufferlength=10' quality='high' allowFullScreen='true' type='application/x-shockwave-flash' width='"+intWidth+"' height='"+intHeight+"' ></embed></object>";
			break;
		case 'real':
			strHtml="<object classid='clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA' width='"+intWidth+"' height='"+intHeight+"'><param name='src' value='"+_PUBLIC_+"/Images/Media/realplay.php?link="+strURL+"' /><param name='controls' value='Imagewindow' /><param name='console' value='clip1' /><param name='autostart' value='true' /><embed src='"+_PUBLIC_+"/Images/Media/realplay.php?link="+strURL+"' type='audio/x-pn-realaudio-plugin' autostart='true' console='clip1' controls='Imagewindow' width='"+intWidth+"' height='"+intHeight+"'></embed></object><br/><object classid='clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA' width='"+intWidth+"' height='44'><param name='src' value='"+_PUBLIC_+"/Images/Media/realplay.php?link="+strURL+"' /><param name='controls' value='ControlPanel' /><param name='console' value='clip1' /><param name='autostart' value='true' /><embed src='"+_PUBLIC_+"/Images/Media/realplay.php?link="+strURL+"' type='audio/x-pn-realaudio-plugin' autostart='true' console='clip1' controls='ControlPanel' width='"+intWidth+"' height='44'></embed></object>";
			break;
	}
	return strHtml;
}

/* 字体大小控制*/
function doZoom(size){
	document.getElementById('content-zoom-text').style.fontSize=size+'px';
}

/* URL 解密*/
function decodeTrackbackUrl(sStr, nIshidden, nUniqueId){
	var sResultStr='';
	if(nIshidden==1){ /*Hidden!*/
		var nRandomNumber1=Math.floor(Math.random()*10+1);
		var nRandomNumber2=Math.floor(Math.random()*10+1);
		sResultStr ="<span id=\"showtbq"+nUniqueId+"\">"+D.L('请输入答案：')+" <span id=\"qa"+nUniqueId+"\">"+nRandomNumber1+"</span> <strong>+</strong> <span id=\"qb"+nUniqueId+"\">"+nRandomNumber2+"</span> <strong>=</strong> <input type='text' id='ans"+nUniqueId+"' maxlength='2' size='2'/> <input type='button' onclick='submitHiddenTbAnswer(\""+nUniqueId+"\");' value='"+D.L('提交')+"'/><span id=\"answertb"+nUniqueId+"\" style=\"display: none;\">"+sStr+"</span></span>";
	}
	else {
		sResultStr="<span id=\"showtbfinal"+nUniqueId+"\">";
		var sCodeStr;
		sCodeStr=sStr.split('%');
		var nSeed=sCodeStr[0];
		for(var nI=1; nI<sCodeStr.length; nI++){
			sResultStr+=String.fromCharCode(sCodeStr[nI]-nSeed);
		}
		sResultStr+="</span> <span onclick=\"copyText('showtbfinal"+nUniqueId+"');\" style=\"cursor: pointer;\">["+D.L('复制')+"]</span>";
	}
	return sResultStr;
}

/* 回答问题*/
function submitHiddenTbAnswer(nUniqueId){
	var nRandomNumber1 =(document.getElementById("qa"+nUniqueId))? parseInt(document.getElementById("qa"+nUniqueId).innerHTML): 0;
	var nRandomNumber2 =(document.getElementById("qb"+nUniqueId))? parseInt(document.getElementById("qb"+nUniqueId).innerHTML): 0;
	var nAnsSubmited=(document.getElementById("ans"+nUniqueId))? parseInt(document.getElementById("ans"+nUniqueId).value): 0;
	if(nRandomNumber1+nRandomNumber2!=nAnsSubmited)showDialog(D.L('对不起，答案错误！'),'alert',D.L('答案错误'));
	else {
		var sResultStr=(document.getElementById("answertb"+nUniqueId))? document.getElementById("answertb"+nUniqueId).innerHTML : null;
		sResultStr=decodeTrackbackUrl(sResultStr, 0, 0);
		if(document.getElementById("showtbq"+nUniqueId)){ document.getElementById("showtbq"+nUniqueId).innerHTML=sResultStr};
	}
}

/* 赋值到剪切板*/
function copyText(id){
	/*copyToClipboard(document.getElementById(id).value); */
	if(document.getElementById(id))	{
		var tocopy=document.getElementById(id).innerHTML;
		tocopy=tocopy.replace(/&amp;/g, "&");
		copy(tocopy);
	}
}

function copy(text2copy){
	if(window.clipboardData){
		window.clipboardData.setData("Text",text2copy);
	}
	else {
		var flashcopier='flashcopier';
		if(!document.getElementById(flashcopier)){
			var divholder=document.createElement('div'); divholder.id=flashcopier;
			document.body.appendChild(divholder);
		}
		document.getElementById(flashcopier).innerHTML='';
		var divinfo='<embed src="'+_PUBLIC_+'/Images/Media/clipboard.swf" FlashVars="clipboard='+escape(text2copy)+'" width="0" height="0" type="application/x-shockwave-flash"></embed>';
		document.getElementById(flashcopier).innerHTML=divinfo;
		showDialog(D.L('地址已经复制到剪贴板！'), 'info');
	}
}

function copyToClipboard(meintext){
	if(window.clipboardData){
		showDialog("ie", 'info');
			/* the IE-manier*/
			window.clipboardData.setData("Text", meintext);
			/* waarschijnlijk niet de beste manier om Moz/NS te detecteren;
			// het is mij echter onbekend vanaf welke versie dit precies werkt:*/
		}
		else if(window.netscape){
			/* dit is belangrijk maar staat nergens duidelijk vermeld:
			// you have to sign the code to enable this, or see notes below */
			netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect');

			/* maak een interface naar het clipboard*/
			var clip=Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
			if(!clip){return};
			showDialog("mozilla", 'info');

			/* maak een transferable*/
			var trans=Components.classes['@mozilla.org/widget/transferable;1']
						.createInstance(Components.interfaces.nsITransferable);
			if(!trans){return};

			/* specificeer wat voor soort data we op willen halen; text in dit geval*/
			trans.addDataFlavor('text/unicode');

			/* om de data uit de transferable te halen hebben we 2 nieuwe objecten
			// nodig om het in op te slaan*/
			var str=new Object();
			var len=new Object();

			var str=Components.classes["@mozilla.org/supports-string;1"]
					.createInstance(Components.interfaces.nsISupportsString);
			var copytext=meintext;
			str.data=copytext;
			trans.setTransferData("text/unicode",str,copytext.length*2);
			var clipid=Components.interfaces.nsIClipboard;
			if(!clip){return false};
			clip.setData(trans,null,clipid.kGlobalClipboard);

		}
		showDialog("Following info was copied to your clipboard:\n\n" + meintext, 'info');
		return false;
}

var authort;
function showauthor(ctrlObj, menuid){
	authort=setTimeout(function(){
		showMenu({'menuid':menuid});
		if(document.getElementById(menuid + '_ma').innerHTML == ''){ document.getElementById(menuid + '_ma').innerHTML=ctrlObj.innerHTML};
	}, 500);
	if(!ctrlObj.onmouseout){
		ctrlObj.onmouseout=function(){
			clearTimeout(authort);
		}
	}
}
