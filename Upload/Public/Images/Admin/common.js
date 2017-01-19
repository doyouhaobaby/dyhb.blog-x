var selectRowIndex=Array();

function edit(id){
	var keyValue;
	if(id){
		keyValue=id;
	}else{
		keyValue=getSelectValue();
	}
	if(!keyValue){
		dyhbAlert(D.L('请选择编辑项！'));
		return false;
	}
	window.location.href=D.U('edit?id='+keyValue);
}

function forbid(id){
	window.location.href=D.U('forbid?id='+id);
}

function resume(id){
	window.location.href=D.U('resume?id='+id);
}

function hide(id){
	window.location.href=D.U('hide?id='+id);
}

function show(id){
	window.location.href=D.U('show?id='+id);
}

function view(id){
	window.location.href=D.U('view?id='+id);
}

function add(id){
	if(id){
		window.location.href=D.U('add?id='+id);
	}
	else{
		window.location.href=D.U('add');
	}
}

function foreverdel(id){
	var keyValue;
	if(id){
		keyValue=id;
	}
	else{
		keyValue=getSelectValues();
	}
	if(!keyValue){
		dyhbAlert(D.L('请选择删除项！'));
		return false;
	}
	dyhbConfirm(D.L('确实要永久删除选择项吗？'),function(){
		Dyhb.AjaxSend(D.U('foreverdelete'),"id="+keyValue+'&ajax=1','',completeDelete);
	});
}

function delUpload(id,url,back){
	if(!id){
		dyhbAlert(D.L('请选择删除的项！'));
		return false;
	}
	Dyhb.AjaxSend(D.U(url+'/foreverdelete'),"id="+id+'&ajax=1','',function(data,status){ if(status==1){dyhbFrame(back,D.L('文件管理器'),700,350);}});
}

function draft(id){
	var keyValue;
	if(id){
		keyValue=id;
	}
	else{
		keyValue=getSelectValues();
	}
	if(!keyValue){
		dyhbAlert(D.L('请选择转入项！'));
		return false;
	}
	dyhbConfirm(D.L('确实要将选择项转入草稿箱吗？'),function(){
		window.location.href=D.U('draft?id='+keyValue);
	});
}

function unDraft(id){
	var keyValue;
	if(id){
		keyValue=id;
	}
	else{
		keyValue=getSelectValues();
	}
	if(!keyValue){
		dyhbAlert(D.L('请选择草稿项！'));
		return false;
	}
	dyhbConfirm(D.L('确实要将草稿项发布吗？'),function(){
		window.location.href=D.U('undraft?id='+keyValue);
	});
}

function isshow(id){
	var keyValue;
	if(id){
		keyValue=id;
	}
	else{
		keyValue=getSelectValues();
	}
	if(!keyValue){
		dyhbAlert(D.L('请选择审核项！'));
		return false;
	}
	dyhbConfirm(D.L('确实要将审核项转入屏蔽吗？'),function(){
		window.location.href=D.U('show?id='+keyValue);
	});
}

function updateDatacache(id){
	var keyValue;
	if(id){
		keyValue=id;
	}
	else{
		keyValue=getSelectValues();
	}
	if(!keyValue){
		dyhbAlert(D.L('请选择更新缓存项！'));
		return false;
	}
	window.location.href=D.U('update_cache?id='+keyValue);
}

function unIsshow(id){
	var keyValue;
	if(id){
		keyValue=id;
	}
	else{
		keyValue=getSelectValues();
	}
	if(!keyValue){
		dyhbAlert(D.L('请选择屏蔽项！'));
		return false;
	}
	dyhbConfirm(D.L('确实要将屏蔽项审核吗？'),function(){
		window.location.href=D.U('hide?id='+keyValue);
	});
}

function top(id){
	var keyValue;
	if(id){
		keyValue=id;
	}
	else{
		keyValue=getSelectValues();
	}
	if(!keyValue){
		dyhbAlert(D.L('请选择置顶项！'));
		return false;
	}
	dyhbConfirm(D.L('确实要将置顶项发布吗？'),function(){
		window.location.href=D.U('top?id='+keyValue);
	});
}

function unTop(id){
	var keyValue;
	if(id){
		keyValue=id;
	}
	else{
		keyValue=getSelectValues();
	}
	if(!keyValue){
		dyhbAlert(D.L('请选择取消置顶项！'));
		return false;
	}
	dyhbConfirm(D.L('确实要将取消置顶项发布吗？'),function(){
		window.location.href=D.U('untop?id='+keyValue);
	});
}

function lock(id){
	var keyValue;
	if(id){
		keyValue=id;
	}
	else{
		keyValue=getSelectValues();
	}
	if(!keyValue){
		dyhbAlert(D.L('请选择锁定项！'));
		return false;
	}
	dyhbConfirm(D.L('确实要将锁定项发布吗？'),function(){
		window.location.href=D.U('lock?id='+keyValue);
	});
}

function unLock(id){
	var keyValue;
	if(id){
		keyValue=id;
	}
	else{
		keyValue=getSelectValues();
	}
	if(!keyValue){
		dyhbAlert(D.L('请选择取解锁项！'));
		return false;
	}
	dyhbConfirm(D.L('确实要将解锁项发布吗？'),function(){
		window.location.href=D.U('unlock?id='+keyValue);
	});
}

function page(id){
	var keyValue;
	if(id){
		keyValue=id;
	}
	else{
		keyValue=getSelectValues();
	}
	if(!keyValue){
		dyhbAlert(D.L('请选择转为页面项！'));
		return false;
	}
	dyhbConfirm(D.L('确实要将转为页面项发布吗？'),function(){
		window.location.href=D.U('page?id='+keyValue);
	});
}

