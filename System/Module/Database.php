<?php
/**
 * KK's Laboratory (c) 2009-2014.
 * Project: KK-Framework
 * User: kookxiang
 */
namespace System\Module;
use System\Database\Adapter;
use System\Database\QueryResult;
use System\Database\Statement;
use System\Exception\DatabaseConnectionException;
use System\Exception\EmptyConnectionPool;

class Database{
	const MASTER = 1;
	const SLAVE = 2;
	private $connection_pool = array(
		self::MASTER => array(),
		self::SLAVE  => array(),
	);

	/**
	 * 注册一台数据库服务器
	 * @param Array $config 数据库配置
	 * @param int $type 服务器类型 (主库/从库)
	 * @throws Exception 数据库配置不正确时抛出异常
	 */
	public function registerServer($config, $type = self::MASTER){
		$adapterClass = '\System\Database\Adapter\\'.$config['adapter'];
		$adapter = new $adapterClass();
		if(!$adapter instanceof Adapter)
			throw new DatabaseConnectionException();
		$adapter->connect($config);
		$this->connection_pool[$type][] = $adapter;
	}

	/**
	 * 从连接池取一条数据库连接
	 * @param int $type 服务器类型 (主库/从库)
	 * @return Adapter 数据库适配器
	 * @throws EmptyConnectionPool 无可用连接时抛出异常
	 */
	public function getServer($type = self::MASTER){
		if(empty($this->connection_pool[self::SLAVE]))
			$type = self::MASTER;
		$offset = array_rand($this->connection_pool[$type]);
		$link = $this->connection_pool[$type][$offset];
		if(!$link) throw new EmptyConnectionPool();
		return $link;
	}

	/**
	 * 发起一次数据库查询请求
	 * 可自动代入 %* 参数
	 * @param String $sql 语句
	 * @return QueryResult 查询结果
	 * @throws Exception 若执行数据库操作时出现错误则抛出异常
	 */
	public function query($sql){
		$statement = new Statement($sql);
		$args = func_get_args();
		array_shift($args);
		if($args)
			call_user_func_array(array($statement, 'format'), $args);
		$adapter = $this->autoDecideServer($statement);
		return $adapter->query($statement);
	}

	/**
	 * 自动选择数据库连接
	 * @param String $sql SQL 语句
	 * @return Adapter 数据库适配器
	 */
	public function autoDecideServer($sql){
		static $keywords = array('INSERT', 'REPLACE', 'UPDATE', 'DELETE', 'CREATE', 'ALTER', 'DROP');
		list($operation) = explode(' ', strtoupper(trim($sql)));
		if(in_array($operation, $keywords))
			return $this->getServer(self::MASTER);
		else
			return $this->getServer(self::SLAVE);
	}

	public function __destruct(){
		$connections = array_merge($this->connection_pool[self::MASTER], $this->connection_pool[self::SLAVE]);
		while($connection = array_pop($connections)){
			$connection->disconnect();
		}
	}
}
