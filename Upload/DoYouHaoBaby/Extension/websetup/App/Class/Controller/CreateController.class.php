<?php
/**

 //  [Websetup!] 图像界面工具
 //  +---------------------------------------------------------------------
 //
 //  “Copyright”
 //  +---------------------------------------------------------------------
 //  | (C) 2010 - 2011 http://doyouhaobaby.net All rights reserved.
 //  | This is not a free software, use is subject to license terms
 //  +---------------------------------------------------------------------
 //
 //  “About This File”
 //  +---------------------------------------------------------------------
 //  | websetup Create控制器 ( 代码创建工具 )
 //  +---------------------------------------------------------------------

*/

!defined('DYHB_PATH') && exit;

class CreateController extends InitController{

	private $_sClassName='';

	public function controllers(){
		$sDyhbWebsetupApp=G::cookie('dyhb_websetup_app');
		if(!is_file($sDyhbWebsetupApp."/App/Config/Config.php")){
			$this->E(G::L("项目%s的配置文件%s不存在！",'app',null,$sDyhbWebsetupApp,$sDyhbWebsetupApp."/App/Config/Config.php"));
		}
		$arrControllers=array();
		$arrControllerFiles=E::includeDirPhpfile($sDyhbWebsetupApp.'/App/Class/Controller');
		if(is_array($arrControllerFiles)){
			foreach($arrControllerFiles as $sKey=>$sVal){
				$sPath=$sVal;
				$sVal=basename($sVal);
				$sVal=str_replace('Controller','',$sVal);
				$sVal=str_replace('.class.php','',$sVal);
				$sClass=ucfirst($sVal).'Controller';
				$arrControllers[$sKey]['class']=$sClass;
				$arrControllers[$sKey]['module']=$sVal;
				$arrControllers[$sKey]['control']=$sVal;
				$arrControllers[$sKey]['path']=$sPath;
			}
		}
		$this->assign('arrControllers',$arrControllers);
		$this->assign('sHelp',G::L('查看已有的控制器，并能够创建新控制器。'));
		$this->display();
	}

	public function newcontroller($bModel=false){
		$sPostName=G::getGpc('name');
		$sPostAttribute=G::getGpc('attribute');
		$sPostAction=G::getGpc('action');
		$sTableName=G::getGpc('table_name');
		$sDyhbWebsetupApp=G::cookie('dyhb_websetup_app');
		if(!is_file($sDyhbWebsetupApp."/App/Config/Config.php")){
			$this->E(G::L("项目%s的配置文件%s不存在！",'app',null,$sDyhbWebsetupApp,$sDyhbWebsetupApp."/App/Config/Config.php"));
		}
		$sTypeName=$bModel ? G::L("模型"):G::L("控制器");
		if($sPostName==''){
			$this->E(G::L('%s不能为空！','app',null,$sTypeName));
		}
		if($sPostAction==''){
			$sPostAction='public|index|*|我是一个测试方法';
		}
		$sGroup='';
		$sName=$sPostName;
		$arrAttribute=array_filter(explode("\n", $sPostAttribute));
		$arrAction=array_filter(explode("\n", $sPostAction));
		Check::RUN();
		if(!Check::C($sName,'number_underline_english')){
			$this->E(G::L('%s名字格式不正确！','app',null,$sTypeName));
		}
		$this->_sClassName=$sName;
		$sPath=$sDyhbWebsetupApp.'/App/Class/'.($bModel ?'Model' : 'Controller').'/'.ucfirst($sName).($bModel ?'Model' : 'Controller').'.class.php';
		$sCode=$this->makeCode_(
			array(
				'name'=>strtolower(trim($sName)),
				'attribute'=>$arrAttribute,
				'action'=>$arrAction,
		),!$bModel
		);
		if(file_exists($sPath)){
			$this->E(G::L('%s:%s已经存在！','app',null,$sTypeName,$sPath));
		}
		if(file_put_contents($sPath,$sCode)){
			$this->S(G::L('创建%s，路径：%s成功！','app',null,$sTypeName.$sName,$sPath));
		}
		else{
			$this->E(G::L('创建%s，路径：%s失败！','app',null,$sTypeName.$sName,$sPath));
		}
	}

	public function getcolumns(){
		$sTableName=G::getGpc('table','P');
		$oDb=Db::RUN();
		$this->assign('arrColumns',$oDb->getConnect()->getColumnNameList($sTableName));
		$this->assign('sTableName' , $sTableName);
		$this->display();
	}

