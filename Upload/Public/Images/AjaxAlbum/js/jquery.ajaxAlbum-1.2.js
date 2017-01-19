/******************************************
Intro:		jQuery仿qq空间和360圈的ajax相册多功能插件 新增：
				1>自动播放,
				2>图片预加载并执行回调，
				3>从而动态设置大图前后切换按钮宽高，
				4>不再用程序输出其属性
Author: 	Ayen[魏泽言] QQ: 475347256 E-mail: weizeyan52@126.com
Version:	1.2
Blog: 		http://designcss.org
Needs:		jquery-1.3.2.min.js
*/
(function($){
	$.fn.extend({
		ajaxAlbum:function(options){
			var set={
				onloadId:		null,//默认加载时所需imgId
				ajaxSend:		{rd:Math.random()},//发送异步请求涉及的参数
				ajaxUrl:		"",//请求地址
				ajaxMethod:		"GET",//请求格式
				slideNum:		7,//每次加载图片数量
				thumbList:  	".ajaxAlbum_thumbList",//缩略图htmlTAG
				thumbChild:		"li",//子标记
				bigImgId:		"bigImg",
				thumbImgClass:	"thumbImg",
				dispClass:		"displayContainer",
				focusClass:		"focus",
				prev_slide:		".prev",
				next_slide:		".next",	
				viewInfo:		".ajaxAlbum_viewInfo",//照片信息htmlTAG
				isOnload:		true,//页面onload载入后，ajax成功返回数据后是否对数据进行操作
				imgDir:			"",
				loadico:		_ROOT_+"/Public/Images/AjaxAlbum/imgcss/indicator_verybig.gif",
				firstIco:		_ROOT_+"/Public/Images/AjaxAlbum/imgcss/back_first.png",
				onErrorFileB:	_ROOT_+"/Public/Images/AjaxAlbum/imgcss/onerrorfileb.png",
				onErrorFileS:	_ROOT_+"/Public/Images/AjaxAlbum/imgcss/onerrorfiles.png",
				prev_id:		null,//从这个地方开始的5个配置，如果不精通js和ajax相关的东西最好别改
				next_id:		null,
				first_id:		null,
				last_id:		null,
				data:			null,//异步全球成功后的JOSN数据
				isInslide:		null,
				seconds:		null,
				delay_tag:		".ajaxAlbum_delay",
				autoPlay_tag:	".ajaxAlbum_autoPlayButton",
				stopPlay_tag:	".ajaxAlbum_stopPlayButton",
				ajaxDataType:	"json"//json xml/ajax返回数据格式
			};
			$.fn.extend(set,options);
			$("body").append("<p class='loading'>"+D.L( '数据载入中，请稍后' )+"</p>");
			var _load=$(".loading");
			var thumbList=$(this).find(set.thumbList);
			var viewInfo=$(this).find(set.viewInfo);
			var prev_slide=$(this).find(set.prev_slide);
			var next_slide=$(this).find(set.next_slide);
			var jDelay=$(this).find(set.delay_tag);
			var jAutoPlay=$(this).find(set.autoPlay_tag);
			var jStopPlay=$(set.stopPlay_tag);
			var intervalnum=0;
			display={//在插件内创建一个全局display对象
					cache:function(){//缓存数据
						_load.fadeIn();
						$.ajax({
							method:set.ajaxMethod,  
							url:set.ajaxUrl,
							data:set.ajaxSend,
							dataType:set.ajaxDataType,
							success:function(data){
								set.data=data;
								set.first_id=set.data[0].photoid;
								var _lastid=((set.data.length-1)!=0)?set.data[set.data.length-1].photoid:set.data[0].photoid;
								set.last_id=_lastid;
								if(set.isOnload==true){//alert("异步返回数据加载完毕 就绪中");
									display.showImg(set.onloadId);
									_load.fadeOut();
								}else{//alert("异步返回数据加载完毕 stop向下执行");
									_load.fadeOut();
									return false;	
								}
							},
							error:function(){
								showDialog(  D.L( '缓存失败' ));
								return false;
							}
						});	
					},
					showImg:function(id){//根据id动态获取图片/此步可以用ajax 
						var id=(isNaN(parseInt(id))===false)?parseInt(id):null;
						if(id!==null){
							thumbList.find("li[photoId="+id+"]").addClass(set.focusClass).siblings().removeClass(set.focusClass);
							if(display.inArray(display.getDataIds(),id)){//判断id是否合法（若不存在则定位到第一张）
								for(i=0;i<set.data.length;i++){
									if(set.data[i].photoid==id){
										var index=i;
									}
								}
							}else{
								showDialog( D.L( '对不起，该图片可能已被删除，请选择浏览其他图片！' ) );
								var index=0;
							}
							var items=set.data.slice(index,index+set.slideNum);
							display.call_datatToDom(items,true,id);	
						}
					},
					nextSlide:function(){//切换下一排,这个方法是根据当前展示图片的ID值进行计算下一排数据/此步可以用ajax
						var _id=(thumbList.find(set.thumbChild+":last-child").attr("photoId")!="0")?thumbList.find(set.thumbChild+":last-child").attr("photoId"):set.last_id;
						for(i=0;i<set.data.length;i++){
							if(set.data[i].photoid==_id){
								var _index=i;	
							}
						}
						var _nextIndex=(_index+1==set.data.length)?0:_index+1;
						var items=set.data.slice(_nextIndex,_nextIndex+set.slideNum);
						display.call_datatToDom(items,false);
					},
					prevSlide:function(){//切换前一排/此步可以用ajax
						var _id=(thumbList.find(set.thumbChild+":first-child").attr("photoId")!="0")?thumbList.find(set.thumbChild+":first-child").attr("photoId"):set.first_id;
						for(i=0;i<set.data.length;i++){
							if(set.data[i].photoid==_id){
								var _index=i;	
							}
						}
						var _nextIndex=((_index-set.slideNum)<0)?0:(_index-set.slideNum);
						var items=set.data.slice(_nextIndex,_nextIndex+set.slideNum);
						display.call_datatToDom(items,false);
					},
					call_datatToDom:function(array,isEchoInfo,id){//将数据写入DOM（公用回调）
						var _thumbList="";
						var _displayHtml="";
						for(i=0;i<array.length;i++){
							if(isEchoInfo!=false){
								var _focus=(i==0)?"class='focus'":"";
							}else{
								var _focus=(array[i].photoid==viewInfo.find(".bigImg").attr("viewId"))?"class='focus'":"";
							}
							var img_thumb=set.imgDir+array[i].imgurl_thumb;
							var img_view=set.imgDir+array[i].imgurl_view;
							var img_source=set.imgDir+array[i].imgurl;
							if(i==0){
								_displayHtml+="<ul class='items_list'><li class='"+set.dispClass+" thumbLoader' title='"+array[i].title+"'><img src='"+img_view+"' viewId='"+array[i].photoid+"' alt='"+array[i].title+"' id='"+set.bigImgId+"' class='bigImg' /><span class='block view_prev'><a  hidefocus='true' href="+"javascript:;"+" title='"+D.L( '上一张' )+"'>&nbsp;</a></span><span  class='block view_next'><a hidefocus='true' href="+"javascript:;"+"  title='"+D.L( '下一张' )+"'>&nbsp;</a></span></li></ul><div><p class='img_intro'><strong>"+array[i].intro+"</strong></p><p class='img_source_link'><a href='"+img_source+"' title='' target='_blank'>"+D.L( '查看原图' )+"</a></p></div>";
							}
							_thumbList+="<li index='"+i+"' onclick='display.showImg("+array[i].photoid+")' photoId='"+array[i].photoid+"' "+_focus+"><a style='width:70px; height:50px;' href='javascript:display.showImg("+array[i].photoid+");' title='"+array[i].title+"' href='###'><table height='100%' width='100%' cellspacing='0' cellpadding='0' border='0'><tr><td valign='middle'><img  class='thumbLoader thumbImg' src='"+img_thumb+"' /></td></tr></table></a></li>";
						}
						if($.trim(thumbList.html())!=""){
							var _slide=[];
							for(i=0;i<thumbList.find(set.thumbChild).size();i++){
								_slide.push(thumbList.find(set.thumbChild).eq(i).attr("photoId"));	
							}
							if(display.inArray(_slide,id)!=true){
								thumbList.html(_thumbList);
								if(thumbList.find(set.thumbChild).size()<set.slideNum){
									insertToList();	
								}
							}
						}else{
							insertToList();
						}
						function insertToList(){
							thumbList.html(_thumbList);
							if(thumbList.find(set.thumbChild).size()<set.slideNum){
								thumbList.append("<li photoId='0' onclick='display.showImg("+set.first_id+")'><a style='width:70px; height:50px;' href='javascript:display.showImg("+set.first_id+")' title='"+D.L( '返回第一张' )+"'><table height='100%' width='100%' cellspacing='0' cellpadding='0' border='0'><tbody><tr><td valign='middle'><img src='"+set.firstIco+"' alt='"+D.L( '返回第一张' )+"'/></td></tr></tbody></table></a></li>");	
							}
						}
						if(isEchoInfo!==false){
							viewInfo.html(_displayHtml);
						}
						var current_id=viewInfo.find(".bigImg").attr("viewId");
						for(i=0;i<set.data.length;i++){
							if(set.data[i].photoid==current_id){
								var index=i;	
							}
						}
						var _nextIndex=(index+1<set.data.length)?(index+1):0;
						if(index==0){
							var _prevIndex=0;
						}else{
							var _prevIndex=index-1;
						}
						set.prev_id=set.data[_prevIndex].photoid;
						set.next_id=set.data[_nextIndex].photoid;
						viewInfo.find(".view_prev a").attr({href:"javascript:display.showImg("+set.prev_id+");"});
						viewInfo.find(".view_next a").attr({href:"javascript:display.showImg("+set.next_id+");"});
						prev_slide.find("a").attr({href:"javascript:display.prevSlide()"});
						next_slide.find("a").attr({href:"javascript:display.nextSlide()"});
						$("#"+set.bigImgId).load(function(){/*图片加载完毕后移除loadign状态*/
							viewInfo.find(".view_prev,.view_next").css({width:parseInt($(this).width()/2)+"px",height:parseInt($(this).height())+"px"});
							$("."+set.dispClass).width(parseInt($(this).width())).height(parseInt($(this).height()));
							$(this).removeClass("thumbLoader");
						}).error(function(){//alert("404错误,图片文件不存在");
							$(this).attr({src:set.onErrorFileB})
						});	
						$("."+set.thumbImgClass).load(function(){
							$("."+set.dispClass).removeClass("thumbLoader");
						}).error(function(){//alert("404错误,图片文件不存在");
							$(this).attr({src:set.onErrorFileB})
						});	
					},
					inArray:function(_arr,_strNum){
						for(i in _arr){
							if(_arr[i]==_strNum){
								return true;	
							}
						}
					},
					init:function(){
						this.cache();
						this.checkPlayButton();
						jDelay.change(function(){
							jStopPlay.click();;
							display.checkPlayButton();					   
						});
						jAutoPlay.click(function(){
							$(this).attr({disabled:true});	
							jStopPlay.attr({disabled:false});
							var _cid=parseInt($("#"+set.bigImgId).attr("viewId"));
							display.showImg(_cid);
							if(thumbList.find(set.thumbChild).filter("[photoId="+_cid+"]").next().html()!=null){
								if(thumbList.find(set.thumbChild).filter("[photoId="+_cid+"]").next().attr("photoId")=="0"){
									display.showImg(set.first_id);
								}else{
									intervalnum=parseInt(thumbList.find(set.thumbChild).filter("[photoId="+_cid+"]").next().attr("index"));	
								}
							}else{
								var _ccid=parseInt(thumbList.find("."+set.focusClass).attr("photoId"));
								for(i=0;i<set.data.length;i++){
									if(set.data[i].photoid==_ccid){
										var _in=i;	
									}
								}
								var nextindex=(_in+1>=set.data.length)?0:_in+1;
								var nextid=set.data[nextindex].photoid;
								display.showImg(nextid);
								intervalnum=0;
							}
							myTime=setInterval(function(){
								display.showImg(parseInt(thumbList.find(set.thumbChild).eq(intervalnum).attr("photoId")));
								if(thumbList.find(set.thumbChild).eq(intervalnum).attr("photoId")=="0"){
									display.showImg(set.first_id);
									intervalnum=0;
								}
								if((intervalnum)>=thumbList.find(set.thumbChild).not("[photoId=0]").size()){
									var _ccid=parseInt(thumbList.find("."+set.focusClass).attr("photoId"));
									for(i=0;i<set.data.length;i++){
										if(set.data[i].photoid==_ccid){
											var _in=i;	
										}
									}
									var nextindex=(_in+1>=set.data.length)?0:_in+1;
									var nextid=set.data[nextindex].photoid;
									display.showImg(nextid);
								}
								intervalnum++;
							},parseInt(jDelay.val()));
							next_slide.click(function(){
								jStopPlay.click();											  
							});
							prev_slide.click(function(){
								jStopPlay.click();										  
							});
							thumbList.find(set.thumbChild).click(function(){
								jStopPlay.click();											  
							});
							viewInfo.find(".view_next,.view_next a,.view_prev,.view_prev a").click(function(){
								jStopPlay.click();										 
							});
						});
						jStopPlay.click(function(){
							$(this).attr({disabled:true});
							jAutoPlay.attr({disabled:false});
							display.stopPlay();
						});
					},
					checkPlayButton:function(){
						if(jDelay.val()=="null"){
							jAutoPlay.attr({disabled:true});
						}else{
							jAutoPlay.attr({disabled:false});
						}
					},
					getDataIds:function(){
						var _ids=[];
						for(i=0;i<set.data.length;i++){
							_ids.push(set.data[i].photoid);
						}
						return _ids;
					},
					getMiniListIds:function(){
						var _ids=[];
						for(i=0;i<thumbList.find(set.thumbChild).not("[photoId=0]").size();i++){
							_ids.push(parseInt(thumbList.find(set.thumbChild).eq(i).attr("photoId")));
						}
						return _ids;
					},
					stopPlay:function(){
						if(typeof(myTime)=="undefined"){
							return false;
						}else{
							if(myTime){
								jAutoPlay.attr({disabled:false});
								clearInterval(myTime);
							}
						}
					}
			};
			display.init();//初始化
			return this;//返回选择器对象本身
		}
	});		  
})(jQuery);//这是一个完整的闭包