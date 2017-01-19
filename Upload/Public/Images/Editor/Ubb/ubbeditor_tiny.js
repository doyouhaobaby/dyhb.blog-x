/* -----------------------------------------------------
UBB Code Editor for DYHB.BLOG X
This Is From Bo-BLOG
------------------------------------------------------- */

var clientVer = navigator.userAgent.toLowerCase(); // Get browser version
var is_firefox = ((clientVer.indexOf("gecko") != -1) && (clientVer.indexOf("firefox") != -1) && (clientVer.indexOf("opera") == -1)); //Firefox or other Gecko

function AddText(NewCode) {
	document.getElementById('comment-content').value+=NewCode
	document.getElementById('comment-content').focus();
}

// From http://www.massless.org/mozedit/
function FxGetTxt(open, close){
	var selLength = document.getElementById('comment-content').textLength;
	var selStart = document.getElementById('comment-content').selectionStart;
	var selEnd = document.getElementById('comment-content').selectionEnd;
	if (selEnd == 1 || selEnd == 2)  selEnd = selLength;
	var s1 = (document.getElementById('comment-content').value).substring(0,selStart);
	var s2 = (document.getElementById('comment-content').value).substring(selStart, selEnd)
	var s3 = (document.getElementById('comment-content').value).substring(selEnd, selLength);
	document.getElementById('comment-content').value = s1 + open + s2 + close + s3;
	return;
}

function email() {
	txt=prompt(D.L('Email 地址','ubbJs'),"name\@domain.com");      
	if (txt!=null) {
		AddTxt="[email]"+txt+"[/email]";
		AddText(AddTxt);
	}
}

function bold() {
	if (document.selection && document.selection.type == "Text") {
		var range = document.selection.createRange();
		range.text = "[b]" + range.text + "[/b]";
	} 
	else if (is_firefox && document.getElementById('comment-content').selectionEnd) {
		txt=FxGetTxt ("[b]", "[/b]");
		return;
	} else {
		txt=prompt(D.L('文字将被变粗','ubbJs'),D.L('文字','ubbJs'));
		if (txt!=null) {
			AddTxt="[b]"+txt;
			AddText(AddTxt);
			AddTxt="[/b]";
			AddText(AddTxt);
		}
	}
}

function italicize() {
	if (document.selection && document.selection.type == "Text") {
		var range = document.selection.createRange();
		range.text = "[i]" + range.text + "[/i]";
	} else if (is_firefox && document.getElementById('comment-content').selectionEnd) {
		txt=FxGetTxt ("[i]", "[/i]");
		return;
	} else {
	txt=prompt(D.L('文字将变斜体','ubbJs'),D.L('文字','ubbJs'));
		if (txt!=null) {
			AddTxt="[i]"+txt;
			AddText(AddTxt);
			AddTxt="[/i]";
			AddText(AddTxt);
		}
	}
}

function underline() {
	if (document.selection && document.selection.type == "Text") {
		var range = document.selection.createRange();
		range.text = "[u]" + range.text + "[/u]";
	} else if (is_firefox && document.getElementById('comment-content').selectionEnd) {
		txt=FxGetTxt ("[u]", "[/u]");
		return;
	} else {
		txt=prompt(D.L('文字将加下划线','ubbJs'),D.L('文字','ubbJs'));
		if (txt!=null) {
			AddTxt="[u]"+txt;
			AddText(AddTxt);
			AddTxt="[/u]";
			AddText(AddTxt);
		}
	}
}

function quoteme() {
	if (document.selection && document.selection.type == "Text") {
		var range = document.selection.createRange();
		range.text = "[quote]" + range.text + "[/quote]";
	} else if (is_firefox && document.getElementById('comment-content').selectionEnd) {
		txt=FxGetTxt ("[quote]", "[/quote]");
		return;
	} else {
		txt=prompt(D.L('被引用的文字','ubbJs'),D.L('文字','ubbJs'));
		if(txt!=null) {
			AddTxt="[quote]"+txt;
			AddText(AddTxt);
			AddTxt="[/quote]";
			AddText(AddTxt);
		}
	}
}

function hyperlink() {
	if (document.selection && document.selection.type == "Text") {
		var range = document.selection.createRange();
		txt=prompt(D.L('为选中文字添加超级链接','ubbJs'),"http://");
		range.text = "[url=" + txt + "]" + range.text + "[/url]";
	} else if (is_firefox && document.getElementById('comment-content').selectionEnd) {
		txt=prompt(D.L('为选中文字添加超级链接','ubbJs'),"http://");
		txt=FxGetTxt ("[url=" + txt + "]", "[/url]");
		return;
	} else {
		txt2=prompt(D.L("链接文本显示.",'ubbJs')+"\n"+D.L("如果不想使用, 可以为空, 将只显示超级链接地址. ",'ubbJs'),"");
		if (txt2!=null) {
			txt=prompt(D.L('超级链接','ubbJs'),"http://");
			if (txt!=null) {
				if (txt2=="") {
					AddTxt="[url]"+txt;
					AddText(AddTxt);
					AddTxt="[/url]";
					AddText(AddTxt);
				} else {
					AddTxt="[url="+txt+"]"+txt2;
					AddText(AddTxt);
					AddTxt="[/url]";
					AddText(AddTxt);
				}
			}
		}
	}
}

function image() {
	txt=prompt(D.L('图片的 URL','ubbJs'),"http://");
	if(txt!=null) {
		AddTxt="[img]"+txt+"[/img]";
		AddText(AddTxt);
	}
}
