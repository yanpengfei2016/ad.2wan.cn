/*
Navicat MySQL Data Transfer

Source Server         : ad-polardb
Source Server Version : 50616
Source Host           : db-stat.rwlb.rds.aliyuncs.com:3306
Source Database       : qiangwan

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-09-19 09:15:58
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for sys_dts_list
-- ----------------------------
DROP TABLE IF EXISTS `sys_dts_list`;
CREATE TABLE `sys_dts_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `TableName` varchar(255) DEFAULT '' COMMENT '待迁移表名',
  `MigrationJobId` varchar(255) DEFAULT '',
  `MigrationJobStatus` varchar(255) DEFAULT 'pendingMigration' COMMENT '迁移状态',
  `Percent` int(11) DEFAULT '0' COMMENT '迁移完成百分比',
  `mtime` varchar(255) DEFAULT '',
  `ctime` varchar(255) DEFAULT '',
  `remark` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='DTS数据迁移';

-- ----------------------------
-- Records of sys_dts_list
-- ----------------------------
INSERT INTO `sys_dts_list` VALUES ('1', '5w_tkio_click_20190917', 'dtsmj9s9bii18bhbsg', 'Migrating', '9', '2019-09-18 17:49:08', '', '{\"StructureInitializationStatus\":{\"Status\":\"Finished\",\"Percent\":\"100\",\"Progress\":\"1\"},\"MigrationMode\":{\"dataInitialization\":true,\"structureInitialization\":true,\"dataSynchronization\":false},\"MigrationJobId\":\"dtsmj9s9bii18bhbsg\",\"MigrationObject\":\"[{\"DBName\":\"qiangwan\",\"NewDBName\":\"qiangwan\",\"TableIncludes\":[{\"TableName\":\"5w_tkio_click_20190917\",\"NewTableName\":\"5w_tkio_click_20190917\"}]}]\",\"MigrationJobClass\":\"medium\",\"MigrationJobStatus\":\"Migrating\",\"PrecheckStatus\":{\"Status\":\"Finished\",\"Detail\":{\"CheckItem\":[{\"CheckStatus\":\"Success\",\"ItemName\":\"CHECK_CONN_SRC\"},{\"CheckStatus\":\"Success\",\"ItemName\":\"CHECK_AUTH_SRC\"},{\"CheckStatus\":\"Success\",\"ItemName\":\"CHECK_CONN_DEST\"},{\"CheckStatus\":\"Success\",\"ItemName\":\"CHECK_AUTH_DEST\"},{\"CheckStatus\":\"Success\",\"ItemName\":\"CHECK_ENGINE\"},{\"CheckStatus\":\"Success\",\"ItemName\":\"CHECK_SRC\"},{\"CheckStatus\":\"Success\",\"ItemName\":\"CHECK_SAME_OBJ\"},{\"CheckStatus\":\"Success\",\"ItemName\":\"CHECK_DB_AVA\"}]},\"Percent\":\"100\"},\"DataInitializationStatus\":{\"Status\":\"Migrating\",\"Percent\":\"9\",\"Progress\":\"15461839\"},\"MigrationJobName\":\"5w_tkio_click_20190917\",\"PayType\":\"PostPaid\"}');
INSERT INTO `sys_dts_list` VALUES ('2', 'tkio_click_20190917', 'dtsojpl92fj18rxbrb', 'Finished', '100', '2019-09-18 17:35:44', '', '{\"StructureInitializationStatus\":{\"Status\":\"Finished\",\"Percent\":\"100\",\"Progress\":\"1\"},\"MigrationMode\":{\"dataInitialization\":true,\"structureInitialization\":true,\"dataSynchronization\":false},\"MigrationJobId\":\"dtsojpl92fj18rxbrb\",\"MigrationJobClass\":\"medium\",\"MigrationJobStatus\":\"Finished\",\"PrecheckStatus\":{\"Status\":\"Finished\",\"Detail\":{\"CheckItem\":[{\"CheckStatus\":\"Success\",\"ItemName\":\"CHECK_CONN_SRC\"},{\"CheckStatus\":\"Success\",\"ItemName\":\"CHECK_AUTH_SRC\"},{\"CheckStatus\":\"Success\",\"ItemName\":\"CHECK_CONN_DEST\"},{\"CheckStatus\":\"Success\",\"ItemName\":\"CHECK_AUTH_DEST\"},{\"CheckStatus\":\"Success\",\"ItemName\":\"CHECK_ENGINE\"},{\"CheckStatus\":\"Success\",\"ItemName\":\"CHECK_SRC\"},{\"CheckStatus\":\"Success\",\"ItemName\":\"CHECK_SAME_OBJ\"},{\"CheckStatus\":\"Success\",\"ItemName\":\"CHECK_DB_AVA\"}]},\"Percent\":\"100\"},\"DataInitializationStatus\":{\"Status\":\"Finished\",\"Percent\":\"100\",\"Progress\":\"32025806\"},\"MigrationJobName\":\"tkio_click_20190917\",\"PayType\":\"PostPaid\"}');
SET FOREIGN_KEY_CHECKS=1;