function blog(id){
	var keyValue;
	if(id){
		keyValue=id;
	}
	else{
		keyValue=getSelectValues();
	}
	if(!keyValue){
		dyhbAlert(D.L('请选择转为日志项！'));
		return false;
	}
	dyhbConfirm(D.L('确实要将转为日志项发布吗？'),function(){
		window.location.href=D.U('blog?id='+keyValue);
	});
}

function changeCategory(oObj,id){
	var keyValue;
	var category_id=oObj.value;
	if(id){
		keyValue=id;
	}
	else{
		keyValue=getSelectValues();
	}
	if(!keyValue){
		dyhbAlert(D.L('请选择移动分类项！'));
		return false;
	}
	dyhbConfirm(D.L('确实要将移动分类项移动吗？'),function(){
		window.location.href=D.U('category?id='+keyValue+'&cid='+category_id);
	});
}

function completeDelete(data,status){
	if(status==1){
		var Table=document.getElementById('checkList');
		var len=selectRowIndex.length;
		if(len==0){
			window.location.reload();
		}
		for(var i=len-1;i>=0;i--){
			Table.deleteRow(selectRowIndex[i]);
		}
		selectRowIndex=Array();
	}
}

function getSelectValue(){
	var obj=document.getElementsByName('key');
	var result='';
	for(var i=0;i<obj.length;i++){
		if(obj[i].checked==true)
			return obj[i].value;
	}
	return false;
}

function getSelectValues(){
	var obj=document.getElementsByName('key');
	var result='';
	var j=0;
	for(var i=0;i<obj.length;i++){
		if(obj[i].checked==true){
				selectRowIndex[j]=i+1;
				result +=obj[i].value+",";
				j++;
		}
	}
	return result.substring(0, result.length-1);
}

function child(id){
	window.location.href=D.U('index?node_parentid='+id);
}

function app(id){
	window.location.href=D.U('app?group_id='+id);
}

function group(id){
	window.location.href=D.U('index?group_id='+id);
}

function user(id){
	window.location.href=D.U('user?id='+id);
}

function cache(){
	DyhbAjax.send(D.U('cache'),'ajax=1');
}

function sort(id){
	var keyValue;
	keyValue=getSelectValues();
	window.location.href=D.U('sort?sort_id='+keyValue);
}

function sortBy(field,sort){
	window.location.href=D.U('?order_='+field+'&sort_='+sort,SORTURL);
}

function clickToInput(field,id){
	var idObj=$('#'+field+'_'+id);
	if($('#'+field+'_input_'+id).attr("type")=="text"){return false;}
	var name=$.trim(idObj.html());
	var m=$.trim(idObj.text());
	idObj.html("<input type='text' value='"+name+"' class='field' id='"+field+"_input_"+id+"' title='"+D.L('点击修改值')+"' >");
	$('#'+field+'_input_'+id).focus();
	$('#'+field+'_input_'+id).blur(function(){
		var n=$.trim($(this).val());
		if(n !=m && n !=""){
			Dyhb.AjaxSend(D.U('input_change_ajax'),'ajax=1&input_ajax_id='+id+'&input_ajax_val='+$('#'+field+'_input_'+id).val()+'&input_ajax_field='+field,'',clickToInputComplete);
		}
		else{
			$(this).parent().html(name);
		}
	});
}

function clickToInputComplete(data,status){
	if(status==1){
		$('#'+data.id).html(data.value);
	}
}

function checkform(){
	if($('#blog_title').val()==""){
		dyhbAlert(D.L('文章标题不能为空'));
		return false;
	}
	if($("#category_id").val()=='new_category'){
		dyhbAlert(D.L('新建分类动作尚未完成。请完成新建或选择其它已存在的分类。'));
		return false;
	}
	if(Editor=='kindeditor')
		var content=editor.html();
	else
		var content=$('#blog_content').val(); 
	if(content==""){
		dyhbAlert(D.L('文章内容不能为空'));
		return false;
	}
	return true;
}

function insertInput(thevalue, InputId){
	$("#"+InputId).val(thevalue);
}

function newPage(content){
	if(!content)
		content='[newpage]';
	if(Editor=='kindeditor')
		editor.insertHtml(content);
	else{
		var myField=document.getElementById('blog_content');
		var noweditorid=document.getElementById('blog_content');
		var oldcontent=document.getElementById("blog_content_old");
		var myValue=content;
		// IE support
		if(document.selection){
			myField.focus();
			sel=document.selection.createRange();
			sel.text=myValue;
			myField.focus();
		}

		// MOZILLA/NETSCAPE support
		else if(myField.selectionStart || myField.selectionStart=='0'){
			oldcontent.value=noweditorid.value; //Fx sometimes crashes using ubb, so this is for saving data back
			var startPos=myField.selectionStart;
			var endPos=myField.selectionEnd;
			var scrollTop=myField.scrollTop;
			myField.value=myField.value.substring(0, startPos)
					+ myValue 
					+ myField.value.substring(endPos, myField.value.length);
			myField.focus();
			myField.selectionStart=startPos + myValue.length;
			myField.selectionEnd=startPos + myValue.length;
			myField.scrollTop=scrollTop;
		}
		else{
			myField.value +=myValue;
			myField.focus();
		}
	}		
}

function E(elementid){  
	var obj;
	try{
		obj=document.getElementById(elementid);
	}
	catch(err){
		dyhbAlert(elementid+" NOT Found");
	}
	return obj;
}

function getE(elementid){
	return E(elementid);
}
