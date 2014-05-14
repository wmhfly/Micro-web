<?php
/**
 * 数据库操作类
 *
 * 基于{@link http://www.php.net/manual/en/ref.pdo.php PDO}实现,自 PHP 5.1.0 起，默认开启PDO 和 PDO_SQLITE 驱动
 * 注意：使用前需确保,你所使用的数据库PDO驱动是否有开启
 * 
 * 创建一个数据库连接，默认是激活连接到数据库:
 * <pre>
 * $connection=new DbConnection($dsn,$username,$password);
 * </pre>
 * 
 * 关闭数据库连接:
 * <pre>
 * $connection->close();
 * </pre>
 * 
 * 自定义sql查找数据库:
 * <pre>
 * $sql = 'SELECT * FROM `table` WHERE `id`=:id';
 * $params = array(':id'=>1);
 * //列名索引的数组结果集
 * $result = $connection->findBySql($sql,$params);
 * </pre>
 * 
 * 统计符合条件的数量:
 * <pre>
 * $sql = 'SELECT COUNT(*) FROM `table` WHERE `id`=? AND `tag`=?';
 * $params = array(1,'hot');
 * $num = $connection->countBySql($sql,$params);
 * </pre>
 * 
 * 关联一张表，然后可对其进行(增，删，查，改)操作:
 * <pre>
 * $tab = $connection->hook('table');
 * </pre>
 * 
 * 方法函数中的参数说明：
 * $condition sql中条件部分，可以是字符串，也可以是数组
 * $params 占位符对应的值，数组类型
 * <pre>
 * $condition = 'id=? AND tag=?';
 * $params = array(1,'js');
 * or
 * $condition = array('id'=>':id','tag'=>':tag');
 * $params = array(':id'=>1,':tag'=>'js');
 * </pre>
 * 
 * $criteria 整条sql配置，数组类型:
 * <pre>
 * $criteria = array(
 * 		'select'=>'id,name,filed',
 * 		'condition'=>'string',
 * 		'params'=>'array',
 * 		'order'=>'id asc',
 * 		'limit'=>'int',
 * 	    'offset'=>'int',
 * 		'group'=>'string',
 * 		'having'=>'string',
 * );
 * </pre>
 * 
 * @author Wu Miao Hui <363539981@qq.com>
 * @link http://www.wmhfly.com/
 * @version 1.0
 */
class DbConnection extends Component {
	
	/**
	 * @var string 数据源名称或叫做 DSN，包含了请求连接到数据库的信息.
	 */
	public $connectionString;
	/**
	 * @var string DSN字符串中的用户名。对于某些PDO驱动，此参数为可选项.
	 */
	public $username='';
	/**
	 * @var string DSN字符串中的密码。对于某些PDO驱动，此参数为可选项.
	 */
	public $password='';
	/**
	 * @var array 一个具体驱动的连接选项的键=>值数组
	 */
	public $driver_options = array();
	/**
	 * @var string 数据库编码
	 */
	public $charset = 'utf8';
	/**
	 * @var bool 是否自动连接
	 */
	public $autoConnect=true;
	/**
	 * @var 禁用prepared statements的仿真效果
	 */
	public $emulatePrepare;
	/**
	 * @var array 不同数据库兼容模式
	 */
	public $driverMap=array(
			'mysql'=>'MysqlSchema',    // MySQL
			'sqlite'=>'SqliteSchema',  // sqlite 3
			'sqlite2'=>'SqliteSchema', // sqlite 2
	);
	
	private $_pdo;
	private $_active=false;
	private $_schema;	
	
	/**
	 * 构造函数
	 * @param string $dsn
	 * @param string $username
	 * @param string $password
	 * @param string $options
	 */
	public function __construct($config){
		parent::__construct($config);
	
		if($this->autoConnect)
			$this->setActive(true);
	}
	
	/**
	 * 返回是否DB连接建立
	 * @return boolean
	 */
	public function getActive(){
		return $this->_active;
	}
	
	/**
	 * 打开或者关闭数据库连接
	 * @param boolean $value
	 */
	public function setActive($value){
		if($value!=$this->_active)
		{
			if($value)
				$this->open();
			else
				$this->close();
		}
	}
	
