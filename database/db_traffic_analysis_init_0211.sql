/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : db_traffic_analysis

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2018-02-11 14:47:20
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for table_event_total_stat
-- ----------------------------
DROP TABLE IF EXISTS `table_event_total_stat`;
CREATE TABLE `table_event_total_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `web_id` int(11) NOT NULL DEFAULT '1' COMMENT '追踪网站id',
  `equipment_type` tinyint(6) NOT NULL DEFAULT '1' COMMENT '设备来源:1=pc,2=wap,3=android,4=ios',
  `category` varchar(20) DEFAULT NULL COMMENT '事件分类 1=咨询,2=模拟开户,3=真实开户,4=登录,5=入金',
  `action` varchar(20) DEFAULT NULL COMMENT '事件动作',
  `name` varchar(20) DEFAULT NULL COMMENT '事件名称',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '点击事件次数',
  `hour` varchar(2) NOT NULL COMMENT '统计小时 H',
  `date` varchar(8) NOT NULL COMMENT '统计日期 Ymd',
  `time` varchar(10) NOT NULL COMMENT '统计时间戳',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='点击事件统计表';

-- ----------------------------
-- Table structure for table_event_user_stat
-- ----------------------------
DROP TABLE IF EXISTS `table_event_user_stat`;
CREATE TABLE `table_event_user_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `web_id` int(11) NOT NULL DEFAULT '1' COMMENT '追踪网站id',
  `event_id` int(11) NOT NULL COMMENT 'event_total_stat主键',
  `user_id` varchar(100) NOT NULL COMMENT '用户行为id',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT 'num数',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户点击事件统计表\r\n与event_total_stat关联，多对一';

-- ----------------------------
-- Table structure for table_traffic_total_stat
-- ----------------------------
DROP TABLE IF EXISTS `table_traffic_total_stat`;
CREATE TABLE `table_traffic_total_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `web_id` int(11) NOT NULL DEFAULT '1' COMMENT '追踪网站id',
  `equipment_type` tinyint(6) NOT NULL DEFAULT '1' COMMENT '设备类型:1=pc,2=wap,3=android,4=ios',
  `pv` int(11) NOT NULL DEFAULT '0' COMMENT 'pv数',
  `uv` int(11) NOT NULL DEFAULT '0' COMMENT 'uv数',
  `page_name` varchar(100) DEFAULT NULL COMMENT '页面名称',
  `page_url` varchar(255) DEFAULT NULL COMMENT '页面地址',
  `hour` varchar(2) NOT NULL COMMENT '统计小时 H',
  `date` varchar(8) NOT NULL COMMENT '统计日期 Ymd',
  `time` varchar(10) NOT NULL COMMENT '统计时间戳',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='流量pv,uv统计表';

-- ----------------------------
-- Table structure for table_traffic_user_stat
-- ----------------------------
DROP TABLE IF EXISTS `table_traffic_user_stat`;
CREATE TABLE `table_traffic_user_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `web_id` int(11) NOT NULL DEFAULT '1' COMMENT '追踪网站id',
  `traffic_id` int(11) NOT NULL COMMENT 'traffic_total_stat主键',
  `user_id` varchar(100) NOT NULL COMMENT '用户行为id',
  `pv` int(11) NOT NULL DEFAULT '0' COMMENT 'pv数',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户流量统计表\r\n与traffic_total_stat关联，多对一';

-- ----------------------------
-- Table structure for table_user_source
-- ----------------------------
DROP TABLE IF EXISTS `table_user_source`;
CREATE TABLE `table_user_source` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `web_id` int(11) NOT NULL DEFAULT '1',
  `equipment_type` tinyint(6) NOT NULL COMMENT '设备类型:1=pc,2=wap,3=android,4=ios',
  `user_id` varchar(50) DEFAULT NULL COMMENT '用户访问记录的key',
  `page_url` varchar(255) DEFAULT NULL COMMENT '页面url路径',
  `source` varchar(20) DEFAULT NULL COMMENT '来源渠道',
  `medium` varchar(20) DEFAULT NULL COMMENT '媒介',
  `campaign` varchar(20) DEFAULT NULL COMMENT '系列',
  `content` varchar(20) DEFAULT NULL COMMENT '内容',
  `term` varchar(20) DEFAULT NULL COMMENT '关键字',
  `date` varchar(8) NOT NULL COMMENT '统计日期 Ymd',
  `time` varchar(10) NOT NULL COMMENT '统计时间戳',
  `update_time` int(10) NOT NULL,
  `create_time` int(10) NOT NULL COMMENT '触发时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户访问明细表';

-- ----------------------------
-- Table structure for table_user_visit
-- ----------------------------
DROP TABLE IF EXISTS `table_user_visit`;
CREATE TABLE `table_user_visit` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `web_id` int(11) NOT NULL DEFAULT '1',
  `equipment_type` tinyint(6) NOT NULL COMMENT '设备类型:1=pc,2=wap,3=android,4=ios',
  `user_id` varchar(50) DEFAULT NULL COMMENT '用户访问记录的key',
  `page_title` varchar(100) DEFAULT NULL COMMENT '页面标题',
  `page_url` varchar(255) DEFAULT NULL COMMENT '页面url路径',
  `event_category` varchar(20) DEFAULT NULL COMMENT '事件分类 1=咨询,2=模拟开户,3=真实开户,4=登录,5=入金',
  `event_action` varchar(20) DEFAULT NULL COMMENT '事件动作',
  `event_name` varchar(20) DEFAULT NULL COMMENT '事件名称',
  `date` varchar(8) NOT NULL COMMENT '统计日期 Ymd',
  `time` varchar(10) NOT NULL COMMENT '统计时间戳',
  `update_time` int(10) NOT NULL,
  `create_time` int(10) NOT NULL COMMENT '触发时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户访问明细表';
