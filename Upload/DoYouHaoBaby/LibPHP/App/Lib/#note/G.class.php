<?dyhb
class G{

	/**
	 * 启动一个验证码
	 *
	 * @access public
	 * @static
	 * @param array|array $arrOption 验证码配置条件
	 * @param boolean $bVisitedDb 验证码是否存在数据库中
	 * @return string
	 */
	public static function seccode($arrOption=null,$bVisitedDb=true){}

	/**
	 * 全局变量统一处理
	 *
	 * @access public
	 * @static
	 * @param string $sKey 键值
	 * @param string $Var 类型
	 * @return string
	 */
	static function getGpc($sKey,$sVar='R'){}

	/**
	 * 去除转义字符（递归）
	 *
	 * @access public
	 * @static
	 * @param $sString string|array  待操作的数据
	 * @param $bRecursive bool 是否递归
	 * @return mixed string|array
	 */
	static public function stripslashes($String,$bRecursive=true){}

	/**
	 * 如果magic为关闭状态，转义（递归）
	 *
	 * @access public
	 * @static
	 * @param string|array $String 待过滤的数据
	 * @param $bRecursive bool 是否递归
	 * @return mixed string|array
	 */
	static public function addslashes($String,$bRecursive=true){}

	/**
	 * 判断Magic的状态
	 *
	 * @access public
	 * @static
	 * @return bool
	 */
	static public function getMagicQuotesGpc(){}

	/**
	 * 判断变量的类型
	 *
	 * < PHP的原生函数is_a()只判断对象，本函数将所有类型和类(class)同等看待 >
	 * < 如果类型是类名，则以is_a判断 >
	 *
	 * @access public
	 * @param mixed $Var 待检查的变量名
	 * @param string $sType 类型
	 * @static
	 * @return void
	 */
	static public function varType($Var,$sType){}

	/**
	 * 自定义异常
	 *
	 * @access public
	 * @static
	 * @param string $sMsg 异常信息
	 * @param string $sType 异常处理类
	 * @param int $nCode 错误代码
	 * @return void
	 */
	static public function throwException($sMsg,$sType='DException',$nCode=0){}

	/**
	 * 为自定义异常定义一个别名
	 *
	 * @access public
	 * @static
	 * @param string $sMsg 异常信息
	 * @param string $sType 异常处理类
	 * @param int $nCode 错误代码
	 * @return void
	 */
	static public function E($sMsg,$sType='DException',$nCode=0){}

	/**
	 * 错误输出
	 *
	 * @access public
	 * @static
	 * @param string|array $Error
	 * @return void
	 */
	static public function halt($Error){}

	/**
	 * 优化include函数
	 *
	 * @access public
	 * @static
	 * @param string $sFile 引入的文件
	 * @return boolean
	 */
	static public function includeFile($sFile){}

	/**
	 * URL重定向
	 *
	 * @access public
	 * @static
	 * @param string $sUrl 跳转的URL
	 * @param int $nTime 等待时间秒
	 * @param string $sMsg 跳转消息
	 * @return void
	 */
	static public function urlGoTo($sUrl,$nTime=0,$sMsg=''){}

	/**
	 * 本函数用于当前系统数据配置
	 *
	 * @access public
	 * @static
	 * @param string|array|null $sName 配置名字(数组的时候为批量设置值)
	 * @param mixed $Value 值
	 * @param boolean $bForce 是否强制写入
	 * @return void
	 */
	static public function C($sName='',$Value='',$bForce=false){}

	/**
	 * 从指定语言的指定语言包取得一条语句
	 *
	 * @access public
	 * @static
	 * @param sting $Value 值
	 * @param string|LangPackage|null $Package 语言包名称、语言包实例或null(使用当前语言包)
	 * @param string|Lang|null $Lang 语言名称、语言实例或null(使用当前语言)
	 * @param mixed Argvs 代入语句的参数
	 * @return string
	 */
	static public function L($sValue,$Package=null,$Lang=null/*Argvs*/){}

	/**
	 * 检查是否为同一个callback
	 *
	 * @access public
	 * @param callback $CallbackA 回调A
	 * @param callback $CallbackB 回调B
	 * @static
	 * @return bool
	 */
	static public function isSameCallback($CallbackA,$CallbackB){}

	/**
	 * 产生乱数字串
	 *
	 * @access public
	 * @param int $nLength 字串长度
	 * @param string|null $sCharBox=null 可用字符
	 * @param bool $bNumeric=false 是否限定数字
	 * @static
	 * @return string
	 */
	 static public function randString($nLength,$sCharBox=null,$bNumeric=false){}

	/**
	 * 返回当前时间（微秒级）
	 *
	 * @access public
	 * @param bool $bExact=true 精确到微秒，该参数false的话，等同 time()
	 * @static
	 * @return float|int
	 */
	static public function now($bExact=true){}

	/**
	 * 渲染输出Widget
	 *
	 * <!-- 使用方法：-->
	 * < W('demo') >
	 * < w('demo',array('test1'=>1,'test2'=>2)) >
	 * < w('demo','test1=1&test2=2') >
	 * < w('demo','test1=1&test2=2',true) >
	 *
	 * @access public
	 * @static
	 * @param string $sName widget名字
	 * @param array|string $Data 数据
	 * @param bool $bReturn=false 是否返回
	 * @return void
	 */
	static public function W($sName,$Data='',$bReturn=FALSE){}