	/**
	 * 创建一个数据库连接，如果当前不存在
	 */
	public function open(){
		if($this->_pdo===null){
			if(empty($this->connectionString))
				self::trace('DbConnection.connectionString cannot be empty');
			try{
				$this->_pdo=$this->createPdoInstance();
				$this->initConnection($this->_pdo);
				$this->_active=true;
			}catch(PDOException $e){
				self::trace('DbConnection::open()  failed to open the DB connection.'.$e->getMessage());
			}
		}
	}
	
	/**
	 * 关闭数据库连接
	 */
	public function close(){
		$this->_pdo=null;
		$this->_active=false;
		$this->_schema = null;
	}
	
	/**
	 * 实例化一个PDO对象
	 * @return PDO
	 */
	public function createPdoInstance(){
		return new PDO($this->connectionString,$this->username,
				$this->password,$this->driver_options);
	}
	
	/**
	 * 初始化数据库连接
	 * @param PDO $pdo
	 */
	public function initConnection($pdo){
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		if($this->emulatePrepare!==null && constant('PDO::ATTR_EMULATE_PREPARES'))
			$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,$this->emulatePrepare);
		if($this->charset!==null){
			$driver=strtolower($pdo->getAttribute(PDO::ATTR_DRIVER_NAME));
			if(in_array($driver,array('mysql')))
				$pdo->exec('SET NAMES '.$pdo->quote($this->charset));
		}

	}
	
	/**
	 * @return PDO 获取当前数据库连接对象
	 */
	public function getPdoInstance(){
		return $this->_pdo;
	}
	
	/**
	 * 返回最后插入id
	 * @param string $sequenceName
	 * @return string
	 */
	public function getLastInsertID($sequenceName=''){
		$this->setActive(true);
		return $this->_pdo->lastInsertId($sequenceName);
	}
	
	/**
	 * 输出调试信息
	 * @param string $tip 输出调试信息
	 */
	public static function trace($tip){
		echo $tip;
		exit;
	}
	
	/**
	 * 返回当前连接的数据库模式
	 * @return DbSchema the database schema for the current connection
	 */
	public function getSchema(){
		if($this->_schema!==null)
			return $this->_schema;
		else{
			$driver=$this->getDriverName();
			if(isset($this->driverMap[$driver])){
				$driverSchema = $this->driverMap[$driver];
				return $this->_schema = new $driverSchema();	
			}else{
				self::trace('DbConnection::getSchema()  does not support reading schema for '.$driver.' database.');
			}
				
		}
	}
	
	/**
	 * 返回当前驱动的名称
	 * @return string name of the DB driver
	 */
	public function getDriverName(){
		if(($pos=strpos($this->connectionString, ':'))!==false)
			return strtolower(substr($this->connectionString, 0, $pos));
	}
	
	/**
	 * 返回一个数据表关联对象
	 * @param string $table
	 * @return ActiveTable
	 */
	public function hook($table){
		return new ActiveTable($this,$table);
	}
	
	/**
	 * diy sql查找数据库
	 * @param string $sql
	 * @param array $params
	 */
	public function findBySql($sql,$params=array()){
		try {
			$stmt =  $this->getStatement($sql);
			if($params)
				$this->bindValues($stmt, $params);
			
			if($stmt->execute()){
				return $stmt->fetchAll(PDO::FETCH_ASSOC);
			}else{
				return false;
			}
		} catch (Exception $e) {
			echo ('DbConnection::findBySql failed to execute the SQL statement: '.$e->getMessage());
			exit;
		}
	}
	
	/**
	 * 统计符合条件的行数
	 * @param string $sql
	 * @param array $params
	 */
	public function countBySql($sql,$params=array()){	
		try {
			$stmt = $this->getStatement($sql);
			if($params)
				$this->bindValues($stmt, $params);
			if($stmt->execute()){
				return $stmt->fetchColumn();
			}else{
				return false;
			}
		} catch (Exception $e) {
			echo 'DbConnection::countBySql fail. '.$e->getMessage();
		}
	}
		
	/**
	 * 预编译sql
	 * @param string $sql
	 * @return PDOStatement
	 */
	public function getStatement($sql){
		return $this->getPdoInstance()->prepare($sql);
	}
	
	/**
	 * 绑定值到占位符，并兼容处理是有无占位符
	 * @param PDOStatement $stmt
	 * @param array $values
	 */
	public function bindValues($stmt, $values){
		if(($n=count($values))===0)
			return;
		if(isset($values[0])){
			for($i=0;$i<$n;++$i)
				$stmt->bindValue($i+1,$values[$i]);
		}
		else{
			foreach($values as $name=>$value){
				if($name[0]!==':')
					$name=':'.$name;
				$stmt->bindValue($name,$value);
			}
		}
	}
	
	
}

