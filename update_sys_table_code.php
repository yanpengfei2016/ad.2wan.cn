<?php
//  非常重要！！！

date_default_timezone_set('Asia/Shanghai');

$cnf = require("config.php");
$polardb_cnf = $cnf["polardb"];
$polardb_link = mysqli_connect($polardb_cnf['host'], $polardb_cnf['user'], $polardb_cnf['password'], $polardb_cnf['database']);

if(empty($polardb_link)){
    sleep(60);
    var_dump(date('Y-m-d H:i:s') . " [ERROR] delete cool data connect polardb failed");
    exit;
}

$hour = intval(date('H'));
if ($hour >= 12) {
    $tail = date('Ymd') . 'pm';
} else {
    $tail = date('Ymd') . 'am';
}

// 抢玩点击表
$table_name = 'tkio_click_' . $tail;
$table_ddl = <<<ddl
CREATE TABLE `{$table_name}` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`game_id` int(11) NOT NULL DEFAULT '0',
`promote_id` int(11) NOT NULL DEFAULT '0',
`promote_account` varchar(20) NOT NULL DEFAULT '',
`ip` varchar(15) NOT NULL DEFAULT '' COMMENT 'IP地址',
`ctime` int(10) NOT NULL DEFAULT '0',
`user_agent` varchar(255) NOT NULL DEFAULT '',
`ua_md5` char(32) NOT NULL DEFAULT '',
PRIMARY KEY (`id`),
KEY `union_idx` (`ip`,`game_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='tkio 点击记录（排重）';
ddl;
check_table_exist($polardb_link, $table_name, $table_ddl, 'tkio_click');

// 我玩点击表
$table_name = '5w_tkio_click_' . $tail;
$table_ddl = <<<ddl
CREATE TABLE `{$table_name}` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`game_id` int(11) NOT NULL DEFAULT '0',
`promote_id` int(11) NOT NULL DEFAULT '0',
`promote_account` varchar(20) NOT NULL DEFAULT '',
`ip` varchar(15) NOT NULL DEFAULT '' COMMENT 'IP地址',
`ctime` int(10) NOT NULL DEFAULT '0',
`user_agent` varchar(255) NOT NULL DEFAULT '',
`ua_md5` char(32) NOT NULL DEFAULT '',
PRIMARY KEY (`id`),
KEY `union_idx` (`ip`,`game_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='tkio 点击记录（排重）';
ddl;
check_table_exist($polardb_link, $table_name, $table_ddl, '5w_tkio_click');

// 易游戏点击表
$table_name = 'yyx_tkio_click_' . $tail;
$table_ddl = <<<ddl
CREATE TABLE `{$table_name}` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`game_id` int(11) NOT NULL DEFAULT '0',
`promote_id` int(11) NOT NULL DEFAULT '0',
`promote_account` varchar(20) NOT NULL DEFAULT '',
`ip` varchar(15) NOT NULL DEFAULT '' COMMENT 'IP地址',
`ctime` int(10) NOT NULL DEFAULT '0',
`user_agent` varchar(255) NOT NULL DEFAULT '',
`ua_md5` char(32) NOT NULL DEFAULT '',
PRIMARY KEY (`id`),
KEY `union_idx` (`ip`,`game_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='tkio 点击记录（排重）';
ddl;
check_table_exist($polardb_link, $table_name, $table_ddl, 'yyx_tkio_click');

// 积分墙上报表
$table_name = 'jfq_record_' . $tail;
$table_ddl = <<<ddl
CREATE TABLE `{$table_name}` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`cid` int(11) NOT NULL DEFAULT '0' COMMENT '渠道ID',
`game_id` int(11) NOT NULL DEFAULT '0' COMMENT '游戏ID',
`promote_id` int(11) NOT NULL DEFAULT '0' COMMENT '渠道标志id',
`md5_device_id` char(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '设备唯一标识符 md5加密',
`device_id` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '设备唯一标识符 原值',
`subchannel_id` varchar(100) NOT NULL DEFAULT '' COMMENT '子渠道id',
`subchannel_name` varchar(100) NOT NULL DEFAULT '' COMMENT '子渠道名称',
`aid` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '广告计划id',
`aid_name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '广告计划名称',
`campaign_id` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '广告创意id',
`campaign_name` varchar(100) NOT NULL DEFAULT '' COMMENT '广告创意名称',
`creatid` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '广告组id',
`create_name` varchar(100) NOT NULL DEFAULT '' COMMENT '广告组名称',
`csite` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '广告投放位置',
`ctype` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '创意样式',
`mac` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '去除分隔符 ":",(采用获取原始值)取 md5sum 摘要(备注:入网硬件地址)',
`user_agent` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '客户端上报数据时http的header中的user_agent',
`android_id` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '安卓手机为androidid md5加密,iOS设备为openudid md5加密',
`ip` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '用户终端的公共IP地址',
`ts` int(11) NOT NULL DEFAULT '0' COMMENT '客户端发生广告点击事件的时间戳',
`callback` varchar(2000) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '回调地址',
`ontime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间戳',
`remark` tinyint(1) NOT NULL DEFAULT '0' COMMENT '渠道类型',
`terrace` tinyint(1) NOT NULL DEFAULT '0' COMMENT '平台 1:抢玩 | 2:我玩 | 3:易游戏 ',
`is_relation` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态: 0 待激活 |  1 已激活 | 2 已回调 | 3 待回调',
`is_reg` tinyint(1) NOT NULL DEFAULT '0' COMMENT '注册状态：0 待注册 | 1 已注册 | 2 已回调 | 3 待回调',
`is_pay` tinyint(1) NOT NULL DEFAULT '0' COMMENT '付费状态：0 待付费 | 1 已付费 | 2 已回调 | 3 待回调',
PRIMARY KEY (`id`),
KEY `idx_device_promote_ontime` (`md5_device_id`,`android_id`,`promote_id`,`game_id`,`ontime`),
KEY `idx_is_reg` (`is_reg`),
KEY `idx_is_pay` (`is_pay`),
KEY `idx_is_relation` (`is_relation`,`cid`) USING BTREE,
KEY `idx_promote_game` (`promote_id`,`game_id`,`cid`,`is_relation`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=gbk ROW_FORMAT=DYNAMIC COMMENT='积分墙上报记录表';
ddl;
check_table_exist($polardb_link, $table_name, $table_ddl, 'jfq_record');

function check_table_exist($link, $table_name, $table_ddl, $table_key){
    $check_sql = "SELECT COUNT(1) AS count FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME= '{$table_name}' LIMIT 1";
    $query = mysqli_query($link, $check_sql);
    $result = mysqli_fetch_assoc($query);
    if($result['count'] !== '1'){
        $res = mysqli_query($link, $table_ddl);
        if($res !== true){
            var_dump(date('Y-m-d H:i:s') . " [ERROR] create table = {$table_name} failed.");
            exit;
        }

        //更新 sys_table_code，加入新表，移走旧表
        $list_query = mysqli_query($link, "SELECT code FROM sys_table_code WHERE `key` = '{$table_key}' LIMIT 1");
        $list = mysqli_fetch_assoc($list_query);
        $list = explode(',', $list['code']);
        array_unshift($list, $table_name);
        $new_list = array_slice($list, 0, 2);//保留最新的2张表
        if(count($new_list) > 0){
            $new_code = implode(',', $new_list);
            mysqli_query($link, "UPDATE sys_table_code SET code = '{$new_code}' WHERE `key` = '{$table_key}' LIMIT 1");
        }
        // var_dump(date('Y-m-d H:i:s') . " [INFO] update sys_table_code, add new table = {$table_name}");
    }
}
