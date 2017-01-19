<?dyhb
/**
 * Coll实现了一个类型安全的对象集合
 *
 * < Coll会检查每一个对象的类型是否符合预期，以便将同一类型的对象组织在一起。>
 * < Coll具有和PHP内置数组相似的性质，因此可以按照使用数组的方式来使用Coll集合 >
 *
 * < 在构造一个集合时，必须指定该集合能够容纳的对象类型：
 *
 *   $coll=new Coll('MyObject');
 *   $coll[]=new MyObject();
 *
 *   // 在尝试存入 MyObject2 类型的对象到 $coll 中时将抛出异常
 *   $coll[]=new MyObject2();
 *
 *   // 指定一个对象
 *   $coll[$offset]=$item;
 *
 *   // 遍历一个集合
 *   foreach ($coll as $offset=>$item){
 *      dump($item, $offset);
 *   } >
 *
 * <!-- 知识补充 -->
 * < php几个接口ArrayAccess,Iterator,Countable >
 * < Countable:实现对类的count。 >
 * < ArrayAccess 实现对类的赋值取值等。>
 * < Iterator迭代器实现遍历 >
 */
class Coll implements Iterator,ArrayAccess,Countable{

	/**
	 * 集合对象的类型
	 *
	 * @var string
	 * @access protected
	 */
	protected $_sType;

	/**
	 * 保存对象的数组
	 *
	 * @var array
	 * @access protected
	 */
	protected $_arrColl=array();

	/**
	 * 指示迭代对象是否有效
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_bIsValid=false;

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param string $type 集合对象类型
	 * @return void
	 */
	public function __construct($sType){}

	/**
	 * 从数组创建一个集合
	 *
	 * < Coll::createFromArray()方法从一个包含多个对象的数组创建集合。
	 *   新建的集合包含数组中的所有对象，并且确保对象的类型符合要求。>
	 *
	 * @access public
	 * @static
	 * @param array $arrObjects 包含多个对象的数组
	 * @param string $sType 集合对象类型
	 * @param boolean $bKeepKeys 是否在创建集合时保持数组的键名
	 * @return Coll新创建的集合对象
	 */
	public static function createFromArray(array $arrObjects,$sType,$bKeepKeys=false){}

	/**
	 * 遍历集合中的所有对象，返回包含特定属性值的数组
	 *
	 * < $coll=new Coll('Post');
	 *   $coll[]=new Post(array('title'=>'t1'));
	 *   $coll[]=new Post(array('title'=>'t2')); >
	 *
	 * < // 此时$titles中包含t1和t2两个值
	 *   $titles=$coll->values('title'); >
	 *
	 * @access public
	 * @param string $sPropName 要获取集合对象的哪一个属性
	 * @return array 包含所有集合对象指定属性值的数组
	 */
	public function values($sPropName){}

	/**
	 * 检查指定索引的对象是否存在，实现ArrayAccess接口
	 *
	 * < echo isset($coll[1]); >
	 *
	 * @access public
	 * @param mixed $Offset
	 * @return boolean
	 */
	public function offsetExists($Offset){}

	/**
	 * 返回指定索引的对象，实现ArrayAccess接口
	 *
	 * < $item = $coll[1]; >
	 *
	 * @access public
	 * @param mixed $Offset
	 * @return mixed
	 */
	public function offsetGet($Offset){}

	/**
	 * 设置指定索引的对象，实现ArrayAccess接口
	 *
	 * < $coll[1] = $item; >
	 *
	 * @access public
	 * @param mixed $Offset 索引
	 * @param mixed $Value 值
	 * @return void
	 */
	public function offsetSet($Offset,$Value){}

	/**
	 * 注销指定索引的对象，实现ArrayAccess接口
	 *
	 * < unset($coll[1]); >
	 *
	 * @access public
	 * @param mixed $Offset 索引
	 * @return void
	 */
	public function offsetUnset($Offset){}

	/**
	 * 返回当前位置的对象，实现Iterator接口
	 *
	 * @access public
	 * @return mixed
	 */
	public function current(){}

	/**
	 * 返回遍历时的当前索引，实现Iterator接口
	 *
	 * @access public
	 * @return mixed
	 */
	public function key(){}

	/**
	 * 遍历下一个对象，实现Iterator接口
	 *
	 * @access public
	 * @return mixed
	 */
	public function next(){}

	/**
	 * 重置遍历索引，实现Iterator接口
	 *
	 * @access public
	 * @return mixed
	 */
	public function rewind(){}

	/**
	 * 判断是否是调用了rewind()或next()之后获得的有效对象，实现 Iterator接口
	 *
	 * @access public
	 * @return boolean
	 */
	public function valid(){}