/**
 * 数据库模式
 *
 */
class DbSchema {
	
	/**
	 * 处理表名
	 * @param string $name database table name
	 * @return string
	 */
	public function quoteTableName($name){
		if(strpos($name,'.')===false)
			return $this->quoteSimpleTableName($name);
		$parts=explode('.',$name);
		foreach($parts as $i=>$part)
			$parts[$i]=$this->quoteSimpleTableName($part);
		return implode('.',$parts);
	
	}
	
	/**
	 * 给表名加单引号
	 * @param string $name database table name
	 * @return string
	 */
	public function quoteSimpleTableName($name){
		return "'".$name."'";
	}
	
	/**
	 * 处理表字段
	 * @param string $name table filed
	 * @return string
	 */
	public function quoteColumnName($name){
		if(($pos=strrpos($name,'.'))!==false){
			$prefix=$this->quoteTableName(substr($name,0,$pos)).'.';
			$name=substr($name,$pos+1);
		}
		else
			$prefix='';
		return $prefix . ($name==='*' ? $name : $this->quoteSimpleColumnName($name));
	}
	
	/**
	 * 给表字段加双引号
	 * @param string $name
	 * @return string
	 */
	public function quoteSimpleColumnName($name){
		return '"'.$name.'"';
	}
}

class MysqlSchema extends DbSchema {
	
	/**
	 * @see DbSchema::quoteSimpleTableName()
	 */
	public function quoteSimpleTableName($name){
		return '`'.$name.'`';
	}
	
	/**
	 * @see DbSchema::quoteSimpleColumnName()
	 */
	public function quoteSimpleColumnName($name){
		return '`'.$name.'`';
	}
}

class SqliteSchema extends DbSchema {
	
}

/**
 * ActiveTable class
 * 
 */
class ActiveTable {
	
	const PARAM_PREFIX=':fly';
	
	private $_table = '';
	private $_connection = null;
	
	/**
	 * 构造函数
	 * @param DbConnection $connection
	 * @param string $table
	 */
	public function __construct(DbConnection $connection,$table){
		$this->_connection = $connection;
		$this->_table = $table;
	}
	
	/**
	 * 根据主键查找
	 * @param int $id
	 * @param string $pk
	 * @return array | false
	 */
	public function findByPk($id,$pk='id'){
		return $this->findByAttr(array('condition'=>$pk.'=?','params'=>array($id)));
	}
	
	/**
	 * 根据主键删除
	 * @param int $id
	 * @param string $pk
	 * @return int | false
	 */
	public function deleteByPk($id,$pk='id'){
		return $this->deleteByAttr($pk.'=?',array($id));
	}
	
	/**
	 * 根据主键更新
	 * @param int $id
	 * @param string $pk
	 * @return int | false
	 */
	public function updateByPk($data,$id,$pk='id'){
		return $this->updateByAttr($data,$pk.'=?',array($id));
	}
	