   /**
	* 自定义异常处理函数
	*
	* @access public
	* @static
	* @param object Exception 异常对象
	* @return
	*/
	static public function exceptionHandler(Exception $oE){}

	/**
	 * 自定义错误处理
	 *
	 * @access public
	 * @static
	 * @param int $nErrno 错误类型
	 * @param string $sErrstr 错误信息
	 * @param string $sErrfile 错误文件
	 * @param int $nErrline 错误行数
	 * @return void
	 */
	static public function errorHandler($nErrno,$sErrstr,$sErrfile,$nErrline){}

	/**
	 * 判断变量 是否符合给定的类型
	 *
	 * < 可以给入多种类型，变量只需符合其中之一。 >
	 * < 如果类型是类名，则以is_a判断 >
	 *
	 * @access public
	 * @param mixed $Var 待检查的变量名
	 * @param string|array $Types 必须符合的各项类型
	 * @static
	 * @return void
	 */
	static public function isThese($Var,$Types){}

	/**
	 * 递归地检查给定的两个类(接口)之间的继承关系。(严格检查)
	 *
	 * < 如果参数$subClass_instance和$baseClass相同，仍然返回true，表示"is_kind_of"关系成立。>
	 * < 如果$subClass_instance或$baseClass传入不存在的类名，简单的返回false，而不会抛出异常，
	 *   或进行更严重的错误处理。>
	 * < php的原生函数is_subclass_of()只对直接继承提供判断，本函数是对is_subclass_of()的加强。>
	 * < 注：is_subclass_of()在判断直接继承时，仍然有用。>
	 *
	 * @access public
	 * @static
	 * @param string|object $SubClass 子类，或子类的一个实例
	 * @param string $sBaseClass 父类
	 * @return bool
	 */
	static public function isKindOf($SubClass,$sBaseClass){}

	/**
	 * 检查一个类或对象是否实现了某个接口
	 *
	 * @access public
	 * @static
	 * @param string|object $Class 待检查的类或对象
	 * @param string $sInterface 应实现的接口名
	 * @param bool $bStrictly=false 是否严格检查所有的接口的所有方法均已实现（非抽象方法）
	 * @return bool
	 */
	static public function isImplementedTo($Class,$sInterface,$bStrictly=false){}

	/**
	 * 以严格的方式检查数组
	 *
	 * @access public
	 * @param array $arrArray 待检查的数组
	 * @param array $arrTypes 类型
	 * @static
	 * @return void
	 */
	static public function checkArray($arrArray,array $arrTypes){}

	/**
	 * Cookie设置、获取、清除
	 *
	 * <!-- 使用方法 -->
	 * < 获取cookie: G::cookie('name') >
	 * < 清空当前设置前缀的所有cookie: G::cookie(null) >
	 * < 删除指定前缀所有cookie: G::cookie(null,'dyhb_') | 注：前缀将不区分大小写 >
	 * < 设置cookie: G::cookie('name','value') | 指定保存时间: G::cookie('name','value',3600) >
	 * < 删除cookie: G::cookie('name',null) >
	 *
	 * <!-- 说明 -->
	 * < $option 可用设置prefix,expire,path,domain
	 *   支持数组形式对参数设置:G::cookie('name','value',array('expire'=>1,'prefix'=>'dyhb_'))
	 *   支持query形式字符串对参数设置:G::cookie('name','value','prefix=dyhb_&expire=10000') >
	 *
	 * @access public
	 * @param null|string $sName Cookie名字
	 * @param mixed $Value Cookie值
	 * @param mixed$Option Cookie设置
	 * @param boolean $bThisPrefix 是否只清理当前前缀的cookie
	 * @param boolean $bReturnCookie 是否返回cookie对象
	 * @static
	 * @return void
	 */
	static public function cookie($sName,$Value='',$Option=null,$bThisPrefix=true,$bReturnCookie=false){}

	/**
	 * 创建目录
	 *
	 * @access public
	 * @static
	 * @param string $Dir 需要创建的目录名称
	 * @param int $nMode 权限
	 * @return true
	 */
	static public function makeDir($Dir,$nMode=0777){}

	/**
	 * URL组装支持不同模式和路由
	 *
	 * @access public
	 * @static
	 * @param string $sUrl URL分析名字
	 * @param array $arrParams URL附带参数
	 * @param bool $bRedirect=false 是否立刻调转
	 * @param bool $bSuffix=true 是否带上后缀
	 * @return string
	 */
	static public function U($sUrl,$arrParams=array(),$bRedirect=false,$bSuffix=true){}

	/**
	 * 返回 路径$sFromPath 到 路径$sToPath 的相对路径
	 *
	 * @access public
	 * @param $sFromPath string 开始路径
	 * @param $sToPath string 目标路径
	 * @static
	 * @return string,null
	 */
	static public function getRelativePath($sFromPath,$sToPath){}

}
