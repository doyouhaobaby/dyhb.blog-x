/**
 * DoYouHaoBaby Framework专用Ajax格式
 */
var m={
	'\b': '\\b',
	'\t': '\\t',
	'\n': '\\n',
	'\f': '\\f',
	'\r': '\\r'
};

Dyhb.Ajax.Dyhb=Dyhb.Ajax.Dyhb || {};

/* 程序版本 */
Dyhb.Ajax.Dyhb.Version='1.1(2011-11-27)';

/* ajax方法 */
Dyhb.Ajax.Dyhb.Method="POST";

/* 提示信息对象 */
Dyhb.Ajax.Dyhb.TipTarget='DyhbAjaxResult';

/* 是否显示提示信息 */
Dyhb.Ajax.Dyhb.ShowTip=true;

/* 提示信息 */
/*Dyhb.Ajax.Dyhb.UpdateTips=Dyhb.L('数据处理中...','js');这里造成了系统频繁解析*/
Dyhb.Ajax.Dyhb.UpdateTips='loading...';

/* 返回状态码 */
Dyhb.Ajax.Dyhb.Status=0; 

/* 返回信息 */
Dyhb.Ajax.Dyhb.Info='';

/* 返回数据 */
Dyhb.Ajax.Dyhb.Data='';

/* 依次是处理中 成功 和错误 显示的图片 */
Dyhb.Ajax.Dyhb.Image=['','',''];

/* 是否显示提示信息，默认开启 */
Dyhb.Ajax.Dyhb.ShowTip=true;

/* JSON EVAL XML */
Dyhb.Ajax.Dyhb.Type=''; 

/* 是否完成 */ 
Dyhb.Ajax.Dyhb.Complete=false; 
Dyhb.Ajax.Dyhb.Debug=false; 

/* ajax回调函数 */
Dyhb.Ajax.Dyhb.Response;
Dyhb.Ajax.Dyhb.Options={ };
