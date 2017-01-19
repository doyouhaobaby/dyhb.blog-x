<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   URL 跳转($) */

$sUrl=urldecode(trim($_GET['go']));
$sUrl=str_replace(array("%2F","%3D","%3F","&amp;"),array('/','=','?','&'),$sUrl);
header("Location: {$sUrl}");
exit();
