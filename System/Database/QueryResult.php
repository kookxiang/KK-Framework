<?php
/**
 * KK's Laboratory (c) 2009-2014.
 * Project: KK-Framework
 * User: kookxiang
 */
namespace System\Database;

class QueryResult{
	/**
	 * @var Resource 查询结果资源
	 */
	private $resource;
	/**
	 * @var Adapter 数据库适配器，自动调用获取查询结果
	 */
	private $adapter;

	public function  __construct($resource, $adapter){
		$this->resource = $resource;
		$this->adapter = $adapter;
	}

	public function getResource(){
		return $this->getResource();
	}

	/**
	 * 取数据库结果
	 * @param int $offset 偏移量
	 * @return mixed
	 */
	public function get($offset = 0){
		return $this->adapter->get($this->resource, $offset);
	}

	/**
	 * 取出最后一次执行 INSERT 操作后产生的 AUTO_INCREMENT ID
	 * @return int AUTO_INCREMENT ID
	 */
	public function getInsertId(){
		return $this->adapter->getInsertId();
	}

	/**
	 * 取查询结果中的下一行
	 * @return Array 一行查询结果
	 */
	public function getRow(){
		return $this->adapter->getRow($this->resource);
	}
} 