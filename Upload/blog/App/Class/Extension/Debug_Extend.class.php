<?php 
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   Debug调式函数($) */

!defined('DYHB_PATH') && exit;

class Debug_Extend{

	static public function showtrace(){
		return "<script type=\"text/javascript\">
<!--
function showTrace(){
	\$('#trace_info').html(\$('#dyhb_page_trace').html());
	\$('#dyhb_page_trace').html(' ');
}
jQuery(function(\$){
	showTrace();
});
//-->
</script>
<div id=\"trace_info\">loading...</div>";
	}

	static public function runtime(){
		return "<script type=\"text/javascript\">
<!--
function showTime(){
	\$('#run_time').html(\$('#dyhb_run_time').html());
	\$('#dyhb_run_time').html(' ');
}
jQuery(function(\$){
	showTime();
});
//-->
</script>
<div class=\"run_time_result\" id=\"run_time\">loading...</div>";
	}

}
