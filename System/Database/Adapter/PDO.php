<?php
/**
 * KK's Laboratory (c) 2009-2014.
 * Project: depot
 * User: kookxiang
 */
namespace System\Database\Adapter;
use System\Database\Adapter;
use System\Database\QueryResult;
use System\Exception\DatabaseException;

class PDO implements Adapter {
	/**
	 * @var \PDO PDO 对象
	 */
	private $pdo;
	private $config;

	public function connect($config = array()) {
		$this->pdo = new \PDO($config['dsn'], $config['username'], $config['password'], $config['option']);
		$this->config = $config;
	}

	public function disconnect() {
		// Doesn't need to disconnect
	}

	public function get($resource, $offset = 0) {
		return $resource->fetchColumn($offset);
	}

	public function getErrorNumber() {
		list($id, $no) = $this->pdo->errorInfo();
		return $id + $no;
	}

	public function getErrorMessage() {
		list(, , $message) = $this->pdo->errorInfo();
		return $message;
	}

	public function getInsertId() {
		return $this->pdo->lastInsertId();
	}

	public function getRow($resource) {
		return $resource->fetch(\PDO::FETCH_ASSOC);
	}

	public function query($sql, $silence = false) {
		$resource = $this->pdo->query($sql);
		if(!$silence && !$resource){
			throw new DatabaseException($sql, $this);
		}
		return new QueryResult($resource, $this);
	}
}
