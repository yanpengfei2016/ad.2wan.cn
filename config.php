<?php

$cnf = [];

$cnf["polardb"] = [
    'host'     => 'pc-bp1on5ba4ye88vp15.rwlb.rds.aliyuncs.com',
    'port'     => 3306,
    'user'     => 'gzy',
    'password' => 'Aliyun@dbstat',
    'database' => 'qiangwan'
];

$cnf["redis"] = [
    ["host" => "192.168.0.2", "port" => 6379],
    ["host" => "192.168.0.129", "port" => 6379],
    ["host" => "192.168.0.131", "port" => 6381],
    ["host" => "192.168.0.131", "port" => 6382]
];

return $cnf;

