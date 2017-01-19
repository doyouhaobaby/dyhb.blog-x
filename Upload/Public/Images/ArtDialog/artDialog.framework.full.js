function dyhbAlert(sContent,sTitleText){
	if(sTitleText=='' || sTitleText==undefined)
		sTitleText='DoYouHaoBaby MessageBox';
	art.dialog({
		title: sTitleText,
		width: 400,
		height: 70,
		resize: false,
		lock: true,
		button: [{
			name: 'Confirm',
			callback: function(){
				this.title('Turn off automatically after 2 seconds').time(2);
				return false;
			},
			focus: true
		}],
		content: sContent
	});
}

function dyhbConfirm(sContent,confrim_accept,sTitleText){
	if(sTitleText=='' || sTitleText==undefined)
		sTitleText='DoYouHaoBaby ConfirmBox';
	art.dialog({
		title: sTitleText,
		width: 400,
		height: 70,
		resize: false,
		yesFn:confrim_accept,
		noText: 'Cancel',
		noFn: true,
		content: sContent
	});
}

function dyhbAjax(src,title,width,height){
	if(!title || title==undefined) title='DoYouHaoBaby Framework';
	if(!width) width=400;
	if(!height) height =400;
	var dialog=art.dialog({
		id: '',
		title: title,
		width: width,
		height: height,
		resize: false,
		lock:false
		});
	$.ajax({
		url: src,
		success: function(data){
			dialog.content(data);
		},
		cache: false
		});
}

function dyhbFrame(src,title,width,height){
	if(!title || title==undefined) 'DoYouHaoBaby Framework';
	if(!width) width=400;
	if(!height) height =400;
	art.dialog.open(src, {title: title, width: width, height:height});
}
