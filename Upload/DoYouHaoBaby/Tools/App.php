<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
	创建一个应用程序($) */

/** 防止PHP页面乱码 */
ini_set('default_charset','utf-8');

/** 读取语言包 */
$sLang=!empty($_GET['lang']) ? ucfirst(addslashes($_GET['lang'])) : 'Zh-cn';
if(is_file(dirname(__FILE__).'/App/'.$sLang.'/App.lang.php')){
	$arrLang=(array)include(dirname(__FILE__).'/App/'.$sLang.'/App.lang.php');
}
else{
	$arrLang=(array)include(dirname(__FILE__).'/App/Zh-cn/App.lang.php');
}

/** 判断工具是否被锁定了 */
if(is_file('./App/AppLock.php')){
	exit(sprintf("This Tool Is Locked，Please Delete File < %s > ,Then You Can use it！",'./App/AppLock.php'));
}

/** 锁定项目创建工具 */
if(isset($_GET['action']) && $_GET['action']=='lock'){
	$sLock ="DoYouHaoBaby Framework App Tools Is Locked !";
	$sPath='./App/AppLock.php';
	if(!file_put_contents($sPath,$sLock)){// 创建锁定文件
		exit(sprintf('Create Locked File：%s failed！',$sPath));
	}
	else{
		header("Location: App.php");// 跳转
	}
}

function listDir($sDir,$bFullPath=FALSE){
	if(is_dir($sDir)){
		$hDir=opendir($sDir);
		while(($sFile=readdir($hDir))!== false){
			if((is_dir($sDir."/".$sFile)) && $sFile!="." && $sFile!=".." && $sFile!='_svn'){
				if($bFullPath===TRUE){$arrFiles[]=$sDir."/".$sFile;}
				else{$arrFiles[]=$sFile;}
			}
		}
		closedir($hDir);
		return $arrFiles;
	}
	else{
		return false;
	}
}

$arrListLang=listDir('./App');

/** 如果填写了应用程序名字 */
if(isset($_POST['app_name'])){
	$arrError=array();// 错误消息
	$sAppName=preg_replace('[^a-z0-9_]', '', $_POST['app_name']);// 过滤App_name中的非法字符
	if(!$sAppName || $sAppName != $_POST['app_name']){
		$arrError[]=sprintf($arrLang[0], $sAppName);
	}
	$sAppPath=trim($_POST['app_path']);
	$sReallyPath=realpath($sAppPath);
	if(!$sAppPath || $sReallyPath == dirname(__FILE__) || !is_dir($sReallyPath)){
		$arrError[]=sprintf($arrLang[1], $sAppPath);
	}
	else{
		$sAppPath=$sReallyPath;
	}
	$sEnterPath=trim($_POST['enter_path']);// 检测独立服务器部署下的入口文件
	$bIndependent=$_POST['app_type']=='independent' ? true :false;
	if($bIndependent and empty($sEnterPath)){
		$arrError[]=sprintf($arrLang[2], $sEnterPath);
	}
	if($bIndependent and substr($sEnterPath,-4)!='.php'){
		$arrError[]=sprintf($arrLang[3], $sEnterPath);
	}
	if($bIndependent===false){
		$sEnterPath='';
	}
	if(empty($arrError)){
		require dirname(dirname(__FILE__)) . '/Extension/Generator/GeneratorApplication.class.php';// 创建应用程序
		ob_start();
		$oApp=new GeneratorApplication();
		$oApp->APP($sAppName, $sAppPath,$sEnterPath);
		$sOutput=ob_get_clean();
		$sAppPath=$sAppName='';
	}
}

