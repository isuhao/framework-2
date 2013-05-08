<?php

namespace TE\Mvc\Service\Db;

use TE\Mvc\Base;
use TE\Db\Connector;

/**
 * Class AbstractTable
 *
 * @package TE\Mvc\Service\Db
 */
abstract class AbstractTable extends Base
{
    /**
     * db  
     * 
     * @var mixed
     */
    protected $serviceDb;

    /**
     * _table  
     * 
     * @var string
     */
    private $_table;

    /**
     * _primaryKey  
     * 
     * @var string
     */
    private $_primaryKey;

    /**
     * setServiceDb
     * 
     * @param Connector $serviceDb
     * @access public
     * @return void
     */
    public function setServiceDb(Connector $serviceDb)
    {
        $this->serviceDb = $serviceDb;
    }

    /**
     * setTable
     *
     * @param $table
     */
    public function setTable($table)
    {
        $this->_table = $table;
    }

    /**
     * 获取表名
     *
     * @return string
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * setPrimaryKey  
     * 
     * @param string $primaryKey 
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->_primaryKey = $primaryKey;
    }

    /**
     * 获取主键名
     *
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->_primaryKey;
    }

    /**
     * add
     *
     * @param array $data
     * @return mixed
     */
    public function add(array $data)
    {
        // 插入数据
        $key = $this->getPrimaryKey();
        $insertId = $this->serviceDb->insert($this->getTable())->values($data)->exec();
        return isset($data[$key]) ? $data[$key] : $insertId;
    }

    /**
     * set
     *
     * @param       $key
     * @param array $data
     * @return int
     */
    public function set($key, array $data)
    {
        return $this->serviceDb->update($this->getTable())->setMultiple($data)
            ->where($this->getPrimaryKey() . ' = ?', $key)
            ->exec();
    }

    /**
     * remove
     *
     * @param $key
     * @return int
     */
    public function remove($key)
    {
        return $this->serviceDb->delete($this->getTable())
            ->where($this->getPrimaryKey() . ' = ?', $key)
            ->exec();
    }

    /**
     * get
     *
     * @param string $key
     * @param mixed $columns
     * @return mixed
     */
    public function get($key, $columns = NULL)
    {
        $result = $this->serviceDb->select($this->getTable(), $columns)
            ->where($this->getPrimaryKey() . ' = ?', $key)
            ->fetchOne();

        return is_string($columns) ? $result[$columns] : $result;
    }

    /**
     * getMultiple
     *
     * @param array $keys
     * @param mixed $columns
     * @return array
     */
    public function getMultiple(array $keys, $columns = NULL)
    {
        $result = $this->serviceDb->select($this->getTable(), $columns)
            ->where($this->getPrimaryKey() . ' IN ?', $keys)
            ->fetchAll();

        return is_string($columns) ? array_map(function ($row) use ($columns) {
            return $row[$columns];
        }, $result) : $result;
    }

    /**
     * findBy
     *
     * @param string $key
     * @param mixed $value
     * @param mixed $columns
     * @return mixed
     */
    public function findBy($key, $value, $columns = NULL)
    {
        $result = $this->serviceDb->select($this->getTable(), $columns)
            ->where("{$key} = ?", $value)
            ->limit(1)
            ->fetchOne();

        return is_string($columns) ? $result[$columns] : $result;
    }
}