	/**
	 * 新增一条数据
	 * @param array $data array('field'=>'value')
	 * @return int | false
	 */
	public function insert($data){
		$fields=array();
		$values=array();
		$placeholders=array();
		$i=0;
		foreach($data as $name=>$value){
			$fields[]=$this->getColumnName($name);
			$placeholders[]=self::PARAM_PREFIX.$i;
			$values[self::PARAM_PREFIX.$i]=$value;
			$i++;
		}
		$table = $this->getTable();
		$sql="INSERT INTO {$table} (".implode(', ',$fields).') VALUES ('.implode(', ',$placeholders).')';
		try {
			$stmt=$this->getStatement($sql);
			$this->bindValues($stmt, $values);
			if($stmt->execute())
				return $this->getLastInsertID();
			
		} catch (Exception $e) {
			echo 'ActiveTable::insert failed to execute the SQL statement: '.$e->getMessage();
			return false;
		}			
	}
	
	/**
	 * 更新数据
	 * @param array $params
	 * @param string $where AND|OR
	 * @return number | false
	 */
	public function updateByAttr($data,$condition='',$params=array(),$where='AND'){
		$fields=array();
		$values=array();
		$i=0;
		foreach($data as $name=>$value){
			$_name = $this->getColumnName($name);
			$fields[]=$_name.'='.self::PARAM_PREFIX.$i;
			$values[self::PARAM_PREFIX.$i]=$value;
			$i++;
			
		}
		$table = $this->getTable();
		$sql="UPDATE {$table} SET ".implode(', ',$fields);
		$sqlData=$this->proxyWhereAndValues($condition,$params,isset($params[0]),$where,$i);
		try {
			$sql.=$sqlData['sqlWhere'];
			$values = array_merge($values,$sqlData['values']);
			$stmt= $this->getStatement($sql);
			$this->bindValues($stmt, $values);
			$stmt->execute();
			return $stmt->rowCount();
		} catch (Exception $e) {
			echo 'ActiveTable::updateByAttr failed to execute the SQL statement: '.$e->getMessage();
			return false;
		}		
	}
	
	/**
	 * 删除记录
	 * @param string|arrray $condition
	 * @param array $params
	 * @param string $where AND|OR
	 * @return number | false
	 */
	public function deleteByAttr($condition='',$params=array(),$where='AND'){
		$table = $this->getTable();
		$sqlData=$this->proxyWhereAndValues($condition,$params,isset($params[0]),$where);
		try {
			$sql="DELETE FROM {$table}".$sqlData['sqlWhere'];
			$stmt=$this->getStatement($sql);
			$this->bindValues($stmt, $sqlData['values']);
			$stmt->execute();
			return $stmt->rowCount();
		} catch (Exception $e) {
			echo ('ActiveTable::deleteByAttr failed to execute the SQL statement: '.$e->getMessage());
			return false;
		}	
	}
	
	/**
	 * 查找一条记录
	 * @param $criteria array
	 * @return mixed
	 */
	public function findByAttr($criteria=array()){
		return $this->query($criteria);
	}
	
	/**
	 * 查找 多条|全部 记录
	 * @param $criteria array
	 * @return mixed
	 */
	public function findAllByAttr($criteria=array()){
		return $this->query($criteria,true);
	}
	
	/**
	 * 返回影响的行数
	 * @param string $condition
	 * @param array $params
	 * @return int
	 */
	public function count($condition='',$params=array()){
		$sql = 'SELECT COUNT(*) FROM '.$this->getTable();
		if($condition)
			$sql.=' WHERE '.$condition;
		
		try {
			$stmt = $this->getStatement($sql);
			if($params) 
				$this->bindValues($stmt, $params);
			if($stmt->execute()){
				return $stmt->fetchColumn();
			}else{
				return false;
			}
		} catch (Exception $e) {
			echo 'ActiveTable::counts fail. '.$e->getMessage();
		}
	}
	
	/**
	 * 判断是否存在
	 * @param string $condition
	 * @param array $params
	 * @return boolean
	 */
	public function exists($condition='',$params=array()){
		return $this->count($condition,$params) > 0;
	}
	
	/**
	 * 查找数据库
	 * @param array $criteria
	 * @param boolean $all
	 */
	private function query($criteria,$all=false){
		if(!$all)
			$criteria['limit'] = 1;
		$sql = $this->createSql($criteria);
		try {
			$stmt = $this->getStatement($sql);
			if(isset($criteria['params'])&&($params=$criteria['params']))
				$this->bindValues($stmt, $params);
			
			if($stmt->execute()){
				return ($all) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : $stmt->fetch(PDO::FETCH_ASSOC);
			}else{
				return false;
			}
						
		} catch (Exception $e) {
			echo ('ActiveTable::query failed to execute the SQL statement: '.$e->getMessage());
			exit;
		}
		
	}
	
