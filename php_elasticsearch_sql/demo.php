<?php
/**
 * Created by PhpStorm.
 * User: xls
 * Date: 2019/12/15
 * Time: 下午3:16
 */


require 'Es.php';
require 'EsSql.php';

$obj  = new EsSql();
$sql  = "select * from demo where name = '张三'";
$rows = $obj->doSql($sql);

var_export($rows);