<?php
/**
 * Author: lizengcai
 * Data: 2018/4/3
 * Time: 10:51
 */
class MysqlPDO {
    protected static $_instance = null;
    protected $dbh;

    private function __construct($param) {
        try {
            $this->dbh = new PDO('mysql:host=' . $param['hostname'] . ';port=' . $param['port'] . ';dbname=' . $param['database'], $param['username'], $param['password'],
                [
                    PDO::ATTR_PERSISTENT => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8mb4;"
                ]);
        } catch (PDOException $e) {
            throw new Exception('Mysql Error: '.$e->getMessage());
            exit;
        }
    }


}