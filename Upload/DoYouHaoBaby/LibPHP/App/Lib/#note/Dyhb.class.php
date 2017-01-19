<?dyhb
class Dyhb{

	/**
	 * 实例
	 *
	 * @access public
	 * @var array
	 * @static
	 */
	private static $INSTANCES=array();

	/**
	 * 取得对象实例 支持调用类的静态方法
	 *
	 * @access public
	 * @static
	 * @param string $sClass 对象类名
	 * @param mixed $Args 类的构造函数参数
	 * @param string $sMethod 类的静态方法名
	 * @param mixed $MethodArgs 类的静态方法参数
	 * @return object
	 */
	static public function instance($sClass,$Args=null,$sMethod=null,$MethodArgs=null){}

	/**
	 * 读取指定的缓存内容，如果内容不存在或已经失效，则返回false
	 *
	 * < 在操作缓存数据时，必须指定缓存的 ID。每一个缓存内容都有一个唯一的ID。
	 *   例如数据A的缓存ID是data-a，而数据B的缓存ID是data-b。>
	 * < Dyhb::cache()可以指定$arrOption参数来覆盖缓存数据本身带有的配置。>
	 * < $sBackendClass用于指定要使用的缓存服务对象类名称。例如FileCache、XCache 等。>
	 *
	 * <!-- 使用举例 -->
	 * < $Data=Dyhb::cache($sId);
	 *   if ($Data===false){
	 *   $Data= ....
	 *   Dyhb::writeCache($sId,$Data);
	 * } >
	 *
	 * @access public
	 * @static
	 * @param string $sId 缓存的 ID
	 * @param array $arrOption 缓存配置
	 * @param string $sBackendClass 要使用的缓存服务
	 * @return mixed 成功返回缓存内容，失败返回false
	 */
	static public function cache($sId,array $arrOption=null,$sBackendClass=null){}

	/**
	 * 将变量内容写入缓存，失败抛出异常
	 *
	 * < $data 参数指定要缓存的内容。如果 $data 参数不是一个字符串，则必须将缓存策略 serialize 设置为 true。
	 *   $arrOption 参数指定了内容的缓存配置，如果没有提供该参数，则使用缓存服务的默认策略。>
	 * < 其他参数同 Dyhb::cache()。>
	 *
	 * @access public
	 * @static
	 * @param string $sId 缓存的 ID
	 * @param mixed $Data 要缓存的数据
	 * @param array $arrOption 缓存配置
	 * @param string $sBackendClass 要使用的缓存服务
	 * @return bool
	 */
	static public function writeCache($sId,$Data,array $arrOption=null,$sBackendClass=null){}

	/**
	 * 删除指定的缓存内容
	 *
	 * < 通常，失效的缓存数据无需清理。但有时候，希望在某些操作完成后立即清除缓存。
	 *   例如更新数据库记录后，希望删除该记录的缓存文件，以便在下一次读取缓存时重新生成缓存文件。>
	 * < Dyhb::cleanCache($sId) >
	 *
	 * @access public
	 * @static
	 * @param string $sId 缓存的 ID
	 * @param array $arrOption 缓存配置
	 * @param string $sBackendClass 要使用的缓存服务
	 * @return bool
	 */
	static public function deleteCache($sId,array $arrOption=null,$sBackendClass=null){}

	/**
	 * 对字符串或数组进行格式化，返回格式化后的数组
	 *
	 * < $sInput 参数如果是字符串，则首先以“,”为分隔符，将字符串转换为一个数组 >
	 * < 接下来对数组中每一个项目使用 trim() 方法去掉首尾的空白字符。最后过滤掉空字符串项目 >
	 * < 本方法值支持字符串和数组，其它会原值返回 >
	 * < 该方法的主要用途是将诸如：“item1, item2, item3” 这样的字符串转换为数组 >
	 *
	 * < $sInput = 'item1,item2,item3';
	 *   $arrOutput=Dyhb::normalize($input);
	 *   $arrOutput 现在是一个数组，结果如下：
	 *   $arrOutput=array(
	 *	  'item1',
	 *	  'item2',
	 *	  'item3',
	 *   ); >
	 *
	 * < $arrOutput='item1|item2|item3';
	 *   // 指定使用什么字符作为分割符
	 *   $arrOutput=Dyhb::normalize($sInput,'|'); >
	 *
	 * @access public
	 * @static
	 * @param array|string $Input 要格式化的字符串或数组
	 * @param string $sDelimiter 按照什么字符进行分割
	 * @param bool $bAllowedEmpty=false 是否允许为空
	 * @return array 格式化结果
	 */
	 static public function normalize($Input,$sDelimiter=',',$bAllowedEmpty=false){}

}