	public function models(){
		$sDyhbWebsetupApp=G::cookie('dyhb_websetup_app');
		if(!is_file($sDyhbWebsetupApp."/App/Config/Config.php")){
			$this->E(G::L("项目%s的配置文件%s不存在！",'app',null,$sDyhbWebsetupApp,$sDyhbWebsetupApp."/App/Config/Config.php"));
		}
		$arrModels=array();
		$arrModelFiles=E::includeDirPhpfile($sDyhbWebsetupApp.'/App/Class/Model');
		if(is_array($arrModelFiles)){
			foreach($arrModelFiles as $sKey=>$sVal){
				$sPath=$sVal;
				$sVal=basename($sVal);
				$sVal=str_replace('Model','',$sVal);
				$sVal=str_replace('.class.php','',$sVal);
				$arrModels[$sKey]['class']=$sVal;
				$arrModels[$sKey]['module']=$sVal;
				$arrModels[$sKey]['tablename']=$sVal;
				$arrModels[$sKey]['path']=$sPath;
			}
		}
		$oDb=Db::RUN();
		$arrTables=$oDb->getConnect()->getTableNameList();
		$this->assign('arrTables',$arrTables);
		$this->assign('arrModels',$arrModels);
		$this->assign('sHelp',G::L('查看已有的模型，并能够创建新模型。'));
		$this->display();
	}

	public function newmodel(){
		$this->newcontroller(true);
	}

