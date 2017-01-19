function _showTip(ctrlobj){
	if(!ctrlobj.id){
		ctrlobj.id='tip_' + Math.random();
	}
	menuid=ctrlobj.id + '_menu';
	if(!document.getElementById(menuid)){
		var div=document.createElement('div');
		div.id=ctrlobj.id + '_menu';
		div.className='tip tip_js';
		div.style.display='none';
		div.innerHTML='<div class="tip_horn"></div><div class="tip_c">' + ctrlobj.getAttribute('tip')+ '</div>';
		document.getElementById('append_parent').appendChild(div);
	}
	document.getElementById(ctrlobj.id).onmouseout=function(){ hideMenu('', 'prompt'); };
	showMenu({'mtype':'prompt','ctrlid':ctrlobj.id,'pos':'210!','duration':2,'zindex':JSMENU['zIndex']['prompt']});
}

function _showPrompt(ctrlid, evt, msg, timeout){
	var menuid=ctrlid ? ctrlid + '_pmenu' : 'ntcwin';
	var duration=timeout ? 0 : 3;
	if(document.getElementById(menuid)){
		document.getElementById(menuid).parentNode.removeChild(document.getElementById(menuid));
	}
	var div=document.createElement('div');
	div.id=menuid;
	div.className=ctrlid ? 'tip tip_js' : 'ntcwin';
	div.style.display='none';
	document.getElementById('append_parent').appendChild(div);
	if(ctrlid){
		msg='<div id="' + ctrlid + '_prompt"><div class="tip_horn"></div><div class="tip_c">' + msg + '</div>';
	} else {
		msg='<table cellspacing="0" cellpadding="0" class="popupcredit"><tr><td class="pc_l">&nbsp;</td><td class="pc_c"><div class="pc_inner">' + msg +
			'</td><td class="pc_r">&nbsp;</td></tr></table>';
	}
	div.innerHTML=msg;
	if(ctrlid){
		if(!timeout){
			evt='click';
		}
		if(document.getElementById(ctrlid)){
			if(document.getElementById(ctrlid).evt !== false){
				var prompting=function(){
					showMenu({'mtype':'prompt','ctrlid':ctrlid,'evt':evt,'menuid':menuid,'pos':'210'});
				};
				if(evt == 'click'){
					document.getElementById(ctrlid).onclick=prompting;
				} else {
					document.getElementById(ctrlid).onmouseover=prompting;
				}
			}
			showMenu({'mtype':'prompt','ctrlid':ctrlid,'evt':evt,'menuid':menuid,'pos':'210','duration':duration,'timeout':timeout,'zindex':JSMENU['zIndex']['prompt']});
			document.getElementById(ctrlid).unselectable=false;
		}
	} else {
		showMenu({'mtype':'prompt','pos':'00','menuid':menuid,'duration':duration,'timeout':timeout,'zindex':JSMENU['zIndex']['prompt']});
		document.getElementById(menuid).style.top=(parseInt(document.getElementById(menuid).style.top)- 100)+ 'px';
	}
}

function _toggle_collapse(objname, noimg, complex, lang){
	var obj=document.getElementById(objname);
	if(obj){
		obj.style.display=obj.style.display == '' ? 'none' : '';
		var collapsed=getcookie('collapse');
		collapsed=updatestring(collapsed, objname, !obj.style.display);
		setcookie('collapse', collapsed,(collapsed ? 2592000 : -2592000));
	}
	if(!noimg){
		var img=document.getElementById(objname + '_img');
		if(img.tagName != 'IMG'){
			if(img.className.indexOf('_yes')== -1){
				img.className=img.className.replace(/_no/, '_yes');
				if(lang){
					img.innerHTML=lang[0];
				}
			} else {
				img.className=img.className.replace(/_yes/, '_no');
				if(lang){
					img.innerHTML=lang[1];
				}
			}
		} else {
			img.src=img.src.indexOf('_yes.gif')== -1 ? img.src.replace(/_no\.gif/, '_yes\.gif'): img.src.replace(/_yes\.gif/, '_no\.gif');
		}
		img.blur();
	}
	if(complex){
		var objc=document.getElementById(objname + '_c');
		if(objc){
			objc.className=objc.className == 'umh' ? 'umh umn' : 'umh';
		}
	}

}