else{
	$arrError=array();
	$sAppPath=dirname(dirname(dirname(__FILE__)));
	$sAppName='home';
	$sEnterPath=$sAppPath.'/index.php';
	$sOutput='';
	$bIndependent=false;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $arrLang[4];?></title>
<style type="text/css">
body{font:400 14px/25px '微软雅黑',Tahoma,sans-serif;background:#F7F7F7;}
a{text-decoration:none; color:#007979; padding:0 5px;}
a:hover{color:#FF0000;}
img{border:0}
input[type="text"]{ color:red; border-bottom:2px dashed blue;border-top:1px solid #fff;border-left:1px solid #fff;border-right:1px solid #fff;}
h1{padding:10px 0;}
h2{font-size:14px;padding:10px;color:#000;border:1px solid #e0e0e0;background:#ffd;}
.hello{width:750px;height:100%;padding:10px; margin:50px auto 0; border:1px solid #DFDFDF;overflow:hidden;background:#fff;}
.output{color: #000; background-color: #45ccf5; border: 1px solid #000; padding: 10px; margin: 10px;width:95%; }
.error{ background:yellow; padding:5px; border:2px solid #000;}
.title{ color:#007979; }
.lang_list{ border:1px solid #000;background:#ccc;padding-left:5px;padding-bottom:5px;padding-right:5px; }
.lang_title{ font-weight:bold;margin-left:20px;color:#000;margin-top:10px; }
.lang_list ul{ list-style:none; }
.lang_list ul li{ float:left; }
.lock{ margin-top:10px;border-top: 1px #dbb dotted; border-bottom: 1px #dbb dotted;background:#fdd;padding-left:5px;padding-bottom:5px;padding-right:5px; }
.lock_title{ font-weight:bold;margin-left:20px;color:#000;margin-top:10px; }
.description{ color:#ccc; }
.footer{font-size:14px;padding:5px;color:#000;border:1px solid #e0e0e0;background:#ccc;text-align:center;}
.warning .title{ color:red; }
.warning{ color:gray; }
.clear{ clear:both; }
</style>
</head>
<body>
<div class="hello">
<h1><a href="http://doyouhaobaby.net" title=""><img src="../LibCom/Img/Logo.gif" title="DoYouHaoBaby官方站"/></a></h1>
<h2><?php echo $arrLang[5];?></h2>
<?php if (!empty($sOutput)): ?>

	 <p class='output'>
		<?php echo nl2br(htmlspecialchars($sOutput)); ?>
	 </p>

<?php else: ?>

	 <?php if (!empty($arrError)): ?>

		  <p class="error">
			 <?php echo implode('<br />', $arrError); ?>
		  </p>

	 <?php endif; ?>
	 <div class="lang_list">
		<div class="lang_title">Please select your language !</div>
		<select name="lang-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;">
			<option value="">-- Please select your language --</option>
			<?php foreach($arrListLang as $sListLang):?>
			<option value="?lang=<?php echo $sListLang;?>" <?php if(strtolower($sLang)==$sListLang):?>selected<?endif;?>><?php echo $sListLang;?></option>
			<?endforeach;?>
		</select>
	</div>
<div class="lock">
	<div class="lock_title">Please Lock This Tools After User it!</div>
	 <a href="app.php?action=lock" title=""><img src="./App/Lock.png"/></a>
</div>
	 <form id="form1" name="form1" method="post" action="App.php?lang=<?php echo strtolower($sLang);?>">
		<p>
		  <strong class="title"><?php echo $arrLang[6];?></strong>
		  <input name="app_name" type="text" id="app_name" size="20" value="<?php echo htmlspecialchars($sAppName); ?>" />
		  <br />
		  <span class="description"><?php echo $arrLang[7];?></span>
		</p>
		<p>
		  <strong class="title"><?php echo $arrLang[8];?></strong>
		  <input name="app_path" type="text" id="app_path" size="60"  value="<?php echo htmlspecialchars($sAppPath); ?>" />
		  <br />
		  <span class="description"><?php echo $arrLang[9];?></span>
		</p>
	  <p>
		  <strong class="title"><?php echo $arrLang[10];?></strong>
			<br />
		 <p>
				<input type="radio" name="app_type" value="virtual"  <?php if(!$bIndependent):?>checked="checked"<?endif;?> onclick="document.getElementById('hidden1').style.display='none';">
				<label><?php echo $arrLang[11];?></label><br />
				<span class="description"><?php echo $arrLang[12];?></span>
			 </p>
		  <p>
				<input type="radio" name="app_type"  value="independent" onclick="document.getElementById('hidden1').style.display='block';" <?php if($bIndependent):?>checked="checked"<?endif;?>>
				<label><?php echo $arrLang[13];?></label><br />
				<span class="description"><?php echo $arrLang[14];?></span>
			</p>
		 <p id="hidden1" style="display:none;">
		  <strong class="title"><?php echo $arrLang[15];?></strong>
		  <input name="enter_path" type="text" size="60" value="<?php echo htmlspecialchars($sEnterPath); ?>" />
		  <br />
		  <span class="description"><?php echo $arrLang[16];?></span>
		 </p>
		</p>
		<p>
		  <label>
		  <input type="submit" name="create" id="create" value="<?php echo $arrLang[17];?>" />
		  </label>
		</p>
	  <p class="warning">
		 <strong class="title"><?php echo $arrLang[18];?></strong>
			<br />
		 <?php echo $arrLang[19];?>
		 <br>
		 <?php echo $arrLang[20];?>
		 <br><?php echo $arrLang[21];?>
	  </p>
	 </form>

<?php endif; ?>
<div class="footer"><?php echo $arrLang[22];?></div>
</div>
</body>
</html>