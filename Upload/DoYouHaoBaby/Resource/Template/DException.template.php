<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   系统异常模版($) */

!defined('DYHB_PATH') && exit;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>DoYouHaoBaby <?php echo G::L( '系统消息','Dyhb' );?></title>
<style type="text/css">
body {
	background-color:#F7F7F7;
	font-family: Arial;
	font-size: 11px;
	line-height:150%;
	word-break:break-all;
}
#head_title{
   background:#7EC0EE;
   border:3px solid #FFE4B5;
   padding:13px;
   font-size:10px;
}
.main {
	background-color:#FFFFFF;
	margin-top:20px;
	font-size: 13px;
	color: #000;
	width:580px;
	margin:10px 200px;
	padding:10px 40px;
	list-style:none;
	border:#DFDFDF 1px solid;
}
.main p {
	line-height: 18px;
	margin: 5px 20px;
}
.main h1 {
    text-align:center;
    color:red;
    font-size:25px;
    padding:10px auto;
}
.main h3{
   font-size:13px;
   color:#fff;
}
.message{
	padding:15px;
	margin:10px 0;
	background:#FFD;
	line-height:100%;
}
.title,.location{
	margin:4px 0;
	color:purple;
	font-weight:bold;
}
a:link,a:visited{
   color:#ccc;
   padding:3px 6px;
}
a:hover{
   color:#000;
}
#trace_content{
   margin-top:10px;
   background:#ccc;
   padding:10px;
   border:2px solid #D1EEEE;
}
.red{
   font-weight:bold;
   color:#fff;
}
</style>
</head>
<body>
<div class="main">
<h1>DoYouHaoBaby Exception<?php echo G::L( '系统消息','Dyhb' );?></h1>
<?php if(isset($arrError['file'])) {?>
<p id="head_title"><strong class="location"><?php echo G::L( '错误位置：','Dyhb' );?></strong>　FILE: <span class="red"><?php echo $arrError['file'] ;?></span>　LINE: <span class="red"><?php echo $arrError['line'];?></span></p>
<?php }?>
<div class="message">
<p><?php echo $arrError['message']; ?></p>
<?php if(isset($arrError['trace'])) {?>
<div id="trace_content">
<p class="title">[ TRACE ]</p>
<p id="trace">
<?php echo $arrError['trace'];?>
</p>
</div>
<?php }?>
</div>
<h3>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>">[<?php echo G::L( '重试','Dyhb' );?>]</a>
<a href="javascript:history.back();">[<?php echo G::L( '返回上一页','Dyhb' );?>]</a>
<a href="<?php if( defined('__APP__') ) { echo __APP__; } else{ echo '#'; } ?>">[<?php echo G::L( '返回首页','Dyhb' );?>]</a>
</h3>
</div>
</body>
</html>