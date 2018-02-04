/*
Navicat MySQL Data Transfer

Source Server         : 京东云
Source Server Version : 50556
Source Host           : 114.67.237.46:3306
Source Database       : gdufeapp

Target Server Type    : MYSQL
Target Server Version : 50556
File Encoding         : 65001

Date: 2017-11-11 15:54:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for feedback
-- ----------------------------
DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sno` bigint(11) DEFAULT NULL COMMENT '学号',
  `content` varchar(500) DEFAULT NULL COMMENT '内容',
  `contact` varchar(50) DEFAULT NULL COMMENT '联系方式',
  `create_time` datetime DEFAULT NULL COMMENT '反馈创建时间',
  `fix` tinyint(4) DEFAULT '0' COMMENT '是否已解决，0为待处理，1为已解决，2为忽略不处理',
  `dev_brand` varchar(50) DEFAULT NULL COMMENT '手机品牌',
  `dev_model` varchar(50) DEFAULT NULL COMMENT '手机型号',
  `os_version` varchar(50) DEFAULT NULL COMMENT '手机系统版本号',
  `comment` varchar(255) DEFAULT NULL COMMENT '管理员对该反馈的评论注释(用户不可见)',
  `app_ver` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8;
