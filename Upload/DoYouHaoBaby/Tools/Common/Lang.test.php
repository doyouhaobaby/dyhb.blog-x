<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
	语言包测试代码($) */

exit();

/** 防止PHP页面乱码 */
ini_set( 'default_charset','utf-8' );

/** 导入语言包制作工具 */
include(dirname(__FILE__).'/../dblog3/DoYouHaoBaby/Tools/Lang.php');

/** 启动计算器 */
$oTimer = new Timer();
$oTimer->start();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>语言包制作工具 For DoYouHaoBaby Framework</title>
<style type="text/css">
body {
	background-color:#F7F7F7;
	font-family: Arial;
	font-size: 12px;
	line-height:150%;
}
a{ color:blue;text-decoration:none; }
a:hover{ color:#000;text-decoration:underline; }
.title{
	font-size:20px;
	color:blue;
	margin:50px auto 10px auto;
	text-align:center;
}
.infobox{
	margin:0 auto;
	text-align:center;
}
.infomessage {
	background-color:#FFFFFF;
	font-size: 12px;
	color: #666666;
	width:800px;
	margin:0 auto;
	padding:10px;
	list-style:none;
	border:#DFDFDF 1px solid;
	text-align:center;
}
.infomessage p {
	line-height: 18px;
	margin: 5px 20px;
}
.marginbot{
	text-align:center;
}
</style>
</head>
<body>
	 <!-- 标题  -->
	 <div class="title">语言包制作工具 For DoYouHaoBaby Framework</div>
		 <div class="infobox">
			<h4 class="infomessage">
			<div style="height: 550px; width: 800px; overflow:auto;text-align:left;margin-left:100px;">
			<?php
				// 批量处理
				$sFromLangPath = dirname(__FILE__).'/../dblog3/install/Theme/Default/Public/Php/Lang/En-us';
				$sToLangPath = dirname(__FILE__).'/../dblog3/install/Theme/Default/Public/Php/Lang/En-us';
				$sThreeLangPath = dirname(__FILE__).'/../dblog3/install/Theme/Default/Public/Php/Lang/Zh-cn';
				MakeLang::many( $sThreeLangPath,$sFromLangPath,$sToLangPath );

				$oTimer->stop();
			?>
			</div>
			</h4>
			<p class="marginbot" id="">程序运行时间 <span style="color:green;"><?php echo $oTimer->spent();?></span> 秒 | <a href="#" onclick="window.location.reload();">刷新</a></p>
		 </div>
	</div>
</body>
</html>