<?php
// 已弃用
exit;
date_default_timezone_set('Asia/Shanghai');
require 'vendor/autoload.php';
$cnf = require("../config.php");

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

// Download：https://github.com/aliyun/openapi-sdk-php
// Usage：https://github.com/aliyun/openapi-sdk-php/blob/master/README.md

AlibabaCloud::accessKeyClient('LTAI4FuLN32GbKsFqjXYG5fK', 'kN3HGxzn1k4Hg1w3JNY6mrKtr4dcsM')
    ->regionId('cn-hangzhou')
    ->asDefaultClient();

try {

    $polardb_cnf = $cnf["polardb"];
    $polardb_link = mysqli_connect($polardb_cnf['host'], $polardb_cnf['user'], $polardb_cnf['password'], $polardb_cnf['database']);

    if(empty($polardb_link)){
        sleep(60);
        var_dump(date('Y-m-d H:i:s') . " [ERROR] dts connect polardb failed");
        exit;
    }

    $query = mysqli_query($polardb_link, "SELECT * FROM sys_dts_list WHERE MigrationJobStatus != 'DONE' ORDER BY id DESC");

    while ($row = mysqli_fetch_assoc($query)) {

        switch ($row["MigrationJobStatus"]) {

            case 'Migrating': //迁移中
                $id = $row['id'];
                $MigrationJobId = $row['MigrationJobId'];
                $result = DescribeMigrationJobStatus($MigrationJobId);
                if($result['MigrationJobId'] == $MigrationJobId) {
                    $MigrationJobStatus = $result['MigrationJobStatus'];
                    $Percent = $result["DataInitializationStatus"]["Percent"];
                    $remark = json_encode($result);
                    $mtime = date('Y-m-d H:i:s');
                    if(!empty($MigrationJobStatus) && !empty($Percent)) {
                        $sql = "UPDATE sys_dts_list SET MigrationJobStatus = '{$MigrationJobStatus}', Percent = '{$Percent}', remark = '{$remark}', mtime = '{$mtime}' WHERE id = $id LIMIT 1";
                        mysqli_query($polardb_link, $sql);
                    }
                }
                break;

            case 'MigrationFailed': //迁移失败
                break;

            case 'Finished': //已完成
                $id = $row['id'];
                $MigrationJobId = $row['MigrationJobId'];
                $Percent = intval($row['Percent']);
                if($Percent == 100) {
                    $result = DeleteMigrationJob($MigrationJobId);
                    $remark = json_encode($result);
                    $mtime = date('Y-m-d H:i:s');
                    if($result['Success'] == true) {
                        $sql = "UPDATE sys_dts_list SET MigrationJobStatus = 'DONE', mtime = '{$mtime}', remark = '{$remark}' WHERE id = $id LIMIT 1";
                        mysqli_query($polardb_link, $sql);
                    }
                }
                break;
            
            default:
                # code...
                break;
        }
    }


} catch (ClientException $e) {
    var_dump(date('Y-m-d H:i:s') . $e->getErrorMessage());
} catch (ServerException $e) {
    var_dump(date('Y-m-d H:i:s') . $e->getErrorMessage());
}

// 启动迁移任务
function StartMigrationJob($MigrationJobId) {
    $result = AlibabaCloud::rpc()
        ->product('Dts')
        ->version('2018-08-01')
        ->action('StartMigrationJob')
        ->method('POST')
        ->host('dts.aliyuncs.com')
        ->options([
            'query' => [
                'MigrationJobId' => $MigrationJobId
            ],
        ])
        ->request();

    return $result->toArray();
}


// 删除迁移任务
function DeleteMigrationJob($MigrationJobId) {
    $result = AlibabaCloud::rpc()
        ->product('Dts')
        ->version('2018-08-01')
        ->action('DeleteMigrationJob')
        ->method('POST')
        ->host('dts.aliyuncs.com')
        ->options([
            'query' => [
                'MigrationJobId' => $MigrationJobId
            ],
        ])
        ->request();

    return $result->toArray();
}


// 查看迁移任务状态
function DescribeMigrationJobStatus($MigrationJobId) {
    $result = AlibabaCloud::rpc()
        ->product('Dts')
        ->version('2018-08-01')
        ->action('DescribeMigrationJobStatus')
        ->method('POST')
        ->host('dts.aliyuncs.com')
        ->options([
            'query' => [
                'MigrationJobId' => $MigrationJobId
            ],
        ])
        ->request();

    return $result->toArray();
}


// 配置迁移任务
function ConfigureMigrationJob($MigrationJobId, $MigrationJobName, $TableName) {
    $result = AlibabaCloud::rpc()
        ->product('Dts')
        ->version('2018-08-01')
        ->action('ConfigureMigrationJob')
        ->method('POST')
        ->host('dts.aliyuncs.com')
        ->options([
            'query' => [
                'MigrationJobId' => $MigrationJobId,
                "MigrationJobName" => $MigrationJobName,
                "SourceEndpoint.InstanceType" => "POLARDB",
                "SourceEndpoint.Region" => "cn-hangzhou",
                "SourceEndpoint.InstanceID" => "pc-bp1on5ba4ye88vp15",
                "SourceEndpoint.EngineName" => "MySQL",
                "SourceEndpoint.UserName" => "gzy",
                "SourceEndpoint.Password" => "Aliyun@dbstat",
                "DestinationEndpoint.InstanceType" => "LocalInstance",
                "DestinationEndpoint.Region" => "cn-hangzhou",
                "DestinationEndpoint.EngineName" => "MySQL",
                "DestinationEndpoint.IP" => "221.234.42.4",
                "DestinationEndpoint.Port" => "3306",
                "DestinationEndpoint.DatabaseName" => "qiangwan",
                "DestinationEndpoint.UserName" => "root",
                "DestinationEndpoint.Password" => "root",
                "MigrationMode.StructureIntialization" => true,
                "MigrationMode.DataIntialization" => true,
                "MigrationMode.DataSynchronization" => false,
                "MigrationObject" => json_encode([[
                    "DBName" => "qiangwan",
                    "NewDBName" => "qiangwan",
                    "TableIncludes" => [[
                        "TableName" => $TableName,
                        "NewTableName" => $TableName
                    ]]
                ]])
            ],
        ])
        ->request();

    return $result->toArray();
    /*
        array(1) {
            ["Success"]=>
                bool(true)
        }
    */
}


// 购买迁移实例
function CreateMigrationJob() {
    $result = AlibabaCloud::rpc()
        ->product('Dts')
        ->version('2018-08-01')
        ->action('CreateMigrationJob')
        ->method('POST')
        ->host('dts.aliyuncs.com')
        ->options([
            'query' => [
                'RegionId' => "cn-hangzhou",
                'Region' => "cn-hangzhou",
                'MigrationJobClass' => "large",
            ],
        ])
        ->request();

    return $result->toArray();

    /*
        print_r($result->toArray());
        Array
        (
            [MigrationJobId] => dtsmj9s9bii18bhbsg
            [Success] => 1
        )
    */
}