	/**
	 * 返回对象总数，实现 Countable接口
	 *
	 * @access public
	 * @return int
	 */
	public function count(){}

	/**
	 * 确定集合是否是空的
	 *
	 * @access public
	 * @return boolean
	 */
	public function isEmpty(){}

	/**
	 * 返回集合中的第一个对象，如果没有任何对象，则抛出异常
	 *
	 * @access public
	 * @return object
	 */
	public function first(){}

	/**
	 * 返回集合中的最后一个对象，如果没有任何对象，则抛出异常
	 *
	 * @access public
	 * @return object
	 */
	public function last(){}

	/**
	 * 追加数组或Coll对象的内容到集合中
	 *
	 * < $data = array(
	 *   $item1,
	 *   $item2,
	 *   $item3
	 *   );
	 *   $coll->append($data); >
	 * < Coll::append() 在追加数据时不会保持键名。>
	 *
	 * @access public
	 * @param array|Coll $Data 要追加的数据
	 * @return Coll 返回集合对象本身，实现连贯接口
	 */
	public function append($Data){}

	/**
	 * 查找符合指定属性值的对象，没找到返回 NULL
	 *
	 * < 在$coll集合中搜索title属性等于T1的第一个对象
	 *   $item=$coll->search('title','T1'); >
	 *
	 * @access public
	 * @param string $sPropName 要搜索的属性名
	 * @param mixed $Needle 需要的属性值
	 * @param boolean $bStrict 是否严格比对属性值
	 * @return mixed
	 */
	public function search($sPropName,$Needle,$bStrict=false){}

	/**
	 * 将集合所有元素的值转换为一个名值对数组
	 *
	 * @access public
	 * @param string $sKeyName 键
	 * @param string $sValueName 值
	 * @return array
	 */
	public function toHashMap($sKeyName,$sValueName=null){}

	/**
	 * 对集合中每一个对象调用指定的方法
	 *
	 * < class OrderItem{
	 *
	 *   public $price;
	 *   public $quantity;
	 *
	 *   // 构造函数
	 *   function __construct($price, $quantity){
	 *       $this->price = $price;
	 *       $this->quantity = $quantity;
	 *   }
	 *
	 *   // 计算订单项目的小计
	 *   function sum(){
	 *
	 *       return $this->price * $this->quantity;
	 *   }
	 *
	 *   // 返回单价
	 *   function price(){
	 *
	 *       return $this->price;
	 *   }
	 *
	 *   // 返回数量
	 *   function quantity(){
	 *
	 *       return $this->quantity;
	 *   }
	 *
	 *   // 累加多个合计
	 *   static function totalSum($objects){
	 *
	 *      $total = 0;
	 *      while (list(, $item) = each($objects)){
	 *
	 *           $total += $item->sum();
	 *      }
	 *      return $total;
	 *   }
	 *
	 *   // 用于 Coll 的回调方法
	 *   static function collCallback_(){
	 *
	 *       return array('sum' => 'totalSum');
	 *   }
	 *  }
	 *
	 *  // 构造一个集合，包含多个 OrderItem 对象
	 *  $coll = Coll::create(array(
	 *   new OrderItem(100, 3),
	 *   new OrderItem(200, 5),
	 *   new OrderItem(300, 2)), 'OrderItem');
	 *
	 *  // 取得集合中所有订单项目的金额合计
	 *  $sum = $coll->sum();
	 *
	 *  // 将会输出 1900 （根据 100 * 3 + 200 * 5 + 300 * 2 计算）
	 *  echo $sum;
	 *
	 *  // 取得每个项目的单价
	 *  $price = $coll->price();
	 *  // 将会输出 array(100, 200, 300)
	 *  dump($price); >
	 *
	 * 当调用 Coll 自身没有定义的方法时，Coll 将认为开发者是要对集合中的每一个对象调用指定方法。
	 *
	 * -  此时，Coll 首先检查集合中的对象是否提供了 collCallback_() 静态方法；
	 * -  如果有，则通过 collCallback_() 取得一个方法映射表；
	 * -  Coll 根据 collCallback_() 返回的方法映射表调用对象的其他静态方法。
	 * -  如果没有提供 collCallback_() 方法，或方法映射表中没有指定的方法。
	 * -  Coll 则遍历集合中的所有对象，尝试调用对象的指定方法。>
	 *
	 * @access public
	 * @param string $sMethod
	 * @param array $arrArgs
	 * @return mixed
	 */
	public function __call($sMethod,$arrArgs){}

	/**
	 * 检查值是否符合类型要求
	 *
	 * @access public
	 * @param object $oObject 对象
	 * @return void
	 */
	protected function checkType_($oObject){}

}
