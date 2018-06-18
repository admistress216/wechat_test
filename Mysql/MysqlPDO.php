<?php
/**
 * Created by PhpStorm.
 * User: tc-net
 * Date: 2016/8/11 0011
 * Time: 16:32
 */
class MysqlPDO{
    //获取对象句柄
    protected $message = 'Unknown exception'; // 异常信息
    protected static $_instance = null;
    protected $dbh;
    protected $where = '';
    protected $wheredata = array();
    protected $order = '';
    private function __construct($param) {
        try {
            //echo 'mysql:host=' . $param['hostname'] . ';port=' . $param['port'] . ';dbname=' . $param['database'], $param['username'], $param['password'];exit;
            $this->dbh = new PDO ( 'mysql:host=' . $param['hostname'] . ';port=' . $param['port'] . ';dbname=' . $param['database'], $param['username'], $param['password'], array (PDO::ATTR_PERSISTENT => false,PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8mb4;") );
//            self::$PDOInstance->query("SET client_encoding='UTF-8';");
//            self::$PDOInstance->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
//            self::$PDOInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch ( PDOException $e ) {
            throw new Exception('MySQL Error: '.$e->getMessage ());
            exit;
        }
    }
    public function clearWhere(){
        $this->where = '';
        $this->wheredata = array();
    }
    private function error($stmt){
        $erorr = $stmt->errorInfo();
        $this->message = "mysql ERROR:".$erorr[1]." ".$erorr[2];
        throw new Exception($this->message);
    }
    /**
     * 防止克隆
     *
     */
    private function __clone() {}

    /**
     * 单例
     *
     */
    public static function getInstance($param,$instance='default'){
        if (!isset(self::$_instance[$instance])||self::$_instance[$instance] === null) {
            self::$_instance[$instance] = new self($param);
        }
        return self::$_instance[$instance];
    }
    public function where($field,$val){
        $this->where[] = preg_match('/[=><]/i',$field)?"`{$field}`":"`{$field}`=";
        if(is_array($val)){
            $this->wheredata = array_merge($this->wheredata,$val);
        }else{
          array_push($this->wheredata,$val);
        }
    }
    public function like_where($field,$val){
        $this->where[] = "`{$field}` like ";
        if(is_array($val)){
            $this->wheredata = array_merge($this->wheredata,$val);
        }else{
            array_push($this->wheredata,$val);
        }
    }
//    public function orderby($field,$sort){
//        $this->order = "ORDER BY `{$field}` {$sort}";
//    }
//    public function select($tablename,$fields='*'){
//        $wheresql = !empty($this->where)?" WHERE ".implode("? AND ",$this->where)."?":'';
//        $sql = "SELECT {$fields} FROM {$tablename} {$wheresql}";
//        $stmt =  $this->dbh->prepare($sql);
//        $r = $stmt->execute();
//        return $r!==false?true:false;
//    }
    /**
     * @param $tablename 表名
     * @param $data array 数据
     * @return bool
     */
    public function update($tablename,$data){
        //防止全表更改
        if(empty($this->where)){
            return false;
        }
        if(empty($data)||!is_array($data)){
            return false;
        }
        $setsql = '';
        foreach($data as $k=>$v){
            $setsql .= "`{$k}`=?,";
        }
        $setsql = trim($setsql,',');
        $wheresql = implode("? AND ",$this->where)."?";
        $sql = "UPDATE {$tablename} SET {$setsql} WHERE {$wheresql}";
        $data = array_values($data);
        $data = array_merge_recursive($data,$this->wheredata);
        $stmt =  $this->dbh->prepare($sql);
        $r = $stmt->execute($data);
        return $r!==false?true:false;
    }

    /**
     * @param $tablename
     * @param $data array()
     * @return bool|string
     */
    public function replace($tablename,$data){
        if(empty($data)||!is_array($data)){
            return false;
        }
        $setsql = '';
        $vals = '';
        foreach($data as $k=>$v){
            $setsql .= "`{$k}`,";
            $vals .="?,";
        }
        $setsql = trim($setsql,',');
        $vals = trim($vals,',');
        $sql = "REPLACE into {$tablename}({$setsql}) VALUES ($vals)";
        $stmt =  $this->dbh->prepare($sql);
        if($stmt->execute(array_values($data))){

            return $this->dbh->lastInsertId();
        }else{
//            var_dump($stmt->errorInfo());exit;
            $this->error($stmt);
        }
        return false;
    }
    public function insert($tablename,$data){
        if(empty($data)||!is_array($data)){
            return false;
        }
        $setsql = '';
        $vals = '';
        foreach($data as $k=>$v){
            $setsql .= "`{$k}`,";
            $vals .="?,";
        }
        $setsql = trim($setsql,',');
        $vals = trim($vals,',');
        $sql = "INSERT INTO {$tablename}({$setsql}) VALUES ($vals)";
       // echo $sql;exit;
        $stmt =  $this->dbh->prepare($sql);
        $data = is_array($data)?array_values($data):array($data);
        if($stmt->execute(array_values($data))){
            return $this->dbh->lastInsertId();
        }else{
//            var_dump($stmt->errorInfo());exit;
            $this->error($stmt);
        }
        return false;
    }

    /**
     * 获取总数
     * @param $sql
     * @param $param
     * @return bool
     */
    public function getTotal($sql,$param=''){
        $stmt =  $this->dbh->prepare($sql);
        $param = !empty($param)&&is_array($param)?array_values($param):array($param);
        //var_dump($stmt->execute($param));
        if ($stmt->execute($param)) {
            $data = $stmt->fetch(PDO::FETCH_NUM);
            return $data[0];
        }else{
//            var_dump($stmt->errorInfo());exit;
            $this->error($stmt);
        }
        return false;
    }

    /**
     * 执行非查询操作
     * @param $sql
     * @param string $param
     * @return bool|string
     */
    public function exec($sql,$param=''){
        $stmt =  $this->dbh->prepare($sql);
        $param = !empty($param)&&is_array($param)?array_values($param):array($param);
        if ($stmt->execute($param)) {
            $id = $this->dbh->lastInsertId();
            return $id?$id:TRUE;
        }else{
//            var_dump($stmt->errorInfo());exit;
            $this->error($stmt);
        }
        return false;
    }
    public function query($sql,$param=''){
        $stmt =  $this->dbh->prepare($sql);
        if(!empty($param)){
            $param = !empty($param)&&is_array($param)?array_values($param):array($param);
        }
        if ($param?$stmt->execute($param):$stmt->execute()) {
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $data?$data:array();
        }else{
//            var_dump($stmt->errorInfo());exit;
            $this->error($stmt);
        }
        return false;
    }
    public function getOne($sql,$param=''){
        $data = array();
        $stmt =  $this->dbh->prepare($sql);
        if(!empty($param)){
            $param = !empty($param)&&is_array($param)?array_values($param):array($param);
        }
        if ($param?$stmt->execute($param):$stmt->execute()) {
            $data = $stmt->fetch(PDO::FETCH_OBJ);

        }else{
//            var_dump($stmt->errorInfo());exit;
            $this->error($stmt);
        }
        return $data?$data:array();
    }

    /**
     * beginTransaction 事务开始
     */
    public function beginTransaction()
    {
        $this->dbh->beginTransaction();
    }

    /**
     * commit 事务提交
     */
    public function commit()
    {
        $this->dbh->commit();
    }

    /**
     * rollback 事务回滚
     */
    public function rollback()
    {
        $this->dbh->rollback();
    }
    /**
     * checkFields 检查指定字段是否在指定数据表中存在
     *
     * @param String $table
     * @param array $arrayField
     */
    public function checkFields($table, $arrayFields){
        $fields = $this->getFields($table);
        foreach ($arrayFields as $key => $value) {
            if (!in_array($key, $fields)) {
                return false;
            }else{
                return true;
            }
        }
    }
    /**
     * getFields 获取指定数据表中的全部字段名
     *
     * @param String $table 表名
     * @return array
     */
    private function getFields($table){
        $fields = array();
        $recordset = $this->dbh->query("SHOW COLUMNS FROM $table");
        $this->getPDOError();
        $recordset->setFetchMode(PDO::FETCH_ASSOC);
        $result = $recordset->fetchAll();
        foreach ($result as $rows) {
            $fields[] = $rows['Field'];
        }
        return $fields;
    }
    /**
     * destruct 关闭数据库连接
     */
    public function __destruct(){
        $this->dbh = null;
        self::$_instance = null;
    }
}
