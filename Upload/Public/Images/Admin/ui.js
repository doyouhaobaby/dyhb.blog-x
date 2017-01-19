jQuery(document).ready(function(e) {
	isRtl=0;
	var a=(isRtl?"left":"right");
	e("#show-settings-link").click(function() {
		if (!e("#screen-options-wrap").hasClass("screen-options-open")) {
			e("#contextual-help-link-wrap").css("visibility", "hidden")
		}
		e("#screen-options-wrap").slideToggle("fast",
		function() {
			if (e(this).hasClass("screen-options-open")) {
				e("#show-settings-link").css({
					backgroundPosition:"top " + a
				});
				e("#contextual-help-link-wrap").css("visibility", "");
				e(this).removeClass("screen-options-open")
			} else {
				e("#show-settings-link").css({
					backgroundPosition:"bottom " + a
				});
				e(this).addClass("screen-options-open")
			}
		});
		return false
	});
	e("#contextual-help-link").click(function() {
		if (!e("#contextual-help-wrap").hasClass("contextual-help-open")) {
			e("#screen-options-link-wrap").css("visibility", "hidden")
		}
		e("#contextual-help-wrap").slideToggle("fast",
		function() {
			if (e(this).hasClass("contextual-help-open")) {
				e("#contextual-help-link").css({
					backgroundPosition:"top " + a
				});
				e("#screen-options-link-wrap").css("visibility", "");
				e(this).removeClass("contextual-help-open")
			} else {
				e("#contextual-help-link").css({
					backgroundPosition:"bottom " + a
				});
				e(this).addClass("contextual-help-open")
			}
		});
		return false
	});
  });

// 颜色
function ColorPicker(){
	var _t=this;
	var cobj;
	//常用的颜色
	var _comcolor=new Array('#000000', '#333333', '#666666', '#999999','#CCCCCC', '#FFFFFF', '#FF000', '#00FF00','#0000FF', '#FFFF00', '#00FFFF', '#FF00FF','#C0C0C0', '#DEDEDE', '#FFFFFF', '#FFFFFF');

	var _hex=new Array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
	var _cnum=new Array(1, 0, 0, 1, 1, 0, 0, 1, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1, 1, 0, 0);

	_t.choose=function(e,curobj){
		_t.cobj=curobj;
		var obj=document.all?event.srcElement :e.target;
		obj.style.position="relative";
		var inputTop=_t.getTop(obj);
		var inputLeft=_t.getLeft(obj);
		var htmlStr="visibility:visible; position:absolute; padding:2px;border:1px solid #c0c0c0;background:#F0F0F0; cursor:pointer;";
		var _cpicker=document.getElementById("_cpicker");
		if (!_cpicker){
			_cpicker=document.createElement("div");
			_cpicker.id="_cpicker";
			_cpicker.style.cssText=htmlStr;
			_cpicker.style.zIndex=30000;
			var _cpickerContent="<div>"+_t._colorTable()+"<div>" ;
			document.body.appendChild(_cpicker);
			_cpicker.innerHTML=_cpickerContent;
		}
		else {
			document.getElementById("_cpicker").style.visibility="visible";
		}

		_cpicker.style.left=(inputLeft) + "px";
		_cpicker.style.top=(inputTop + obj.clientHeight) + "px";
		  
		if(!_cpicker.onclick){
			_cpicker.onclick=function(oEvent){
				e=oEvent || window.event;
				var ev=document.all?event.srcElement :e.target ;
				if(ev.bgColor!=undefined){
					_t.chooseColor(ev.bgColor);
				}else{
					_t.chooseColor("");
				}
				this.style.visibility="hidden";
				if (document.all) {
					e.cancelBubble=true;
				}
				else {
					e.stopPropagation();
				}

			};
		}

		if(!_cpicker.onmouseover){
			_cpicker.onmouseover=function(oEvent){
				e=oEvent || window.event;
				var ev=document.all?event.srcElement :e.target ;
				if(ev.bgColor!=undefined){
					_t.previewColor(ev.bgColor);
				}else{
					_t.previewColor("");
				}
			};
		} 

		if(!document.all){
			_cpicker.setAttribute('flag','flag'); 
			obj.setAttribute('flag','flag');
		}else{
			_cpicker.flag="flag";
			obj.flag="flag";
		}
	  
		if(!document.onclick){
			document.onclick=function(e){
				var ev=document.all?event.srcElement :e.target ;

				if (ev.getAttribute("flag")==null){
					document.getElementById("_cpicker").style.visibility="hidden";
				}
			};
		}

	}

	//Interface
	_t.previewColor=function(color){
		
	}
	//Interface
	_t.chooseColor=function(color){
		
	}
	_t._toHex=function(n){
		var h, l;
		n=Math.round(n);
		l=n % 16;
		h=Math.floor((n / 16)) % 16;
		return (_hex[h] + _hex[l]);
	}

	_t._colorTd=function(r, g, b, n){
		r=((r * 16 + r) * 3 * (15 - n) + 0x80 * n) / 15;
		g=((g * 16 + g) * 3 * (15 - n) + 0x80 * n) / 15;
		b=((b * 16 + b) * 3 * (15 - n) + 0x80 * n) / 15;
		return '<TD BGCOLOR="#' + _t._toHex(r) + _t._toHex(g) + _t._toHex(b) + '" height=6 width=6></TD>';
	}

	_t._colorTable =function(){
		var trStr="<table CELLPADDING=0 CELLSPACING=0>";
		for (i=0; i < 16; i++) {
			trStr += '<TR>';
			trStr += '<TD BGCOLOR="#000000"  height=6 width=3></TD>';
			trStr += '<TD BGCOLOR="' + _comcolor[i] + '" height=6 width=6></TD>';
			trStr += '<TD BGCOLOR="#000000"  height=6 width=3></TD>';
			for (j=0; j < 30; j++) {
				n1=j % 5;
				n2=Math.floor(j / 5) * 3;
				n3=n2 + 3;
				trStr += _t._colorTd((_cnum[n3] * n1 + _cnum[n2] * (5 - n1)), (_cnum[n3 + 1] * n1 + _cnum[n2 + 1] * (5 - n1)), (_cnum[n3 + 2] * n1 + _cnum[n2 + 2] * (5 - n1)), i);
			}
			trStr += '</TR>';
		}
		trStr += "</table>";
		return trStr;
	}

	_t.getTop=function(e){
		var offset=e.offsetTop;
		if (e.offsetParent != null)
		offset += _t.getTop(e.offsetParent);
		return offset;
	}
	
	_t.getLeft=function(e){
		var offset=e.offsetLeft;
		if (e.offsetParent != null) 
		offset += _t.getLeft(e.offsetParent);
		return offset;
	}
}
var colorpicker=new ColorPicker();