	private function makeCode_(array $arrayData,$isController=true){
		$sControllerName=ucwords($arrayData['name']);
		$arrAttribute=$arrayData['attribute'];
		$arrAction=array();
		if($isController===TRUE)
			$arrAction[]='public|__contruct|*|构造函数|parent++__construct();';
		$arrAction=array_merge($arrAction,$arrayData['action']);
		$sTypeValue=array();
		if($isController===true){
			$sTypeValue=array(
			'Controller',
			G::L('控制器')
			);
		}
		else{
			$sTypeValue=array(
			'Model',
			G::L('模型')
			);
		}
		$sDyhbWebsetupApp=G::cookie('dyhb_websetup_app');
		$arrApps=Dyhb::normalize($sDyhbWebsetupApp,'/');
		$sAppName=array_pop($arrApps);
		$sStart="<?php
/**

 //  [DoYouHaoBaby!] Init APP - {$sAppName}
 //  +---------------------------------------------------------------------
 //
 //  “Copyright”
 //  +---------------------------------------------------------------------
 //  |(C)2010 - 2011 http://doyouhaobaby.net All rights reserved.
 //  | This is not a free software, use is subject to license terms
 //  +---------------------------------------------------------------------
 //
 //  “About This File”
 //  +---------------------------------------------------------------------
 //  | {$sControllerName} {$sTypeValue[1]}
 //  +---------------------------------------------------------------------

*/

!defined('DYHB_PATH')&& exit;

class {$sControllerName}{$sTypeValue[0]} extends {$sTypeValue[0]}{
";
		if(!$isController){
			$sPostThin=G::getGpc('thin','P');
			if($sPostThin==0){
				$sStart.="
	/**
	 * 返回对象的定义
	 *
	 * @access public
	 * @static
	 * @return array
	 */
	static public function init__(){
		return array(
			/**
			 * 指定该 ActiveRecord 要使用的行为插件
			 *
			 * @return string
			 */
			'behaviors'=>'rbac',

			/**
			 * 指定行为插件的配置
			 *
			 * @return array
			 */
			'behaviors_settings'=>array(
				// '插件名'=>array('选项'=>设置),
				'rbac'=>array(
							'password_prop'=>'user_password',
							'rbac_data_props'=>'user_id,user_name,user_lastlogintime,user_lastloginip,user_logincount,user_email,user_remark,create_dateline,user_registerip,update_dateline,user_status'
				),
			),

			/**
			 * 用什么数据表保存对象
			 *
			 * < 数据库表不带前缀 >
			 *
			 * @return string
			 */
			'table_name'=>'user',

			/**
			 * 指示使用哪一个表数据入口对象来负责对象属性值的存取工作
			 *
			 * <  这个值通常可以不设定，系统默认的数据库入口表为 DbTableRecordSet。
			 *    table_class 的值应该是一个类名称，例如 DbTableRecordSetUser。如果指定了这个设置，则自动忽略 table_name 设置。>
			 *
			 * @return string
			 */
			// 'table_class'=>'user',

			/**
			 * 数据库连接
			 *
			 * <  指示连接数据表时的设置，通常用于进行跨数据库访问或指定不同的数据表前缀。
			 *    table_config 的值应该是一个数组。
			 *    分部式服务器部署模式下为二维数组 。
			 *    普通模式下为一维数组 。
			 *    本项一般不用设置，遇到大型项目才需要设置 。>
			 *
			 * @return array
			 */
			//分布式数据库连接
			//'table_config' =>array(
			//  //第一个为主库
			// array(
			//
			//'db_host'=>'localhost',
			//'db_port'=>3360,
			//'db_type'=>'mysql',
			//'db_user'=>'root',
			//'db_password'=>'',
			//'db_name'=>'',
			//'db_dsn'=>'',
			//'db_params'=>'',
			//'db_schema'=>'',
			//'db_prefix'=>'',
			//'connect'=>'',
			//'pk'=>'',
			//),
			//
			// //剩下的都是从库
			//array(
			//
			//'db_host'=>'localhost',
			//'db_port'=>3360,
			//'db_type'=>'mysql',
			//'db_user'=>'root',
			//'db_password'=>'',
			//'db_name'=>'',
			//'db_dsn'=>'',
			//'db_params'=>'',
			//'db_schema'=>'',
			//'db_prefix'=>'',
			//'connect'=>'',
			//'pk'=>'',
			//),
			//),
			//
			// //普通数据库连接
			//'table_config'=>array(
			//
			//'db_host'=>'localhost',
			//'db_port'=>3360,
			//'db_type'=>'mysql',
			//'db_user'=>'root',
			//'db_password'=>'',
			//'db_name'=>'',
			//'db_dsn'=>'',
			//'db_params'=>'',
			//'db_schema'=>'',
			//'db_prefix'=>'',
			//'connect'=>'',
			//'pk'=>'',
			//),

			/**
			 * 指定数据表记录字段与对象属性之间的映射关系
			 *
			 * < 指示模型中各个属性、关联的设置
			 *   没有在这里指定设置的数据表字段自动成为模型的普通属性。
			 *   例如数据表中有 user_id、user_title 两个字段，而 props 中
			 *   没有指定 user_title 属性的设置，则 user_title 字段成为该模型的 user_title 属性，并且可以读写。>
			 *
			 * @return array
			 */
			'props'=>array(
				// 主键应该是只读，确保领域对象的“不变量”
				'user_id'=>array('readonly'=>true),
				// 可以在此添加其他属性的设置
				'user_name'=>array('readonly'=>true),
				// 虚拟属性
				//  所谓虚拟属性，就是指该属性并不对应到数据表中的一个字段。
				//  例如 User 模型有一个虚拟属性 full_name，但该属性并没有存储在 users 数据表中，而是通过 last_name 和 first_name 属性来构造的。
				//  'full_name'=>array('getter'=>'getFullName', 'setter'=>'setFullName'),
				//
				// 添加一个getFullName方法
				//  < protected function getFullName(){
				//
				//  return \$this->user_last_name . ', ' . \$this->user_first_name;
				//  } >
				//
				// 添加一个setFullName方法
				// < protected function setFullName(\$sFullName){
				//
				//      \$arrValue=explode(',', \$sFullName);
				//      \$this->user_last_nam =trim(\$arrValue[0]);
				//      \$this->user_first_name=trim(\$arrValue[1]);
				//  } >
				// 添加对象间的关联
				// 'task'=>array(DB::HAS_MANY=>'TaskModel', 'target_key'=>'user_id'),
			),

			/**
			 * 自动捕获表单数据时可以通过映射，防止别人通过表单猜想数据库的设计
			 *
			 * @return array
			 */
			'post_map_field'=>array(
				// 表单名=>字段
				//'user_name'=>'myusername'
			),

			/**
			 * 允许使用 mass-assignment 方式赋值的属性
			 *
			 * < 指示在通过 new 方法构造对象时能够指定的属性值。这个设置是一个安全性设置。>
			 * < 如果指定了 attr_accessible，则忽略 attr_protected 的设置。>
			 * < 如：\$oUser= new UserModel(array('user_name'=>'小牛哥','user_password'=>'123456789'))
			 *   如果，我们设置了'attr_accessible'='user_name'的话，那么我们系统会抛出错误，因为user_name不允许通过new方法来实现，即构造函数传递进来数据。
			 *   一旦指定了 attr_accessible 设置，则只有指定的属性可以通过构造函数赋值，除以以外的属性都不行。
			 *   相对而言，attr_accessible 设置具有更高的安全性，可以避免因为忘记保护重要属性而导致的安全性问题。
			 *   不过一旦增加了属性，一定要记得修改 attr_accessible 设置，否则新增属性就没法通过构造函数赋值了。
			 *   支持多个字段，支持数组和字符串，实际的结果都会转化为数组 。
			 *   如 'user_name,user_password' or array('user_name','user_password')两者等效 >
			 *
			 * @return mixed
			 */
			'attr_accessible'=>'',

			/**
			 * 拒绝使用 mass-assignment 方式赋值的属性
			 *
			 * < 功能同上，如果上面是白名单的话，那么下面这个就是黑名单。
			 *   这里设置了属性，那么设置后的属性不能通过构造函数来赋值。 >
			 *
			 * @return mixed
			 */
			'attr_protected'=>'user_id',

			/**
			 * 指定在数据库中创建对象时，哪些属性的值不允许由外部提供
			 *
			 * < 在数据库中新建对象时，要忽略掉的属性。
			 *   有些时候，我们的数据库结构要求在创建新记录时不能指定某些字段的值，
			 *   这时就可以通过 create_reject 设置来指定要排除的属性（属性名会自动对应到字段名）。
			 *   这里指定的属性会在创建记录时被过滤掉，从而让数据库自行填充值 。
			 *   支持多个字段，支持数组和字符串，实际的结果都会转化为数组。
			 *   如 'user_name,user_password' or array('user_name','user_password')两者等效 >
			 *
			 * @return mixed
			 */
			'create_reject'=>'',

			/**
			 * 指定更新数据库中的对象时，哪些属性的值不允许由外部提供
			 *
			 * < 同上，只是一个为创建数据对象时，一个为更新数据对象时 >
			 *
			 * @return mixed
			 */
			'update_reject'=>'',

			/**
			 * 指定在数据库中创建或者对象时，哪些属性的值由下面指定的内容进行覆盖（自动填充）
			 *
			 *  < 填充条件 >
			 *  < Model:: MODEL_CREATE 或者'create'(不区分大小写，即CREATE、Create都一样)新增数据的时候处理（默认）
			 *    Model:: MODEL_UPDATE 或者'update'(这个也不区分大小写)更新数据的时候处理
			 *    Model:: MODEL_ALL 或者all(这个也不区分大小写)所有情况都进行处理
			 *    因为一般使用数据库开发，所以命名上可能与你相的不太一样。 >
			 *
			 *  < 填充字段 >
			 *  < 如果，我们的数据库字段为Model::FIELD_DATETIME 或者 dateline(字段只能是小写，否则无法自动填充)，系统会在任何时候使用Linux时间戳自动填充该时间 。
			 *    如果，我们的数据库字段为Modle::FIELD_CREATE_DATELINE 或者 create_dateline(字段只能是小写，否则无法自动填充)，那么系统会在创建数据库对象的时候自动使用Linux时间戳来填充 。
			 *    如果，我们的数据库字段为Model::FIELD_UPDATE_DATELINE 或者 update_dateline(字段只能是小写，否则无法自动填充)，那么系统会在更新数据库对象的时候自动使用Linux时间戳来填充。>
			 *
			 *  < 填充内容 >
			 *  < 通常填充内容没有什么特别支持，但是系统为了简化一些常用时间戳，放置了一些常用时间戳规则：
			 *    如果填充内容为 Model::AUTOFILL_DATETIME 或者'DATETIME'(不区分大小写)，那么它会被自动替换为 date('Y-m-d H:i:s', CURRENT_TIMESTAMP)
			 *    如果填充内容为 Model::AUTOFILL_TIMESTAMP 或者'TIMESTAMP'(不区分大小写)，那么它会被自动替换为 CURRENT_TIMESTAMP
			 *    如果填充内容为 Model::AUTOFILL_DATE_ 或者'DATE_'(不区分大小写)，那么它会被自动替换为 date('Y-m-d', CURRENT_TIMESTAMP)
			 *    如果填充内容为 Model::AUTOFILL_TIME_ 或者'TIME_'(不区分大小写)，那么它会被自动替换为 date('H:i:s', CURRENT_TIMESTAMP)
			 *    注意：这里的CURRENT_TIMESTAMP 是系统定义的常量，其值为函数time()返回的Linux时间戳，其目的是为了减少time()的反复调用，提供系统性能。 >
			 *
			 *  < 附加规则 >
			 *  < function ：使用函数，表示填充的内容是一个函数名
			 * callback ：回调方法 ，表示填充的内容是一个当前模型的方法
			 * field ：用其它字段填充，表示填充的内容是一个其他字段的值
			 * string ：字符串（默认方式）
			 * 如果没有指定，那么系统会用模型中数据来填充 >
			 *
			 * @return array
			 */
			'autofill'=>array(
			 //array(填充字段,填充内容,填充条件,附加规则)
			 //array('user_status',1),  // 新增的时候把user_statu字段设置为1
			 //array('password','md5','create','function'/* args支持多参数 */), // 对password字段在新增的时候使md5函数处理
			 // |__ 接上面的话 ___-> 字段为第一个参数，当然这里字段必须存在，额外参数作为回调方法的后续参数
			 //array('user_name','getName','update','callback'), // 对name字段在新增的时候回调getName方法
			),

			/**
			 * 在保存对象时，会按照下面指定的验证规则进行验证。验证失败会发送错误，我们可以通过系统提供的方法拦截。
			 *
			 * < 除了在保存时自动验证，还可以通过对象的 模型::M()->check()方法对数组数据进行验证。
			 *   系统提供了50多个验证规则，所有验证规则可以在 DoYouHaoBaby 完全开发手册中 ~handbook\package\check\check.html中全部罗列出来。 >
			 *
			 * < 如果需要添加一个自定义验证，应该写成 >
			 * < 'title'=>array(
			 *
			 *    array(array(__CLASS__, 'checkTitle'), '标题不能为空'),
			 *)>
			 *
			 * < 同时上面的验证规则，可以用基于ThinkPHP的验证规则做到，下面我们将会有实例来做。>
			 *
			 * @return array
			 */
			'check'=>array(
			 // 最普通的验证规则
			 // '字段' =>array(
			 //
			 // array('规则',/* 参数args1,args2... */,'验证失败消息')// 其中验证失败消息可以为null和''，这个时候他会返回验证规则中提供的默认错误消息
			 //)
			 // 也许你会觉得复杂？
			 // 我们在文档中，将所有的内置验证器罗列出来了，便于你查询。
			 /*'user_name'=>array(
				array('require','用户名不能为空'), // 同意
				// array('not_empty', '用户名不能为空'),// 同上
				array('number_underline_english', '用户名只能是由字母、数字和英文字母组成'),// 支持别名
				array('num_underline_en', '用户名只能是由字母、数字和英文字母组成'),// 同上
				// - num_un_en
				// - n_u_e
				// - nue
				array('url','用户名只能是一个URL'),// 是否严格
				array('url2','用户名只能是一个URL'), // 同上，比上面的URL要 严格验证一些
				array('min_length', 5, '用户名不能少于 5 个字符'),
				array('max_length', 15, '用户名不能超过 15 个字符'),
				array('alnum', '用户名只能由字母和数字组成'),
				),*/
				// 带上验证时间
				// 这个时候创建数据对象的时候只有on_create才能够起作用
				// 同理还有一个on_update ，如果指定了，那么只有只有更新数据库对象的时候才会该验证才会起作用
				/*'user_name'=>array(
						array('not_empty', '用户名不能为空'),
						array('min_length', 5, '用户名不能少于 5 个字符'),
						'on_create'=>array(
							array('max_length', 15, '用户名不能超过 15 个字符'),
							array('alnum', '用户名只能由字母和数字组成'),
					)
				),*/
				// 控制验证过程
				// 有时候我们对于属性的验证可能是“如果某个属性没有指定值，则不进行验证”，
				// 这类需求可以通过在验证规则组中最前面添加一条 skip_empty 规则来实现。
				// skip_null：当属性值为 null 时忽略余下的验证规则。
				// skip_on_failed: 属性的多个验证规则中只要有一个没有通过，就忽略余下的验证规则。
					/*'user_name'=>array(
						array('skip_empty'), // null 或者空字符串将会通过，0不会通过
						array('min_length', 5, '用户名不能少于 5 个字符'),
				),*/
			 // 基于ThinkPHP的规则来验证
			 // '字段' =>array(
			 //
			 //  array('规则',/* 参数args1,args2... */,'验证失败消息','extend'=>'附件规则','condtion'=>'验证条件','time'=>'验证时间'),
			 //)
			 // 说明：extend，condition,time 顺序可以随意调换，它们的意义就和ThinkPHP中意义一摸一样
			 // extend这些键值只能是小写，然后其值‘附件规则’则忽略大小写
			 // 如 array('extend'=>'附件规则','规则','condtion'=>'验证条件',/* 参数args1,args2... */,'验证失败消息','time'=>'验证时间'),
			 // 它们可以指定一个，二个，或者全部指定，这个你懂的，自由。
			 //
			 // < extend 附加规则 >
			 // < 附加规则： 配合验证规则使用（可选），包括：
			 //   regex 使用正则进行验证，表示前面定义的验证规则是一个正则表达式（默认）
			 //   function 使用函数验证，前面定义的验证规则是一个函数名
			 //   callback 使用方法验证，前面定义的验证规则是当前Model类的一个方法
			 //   confirm 验证表单中的两个字段是否相同，前面定义的验证规则是一个字段名
			 //   equal 验证是否等于某个值，该值由前面的验证规则定义
			 //   in 验证是否在某个范围内，前面定义的验证规则必须是一个数组
			 //   当然，这里的附件规则提供和系统提供的验证规则有部分重复，当然不会重复验证，只是多一种选择嘛。 >
			 //
			 // < time 验证时间 >
			 // < Model:: MODEL_CREATE 或者1新增数据时候验证
			 //   Model:: MODEL_UPDATE 或者2编辑数据时候验证
			 //   Model:: MODEL_ALL 或者3 全部情况下验证（默认）>
			 //
			 // < condition 验证条件 >
			 //   Model::MUST_TO_CHECKDATE 或者'must'(不区分大小写，即MUST、Must都一样)必须验证
			 //   Model::EXISTS_TO_CHECKDATE 或者'exist'(不区分大小写，即EXIST、Exist都一样)存在字段就验证 （默认）
			 //   Model::VALUE_TO_CHECKDATE或者'notempty'(不区分大小写，即NOTEMPTY、Notempty都一样)值不为空的时候验证 >
				/*'user_password'=>array(
				// 一些示例验证
				array(array(1,2,3),'值的范围不正确！',time=>'notempty','extend'=>'in'), // 当值不为空的时候判断是否在一个范围内
				array('user_re_password','确认密码不正确','extend'=>'confirm'), // 验证确认密码是否和密码一致
				array('checkPwd','密码格式不正确',’function’), // 自定义函数验证密码格式
				),*/
			),
		);
	}

	/**
	 * 开启一个查询，查找符合条件的对象或对象集合
	 *
	 * @static
	 * @return DbSelect
	 */
	static function F(){
		\$arrArgs=func_get_args();
		return ModelMeta::instance(__CLASS__)->findByArgs(\$arrArgs);
	}

	/**
	 * 返回当前 Model 类的元数据对象
	 *
	 * @static
	 * @return ModelMeta
	 */
	static function M(){
		return ModelMeta::instance(__CLASS__);
	}

	/**
	 * 获取虚拟属性full_name
	 *
	 * @access protected
	 * @return string
	 */
	/*protected function getFullName(){
		return \$this->user_last_name.','.\$this->user_first_name;
	}*/

	/**
	 * 设置真实属性user_last_name 和 user_last_name
	 *
	 * @access protected
	 * @param \$sFullName  string  虚拟属性
	 * @return string
	 */
	 /*protected function setFullName(\$sFullName){
		\$arrValue=explode(',', \$sFullName);
		\$this->user_last_name =trim(\$arrValue[0]);
		\$this->user_first_name=trim(\$arrValue[1]);
	 }*/
";
							 }
							else{
								$sStart.="
	/**
	 * 返回对象的定义
	 *
	 * @access public
	 * @static
	 * @return array
	 */
	static public function init__(){
		return array(
			/**
			 * 指定该 ActiveRecord 要使用的行为插件
			 *
			 * @return string
			 */
			'behaviors'=>'rbac',

			/**
			 * 指定行为插件的配置
			 *
			 * @return array
			 */
			'behaviors_settings'=>array(
				// '插件名'=>array('选项'=>设置),
				'rbac'=>array(
				'password_prop'=>'user_password',
				'rbac_data_props'=>'user_id,user_name,user_lastlogintime,user_lastloginip,user_logincount,user_email,user_remark,create_dateline,user_registerip,update_dateline,user_status'
			),
		),

		/**
		 * 用什么数据表保存对象
		 *
		 * < 数据库表不带前缀 >
		 *
		 * @return string
		 */
		'table_name'=>'user',

		/**
		 * 指示使用哪一个表数据入口对象来负责对象属性值的存取工作
		 *
		 * <  这个值通常可以不设定，系统默认的数据库入口表为 DbTableRecordSet。
		 *    table_class 的值应该是一个类名称，例如 DbTableRecordSetUser。如果指定了这个设置，则自动忽略 table_name 设置。>
		 *
		 * @return string
		 */
		// 'table_class'=>'user',

		/**
		 * 数据库连接
		 *
		 * <  指示连接数据表时的设置，通常用于进行跨数据库访问或指定不同的数据表前缀。
		 *    table_config 的值应该是一个数组。
		 *    分部式服务器部署模式下为二维数组 。
		 *    普通模式下为一维数组 。
		 *    本项一般不用设置，遇到大型项目才需要设置 。>
		 *
		 * @return array
		 */
		 //'table_config' =>array(
		 //
		 // 'db_host'=>'localhost',
		 // 'db_port'=>3360,
		 // 'db_type'=>'mysql',
		 // 'db_user'=>'root',
		 // 'db_password'=>'',
		 // 'db_name'=>'',
		 // 'db_dsn'=>'',
		 // 'db_params'=>'',
		 // 'db_schema'=>'',
		 // 'db_prefix'=>'',
		 // 'connect'=>'',
		 // 'pk'=>'',
		 //),

		/**
		 * 指定数据表记录字段与对象属性之间的映射关系
		 *
		 * < 指示模型中各个属性、关联的设置
		 *   没有在这里指定设置的数据表字段自动成为模型的普通属性。
		 *   例如数据表中有 user_id、user_title 两个字段，而 props 中
		 *  没有指定 user_title 属性的设置，则 user_title 字段成为该模型的 user_title 属性，并且可以读写。>
		 *
		 * @return array
		 */
		'props'=>array(
			// 主键应该是只读，确保领域对象的“不变量”
			'user_id'=>array('readonly'=>true),
			// 可以在此添加其他属性的设置
			'user_name'=>array('readonly'=>true),
			// 添加对象间的关联
			// 'task'=>array(DB::HAS_MANY=>'TaskModel', 'target_key'=>'user_id'),
		),

		/**
		 * 自动捕获表单数据时可以通过映射，防止别人通过表单猜想数据库的设计
		 *
		 * @return array
		 */
		'post_map_field'=>array(
			// 表单名=>字段
			//'user_name'=>'myusername'
		),

		/**
		 * 允许使用 mass-assignment 方式赋值的属性
		 *
		 * < 指示在通过 new 方法构造对象时能够指定的属性值。这个设置是一个安全性设置。>
		 * < 如果指定了 attr_accessible，则忽略 attr_protected 的设置。>
		 * < 如：\$oUser= new UserModel(array('user_name'=>'小牛哥','user_password'=>'123456789'))
		 *   如果，我们设置了'attr_accessible'='user_name'的话，那么我们系统会抛出错误，因为user_name不允许通过new方法来实现，即构造函数传递进来数据。
		 *   一旦指定了 attr_accessible 设置，则只有指定的属性可以通过构造函数赋值，除以以外的属性都不行。
		 *   相对而言，attr_accessible 设置具有更高的安全性，可以避免因为忘记保护重要属性而导致的安全性问题。
		 *   不过一旦增加了属性，一定要记得修改 attr_accessible 设置，否则新增属性就没法通过构造函数赋值了。
		 *   支持多个字段，支持数组和字符串，实际的结果都会转化为数组 。
		 *   如 'user_name,user_password' or array('user_name','user_password')两者等效 >
		 *
		 * @return mixed
		 */
		'attr_accessible'=>'',

		/**
		 * 拒绝使用 mass-assignment 方式赋值的属性
		 *
		 * < 功能同上，如果上面是白名单的话，那么下面这个就是黑名单。
		 *   这里设置了属性，那么设置后的属性不能通过构造函数来赋值。 >
		 *
		 * @return mixed
		 */
		'attr_protected'=>'user_id',

		/**
		 * 指定在数据库中创建对象时，哪些属性的值不允许由外部提供
		 *
		 * < 在数据库中新建对象时，要忽略掉的属性。
		 *   有些时候，我们的数据库结构要求在创建新记录时不能指定某些字段的值，
		 *   这时就可以通过 create_reject 设置来指定要排除的属性（属性名会自动对应到字段名）。
		 *   这里指定的属性会在创建记录时被过滤掉，从而让数据库自行填充值 。
		 *   支持多个字段，支持数组和字符串，实际的结果都会转化为数组。
		 *   如 'user_name,user_password' or array('user_name','user_password')两者等效 >
		 *
		 * @return mixed
		 */
		'create_reject'=>'',

		/**
		 * 指定更新数据库中的对象时，哪些属性的值不允许由外部提供
		 *
		 * < 同上，只是一个为创建数据对象时，一个为更新数据对象时 >
		 *
		 * @return mixed
		 */
		'update_reject'=>'',

		/**
		 * 指定在数据库中创建或者对象时，哪些属性的值由下面指定的内容进行覆盖（自动填充）
		 *
		 * @return array
		 */
		'autofill'=>array(
			//array(填充字段,填充内容,填充条件,附加规则)
			//array('user_status',1),  // 新增的时候把user_statu字段设置为1
			//array('password','md5','create','function'/* args支持多参数 */), // 对password字段在新增的时候使md5函数处理
			// |__ 接上面的话 ___-> 字段为第一个参数，当然这里字段必须存在，额外参数作为回调方法的后续参数
			//array('user_name','getName','update','callback'), // 对name字段在新增的时候回调getName方法
		),

		/**
		 * 在保存对象时，会按照下面指定的验证规则进行验证。验证失败会发送错误，我们可以通过系统提供的方法拦截。
		 *
		 * < 除了在保存时自动验证，还可以通过对象的 模型::M()->check()方法对数组数据进行验证。
		 *   系统提供了50多个验证规则，所有验证规则可以在 DoYouHaoBaby 完全开发手册中 ~handbook\package\check\check.html中全部罗列出来。 >
		 *
		 * @return array
		 */
		'check'=>array(
			'user_name'=>array(
				array('not_empty', '用户名不能为空'),
				array('min_length', 5, '用户名不能少于 5 个字符'),
				'on_create'=>array(
					array('max_length', 15, '用户名不能超过 15 个字符'),
						array('alnum', '用户名只能由字母和数字组成'),
					)
				),
			),
		);
	}

	/**
	 * 开启一个查询，查找符合条件的对象或对象集合
	 *
	 * @static
	 * @return DbSelect
	 */
	static function F(){
		\$arrArgs=func_get_args();
		return ModelMeta::instance(__CLASS__)->findByArgs(\$arrArgs);
	}

	/**
	 * 返回当前 Model 类的元数据对象
	 *
	 * @static
	 * @return ModelMeta
	 */
	static function M(){
		return ModelMeta::instance(__CLASS__);
	}
";
			}
		}
		$sContent='';
		if($arrAttribute){
			foreach($arrAttribute as $value){
				$sContent.=$this->makeAttribute_($value);
			}
		}
		if($arrAction){
			foreach($arrAction as $value){
				$sContent.=$this->makeAction_($value);
			}
		}
		$sEnd="
}

";
		return $sStart.$sContent.$sEnd;
	}

