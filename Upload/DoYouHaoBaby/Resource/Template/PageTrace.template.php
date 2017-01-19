<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   系统调试模版($) */

!defined( 'DYHB_PATH' ) && exit;
?>
<div id="dyhb_page_trace" style="display:none;background:white;margin:6px;font-size:14px;border:1px dashed silver;padding:8px;">
<fieldset id="querybox" style="margin:5px;">
<legend style="color:gray;font-weight:bold"><?php echo G::L( '页面Trace信息','Dyhb' );?></legend>
<div style="overflow:auto;height:300px;text-align:left;">
<?php foreach ($arrTrace as $sKey=>$sInfo){
echo $sKey.' : '.$sInfo.'<br/>';
}?>
</div>
</fieldset>
</div>