<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	日志模型($)*/

!defined('DYHB_PATH') && exit;

class BlogModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'blog',
			'props'=>array(
				'blog_id'=>array('readonly'=>true),
				'category'=>array(Db::BELONGS_TO=>'CategoryModel','target_key'=>'category_id'),
				'user'=>array(Db::BELONGS_TO=>'UserModel','target_key'=>'user_id'),
			),
			'attr_protected'=>'blog_id',
			'autofill'=>array(
				array('user_id','getUserid','create','callback'),
				array('blog_ip','getIp','create','callback'),
			),
			'check'=>array(
				'blog_title'=>array(
					array('require',G::L('日志标题不能为空')),
					array('max_length',300,G::L('日志最大长度为300'))
				),
				'blog_content'=>array(
					array('require',G::L('日志内容不能为空')),
				),
				'blog_fromurl'=>array(
					array('empty'),
					array('url',G::L('来源地址必须为正确的URL 格式')),
				),
				'blog_urlname'=>array(
					array('empty'),
					array('number_underline_english',G::L('URL 别名只能是字母，数字和下划线')),
					array('max_length',50,G::L('URL 别名最大长度为50')),
				),
				'blog_gotourl'=>array(
					array('empty'),
					array('url',G::L('转向地址必须为正确的URL 格式')),
				),
			),
		);
	}

	static function F(){
		$arrArgs=func_get_args();
		return ModelMeta::instance(__CLASS__)->findByArgs($arrArgs);
	}

	static function M(){
		return ModelMeta::instance(__CLASS__);
	}

	public function logAction($arrLogIds,$sAction){
		foreach($arrLogIds as $nValue){
			$oBlog=self::F('blog_id=?',$nValue)->query();
			switch($sAction){
				case 'draft':
					$oBlog->blog_isshow=0;
					break;
				case 'unDraft':
					$oBlog->blog_isshow=1;
					break;
				case 'top':
					$oBlog->blog_istop=1;
					break;
				case 'unTop':
					$oBlog->blog_istop=0;
					break;
				case 'lock':
					$oBlog->blog_islock=1;
					break;
				case 'unLock':
					$oBlog->blog_islock=0;
					break;
				case 'page':
					$oBlog->blog_ispage=1;
					break;
				case 'blog':
					$oBlog->blog_ispage=0;
					break;
			}
			$oBlog->setAutofill(FALSE);
			$oBlog->save(0,'update');
		}
	}

	public function getDateline(){
		$nId=intval(G::getGpc('id','P'));
		$nNewyear=intval(G::getGpc('newyear','P'));
		$nNewmonth=intval(G::getGpc('newmonth','P'));
		$nNewday=intval(G::getGpc('newday','P'));
		$nNewhour=intval(G::getGpc('newhour','P'));
		$nNewmin=intval(G::getGpc('newmin','P'));
		$nNewsec=intval(G::getGpc('newsec','P'));
		if($nId){
			if(checkdate($nNewmonth,$nNewday,$nNewyear)){
				if(substr(PHP_OS,0,3)=='WIN' && $nNewyear<1970){
					return CURRENT_TIMESTAMP;
				}
				else{
					return mktime($nNewhour,$nNewmin,$nNewsec,$nNewmonth,$nNewday,$nNewyear);
				}
			}
			else{
				return CURRENT_TIMESTAMP;
			}
		}
		else{
			return CURRENT_TIMESTAMP;
		}
	}

	public function getUserid(){
		$arrUserData=$GLOBALS['___login___'];
		return $arrUserData['user_id']?$arrUserData['user_id']:-1;
	}

	public function getIp(){
		return E::getIp();
	}

	public function getBlogType(){
		$arrBlogFlags=G::getGpc('blog_flags','P');
		return $arrBlogFlags!==null?implode(',',$arrBlogFlags):'';
	}

}