	private function makeAttribute_($value){
		$arrAttribute=explode('|', trim($value));
		return "
	/**
	 * {$arrAttribute[3]}
	 *
	 * @var string
	 */
	{$arrAttribute[0]} {$arrAttribute[1]}={$arrAttribute[2]};
";
	}

	private function makeAction_($value){
		$sDyhbWebsetupApp=G::cookie('dyhb_websetup_app');
		if(!is_file($sDyhbWebsetupApp."/App/Config/Config.php")){
			$this->E(G::L("项目%s的配置文件%s不存在！",'app',null,$sDyhbWebsetupApp,$sDyhbWebsetupApp."/App/Config/Config.php"));
		}
		$arrAction=explode('|',trim($value));
		$sArgs=$arrAction[2]=='*'?'':$arrAction[2];
		$sContent='';
		$sActionBodyValue='';
		$sActionBody=isset($arrAction[4])?$arrAction[4]:'';
		if($sActionBody){
			$sBlock='';
			$sBlockItem='';
			if(strpos($sActionBody, ':')){
				$arrayValue =explode(':',$sActionBody);
				$sBlockItem=trim(array_pop($arrayValue));
				$sBlock=implode('.',$arrayValue);
			}
			else{
				$sBlock=$sActionBody;
			}
			$sBlockPath=APP_PATH.'/Static/Blocks/block_'.$sBlock.'.php';
			if(file_exists($sBlockPath)){
				G::includeFile($sBlockPath);
				$sFunction='block_'.$sBlock;
				if(function_exists($sFunction)){
					$sActionBodyValue=$sFunction($this->_sClassName);
					if($sBlockItem)$sActionBodyValue=$sActionBodyValue[$sBlockItem];
					else $sActionBodyValue=$sFunction($this->_sClassName);
				}
			}
			else{
				$sActionBodyValue=str_replace('+',':',$sActionBody);
			}
		}
		$arrArgs=array();
		if($sArgs){$arrArgs=explode(',',$sArgs);}
		if($arrArgs){
			foreach($arrArgs as $value){
				$sContent.='
	* @param string '.$value;
			}
		}
		return "
	/**
	 * {$arrAction[3]}{$sContent}
	 */
	{$arrAction[0]} function {$arrAction[1]}({$sArgs}){
		{$sActionBodyValue}
	}
";
	}

}
