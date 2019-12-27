<?php

date_default_timezone_set('Asia/Shanghai');
$start_time = time();

$cnf = require("config.php");
$polardb_cnf = $cnf["polardb"];
$redis_cnf = $cnf["redis"];
$redis_cluster = [];

foreach ($redis_cnf as $value) {
    $redis = new Redis();
    $redis->connect($value["host"], $value["port"]);
    $connect_result = $redis->ping();
    if($connect_result == '+PONG'){
        $redis_cluster[] = ['link' => $redis, 'info' => "{$value['host']}:{$value['port']}"];
    }
}

$polardb_link = mysqli_connect($polardb_cnf['host'], $polardb_cnf['user'], $polardb_cnf['password'], $polardb_cnf['database']);

if(empty($polardb_link)){
    var_dump(date('Y-m-d H:i:s') . " [ERROR] connect polardb failed");
    exit;
}
if(empty($redis_cluster)){
    var_dump(date('Y-m-d H:i:s') . " [ERROR] connect redis failed");
    exit;
}

$table_query = mysqli_query($polardb_link, "SELECT code FROM sys_table_code WHERE `key` = 'jfq_record' LIMIT 1");
$table_datas = mysqli_fetch_assoc($table_query);
if(!empty($table_datas['code'])) {
    $table_list = explode(',', $table_datas['code']);
    $table_name = $table_list[0];
}
if(empty($table_name)){
    $table_name = 'jfq_record_fallback';
}

$insert_sql_pre = "INSERT INTO {$table_name} (cid,game_id,promote_id,md5_device_id,aid,aid_name,campaign_id,creatid,csite,ctype,mac,user_agent,android_id,ip,ts,callback,terrace,ontime,remark) VALUES"; # look here

while (true) {
    $end_time = time();
    if($end_time - $start_time > 59){
        exit;
    }

    foreach ($redis_cluster as $redis) {
        consume_queue($redis, $polardb_link);
    }

    usleep(100000);//0.1秒
}

function consume_queue($redis, $polardb_link){
    global $insert_sql_pre;
    $sql_arr = [];
    $sql_arr_start_time = time();
    $redis_link = $redis['link'];
    $redis_info = $redis['info'];

    while (true){
        $data = $redis_link->lpop('ad_jfq_queue'); # look here
        $sql = trim($data);
        if(!empty($sql)){
            $sql_arr[] = $sql;
        }else{
            break;
        }

        if(count($sql_arr) >= 200){
            break;
        }else{
            $sql_arr_end_time = time();
            if($sql_arr_end_time - $sql_arr_start_time > 1){
                break;
            }
        }
    }

    if(!empty($sql_arr)){
        $sql = "{$insert_sql_pre} " . implode(',', $sql_arr);
        insert_db($polardb_link, $sql);
    }
}

function insert_db($link, $sql){
    $result = mysqli_query($link, $sql);
    if(empty($result)){
        // var_dump(date('Y-m-d H:i:s') . " [ERROR] ad_jfq_queue_consumer SQL query failed : ".mysqli_error($link));
        insert_db_retry($sql);
        exit;
    }
}

function insert_db_retry($sql){
    global $polardb_cnf;
    $polardb_link = mysqli_connect($polardb_cnf['host'], $polardb_cnf['user'], $polardb_cnf['password'], $polardb_cnf['database']);
    $result = mysqli_query($polardb_link, $sql);
    if(empty($result)){
        var_dump($sql);
        var_dump(date('Y-m-d H:i:s') . " [ERROR] retry ad_jfq_queue_consumer SQL query failed : ".mysqli_error($polardb_link));
        exit;
    }
}
