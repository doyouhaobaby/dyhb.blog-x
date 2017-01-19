/**
 * DoYouHaoBaby Framework专用Ajax格式
 *
 * @param string sTarget 消息DIV ID
 * @param string sTips Ajax加载消息
 */
Dyhb.Ajax.Dyhb.Loading=function(sTarget,sTips){
	if(sTarget){
		sTraget=document.getElementById(sTarget);
		sTraget.style.display="block";
		if('' != Dyhb.Ajax.Dyhb.Image[0]){
			sTraget.innerHTML= '<IMG SRC="'+Dyhb.Ajax.Dyhb.Image[0]+'"  BORDER="0" ALT="loading..." align="absmiddle"> '+sTips;
		}
		else{
			sTraget.innerHTML=sTips;
		}
	}
};
