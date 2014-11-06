<?php
/**
 * KK's Laboratory (c) 2009-2014.
 * Project: KK-Framework
 * User: kookxiang
 */
namespace System\Database\Adapter;
use System\Database\Adapter;
use System\Database\QueryResult;
use System\Exception\DatabaseConnectionException;
use System\Exception\DatabaseException;

class MySQL implements Adapter{
	private $link;
	private $config;

	public function connect($config = array()){
		$function = $config['pconnect'] ? 'mysql_pconnect' : 'mysql_connect';
		$this->link = @$function($config['host'], $config['username'], $config['password'], 1);
		if(!$this->link)
			throw new DatabaseConnectionException('Cannot connect to MySQL server<br>'.$this->getErrorMessage(), $this->getErrorNumber());
		if($config['charset']){
			$sql = 'SET character_set_connection='.$config['charset'].', character_set_results='.$config['charset'].', character_set_client=binary, sql_mode=\'\'';
			mysql_query($sql, $this->link);
		}
		if(!@mysql_select_db($config['database'], $this->link))
			throw new DatabaseConnectionException('Fail to select your database<br>'.$this->getErrorMessage(), $this->getErrorNumber());
		$this->config = $config;
	}

	public function disconnect(){
		if($this->config['pconnect']) return;
		mysql_close($this->link);
	}

	public function get($resource, $offset = 0){
		return mysql_result($resource, $offset);
	}

	public function getErrorNumber(){
		if($this->link){
			return mysql_errno($this->link);
		}else{
			return mysql_errno();
		}
	}

	public function getErrorMessage(){
		if($this->link){
			return mysql_error($this->link);
		}else{
			return mysql_error();
		}
	}

	public function getInsertId(){
		$id = mysql_insert_id($this->link);
		if(!$id) return $id;
		return $this->query('SELECT last_insert_id()')->get(0);
	}

	public function getRow($resource){
		return mysql_fetch_array($resource, MYSQL_ASSOC);
	}

	public function query($sql, $silence = false){
		$resource = mysql_query($sql, $this->link);
		if(!$silence && !$resource){
			throw new DatabaseException($sql, $this);
		}
		return new QueryResult($resource, $this);
	}
}