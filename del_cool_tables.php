<?php

date_default_timezone_set('Asia/Shanghai');

$cnf = require("config.php");
$polardb_cnf = $cnf["polardb"];
$polardb_link = mysqli_connect($polardb_cnf['host'], $polardb_cnf['user'], $polardb_cnf['password'], $polardb_cnf['database']);


$sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE (TABLE_NAME LIKE '%tkio_click%' OR TABLE_NAME LIKE '%jfq_record%') AND TABLE_NAME NOT IN ('jfq_record_inflow','5w_tkio_click_fallback','jfq_record_fallback','tkio_click_fallback','yyx_tkio_click_fallback')";
$query = mysqli_query($polardb_link, $sql);
$all_list = [];
while ($row = mysqli_fetch_assoc($query)) {
    $all_list[] = $row['TABLE_NAME'];
}

$sql = "SELECT code FROM sys_table_code WHERE `key` IN ('tkio_click','5w_tkio_click','jfq_record','yyx_tkio_click')";
$query = mysqli_query($polardb_link, $sql);
$used_list = [];
while ($row = mysqli_fetch_assoc($query)) {
    $used_list = array_merge($used_list, explode(',', $row['code']));
}

if( (count($all_list) > 8) && (count($used_list) == 8) ) {
    $del_list = array_diff($all_list, $used_list);

    foreach ($del_list as $value) {
        $query2 = mysqli_query($polardb_link, "SELECT * FROM sys_table_code WHERE `code` like '%{$value}%' LIMIT 1");
        $double_check = mysqli_fetch_assoc($query2);
        if(empty($double_check)){
            if(stristr($value, "jfq_record_inflow") === false){
                // var_dump("DROP TABLE {$value};");
                mysqli_query($polardb_link, "DROP TABLE {$value};");
            }
        }
    }
}
