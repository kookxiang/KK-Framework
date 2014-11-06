<?php
/**
 * KK's Laboratory (c) 2009-2014.
 * Project: KK-Framework
 * User: kookxiang
 */
namespace System\Database;

interface Adapter {
	public function connect($config = array());
	public function disconnect();
	public function get($resource, $offset = 0);
	public function getErrorNumber();
	public function getErrorMessage();
	public function getInsertId();
	public function getRow($resource);
	public function query($sql, $silence = false);
} 