	/**
	 * 代理生成sql where语句 和返回替换字段的数组values
	 * @param array|string $condition
	 * @param array $params
	 * @param bool $bindByPosition
	 * @param string $where
	 * @param int $i
	 * @return array
	 */
	private function proxyWhereAndValues($condition,$params,$bindByPosition,$where,$i=0){
		$sqlWhere = '';
		$values=array();
		if(is_array($condition)){
			$w_fields=array();
			foreach($condition as $name=>$value){
				$_name = $this->getColumnName($name);
				if($bindByPosition){
					$_value = array_shift($params);
					$_field = self::PARAM_PREFIX.$i;
	
				}else{
					$_value = $params[$value];
					$_field = $value;
	
				}
				$w_fields[]=$_name.'='.$_field;
				$values[$_field]=$_value;
				$i++;
			}
			$sqlWhere =' WHERE '.implode(' '.$where.' ',$w_fields);
		}else{
			if($bindByPosition){
				while (strpos($condition, '?')!==false){
					$_field = self::PARAM_PREFIX.$i;
					$condition = preg_replace("/\?/", $_field, $condition,1);
					$values[$_field] = array_shift($params);
					$i++;
				}
			}else{
				foreach ($params as $name=>$value)
					$values[$name] = $value;
			}
			$sqlWhere =' WHERE '.$condition;
		}
		return array('sqlWhere'=>$sqlWhere,'values'=>$values);
	}
	
	
	/**
	 * 拼接生成sql语句
	 * @param array $criteria
	 */
	private function createSql($criteria){
		$sql = 'SELECT';
		if(is_array($criteria)){
			//select fileds
			if(isset($criteria['select'])&&($select = $criteria['select'])){
				$fileds = explode(',', $select);
				$fileds = array_map(array($this,'getColumnName'), $fileds);
				$sql.=' '.implode(',', $fileds);
			}else{
				$sql.=' *';
			}
			$sql.= ' FROM '.$this->getTable();
			//where
			if(isset($criteria['condition'])&&($condition = $criteria['condition'])){
				$sql.=' WHERE '.$condition;
			}
			//group by
			if(isset($criteria['group'])&&($group = $criteria['group'])){
				$sql.=' GROUP BY '.$group;
			}
			//having
			if(isset($criteria['having'])&&($having = $criteria['having'])){
				$sql.=' HAVING '.$having;
			}
			//order by
			if(isset($criteria['order'])&&($order = $criteria['order'])){
				$sql.=' ORDER BY '.$order;
			}
			//limit			
			if(isset($criteria['limit'])&&($limit = $criteria['limit'])){
				$sql.=' LIMIT '.$limit;
			}
			//offset
			if(isset($criteria['offset'])&&($offset = $criteria['offset'])){
				$sql.=' OFFSET '.$offset;
			}
		}else{
			$sql .= ' * FROM '.$this->getTable();
		}
		return $sql;
	}
	
	/**
	 * 返回兼容处理过的表格
	 * return string table
	 */
	private function getTable(){
		return $this->_connection->getSchema()->quoteTableName($this->_table);
	}
	
	/**
	 * 返回兼容后的表字段
	 * @param string $name table filed
	 * @return string
	 */
	private function getColumnName($name){
		return $this->_connection->getSchema()->quoteColumnName($name);
	}
	
	/**
	 * @param string $sql
	 * @return PDOStatement
	 */
	private function getStatement($sql){
		return $this->_connection->getStatement($sql);
	}
	
	/**
	 * @param PDOStatement $stmt
	 * @param array $values
	 */
	private function bindValues($stmt, $values){
		$this->_connection->bindValues($stmt, $values);
	}
	
	/**
	 * @return int insert_id
	 */
	private function getLastInsertID(){
		return $this->_connection->getLastInsertID();
	}
	
}
?>