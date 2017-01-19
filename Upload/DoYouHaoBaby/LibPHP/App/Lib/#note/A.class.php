<?dyhb
class A{

	/**
	 * 判断是否启用断言
	 *
	 * @access private
	 * @static
	 * @return void
	 */
	static private function openAssert(){}

	/**
	 * 本函数在$Exp不满足时抛出一个AssertDException异常
	 *
	 * @access public
	 * @static
	 * @param bool $Exp 表达式，当该表达式的结果为false,null,0或空时，断言函数抛出异常
	 * @param string $sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function ASSERT_($Exp,$sDes=null){}

	/**
	 * 整数断言，传入的变量必须为整数类型
	 *
	 * @access public
	 * @static
	 * @param mixed $Var 传入检查的变量
	 * @param string $sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function INT($Var,$sDes=null){}

	/**
	 * 数字断言，传入的变量必须为一个数字（整数和浮点）
	 *
	 * @access public
	 * @static
	 * @param mixed $Var 传入检查的变量
	 * @param string $sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function NUM($Var,$sDes=null){}

	/**
	 * 字符串断言，传入的变量必须为字符串类型
	 *
	 * @access public
	 * @static
	 * @param mixed $Var 传入检查的变量
	 * @param string $sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function STRING($Var,$sDes=null){}

	/**
	 * 对象断言，传入的变量必须为对象类型
	 *
	 * @access public
	 * @static
	 * @param mixed $Var 传入检查的变量
	 * @param string $sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function OBJ($Var,$sDes=null){}

	/**
	 * 布尔类型断言，传入的变量必须为布尔类型
	 *
	 * @access public
	 * @static
	 * @param mixed $Var 传入检查的变量
	 * @param string $sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function BOOL($Var,$sDes=null){}

	/**
	 * 对象实例断言，传入的变量必须为对象
	 *
	 * @access public
	 * @static
	 * @param mixed $Var 传入检查的变量
	 * @param string $class='' 对象类型,null或''则表示不限，只要是对象即可。此断言对类的检查是不严格的，满足isKindOf()即可
	 * @param string $sDes='' 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function INSTANCE($Var,$Class='',$sDes=''){}

	/**
	 * 数组断言，传入的变量必须为数组类型
	 *
	 * @access public
	 * @static
	 * @param mixed $Var 传入检查的变量
	 * @param array $eleTypes=null 数组各元素的类型,null或''则表示不限。$eleTypes可以是：string,int,float,bool,array,object等基础类型，或是一个类名。此断言对类的检查是不严格的，满足isKindOf()即可
	 * @param string $sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function DARRAY($Var,$eleTypes=null,$sDes=null){}

	/**
	 * 空断言，传入的变量必须为null
	 *
	 * < 此断言严格检查传入变量是否为null，即使0和''也会遭至断言失败。>
	 *
	 * @access public
	 * @static
	 * @param mixed $Var 传入检查的变量
	 * @param string $sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function DNULL($Var,$sDes=null){}

	/**
	 * 非空断言，传入的变量不能为null。
	 *
	 * < 此断言仅仅严格检查传入变量是否为null,0和''不会遭至断言失败。>
	 *
	 * @access public
	 * @static
	 * @param mixed$Var 传入检查的变量
	 * @param string$sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function NOTNULL($Var,$sDes=null){}

	/**
	 * 路径断言，检查传入变量所代表的路径是否有效。
	 *
	 * < 此断言不对文件或目录作出判断。>
	 *
	 * @access public
	 * @static
	 * @param mixed$Var 传入检查的路径
	 * @param string$sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function PATH($Var,$sDes=null){}

	/**
	 * 文件断言，检查传入变量所代表的文件是否有效。
	 *
	 * < 此断言不但要求路径正确有效，同时还必须是一个文件。>
	 *
	 * @access public
	 * @static
	 * @param mixed $Var 传入检查的路径
	 * @param string $sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function DFILE($Var,$sDes=null){}

	/**
	 * 目录断言，检查传入变量所代表的目录是否有效。
	 *
	 * < 此断言不但要求路径正确有效，同时还必须是一个目录。>
	 *
	 * @access public
	 * @static
	 * @param mixed $Var 传入检查的路径
	 * @param string $sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function DDIR($Var,$sDes=null){}

	/**
	 * 资源断言，检查传入变量是否为一个有效的资源
	 *
	 * @access public
	 * @static
	 * @param mixed $Var 传入检查的资源
	 * @param string $sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function RESOURCE($Var,$sDes=null){}

	/**
	 * Callback回调断言，检查传入变量是否为一个有效的回调
	 *
	 * @access public
	 * @static
	 * @param mixed $Var 传入检查的资源
	 * @param string $sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function CALLBACK($Var,$sDes=null){}

	/**
	 * 继承断言，检查传入的类或对象是否继承自某个类
	 *
	 * @access public
	 * @static
	 * @param string|object $SubClassName 子类名或对象变量
	 * @param string $sParClas 父类名
	 * @param string $sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function INHERIT($SubClassName,$sParClass,$sDes=null){}

	/**
	 * 接口断言，检查传入的对象或类是否实现一个接口
	 *
	 * @access public
	 * @static
	 * @param string|object $SubClassName 类名或对象变量
	 * @param string $sInterfaceName 接口名
	 * @param bool $bStrictly=true 是否严格检查
	 * @param string $sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function DIMPLEMENTS($SubClassName,$sInterfaceName,$bStrictly=true,$sDes=null){}

	/**
	 * 检查变量类型，传入的变量必须至少符合随后给出的类型中的一个
	 *
	 * @access public
	 * @static
	 * @param mixed $Var 待检查的变量名
	 * @param array|string $Types 类型
	 * @param string $sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function ISTHESE($Var,$Types,$sDes=null){}

	/**
	 * 检查变量类型，传入的变量必须至少符合随后给出的类型中的一个
	 *
	 * @access public
	 * @static
	 * @param string $className 待检查的类名
	 * @param array:string|string $Base=array() 基类或接口
	 * @param string $sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function ISCLASS($sClassName,$Base=array(),$sDes=null){}

	/**
	 * 检查变量值，传入的变量必须至少符合随后给出的值中的一个
	 *
	 * @access public
	 * @static
	 * @param mixed $Var 待检查的变量名
	 * @param array $arrValues 变量值
	 * @param string $sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function INVALUE($Var,array $arrValues,$sDes=null){}

	/**
	 * 检查变量值，传入变量的值必须为数字类型，且在随后给出的范围内
	 *
	 * @access public
	 * @static
	 * @param mixed $Var 待检查的变量名
	 * @param int|float $Min 范围的左端
	 * @param int|float $Max 范围的右端
	 * @param string $sDes=null 当断言异常被触发时，描述该断言的消息
	 * @return void
	 */
	static public function INRANGE($Var,$Min,$Max,$sDes=null){}

}
