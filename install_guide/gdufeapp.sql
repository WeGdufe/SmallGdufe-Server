/*
 Navicat Premium Data Transfer

 Source Server         : 
 Source Server Type    : MySQL
 Source Server Version : 100122
 Source Host           : 127.0.0.1:3306
 Source Schema         : gdufeapp

 Target Server Type    : MySQL
 Target Server Version : 100122
 File Encoding         : 65001

 Date: 05/02/2018 00:06:00
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

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

SET FOREIGN_KEY_CHECKS = 1;


-- ----------------------------
-- Table structure for im_feed
-- ----------------------------
DROP TABLE IF EXISTS `im_feed`;
CREATE TABLE `im_feed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `user_id` bigint(11) NOT NULL DEFAULT '0' COMMENT '学号或者老师工号',
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  `photos` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `create_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Records of im_feed
-- ----------------------------
BEGIN;
INSERT INTO `im_feed`(`id`, `parent_id`, `user_id`, `content`, `is_deleted`, `photos`, `create_time`) VALUES (1, 0, 13251102210, '关于APP和小程序：APP是[13级光]在17年4月当个人毕设发版的，最初计划的就是一个长久维护的产品，就做成了开源的，由一届一届的师弟妹一直维护下去。\n', 0, '', 1517758608);
INSERT INTO `im_feed`(`id`, `parent_id`, `user_id`, `content`, `is_deleted`, `photos`, `create_time`) VALUES (2, 0, 13251102210, '17年6月，[14级发]接手做出了小程序版，小程序包含了Dr.com和桌面控件之外的大部分功能，更新频率较高。', 0, '', 1517758607);
INSERT INTO `im_feed`(`id`, `parent_id`, `user_id`, `content`, `is_deleted`, `photos`, `create_time`) VALUES (3, 0, 13251102210, 'iOS版：18年2月发布，AppStore搜 \'茶珂\' ，下个版本会更名小广财。\nThanks: [14级文]和[14级权]', 0, '', 1517758606);
INSERT INTO `im_feed`(`id`, `parent_id`, `user_id`, `content`, `is_deleted`, `photos`, `create_time`) VALUES (4, 0, 13251102210, '现[13级光]已毕业，两个14级的今年也该毕业了，故需要一些给力的15，16级继续维护。开发群可加 631036490，通用编程咨询群 649033516', 0, 'http://ww2.sinaimg.cn/large/0060lm7Tly1fogf5p4hyzj30u01bcwk2.jpg', 1517758605);
INSERT INTO `im_feed`(`id`, `parent_id`, `user_id`, `content`, `is_deleted`, `photos`, `create_time`) VALUES (5, 0, 13251102210, '推荐个隐藏比较深的功能：桌面课表，是系统小控件，另外Dr.com现在提速账号稳定性很好', 0, 'http://ww1.sinaimg.cn/large/0060lm7Tly1fo4uhzkwr4j30tz0v3npd.jpg', 1517759220);
INSERT INTO `im_feed`(`id`, `parent_id`, `user_id`, `content`, `is_deleted`, `photos`, `create_time`) VALUES (6, 0, 0, 'iOS版于2018.02.15 发布啦，AppStore搜  小广财', 0, 'http://ww2.sinaimg.cn/large/00711cMmly1fogfea2oofj30ra0smjzf.jpg', 1518624562);
COMMIT;

-- ----------------------------
-- Table structure for im_feed_reply
-- ----------------------------
DROP TABLE IF EXISTS `im_feed_reply`;
CREATE TABLE `im_feed_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `target_user_id` int(11) NOT NULL,
  `user_id` bigint(11) NOT NULL COMMENT '学号或者老师工号',
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `photos` varchar(255) COLLATE utf8_bin NOT NULL,
  `create_time` int(11) NOT NULL,
  `is_deleted` tinyint(4) NOT NULL,
  `up_count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `user_id` bigint(11) NOT NULL,
  `role` tinyint(4) NOT NULL COMMENT '1学生,2老师,3学校管理员',
  `college` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '学院',
  `faculty` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '系，如计算机系',
  `name` varchar(30) COLLATE utf8_bin NOT NULL,
  `password` varchar(64) COLLATE utf8_bin NOT NULL,
  `create_time` int(11) NOT NULL,
  `last_login_time` int(11) NOT NULL,
  `admin_role` tinyint(4) NOT NULL COMMENT '0普通人,1系统管理员,2开发者',
  `avatar` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Records of user
-- ----------------------------
BEGIN;
INSERT INTO `user` VALUES (0, 13251102210, 0, '', '', '', '', 1510506379, 1514710087, 0, '');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
